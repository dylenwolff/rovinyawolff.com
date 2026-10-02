@extends('layouts.site')

@section('title', 'Privacy | '.$settings->name)
@section('description', 'How personal information submitted through rovinyawolff.com is collected, used, and protected.')

@section('content')
<section class="site-container py-20 sm:py-28">
    <div class="mx-auto max-w-3xl">
        <p class="eyebrow">Privacy</p>
        <h1 class="display-title mt-4">Your information stays focused on the conversation.</h1>
        <p class="section-copy">This website collects only the information you choose to provide when contacting me.</p>

        <div class="mt-12 space-y-10 text-lg leading-8 text-[#6d6269] dark:text-[#cfc4ca]">
            <section><h2 class="text-2xl font-bold text-slate-950 dark:text-white">What is collected</h2><p class="mt-3">The contact form collects your name, email address, subject, and message. Basic technical records may also be retained by the web server for security and reliability.</p></section>
            <section><h2 class="text-2xl font-bold text-slate-950 dark:text-white">How it is used</h2><p class="mt-3">Your information is used only to understand and respond to your enquiry, discuss a possible project, and protect the website from misuse. It is not sold or used for unrelated marketing.</p></section>
            <section><h2 class="text-2xl font-bold text-slate-950 dark:text-white">How long it is kept</h2><p class="mt-3">Enquiry information is retained only for as long as reasonably needed for communication, record keeping, or security purposes.</p></section>
            <section><h2 class="text-2xl font-bold text-[#211b26] dark:text-white">Your request</h2><p class="mt-3">You may ask about, correct, or request deletion of information you submitted by emailing <a class="font-semibold text-[#8d4f7f]" href="mailto:{{ $settings->email }}">{{ $settings->email }}</a>.</p></section>
        </div>
    </div>
</section>
@endsection
