<?php

namespace App\Modules\Crm\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Modules\Crm\Models\LeadContact;
use App\Modules\Crm\Services\LeadContactService;
use App\Modules\Leads\Models\Lead;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ContactsController extends Controller
{
    public function __construct(protected LeadContactService $contacts) {}

    public function index(Request $request): View
    {
        $leads = Lead::query()
            ->forUser($request->user()->id)
            ->with(['place', 'contacts'])
            ->orderByDesc('created_at')
            ->get();

        $leads->each(fn (Lead $lead) => $this->contacts->syncPrimaryFromLead($lead));
        $leads->load('contacts');

        $contacts = LeadContact::query()
            ->forUser($request->user()->id)
            ->with(['lead.place'])
            ->latest()
            ->get();

        return view('crm::user.contacts.index', [
            'contacts' => $contacts,
            'leads' => $leads,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $lead = Lead::query()
            ->forUser($request->user()->id)
            ->findOrFail($request->input('lead_id'));

        $this->contacts->create($request->user(), $lead, $this->validated($request));

        return redirect()->route('user.contacts.index')->with('success', __('Contact saved.'));
    }

    public function update(Request $request, LeadContact $contact): RedirectResponse
    {
        $this->authorizeOwnership($request, $contact);

        $this->contacts->update($contact->load('lead'), $this->validated($request));

        return redirect()->route('user.contacts.index')->with('success', __('Contact updated.'));
    }

    public function destroy(Request $request, LeadContact $contact): RedirectResponse
    {
        $this->authorizeOwnership($request, $contact);

        $contact->delete();

        return redirect()->route('user.contacts.index')->with('success', __('Contact deleted.'));
    }

    /**
     * @return array{name: string, role?: string|null, email?: string|null, phone?: string|null, note?: string|null, is_primary?: bool}
     */
    protected function validated(Request $request): array
    {
        return $request->validate([
            'lead_id' => [
                Rule::requiredIf($request->isMethod('post')),
                'nullable',
                'integer',
            ],
            'name' => ['required', 'string', 'max:120'],
            'role' => ['nullable', 'string', 'max:80'],
            'email' => ['nullable', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:40'],
            'note' => ['nullable', 'string', 'max:500'],
            'is_primary' => ['nullable', 'boolean'],
        ]);
    }

    protected function authorizeOwnership(Request $request, LeadContact $contact): void
    {
        abort_unless($contact->user_id === $request->user()->id, 404);
    }
}
