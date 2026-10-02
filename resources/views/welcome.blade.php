@extends('layouts.site')
@section('description','Rovinya Wolff is an editorial and visual designer creating publications, directories, magazines, and campaign design.')
@section('content')
<section class="editorial-hero">
  <div class="site-container hero-grid">
    <div class="hero-copy" data-reveal>
      <p class="kicker">INDEPENDENT DESIGNER · COLOMBO</p>
      <h1>Stories,<br><em>shaped</em><br>visually.</h1>
      <p class="hero-intro">I transform information, people, and purpose into publications and campaigns that feel clear, distinctive, and worth remembering.</p>
      <div class="mt-8 flex flex-wrap gap-3"><a class="button-primary" href="{{ route('work') }}">Explore the work</a><a class="button-text" href="{{ route('contact') }}">Discuss a project <span>↗</span></a></div>
    </div>
    <div class="cover-stage" data-cover-stage aria-label="Selected publication covers">
      <figure class="cover-card cover-a"><img src="{{ asset('images/portfolio/novara-cover.jpg') }}" alt="Novara magazine cover designed by Rovinya Wolff"><figcaption>NOVARA · 2026</figcaption></figure>
      <figure class="cover-card cover-b"><img src="{{ asset('images/portfolio/district-directory-cover.jpg') }}" alt="District Directory cover designed by Rovinya Wolff"><figcaption>DISTRICT DIRECTORY</figcaption></figure>
      <figure class="cover-card cover-c"><img src="{{ asset('images/portfolio/md-pulse-cover.jpg') }}" alt="MD Pulse magazine cover designed by Rovinya Wolff"><figcaption>MD PULSE</figcaption></figure>
      <span class="stage-note">SELECTED EDITORIAL WORK<br>2025 / 2026</span>
    </div>
  </div>
  <div class="hero-index"><span>PUBLICATIONS</span><i></i><span>CAMPAIGNS</span><i></i><span>SOCIAL STORIES</span><i></i><span>DIRECTORIES</span></div>
</section>

<section class="manifesto-section"><div class="site-container manifesto-grid"><p class="section-number">01 / APPROACH</p><div data-reveal><h2>Design that gives<br>information <em>a voice.</em></h2><p>Rovinya’s work moves between detailed editorial systems and immediate campaign communication. The thread connecting both is thoughtful hierarchy, expressive imagery, and a strong sense of occasion.</p></div></div></section>

<section class="work-preview">
  <div class="site-container"><div class="section-heading" data-reveal><div><p class="kicker">SELECTED WORK</p><h2>Made page by page.<br>Seen all at once.</h2></div><a href="{{ route('work') }}">View the full archive <span>↗</span></a></div>
  <div class="editorial-grid">
  @forelse($projects as $project)
    <article class="editorial-project" data-reveal><a href="{{ route('work.show',$project) }}"><div class="project-visual">@if($project->image_path)<img src="{{ asset('storage/'.$project->image_path) }}" alt="{{ $project->title }}">@else<div class="fallback-cover"><span>{{ mb_substr($project->title,0,1) }}</span></div>@endif</div><div class="project-meta"><div><small>{{ $project->category }}</small><h3>{{ $project->title }}</h3></div><span>↗</span></div></a></article>
  @empty
    <article class="editorial-project project-tall" data-reveal><a href="{{ route('work.publications') }}"><div class="project-visual"><img src="{{ asset('images/portfolio/novara-cover.jpg') }}" alt="Novara magazine"></div><div class="project-meta"><div><small>MAGAZINE DESIGN</small><h3>Novara</h3></div><span>↗</span></div></a></article>
    <article class="editorial-project project-wide" data-reveal><a href="{{ route('work.publications') }}"><div class="project-visual"><img src="{{ asset('images/portfolio/district-directory-cover.jpg') }}" alt="District Directory"></div><div class="project-meta"><div><small>DIRECTORY DESIGN</small><h3>District Directory 2026/27</h3></div><span>↗</span></div></a></article>
    <article class="editorial-project project-tall" data-reveal><a href="{{ route('work.publications') }}"><div class="project-visual"><img src="{{ asset('images/portfolio/md-pulse-cover.jpg') }}" alt="MD Pulse magazine"></div><div class="project-meta"><div><small>EDITORIAL SERIES</small><h3>MD Pulse</h3></div><span>↗</span></div></a></article>
  @endforelse
  </div></div>
</section>

<section class="disciplines"><div class="site-container"><p class="section-number">02 / DISCIPLINES</p><div class="discipline-row"><a href="{{ route('work.publications') }}"><span>01</span><h2>Publication<br>design</h2><p>Magazines, directories, newsletters, reports, and long-form editorial systems.</p><b>Explore publications ↗</b></a><a href="{{ route('work.social') }}"><span>02</span><h2>Campaign &<br>social design</h2><p>Event identities, posters, social series, and visual storytelling for communities.</p><b>Explore campaigns ↗</b></a></div></div></section>

<section class="closing-story"><div class="site-container"><div class="closing-panel" data-reveal><p class="kicker">HAVE A STORY TO SHARE?</p><h2>Let’s make it<br><em>matter visually.</em></h2><a class="button-primary" href="{{ route('contact') }}">Start a conversation</a><span class="closing-mark">R</span></div></div></section>
@endsection
