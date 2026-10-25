<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpMailer;
use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\Support\Facades\{Auth, Cache, Hash, RateLimiter};
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        return Auth::check() ? redirect()->route('admin.dashboard') : view('admin.auth.login', ['email' => $request->session()->get('otp_email')]);
    }

    public function send(Request $request, OtpMailer $mailer): RedirectResponse
    {
        $email = strtolower((string) $request->validate(['email'=>'required|email'])['email']);
        abort_unless(in_array($email, config('app.admin_emails', []), true), 403, 'This email is not authorised.');
        $key = 'admin-otp-send:'.$request->ip().':'.$email;
        abort_if(RateLimiter::tooManyAttempts($key, 5), 429, 'Too many codes requested.');
        RateLimiter::hit($key, 600);
        $code = (string) random_int(100000, 999999);
        Cache::put('admin-otp:'.hash('sha256', $email), Hash::make($code), now()->addMinutes(10));
        $mailer->send($email, $code);
        $request->session()->put('otp_email', $email);
        return back()->with('success', 'A six-digit code was sent to '.$email.'.');
    }

    public function verify(Request $request): RedirectResponse
    {
        $data = $request->validate(['code'=>'required|digits:6']);
        $email = strtolower((string) $request->session()->get('otp_email'));
        abort_unless(in_array($email, config('app.admin_emails', []), true), 403);
        $stored = Cache::get('admin-otp:'.hash('sha256', $email));
        if (! is_string($stored) || ! Hash::check($data['code'], $stored)) return back()->withErrors(['code'=>'The code is invalid or expired.']);
        Cache::forget('admin-otp:'.hash('sha256', $email));
        $user = User::firstOrCreate(['email'=>$email], ['name'=>'Rovinya Wolff','password'=>Str::random(64),'email_verified_at'=>now()]);
        Auth::login($user, true); $request->session()->regenerate(); $request->session()->forget('otp_email');
        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout(); $request->session()->invalidate(); $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
