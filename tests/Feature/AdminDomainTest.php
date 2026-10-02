<?php

namespace Tests\Feature;

use Tests\TestCase;

class AdminDomainTest extends TestCase
{
    public function test_admin_subdomain_redirects_guests_to_the_cms_login(): void
    {
        $response = $this->get('http://admin.rovinyawolff.com/');

        $response->assertRedirect('http://admin.rovinyawolff.com/login');
    }

    public function test_cms_is_not_exposed_under_the_public_website_admin_path(): void
    {
        $response = $this->get('http://rovinyawolff.com/admin');

        $response->assertNotFound();
    }

    public function test_admin_password_reset_request_page_is_available(): void
    {
        $response = $this->get('http://admin.rovinyawolff.com/password-reset/request');

        $response->assertOk();
    }
}
