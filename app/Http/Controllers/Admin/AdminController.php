<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Activity, OutreachEmail};
use App\Services\NativeMailService;
use App\Support\AdminResources;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class AdminController extends Controller
{
    public function dashboard(): View { return view('admin.dashboard', ['resources'=>AdminResources::all()]); }

    public function index(Request $request, string $resource): View
    {
        $config=AdminResources::get($resource); $model=$config['model'];
        $query=$model::query();
        if ($search=trim((string)$request->query('q'))) $query->where(function($q)use($config,$search){foreach($config['columns'] as $i=>$column){if(str_contains($column,'.'))continue;$i?$q->orWhere($column,'like',"%{$search}%"):$q->where($column,'like',"%{$search}%");}});
        return view('admin.resources.index', ['resource'=>$resource,'config'=>$config,'records'=>$query->latest('id')->paginate(20)->withQueryString()]);
    }

    public function create(string $resource): View|RedirectResponse
    {
        $config=AdminResources::get($resource);
        if(($config['singleton']??false)&&$record=$config['model']::first()) return redirect()->route('admin.resources.edit',[$resource,$record]);
        return $this->form($resource,$config,new $config['model']);
    }

    public function edit(string $resource, int $record): View { $config=AdminResources::get($resource); return $this->form($resource,$config,$config['model']::findOrFail($record)); }

    private function form(string $resource,array $config,Model $record): View
    {
        $relations=[]; foreach($config['fields'] as $name=>$field) if(($field['type']??'')==='relation') $relations[$name]=$field['model']::query()->orderBy($field['label_field'])->get();
        return view('admin.resources.form',compact('resource','config','record','relations'));
    }

    public function store(Request $request,string $resource): RedirectResponse { $config=AdminResources::get($resource); $model=new $config['model']; return $this->save($request,$resource,$config,$model); }
    public function update(Request $request,string $resource,int $record): RedirectResponse { $config=AdminResources::get($resource); return $this->save($request,$resource,$config,$config['model']::findOrFail($record)); }

    private function save(Request $request,string $resource,array $config,Model $record): RedirectResponse
    {
        $rules=[]; foreach($config['fields'] as $name=>$field){$type=$field['type']??'text'; if(in_array($type,['file','files'],true)){$rules[$name]=$type==='files'?'nullable|array':'nullable|file'; if($type==='files')$rules[$name.'.*']='file|max:10240'; continue;} $rule=($field['required']??false)?'required':'nullable'; $rule.='|'.match($type){'email'=>'email','url'=>'url','number'=>'numeric','checkbox'=>'boolean',default=>'string'}; $rules[$name]=$rule;}
        $data=$request->validate($rules);
        foreach($config['fields'] as $name=>$field){$type=$field['type']??'text'; if($type==='checkbox')$data[$name]=$request->boolean($name); if($type==='tags')$data[$name]=array_values(array_filter(array_map('trim',explode(',',(string)($data[$name]??''))))); if($type==='file'&&$request->hasFile($name))$data[$name]=$request->file($name)->store($field['directory'],'public'); if($type==='files'&&$request->hasFile($name))$data[$name]=array_map(fn($file)=>$file->store($field['directory'],'public'),$request->file($name));}
        $record->fill($data)->save();
        return redirect()->route('admin.resources.index',$resource)->with('success',$config['label'].' saved.');
    }

    public function destroy(string $resource,int $record): RedirectResponse { $config=AdminResources::get($resource); $config['model']::findOrFail($record)->delete(); return back()->with('success','Record deleted.'); }

    public function sendEmail(int $record,NativeMailService $mailer): RedirectResponse
    {
        $email=OutreachEmail::findOrFail($record); abort_unless(in_array($email->status,['ready','approved'],true)&&!$email->sent_at,422);
        try{$id=$mailer->send($email->recipient_email,$email->subject,$email->body,$email->recipient_name);$follow=$email->follow_up_at??now()->addDays(4);$email->update(['status'=>'sent','sent_at'=>now(),'external_message_id'=>$id,'follow_up_at'=>$follow]);$email->lead?->update(['status'=>'contacted','next_follow_up_at'=>$follow]);Activity::create(['contact_id'=>$email->contact_id,'lead_id'=>$email->lead_id,'type'=>'email','subject'=>$email->subject,'details'=>'Outreach email sent to '.$email->recipient_email.'. Message ID: '.$id,'occurred_at'=>now()]);return back()->with('success','Email sent and archived in Roundcube Sent.');}catch(Throwable $e){report($e);return back()->withErrors(['email'=>$e->getMessage()]);}
    }
}
