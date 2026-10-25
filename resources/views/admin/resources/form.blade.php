@extends('admin.layout')
@section('title',($record->exists?'Edit ':'New ').$config['label'])
@section('content')
<header class="admin-page-header is-form"><div><a class="admin-back-link" href="{{ route('admin.resources.index',$resource) }}">← Back to {{ str($config['label'])->lower() }}</a><h1>{{ $record->exists ? 'Edit record' : 'Add new record' }}</h1><p>{{ $record->exists ? 'Update the details below and save your changes.' : 'Complete the details below to add it to the studio.' }}</p></div></header>
<form method="POST" enctype="multipart/form-data" action="{{ $record->exists ? route('admin.resources.update',[$resource,$record->id]) : route('admin.resources.store',$resource) }}" class="admin-form-panel">@csrf @if($record->exists)@method('PUT')@endif
<div class="admin-form-intro"><span>01</span><div><strong>Record details</strong><p>Fields marked with an asterisk are required.</p></div></div><div class="admin-form-grid">
@foreach($config['fields'] as $name=>$field)
@php
    $type = $field['type'] ?? 'text';
    $value = old($name, $record->{$name} ?? ($field['default'] ?? null));
    $readonly = in_array($name, $config['readonly'] ?? []);
    $wide = in_array($type, ['textarea','file','files','tags']) || in_array($name, ['hero_heading','hero_accent','about_heading','contact_heading']);
@endphp
<label class="admin-field {{ $wide ? 'is-wide' : '' }}"><span>{{ str($name)->replace('_',' ')->title() }} @if($field['required']??false)<b>*</b>@endif</span>
@if($type==='textarea')<textarea name="{{ $name }}" rows="{{ $name==='body'?14:5 }}" {{ $readonly?'readonly':'' }}>{{ $value }}</textarea>
@elseif($type==='select')<select name="{{ $name }}">@foreach($field['options'] as $key=>$label)<option value="{{ $key }}" @selected((string)$value===(string)$key)>{{ $label }}</option>@endforeach</select>
@elseif($type==='relation')<select name="{{ $name }}"><option value="">None selected</option>@foreach($relations[$name] as $option)<option value="{{ $option->id }}" @selected((string)$value===(string)$option->id)>{{ $option->{$field['label_field']} }}</option>@endforeach</select>
@elseif($type==='checkbox')<input type="hidden" name="{{ $name }}" value="0"><span class="admin-toggle-row"><input type="checkbox" name="{{ $name }}" value="1" class="toggle" @checked((bool)$value)><em>Enabled</em></span>
@elseif(in_array($type,['file','files']))@if($value)<small>Current: {{ is_array($value)?implode(', ',$value):$value }}</small>@endif<input type="file" name="{{ $name }}{{ $type==='files'?'[]':'' }}" accept="{{ $field['accept']??'' }}" {{ $type==='files'?'multiple':'' }}>
@elseif($type==='tags')<input name="{{ $name }}" value="{{ is_array($value)?implode(', ',$value):$value }}" placeholder="Separate items with commas">
@else<input type="{{ $type }}" name="{{ $name }}" value="{{ $value instanceof \DateTimeInterface ? ($type==='datetime-local'?$value->format('Y-m-d\TH:i'):$value->format('Y-m-d')) : $value }}" {{ $readonly?'readonly':'' }} @if($field['required']??false) required @endif>@endif</label>
@endforeach</div><footer class="admin-form-actions"><a href="{{ route('admin.resources.index',$resource) }}">Cancel</a><button>Save {{ str($config['label'])->singular()->lower() }}</button></footer></form>
@endsection
