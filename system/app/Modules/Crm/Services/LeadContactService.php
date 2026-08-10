<?php

namespace App\Modules\Crm\Services;

use App\Models\User;
use App\Modules\Crm\Models\LeadContact;
use App\Modules\Leads\Models\Lead;
use Illuminate\Support\Str;

class LeadContactService
{
    /**
     * Ensure a generated/saved lead with contact details has one editable CRM
     * contact row. This keeps Contacts synced with lead generation.
     */
    public function syncPrimaryFromLead(Lead $lead): ?LeadContact
    {
        $lead->loadMissing('place');

        if (! $lead->email && ! $lead->place?->phone) {
            return null;
        }

        $existing = LeadContact::query()
            ->where('lead_id', $lead->id)
            ->where('user_id', $lead->user_id)
            ->where('is_primary', true)
            ->first();

        if ($existing) {
            return $existing;
        }

        $name = $this->defaultName($lead);

        return LeadContact::query()->create([
            'lead_id' => $lead->id,
            'user_id' => $lead->user_id,
            'name' => $name,
            'role' => __('Primary contact'),
            'email' => $lead->email,
            'phone' => $lead->place?->phone,
            'note' => __('Created from generated lead contact details.'),
            'is_primary' => true,
        ]);
    }

    /**
     * @param  array{name: string, role?: string|null, email?: string|null, phone?: string|null, note?: string|null, is_primary?: bool}  $data
     */
    public function create(User $user, Lead $lead, array $data): LeadContact
    {
        if (! empty($data['is_primary'])) {
            $this->clearPrimary($lead);
        }

        return LeadContact::query()->create([
            ...$data,
            'lead_id' => $lead->id,
            'user_id' => $user->id,
            'is_primary' => (bool) ($data['is_primary'] ?? false),
        ]);
    }

    /**
     * @param  array{name: string, role?: string|null, email?: string|null, phone?: string|null, note?: string|null, is_primary?: bool}  $data
     */
    public function update(LeadContact $contact, array $data): LeadContact
    {
        if (! empty($data['is_primary'])) {
            $this->clearPrimary($contact->lead);
        }

        $contact->update([
            ...$data,
            'is_primary' => (bool) ($data['is_primary'] ?? false),
        ]);

        return $contact;
    }

    protected function clearPrimary(Lead $lead): void
    {
        LeadContact::query()
            ->where('lead_id', $lead->id)
            ->update(['is_primary' => false]);
    }

    protected function defaultName(Lead $lead): string
    {
        $placeName = (string) $lead->place?->name;

        if ($lead->email) {
            $local = Str::before($lead->email, '@');
            $label = Str::of($local)->replace(['.', '_', '-'], ' ')->title()->squish()->toString();

            if ($label !== '' && ! in_array(strtolower($label), ['info', 'hello', 'contact', 'care', 'team', 'appointments', 'bookings'], true)) {
                return $label;
            }
        }

        return $placeName !== '' ? __('Front desk at :business', ['business' => $placeName]) : __('Primary contact');
    }
}
