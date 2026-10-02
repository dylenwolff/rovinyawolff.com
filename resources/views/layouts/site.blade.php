<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
@php($metaTitle=trim($__env->yieldContent('title'))?:$settings->name.' | '.$settings->professional_title)
@php($metaDescription=trim($__env->yieldContent('description'))?:'Editorial, publication, and campaign design by Rovinya Wolff.')
@php($metaImage=asset('images/portfolio/novara-cover.jpg'))
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="description" content="{{ $metaDescription }}"><meta name="theme-color" content="#f2eee6"><link rel="canonical" href="{{ url()->current() }}">
<meta property="og:type" content="website"><meta property="og:site_name" content="{{ $settings->name }}"><meta property="og:title" content="{{ $metaTitle }}"><meta property="og:description" content="{{ $metaDescription }}"><meta property="og:url" content="{{ url()->current() }}"><meta property="og:image" content="{{ $metaImage }}"><meta name="twitter:card" content="summary_large_image"><link rel="icon" href="{{ asset('images/favicon.svg') }}" type="image/svg+xml"><title>{{ $metaTitle }}</title>
<script type="application/ld+json">{!! json_encode(['@context'=>'https://schema.org','@type'=>'Person','name'=>$settings->name,'url'=>route('home'),'jobTitle'=>$settings->professional_title,'email'=>'mailto:'.$settings->email,'sameAs'=>array_values(array_filter([$settings->linkedin_url,$settings->instagram_url,$settings->behance_url])),'knowsAbout'=>['Editorial design','Publication design','Magazine design','Social media design','Campaign design']],JSON_UNESCAPED_SLASHES) !!}</script>
@vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen overflow-x-hidden bg-[#f2eee6] text-[#201b18] antialiased">
<div class="paper-grain" aria-hidden="true"></div>
<header class="site-header" data-header>
  <nav class="site-container flex h-20 items-center justify-between" aria-label="Main navigation">
    <a href="{{ route('home') }}" class="brand-lockup"><span class="brand-mark">R</span><span><strong>{{ $settings->name }}</strong><small>EDITORIAL & VISUAL DESIGN</small></span></a>
    <div class="hidden items-center gap-8 text-[.78rem] font-bold uppercase tracking-[.13em] md:flex">
      <a class="nav-link {{ request()->routeIs('home')?'active':'' }}" href="{{ route('home') }}">Home</a>
      <div class="group relative"><a class="nav-link {{ request()->routeIs('work*')?'active':'' }}" href="{{ route('work') }}">Work</a><div class="nav-popover"><a href="{{ route('work.publications') }}">Publications</a><a href="{{ route('work.social') }}">Campaigns & social</a><a href="{{ route('work') }}">All work</a></div></div>
      <a class="nav-link {{ request()->routeIs('about')?'active':'' }}" href="{{ route('about') }}">About</a>
      <a class="nav-link {{ request()->routeIs('contact*')?'active':'' }}" href="{{ route('contact') }}">Contact</a>
    </div>
    <div class="flex items-center gap-2"><a class="header-cta hidden sm:inline-flex" href="{{ route('contact') }}">Start a project <span>↗</span></a><button data-menu-toggle class="menu-button md:hidden" aria-expanded="false" aria-controls="mobile-menu">Menu</button></div>
  </nav>
  <div id="mobile-menu" data-mobile-menu class="mobile-panel hidden"><a href="{{ route('home') }}">Home</a><a href="{{ route('work') }}">All work</a><a href="{{ route('work.publications') }}">Publications</a><a href="{{ route('work.social') }}">Campaigns & social</a><a href="{{ route('about') }}">About</a><a href="{{ route('contact') }}">Contact</a></div>
</header>
<main>@yield('content')</main>
<footer class="site-footer"><div class="site-container grid gap-10 md:grid-cols-[1fr_auto] md:items-end"><div><p class="footer-name">Rovinya Wolff</p><p class="mt-2 max-w-lg text-sm leading-6 text-[#d6ccbd]">Editorial design, publications, and visual campaigns shaped with clarity and character.</p></div><div class="footer-links">@if($settings->instagram_url)<a href="{{ $settings->instagram_url }}">Instagram</a>@endif @if($settings->behance_url)<a href="{{ $settings->behance_url }}">Behance</a>@endif @if($settings->linkedin_url)<a href="{{ $settings->linkedin_url }}">LinkedIn</a>@endif<a href="mailto:{{ $settings->email }}">Email</a></div></div><div class="site-container mt-14 flex flex-wrap justify-between gap-4 border-t border-white/15 pt-6 text-xs text-[#9f9588]"><p>© {{ date('Y') }} Rovinya Wolff</p><a href="{{ route('privacy') }}">Privacy</a></div></footer>
</body></html>
