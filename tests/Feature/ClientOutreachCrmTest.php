<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\Lead;
use App\Models\Organization;
use App\Models\OutreachEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientOutreachCrmTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_prospect_can_be_tracked_from_organization_to_email_draft(): void
    {
        $organization = Organization::create([
            'name' => 'Example Association',
            'industry' => 'Membership organisation',
            'website' => 'https://example.org',
        ]);

        $contact = Contact::create([
            'organization_id' => $organization->id,
            'first_name' => 'Alex',
            'last_name' => 'Perera',
            'email' => 'alex@example.org',
        ]);

        $lead = Lead::create([
            'organization_id' => $organization->id,
            'contact_id' => $contact->id,
            'title' => 'Annual report design',
            'source' => 'direct_outreach',
        ]);

        $email = OutreachEmail::create([
            'organization_id' => $organization->id,
            'contact_id' => $contact->id,
            'lead_id' => $lead->id,
            'recipient_name' => $contact->full_name,
            'recipient_email' => $contact->email,
            'subject' => 'A design idea for your next report',
            'body' => 'A personalised draft.',
        ]);

        $this->assertSame('Example Association', $email->organization->name);
        $this->assertSame('Alex Perera', $email->contact->full_name);
        $this->assertSame('draft', $email->refresh()->status);
        $this->assertDatabaseHas('leads', ['title' => 'Annual report design']);
    }
}
