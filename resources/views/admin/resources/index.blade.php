@extends('admin.layout')
@section('title',$config['label'])
@section('content')
<header class="admin-page-header"><div><p class="admin-eyebrow">{{ str($resource)->replace('-',' ') }}</p><h1>{{ $config['label'] }}</h1><p>Review, organise and keep this part of the studio up to date.</p></div>@unless(($config['singleton']??false)&&$records->count())<a class="admin-primary-button" href="{{ route('admin.resources.create',$resource) }}"><span>＋</span> Add new</a>@endunless</header>
<section class="admin-panel"><div class="admin-panel-toolbar"><form class="admin-search"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg><input name="q" value="{{ request('q') }}" placeholder="Search {{ str($config['label'])->lower() }}"><button>Search</button></form><div class="admin-record-count"><strong>{{ $records->total() }}</strong> {{ Str::plural('record',$records->total()) }}</div></div>
<div class="admin-table-wrap"><table class="admin-table"><thead><tr>@foreach($config['columns'] as $column)<th>{{ str($column)->afterLast('.')->replace('_',' ')->title() }}</th>@endforeach<th><span class="sr-only">Actions</span></th></tr></thead><tbody>
@forelse($records as $record)<tr>@foreach($config['columns'] as $column)
@php
    $value = data_get($record, $column);
    $isChip = in_array(str($column)->afterLast('.')->value(), ['status','stage','type','source','country','priority']);
@endphp
<td>@if(is_bool($value))<span class="admin-boolean {{ $value?'is-yes':'is-no' }}"><i></i>{{ $value?'Yes':'No' }}</span>@elseif($value instanceof \DateTimeInterface)<span class="admin-muted">{{ $value->format('d M Y, H:i') }}</span>@elseif($isChip)<span class="admin-chip">{{ str((string)$value)->replace('_',' ')->title() }}</span>@else<span class="admin-cell-text">{{ Str::limit((string)$value,64) ?: '—' }}</span>@endif</td>@endforeach<td class="admin-row-actions"><div>@if($resource==='outreach-emails'&&in_array($record->status,['ready','approved'])&&!$record->sent_at)<form method="POST" action="{{ route('admin.outreach.send',$record) }}" onsubmit="return confirm('Send this email now?')">@csrf<button class="admin-action is-send">Send</button></form>@endif<a class="admin-action" href="{{ route('admin.resources.edit',[$resource,$record->id]) }}">Edit</a><form method="POST" action="{{ route('admin.resources.destroy',[$resource,$record->id]) }}" onsubmit="return confirm('Delete this record?')">@csrf @method('DELETE')<button class="admin-action is-delete">Delete</button></form></div></td></tr>
@empty<tr><td colspan="{{ count($config['columns'])+1 }}"><div class="admin-empty"><span>◇</span><strong>No records yet</strong><p>Add the first record to get started.</p></div></td></tr>@endforelse
</tbody></table></div>@if($records->hasPages())<div class="admin-pagination">{{ $records->links() }}</div>@endif</section>
@endsection
