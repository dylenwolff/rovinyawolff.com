<?php

namespace App\Http\Controllers;

use App\Mail\ContactEnquiryMail;
use App\Models\Award;
use App\Models\ContactMessage;
use App\Models\Project;
use App\Models\Qualification;
use App\Models\Service;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Throwable;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('welcome', [
            'settings' => SiteSetting::current(),
            'services' => Service::query()->where('is_published', true)->orderBy('sort_order')->limit(3)->get(),
            'projects' => Project::query()->where('is_published', true)->orderBy('sort_order')->limit(4)->get(),
            'awards' => Award::query()->where('is_published', true)->where('is_featured', true)->orderBy('sort_order')->limit(3)->get(),
        ]);
    }

    public function work(): View
    {
        return view('pages.work', $this->shared([
            'projects' => Project::query()->where('is_published', true)->orderBy('sort_order')->get(),
        ]));
    }

    public function project(Project $project): View
    {
        abort_unless($project->is_published, 404);

        return view('pages.project', $this->shared([
            'project' => $project,
            'previous' => Project::query()->where('is_published', true)->where('sort_order', '<', $project->sort_order)->orderByDesc('sort_order')->first(),
            'next' => Project::query()->where('is_published', true)->where('sort_order', '>', $project->sort_order)->orderBy('sort_order')->first(),
        ]));
    }

    public function about(): View
    {
        return view('pages.about', $this->shared([
            'qualifications' => Qualification::query()->where('is_published', true)->orderBy('sort_order')->get(),
            'awards' => Award::query()->where('is_published', true)->orderBy('sort_order')->get(),
        ]));
    }

    public function services(): View
    {
        return view('pages.services', $this->shared([
            'services' => Service::query()->where('is_published', true)->orderBy('sort_order')->get(),
        ]));
    }

    public function contactPage(): View
    {
        return view('pages.contact', $this->shared());
    }

    public function privacy(): View
    {
        return view('pages.privacy', $this->shared());
    }

    public function sitemap(): Response
    {
        $fixed = collect([
            ['loc' => route('home'), 'lastmod' => now()->toDateString()],
            ['loc' => route('about'), 'lastmod' => now()->toDateString()],
            ['loc' => route('services'), 'lastmod' => now()->toDateString()],
            ['loc' => route('work'), 'lastmod' => now()->toDateString()],
            ['loc' => route('contact'), 'lastmod' => now()->toDateString()],
            ['loc' => route('privacy'), 'lastmod' => now()->toDateString()],
        ]);

        $projects = Project::query()->where('is_published', true)->get()->map(fn (Project $project) => [
            'loc' => route('work.show', $project),
            'lastmod' => $project->updated_at->toDateString(),
        ]);

        return response()
            ->view('sitemap', ['urls' => $fixed->concat($projects)])
            ->header('Content-Type', 'application/xml');
    }

    private function shared(array $data = []): array
    {
        return array_merge(['settings' => SiteSetting::current()], $data);
    }

    public function contact(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'subject' => ['nullable', 'string', 'max:150'],
            'message' => ['required', 'string', 'min:20', 'max:5000'],
            'website' => ['nullable', 'max:0'],
        ]);

        $contact = ContactMessage::create($validated);

        try {
            Mail::to(SiteSetting::current()->email)->send(new ContactEnquiryMail($contact));
        } catch (Throwable $exception) {
            Log::error('Contact notification could not be sent.', ['contact_message_id' => $contact->id, 'exception' => $exception]);
        }

        return back()->with('contact_success', 'Thank you, your message has been received. I’ll get back to you soon.');
    }
}
