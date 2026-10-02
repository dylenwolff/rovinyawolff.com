@extends('layouts.site')
@section('description','Publication and social media design by Rovinya Wolff. Distinct visual experiences for pages, campaigns, and communities.')
@section('content')
<section class="hero-live relative min-h-screen overflow-hidden bg-[#131014] text-white" data-hero>
    <div class="hero-aurora" aria-hidden="true"><i></i><i></i><i></i></div>
    <div class="site-container relative z-10 flex min-h-screen flex-col justify-center pb-16 pt-32">
        <div class="mx-auto max-w-5xl text-center" data-reveal>
            <p class="eyebrow text-[#ffc7e8]">{{ $settings->professional_title }} • Colombo</p>
            <h1 class="mt-6 text-[clamp(4rem,11vw,9.5rem)] font-black leading-[.78] tracking-[-.075em]">IDEAS THAT<br><span class="gradient-word">MOVE.</span></h1>
            <p class="mx-auto mt-8 max-w-2xl text-lg leading-8 text-white/65 sm:text-xl">I turn stories, communities, and campaigns into visual experiences people want to notice, explore, and remember.</p>
        </div>

        <div class="discipline-stage mx-auto mt-14 grid w-full max-w-6xl items-center gap-7 lg:grid-cols-[1fr_1.15fr_1fr]" data-tilt-stage>
            <a href="{{ route('work.publications') }}" class="discipline-copy group text-center lg:text-right" data-reveal><span class="text-xs font-bold uppercase tracking-[.22em] text-[#b9ffec]">Pages with presence</span><h2 class="mt-2 text-4xl font-black tracking-[-.04em] sm:text-5xl">Publication<br>design</h2><p class="mt-3 text-sm leading-6 text-white/55">Magazines, club directories, reports, and commemorative books.</p><span class="mt-5 inline-flex text-sm font-bold text-[#b9ffec]">Explore publications ↗</span></a>

            <a href="{{ route('work') }}" class="visual-stack" aria-label="Explore all work" data-tilt>
                <div class="visual-card visual-card-back"><span>RW</span><small>VISUAL STORIES</small></div>
                <div class="visual-card visual-card-mid"><div class="poster-orbit"></div><strong>MAKE IT<br>MEAN<br>SOMETHING.</strong></div>
                <div class="visual-card visual-card-front"><span class="text-[10px] tracking-[.25em]">SELECTED WORK</span><b>R</b><small>ROVINYA WOLFF</small></div>
            </a>

            <a href="{{ route('work.social') }}" class="discipline-copy group text-center lg:text-left" data-reveal><span class="text-xs font-bold uppercase tracking-[.22em] text-[#ffd2a8]">Designed to connect</span><h2 class="mt-2 text-4xl font-black tracking-[-.04em] sm:text-5xl">Social<br>design</h2><p class="mt-3 text-sm leading-6 text-white/55">Campaigns, posters, announcements, and visual content systems.</p><span class="mt-5 inline-flex text-sm font-bold text-[#ffd2a8]">Explore campaigns ↗</span></a>
        </div>
        <div class="mt-16 flex flex-wrap justify-center gap-3"><a href="{{ route('work') }}" class="button-primary bg-white text-[#171318] hover:bg-[#ffc7e8]">View my work</a><a href="{{ route('contact') }}" class="button-secondary border-white/20 bg-white/5 text-white">Start a project</a></div>
    </div>
</section>

<div class="marquee" aria-hidden="true"><div><span>PUBLICATION DESIGN</span><i>✦</i><span>SOCIAL MEDIA</span><i>✦</i><span>POSTERS</span><i>✦</i><span>VISUAL STORIES</span><i>✦</i><span>PUBLICATION DESIGN</span><i>✦</i><span>SOCIAL MEDIA</span></div></div>

<section class="site-container py-24 sm:py-32"><div class="grid gap-12 lg:grid-cols-[.75fr_1.25fr]"><div data-reveal><p class="eyebrow">The work</p><h2 class="mt-4 text-5xl font-black leading-[.9] tracking-[-.055em] sm:text-7xl">Two worlds.<br><span class="font-[family-name:var(--font-display)] font-normal italic text-[#9d3f78]">One visual voice.</span></h2><p class="section-copy">Some stories deserve to unfold page by page. Others need to make an impact in a second. I design for both.</p></div><div class="grid gap-5 sm:grid-cols-2">
<a href="{{ route('work.publications') }}" class="world-card world-publications" data-reveal><span>01 / PUBLICATIONS</span><div class="mini-book"><i></i><i></i><i></i></div><h3>Stories you can hold onto.</h3><p>Editorial systems with rhythm, hierarchy, and character.</p><b>See the books →</b></a>
<a href="{{ route('work.social') }}" class="world-card world-social" data-reveal><span>02 / SOCIAL</span><div class="mini-grid"><i></i><i></i><i></i><i></i></div><h3>Ideas built to stop the scroll.</h3><p>Flexible visuals that make campaigns feel connected.</p><b>See the campaigns →</b></a>
</div></div></section>

<section class="overflow-hidden bg-[#151217] py-24 text-white sm:py-32"><div class="site-container"><div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between" data-reveal><div><p class="eyebrow text-[#ffc7e8]">Selected work</p><h2 class="mt-4 text-5xl font-black tracking-[-.055em] sm:text-7xl">Made to stand out.</h2></div><a href="{{ route('work') }}" class="font-bold text-[#b9ffec]">View the full portfolio ↗</a></div>
<div class="project-ribbon mt-14">@forelse($projects as $project)<a href="{{ route('work.show',$project) }}" class="living-project" data-reveal>@if($project->image_path)<img src="{{ asset('storage/'.$project->image_path) }}" alt="{{ $project->title }}" loading="lazy">@else<div class="project-placeholder"><i></i><strong>{{ $project->title }}</strong></div>@endif<div><small>{{ $project->category }}</small><h3>{{ $project->title }}</h3></div></a>@empty
<a href="{{ route('work.publications') }}" class="living-project demo-one"><div class="project-placeholder"><i></i><strong>Publication<br>stories</strong></div><div><small>MAGAZINES & DIRECTORIES</small><h3>Designed page by page</h3></div></a><a href="{{ route('work.social') }}" class="living-project demo-two"><div class="project-placeholder"><i></i><strong>Social<br>energy</strong></div><div><small>CAMPAIGNS & POSTERS</small><h3>Visuals that connect</h3></div></a>
@endforelse</div></div></section>

<section class="site-container py-24 sm:py-32"><div class="cta-alive" data-reveal><div class="cta-orb"></div><p class="eyebrow relative z-10 text-[#4b2742]">Let’s make something alive</p><h2 class="relative z-10 mt-5 max-w-4xl text-5xl font-black leading-[.93] tracking-[-.06em] sm:text-7xl">Your next idea deserves more than a template.</h2><div class="relative z-10 mt-8 flex flex-wrap gap-3"><a class="button-primary" href="{{ route('contact') }}">Tell me about it</a><a class="button-secondary" href="{{ route('about') }}">Meet Rovinya</a></div></div></section>
@endsection
