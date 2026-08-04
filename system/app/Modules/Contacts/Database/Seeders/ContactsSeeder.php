<?php

namespace App\Modules\Contacts\Database\Seeders;

use App\Modules\Contacts\Models\Contact;
use Illuminate\Database\Seeder;

class ContactsSeeder extends Seeder
{
    public function run(): void
    {
        $contacts = [
            [
                'name' => 'Avery Stone',
                'email' => 'avery@example.com',
                'phone' => '+1 555 0123',
                'subject' => 'Wholesale product question',
                'message' => 'I would like to know more about wholesale supplement pricing and delivery timelines.',
                'terms_accepted' => true,
                'read_at' => now(),
            ],
            [
                'name' => 'Jordan Lee',
                'email' => 'jordan@example.com',
                'phone' => '+1 555 0198',
                'subject' => 'Need help choosing a product',
                'message' => 'Can your team recommend a daily energy supplement for a beginner?',
                'terms_accepted' => true,
                'read_at' => null,
            ],
        ];

        foreach ($contacts as $contact) {
            Contact::updateOrCreate(
                ['email' => $contact['email'], 'subject' => $contact['subject']],
                $contact
            );
        }
    }
}
