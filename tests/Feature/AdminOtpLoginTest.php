<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\OtpMailer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class AdminOtpLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.admin_emails' => ['hello@rovinyawolff.com', 'rovinyask@gmail.com']]);
    }

    public function test_an_allowed_email_can_request_a_sign_in_code(): void
    {
        $mailer = $this->mock(OtpMailer::class);
        $mailer->shouldReceive('send')
            ->once()
            ->with('hello@rovinyawolff.com', \Mockery::type('string'));

        $this->post('http://admin.rovinyawolff.com/login/code', ['email' => 'hello@rovinyawolff.com'])
            ->assertRedirect();

        $this->assertTrue(Cache::has('admin-otp:'.hash('sha256', 'hello@rovinyawolff.com')));
    }

    public function test_an_unlisted_email_cannot_request_a_sign_in_code(): void
    {
        $mailer = $this->mock(OtpMailer::class);
        $mailer->shouldNotReceive('send');

        $this->post('http://admin.rovinyawolff.com/login/code', ['email' => 'someone@example.com'])
            ->assertForbidden();
    }

    public function test_an_authenticated_admin_can_open_the_dashboard(): void
    {
        $user = User::factory()->create(['email' => 'rovinyask@gmail.com']);

        $this->actingAs($user)
            ->get('http://admin.rovinyawolff.com/')
            ->assertOk()
            ->assertSee('Dashboard');
    }
}
