<?php

namespace Database\Seeders;

use App\Models\Lead;
use App\Models\Organization;
use App\Models\OutreachEmail;
use Illuminate\Database\Seeder;

class InitialOutreachProspectsSeeder extends Seeder
{
    public function run(): void
    {
        $prospects = [
            [
                'name' => 'Sri Lanka Association for the Advancement of Science',
                'industry' => 'Scientific membership association',
                'website' => 'https://www.slaas.lk/',
                'email' => 'office@slaas.lk',
                'opportunity' => 'Newsletter and scientific publication design',
                'subject' => 'Design support for future SLAAS publications',
                'body' => "Hello SLAAS team,\n\nI came across your newsletter archive and noticed how much scientific and organisational content SLAAS publishes across its sections. I specialise in turning detailed material into clear, engaging magazines, newsletters, directories, and reports.\n\nMy recent work includes multi-page publications for membership and community organisations, with strong editorial hierarchy, image-led layouts, and print-ready delivery. You can view selected publication work here: https://rovinyawolff.com/work/publications\n\nIf SLAAS has an upcoming newsletter, annual-session publication, or report, I would be happy to suggest a visual direction and provide a quotation. Would it be useful if I sent a short sample concept based on one of your existing issues?\n\nBest regards,\nRovinya Wolff\nPublication & Visual Designer\nhttps://rovinyawolff.com",
                'notes' => 'SLAAS publishes multiple general and sectional newsletters and serves more than 10,000 members.',
            ],
            [
                'name' => 'Sri Lanka Library Association',
                'industry' => 'Professional membership association',
                'website' => 'https://slla.lk/',
                'email' => 'info@slla.lk',
                'opportunity' => 'Annual report, newsletter, and journal design',
                'subject' => 'Publication design support for SLLA',
                'body' => "Hello SLLA team,\n\nI was looking through SLLA's publications and saw the breadth of material you produce—from annual reports and newsletters to professional journals and course communications. That is exactly the kind of content I enjoy shaping into publications that are structured, readable, and visually consistent.\n\nI design magazines, directories, newsletters, and reports for organisations with detailed content and many contributors. Selected examples are available here: https://rovinyawolff.com/work/publications\n\nIf you are planning a future newsletter, annual report, programme, or member publication, I would be glad to discuss the scope and prepare a quotation. Would a brief sample layout for a few pages be helpful?\n\nBest regards,\nRovinya Wolff\nPublication & Visual Designer\nhttps://rovinyawolff.com",
                'notes' => 'SLLA reports four newsletter issues in progress/published, two journal issues, and periodic annual reports.',
            ],
            [
                'name' => 'Sri Lanka Association for Laboratory Animal Science',
                'industry' => 'Scientific association',
                'website' => 'https://slalas.lk/',
                'email' => 'slalasci@gmail.com',
                'opportunity' => 'Scientific newsletter redesign and production',
                'subject' => 'A design idea for the next SLALAS newsletter',
                'body' => "Hello Dr. Senevirathne and the SLALAS team,\n\nI found the SLALAS newsletter while researching Sri Lankan scientific associations. The mix of specialist articles, event coverage, member news, and upcoming programmes would lend itself well to a strong, repeatable editorial design system.\n\nI design multi-page newsletters, magazines, directories, and reports, including publications for member-led organisations. My work can be viewed here: https://rovinyawolff.com/work/publications\n\nFor a future SLALAS issue, I could help with the page system, article hierarchy, image treatment, cover design, and final print/digital files. Would you be open to seeing a short sample direction and quotation?\n\nBest regards,\nRovinya Wolff\nPublication & Visual Designer\nhttps://rovinyawolff.com",
                'notes' => 'SLALAS publishes a multi-page scientific newsletter and has a clearly identified secretary contact.',
            ],
            [
                'name' => 'Sri Lanka Association for Improving Higher Education Effectiveness',
                'industry' => 'Higher education association',
                'website' => 'https://www.slaihee.org/',
                'email' => 'slaihee@gmail.com',
                'opportunity' => 'Newsletter and conference publication design',
                'subject' => 'Design support for SLAIHEE newsletters and conference material',
                'body' => "Hello SLAIHEE team,\n\nI came across your newsletter and saw that it brings together committee updates, conference information, articles, and membership communication. I specialise in organising exactly this kind of detailed content into clear, polished publications.\n\nMy portfolio includes magazines, newsletters, directories, reports, posters, and social-media campaigns for community and membership organisations: https://rovinyawolff.com/work/publications\n\nIf you have another newsletter, conference programme, abstract book, or annual publication coming up, I would be happy to propose a visual direction and quotation. May I send a small sample concept based on your current newsletter?\n\nBest regards,\nRovinya Wolff\nPublication & Visual Designer\nhttps://rovinyawolff.com",
                'notes' => 'SLAIHEE publishes a newsletter and invites suggestions and feedback at its public contact address.',
            ],
            [
                'name' => 'Animals’ Welfare and Protection Association Sri Lanka',
                'industry' => 'Animal welfare nonprofit',
                'website' => 'https://awpasl.org/',
                'email' => 'info.awpa@gmail.com',
                'opportunity' => 'Animal World annual magazine and campaign design',
                'subject' => 'A fresh visual direction for Animal World',
                'body' => "Hello AWPA team,\n\nI found Animal World while looking at publications by Sri Lankan nonprofit organisations. The stories, rescue photography, welfare updates, and supporter information give you wonderful material for an emotionally engaging annual magazine.\n\nI design magazines, reports, directories, posters, and social-media campaigns, with particular experience turning community stories and large photo collections into cohesive visual narratives. Selected publications are here: https://rovinyawolff.com/work/publications\n\nIf you are planning the next Animal World issue or a campaign publication, I would love to discuss a refreshed design system that still feels recognisably AWPA. Would you be open to a short sample concept and quotation?\n\nBest regards,\nRovinya Wolff\nPublication & Visual Designer\nhttps://rovinyawolff.com",
                'notes' => 'AWPA publishes Animal World, an annual magazine featuring welfare stories, photography, AGM material, and supporter information.',
            ],
        ];

        foreach ($prospects as $prospect) {
            $organization = Organization::updateOrCreate(
                ['email' => $prospect['email']],
                ['name' => $prospect['name'], 'industry' => $prospect['industry'], 'website' => $prospect['website'], 'status' => 'prospect', 'notes' => $prospect['notes']],
            );

            $lead = Lead::updateOrCreate(
                ['organization_id' => $organization->id, 'title' => $prospect['opportunity']],
                ['source' => 'direct_outreach', 'status' => 'researching', 'description' => $prospect['notes']],
            );

            OutreachEmail::updateOrCreate(
                ['recipient_email' => $prospect['email'], 'subject' => $prospect['subject']],
                ['organization_id' => $organization->id, 'lead_id' => $lead->id, 'body' => $prospect['body'], 'status' => 'ready', 'notes' => 'Research-backed first-contact draft. Review before sending.'],
            );
        }
    }
}
