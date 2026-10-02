<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class PortfolioPublicationSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [
            ['novara-issue-01','Novara, Issue 01','Magazine Design','Novara is the official magazine of Leo District 306 D6, combining leadership stories, interviews, and editorial features in a bold visual system.','2026-09-30','novara-issue-01.pdf'],
            ['district-directory-2026-27','District Directory 2026/27','Directory Design','A structured district directory designed to make organisational information clear, useful, and visually connected to the district identity.','2026-09-13','district-directory-2026-27.pdf'],
            ['club-membership-directory-2026-27','Club Membership Directory 2026/27','Directory Design','A complete membership directory for the Leo Club of Kolonnawa, balancing official information, member profiles, and a distinctive visual theme.','2026-09-04','club-membership-directory-2026-27.pdf'],
            ['md-pulse-volume-01','MD Pulse, Volume 01','Magazine Design','A photo-led publication for Leo Multiple District 306 featuring leadership, progress, and stories from across the movement.','2026-08-28','md-pulse-volume-01.pdf'],
            ['club-directory-2026','Club Directory 2026','Directory Design','An annual club directory bringing member, leadership, and organisational information into one polished publication.','2026-09-26','club-directory-2026.pdf'],
            ['witness-december-2025','Witness, December 2025','Newsletter Design','The December 2025 edition of Witness, documenting club activity, people, and community impact through editorial storytelling.','2025-12-31','witness-december-2025.pdf'],
            ['witness-september-2025','Witness, September 2025','Newsletter Design','The September 2025 edition of the Leo Club of Kolonnawa newsletter series.','2025-09-30','witness-september-2025.pdf'],
            ['witness-april-2025','Witness, April 2025','Newsletter Design','The April 2025 edition of Witness, presenting projects and club stories through an accessible magazine layout.','2025-04-30','witness-april-2025.pdf'],
            ['witness-march-2025','Witness, March 2025','Newsletter Design','The March 2025 edition of Witness, designed as part of the club’s continuing editorial series.','2025-03-31','witness-march-2025.pdf'],
            ['multiple-newsletter-01','Multiple District Newsletter, Issue 01','Newsletter Design','A multiple-district publication created to communicate leadership, service, and movement-wide stories.','2025-02-08','multiple-newsletter-01.pdf'],
            ['leo-next-magazine-issue-01','Leo Next Magazine, Issue 01','Magazine Design','The first issue of Leo Next, a feature-led magazine created for the wider Leo community.','2025-02-02','leo-next-magazine-issue-01.pdf'],
            ['witness-january-2025','Witness, January 2025','Newsletter Design','The January 2025 edition of the Witness newsletter series.','2025-01-31','witness-january-2025.pdf'],
            ['witness-december-2024','Witness, December 2024','Newsletter Design','The December 2024 edition of Witness, preserving the month’s projects and achievements in an editorial format.','2024-12-31','witness-december-2024.pdf'],
            ['witness-november-2024','Witness, November 2024','Newsletter Design','The November 2024 edition of the Witness newsletter series.','2024-11-30','witness-november-2024.pdf'],
            ['witness-october-2024','Witness, October 2024','Newsletter Design','The October 2024 edition of the Witness newsletter series.','2024-10-31','witness-october-2024.pdf'],
            ['club-directory-2024-25','Club Directory 2024/25','Directory Design','A club directory developed to organise membership, leadership, and annual information into a cohesive reference publication.','2025-05-20','club-directory-2024-25.pdf'],
        ];

        foreach ($projects as $index => [$slug,$title,$category,$summary,$date,$filename]) {
            Project::updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => $title,
                    'category' => $category,
                    'type' => 'publication',
                    'summary' => $summary,
                    'organization' => str_contains($slug, 'multiple') || str_contains($slug, 'leo-next') || str_contains($slug, 'md-pulse') ? 'Leo Multiple District 306' : (str_contains($slug, 'district-directory') || str_contains($slug, 'novara') ? 'Leo District 306 D6' : 'Leo Club of Kolonnawa'),
                    'status' => 'Published',
                    'published_at' => $date,
                    'challenge' => 'Transform detailed organisational content, stories, photography, and formal information into a publication that remains clear, engaging, and easy to navigate.',
                    'solution' => 'A structured editorial system using purposeful hierarchy, strong imagery, recurring visual elements, and layouts adapted to the character of each publication.',
                    'responsibilities' => 'Publication design, editorial layout, visual hierarchy, image composition, and production preparation.',
                    'image_path' => 'projects/covers/'.str_replace('.pdf','.jpg',$filename),
                    'pdf_path' => 'projects/pdfs/'.$filename,
                    'is_featured' => $index < 6,
                    'is_downloadable' => true,
                    'sort_order' => ($index + 1) * 10,
                    'is_published' => true,
                ]
            );
        }
    }
}
