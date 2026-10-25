<?php

namespace Tests\Unit;

use App\Models\User;
use Filament\Panel;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserPanelAccessTest extends TestCase
{
    #[Test]
    public function the_site_owner_can_access_the_admin_panel(): void
    {
        config(['app.admin_emails' => ['hello@rovinyawolff.com', 'rovinyask@gmail.com']]);
        $user = new User(['email' => 'hello@rovinyawolff.com']);
        $panel = Panel::make()->id('admin');

        $this->assertTrue($user->canAccessPanel($panel));
    }

    #[Test]
    public function the_second_allowed_administrator_can_access_the_admin_panel(): void
    {
        config(['app.admin_emails' => ['hello@rovinyawolff.com', 'rovinyask@gmail.com']]);
        $user = new User(['email' => 'ROVINYASK@GMAIL.COM']);
        $panel = Panel::make()->id('admin');

        $this->assertTrue($user->canAccessPanel($panel));
    }

    #[Test]
    public function other_users_cannot_access_the_admin_panel(): void
    {
        config(['app.admin_emails' => ['hello@rovinyawolff.com', 'rovinyask@gmail.com']]);
        $user = new User(['email' => 'someone@example.com']);
        $panel = Panel::make()->id('admin');

        $this->assertFalse($user->canAccessPanel($panel));
    }
}
