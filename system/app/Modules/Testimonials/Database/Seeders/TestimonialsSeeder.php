<?php

namespace App\Modules\Testimonials\Database\Seeders;

use App\Modules\Testimonials\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialsSeeder extends Seeder
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public static function definitions(): array
    {
        return [
            [
                'client_name' => 'Rachel Mendez',
                'company_name' => 'Northgate Digital',
                'designation' => 'Founder',
                'quote' => 'We used to burn a full day a week copying business details off Maps into a sheet. Now one search does the same thing before the coffee is cold.',
                'rating' => 5,
                'sort_order' => 1,
                'avatar' => 'avatar-3.jpg',
            ],
            [
                'client_name' => 'Daniel Okafor',
                'company_name' => 'Certa Group',
                'designation' => 'Sales lead',
                'quote' => 'The score is only useful because it shows its reasoning. I can see it flagged a practice for having no booking system, and I know that is a real conversation to have.',
                'rating' => 5,
                'sort_order' => 2,
                'avatar' => 'avatar-4.jpg',
            ],
            [
                'client_name' => 'Sofia Kallio',
                'company_name' => null,
                'designation' => 'Freelance consultant',
                'quote' => 'I rewrite maybe a third of the drafted emails. The other two thirds go out as written, and they mention something specific about the business — which is why they get answered.',
                'rating' => 5,
                'sort_order' => 3,
                'avatar' => 'avatar-2.jpg',
            ],
            [
                'client_name' => 'Marcus Bell',
                'company_name' => 'Bell & Co Signage',
                'designation' => 'Owner',
                'quote' => 'Searching costs nothing, so I check a new town before I decide whether it is worth working. That was never true of the tools I used before.',
                'rating' => 4,
                'sort_order' => 4,
                'avatar' => 'avatar-1.jpg',
            ],
            [
                'client_name' => 'Priya Raman',
                'company_name' => 'Halden Fit',
                'designation' => 'Ops manager',
                'quote' => 'Everything lands in the CRM we already use. Nobody on the team had to learn a second place to look, which is the only reason it stuck.',
                'rating' => 5,
                'sort_order' => 5,
                'avatar' => 'avatar-3.jpg',
            ],
            [
                'client_name' => 'Tomas Lindqvist',
                'company_name' => 'Vantage POS',
                'designation' => 'Field sales',
                'quote' => 'Seeing them on a map changed how I plan a week. I work one neighbourhood at a time now instead of driving across the city for two appointments.',
                'rating' => 4,
                'sort_order' => 6,
                'avatar' => 'avatar-4.jpg',
            ],
            [
                'client_name' => 'Amara Nwosu',
                'company_name' => 'Brightline Web',
                'designation' => 'Founder',
                'quote' => 'It tells me what a business is missing before I call. Opening with something specific beats a pitch, and I no longer spend the first minute working out who they are.',
                'rating' => 5,
                'sort_order' => 7,
                'avatar' => 'avatar-2.jpg',
            ],
            [
                'client_name' => 'Greg Whitfield',
                'company_name' => 'Whitfield Media',
                'designation' => 'Director',
                'quote' => 'I pay for what I actually enrich, and a lead with no contact details refunds itself. That was the part I expected to be a catch, and it was not.',
                'rating' => 5,
                'sort_order' => 8,
                'avatar' => 'avatar-1.jpg',
            ],
        ];
    }

    public function run(): void
    {
        foreach (self::definitions() as $definition) {
            Testimonial::query()->updateOrCreate(
                ['client_name' => $definition['client_name']],
                [
                    'company_name' => $definition['company_name'],
                    'designation' => $definition['designation'],
                    'quote' => $definition['quote'],
                    'rating' => $definition['rating'],
                    'sort_order' => $definition['sort_order'],
                    'active' => true,
                    'avatar' => 'assets/frontend/leadatlas/images/avatars/'.$definition['avatar'],
                ]
            );
        }
    }
}
