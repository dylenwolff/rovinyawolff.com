<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Throwable;

class OtpLogin extends Login
{
    public bool $codeRequested = false;

    #[Locked]
    public ?string $challengeEmail = null;

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            $this->getEmailFormComponent(),
            TextInput::make('code')
                ->label('Six-digit sign-in code')
                ->numeric()
                ->length(6)
                ->autocomplete('one-time-code')
                ->autofocus()
                ->required(fn (): bool => $this->codeRequested)
                ->visible(fn (): bool => $this->codeRequested),
        ]);
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('Email address')
            ->email()
            ->required()
            ->autocomplete('email')
            ->readOnly(fn (): bool => $this->codeRequested)
            ->autofocus(fn (): bool => ! $this->codeRequested);
    }

    public function authenticate(): ?LoginResponse
    {
        return $this->codeRequested
            ? $this->verifyCode()
            : $this->sendCode();
    }

    protected function sendCode(): null
    {
        $data = $this->form->getState();
        $email = strtolower(trim((string) ($data['email'] ?? '')));

        if (! in_array($email, config('app.admin_emails', []), true)) {
            $this->throwEmailValidationException();
        }

        $rateLimitKey = 'admin-otp-send:'.request()->ip().':'.$email;

        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            throw ValidationException::withMessages([
                'data.email' => 'Too many codes requested. Please try again in '.RateLimiter::availableIn($rateLimitKey).' seconds.',
            ]);
        }

        RateLimiter::hit($rateLimitKey, 600);

        $code = (string) random_int(100000, 999999);
        Cache::put($this->cacheKey($email), Hash::make($code), now()->addMinutes(10));

        try {
            Mail::raw(
                "Your Rovinya Wolff Studio admin sign-in code is {$code}.\n\nThis code expires in 10 minutes. If you did not request it, you can ignore this email.",
                fn ($message) => $message
                    ->to($email)
                    ->subject('Your admin sign-in code'),
            );
        } catch (Throwable $exception) {
            report($exception);
            Cache::forget($this->cacheKey($email));

            throw ValidationException::withMessages([
                'data.email' => 'The sign-in code could not be sent. Please try again shortly.',
            ]);
        }

        $this->challengeEmail = $email;
        $this->codeRequested = true;
        $this->data['email'] = $email;
        $this->data['code'] = null;

        Notification::make()
            ->title('Check your email')
            ->body('We sent a six-digit code that expires in 10 minutes.')
            ->success()
            ->send();

        return null;
    }

    protected function verifyCode(): ?LoginResponse
    {
        $data = $this->form->getState();
        $email = strtolower(trim((string) $this->challengeEmail));
        $code = (string) ($data['code'] ?? '');

        if (! in_array($email, config('app.admin_emails', []), true)) {
            $this->throwEmailValidationException();
        }

        $rateLimitKey = 'admin-otp-verify:'.request()->ip().':'.$email;

        if (RateLimiter::tooManyAttempts($rateLimitKey, 10)) {
            throw ValidationException::withMessages([
                'data.code' => 'Too many attempts. Please request a new code later.',
            ]);
        }

        RateLimiter::hit($rateLimitKey, 600);
        $storedCode = Cache::get($this->cacheKey($email));

        if (! is_string($storedCode) || ! Hash::check($code, $storedCode)) {
            throw ValidationException::withMessages([
                'data.code' => 'The code is invalid or has expired.',
            ]);
        }

        Cache::forget($this->cacheKey($email));
        RateLimiter::clear($rateLimitKey);

        $user = User::query()->firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Rovinya Wolff',
                'password' => Str::random(64),
                'email_verified_at' => now(),
            ],
        );

        Filament::auth()->login($user, true);
        session()->regenerate();

        return app(LoginResponse::class);
    }

    protected function cacheKey(string $email): string
    {
        return 'admin-otp:'.hash('sha256', $email);
    }

    protected function throwEmailValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.email' => 'This email address is not authorised for the admin area.',
        ]);
    }

    protected function getAuthenticateFormAction(): Action
    {
        return Action::make('authenticate')
            ->label($this->codeRequested ? 'Verify code' : 'Email me a code')
            ->submit('authenticate');
    }

    public function getHeading(): string | Htmlable | null
    {
        return $this->codeRequested ? 'Enter your sign-in code' : 'Sign in to the studio';
    }

    public function getSubheading(): string | Htmlable | null
    {
        return $this->codeRequested
            ? 'Use the six-digit code sent to your email address.'
            : 'Enter an authorised email address. No password is required.';
    }
}
