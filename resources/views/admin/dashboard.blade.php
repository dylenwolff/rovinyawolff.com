@extends('admin.layout')
@section('title','Dashboard')
@section('content')
<header class="admin-page-header"><div><p class="admin-eyebrow">Studio overview</p><h1>Good to see you.</h1><p>Everything for the portfolio and client pipeline, in one calm workspace.</p></div><a class="admin-primary-button" href="{{ route('admin.resources.create','organizations') }}"><span>＋</span> Add prospect</a></header>
<div class="admin-dashboard-grid">@foreach($resources as $key=>$config)<a class="admin-dashboard-card" href="{{ route('admin.resources.index',$key) }}"><span class="admin-card-index">{{ str_pad($loop->iteration,2,'0',STR_PAD_LEFT) }}</span><div><strong>{{ $config['model']::count() }}</strong><h2>{{ $config['label'] }}</h2><p>Open and manage records</p></div><i>↗</i></a>@endforeach</div>
@endsection
