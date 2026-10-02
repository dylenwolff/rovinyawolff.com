@extends('layouts.site')
@section('title',$portfolioHeading.' | '.$settings->name)
@section('description',$portfolioIntro)
@section('content')
<section class="archive-hero"><div class="site-container"><p class="kicker">PORTFOLIO ARCHIVE</p><div class="archive-title"><h1>{{ $portfolioHeading }}</h1><p>{{ $portfolioIntro }}</p></div><div class="archive-filters"><a class="{{ request()->routeIs('work')?'active':'' }}" href="{{ route('work') }}">All work</a><a class="{{ request()->routeIs('work.publications')?'active':'' }}" href="{{ route('work.publications') }}">Publications</a><a class="{{ request()->routeIs('work.social')?'active':'' }}" href="{{ route('work.social') }}">Campaigns & social</a></div></div></section>
<section class="site-container archive-body"><div class="archive-grid">
@forelse($projects as $project)
<article class="archive-item" data-reveal><a href="{{ route('work.show',$project) }}"><div class="archive-image">@if($project->image_path)<img src="{{ asset('storage/'.$project->image_path) }}" alt="{{ $project->title }}" loading="lazy">@else<div class="fallback-cover"><span>{{ mb_substr($project->title,0,1) }}</span></div>@endif<span class="view-badge">View project ↗</span></div><div class="project-meta"><div><small>{{ $project->category }}</small><h2>{{ $project->title }}</h2></div><span class="project-type">{{ $project->type }}</span></div><p>{{ $project->summary }}</p></a></article>
@empty
@php($samples=request()->routeIs('work.social') ? [
 ['social-odyssey.jpg','Odyssey Workshop','Campaign system'],['social-community.jpg','Community Stories','Social storytelling'],['social-council.jpg','Council Communications','Event campaign']
] : [
 ['novara-cover.jpg','Novara','Magazine design'],['district-directory-cover.jpg','District Directory 2026/27','Directory design'],['md-pulse-cover.jpg','MD Pulse','Magazine design'],['club-directory-cover.jpg','Club Membership Directory','Directory design']
])
@foreach($samples as $sample)<article class="archive-item" data-reveal><div class="archive-image"><img src="{{ asset('images/portfolio/'.$sample[0]) }}" alt="{{ $sample[1] }}"></div><div class="project-meta"><div><small>{{ $sample[2] }}</small><h2>{{ $sample[1] }}</h2></div><span class="project-type">Selected</span></div><p>A selected piece from Rovinya’s growing visual design archive. The full case study is being prepared.</p></article>@endforeach
@endforelse
</div></section>
<section class="closing-story archive-cta"><div class="site-container"><div class="closing-panel"><p class="kicker">YOUR PROJECT COULD BE NEXT</p><h2>Bring the content.<br><em>Let’s shape the story.</em></h2><a class="button-primary" href="{{ route('contact') }}">Start a conversation</a></div></div></section>
@endsection
