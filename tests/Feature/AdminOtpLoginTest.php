<?php

namespace Tests\Feature;

use App\Filament\Pages\Auth\OtpLogin;
use App\Services\OtpMailer;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use Tests\TestCase;

class AdminOtpLoginTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['app.admin_emails' => ['hello@rovinyawolff.com', 'rovinyask@gmail.com']]);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_an_allowed_email_can_request_a_sign_in_code(): void
    {
        $mailer = $this->mock(OtpMailer::class);
        $mailer->shouldReceive('send')
            ->once()
            ->with('hello@rovinyawolff.com', \Mockery::type('string'));

        Livewire::test(OtpLogin::class)
            ->set('data.email', 'hello@rovinyawolff.com')
            ->call('authenticate')
            ->assertSet('codeRequested', true)
            ->assertSet('challengeEmail', 'hello@rovinyawolff.com');

        $this->assertTrue(Cache::has('admin-otp:'.hash('sha256', 'hello@rovinyawolff.com')));
    }

    public function test_an_unlisted_email_cannot_request_a_sign_in_code(): void
    {
        $mailer = $this->mock(OtpMailer::class);
        $mailer->shouldNotReceive('send');

        Livewire::test(OtpLogin::class)
            ->set('data.email', 'someone@example.com')
            ->call('authenticate')
            ->assertHasErrors(['data.email']);

    }
}
