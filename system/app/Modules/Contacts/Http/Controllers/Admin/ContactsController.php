<?php

namespace App\Modules\Contacts\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Contacts\Http\Requests\UpdateContactRequest;
use App\Modules\Contacts\Models\Contact;
use App\Modules\Contacts\Services\ContactsService;
use App\Modules\Contacts\Tables\ContactsTable;
use App\Modules\Shared\Support\Tables\TableDefinition;
use App\Modules\Shared\Traits\HasCrudActions;
use App\Modules\Shared\Traits\HasExportActions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ContactsController extends Controller implements HasMiddleware
{
    use HasCrudActions;
    use HasExportActions;

    protected string $viewPath = 'contacts::admin';

    protected string $routePrefix = 'admin.contacts';

    protected string $resourceName = 'contacts';

    public static function middleware(): array
    {
        return [
            new Middleware('permission:contacts.view', only: ['index', 'show', 'export']),
            new Middleware('permission:contacts.edit', only: ['markRead', 'markUnread', 'update']),
            new Middleware('permission:contacts.delete', only: ['destroy']),
            ...static::exportMiddleware('contacts'),
        ];
    }

    public function __construct(
        protected ContactsService $service
    ) {}

    public function show($record)
    {
        $contact = $this->resolveRecord($record);
        $this->service->markAsRead($contact);

        return view("{$this->viewPath}.show", [
            'contact' => $contact->fresh(),
        ]);
    }

    public function update(UpdateContactRequest $request, $record): RedirectResponse
    {
        $record = $this->resolveRecord($record);
        $this->service->update($record, $request->validated());

        return redirect()
            ->route("{$this->routePrefix}.index")
            ->with('success', __('Updated successfully'));
    }

    public function markRead(Contact $contact): RedirectResponse
    {
        $this->service->markAsRead($contact);

        return back()->with('success', __('Contact request marked as read.'));
    }

    public function markUnread(Contact $contact): RedirectResponse
    {
        $this->service->markAsUnread($contact);

        return back()->with('success', __('Contact request marked as unread.'));
    }

    protected function tableDefinition(Request $request): ?TableDefinition
    {
        return ContactsTable::make();
    }

    protected function getFilters(Request $request): array
    {
        return [
            'search' => $request->input('search'),
            'sort_by' => $request->input('sort_by', 'created_at'),
            'sort_order' => $request->input('sort_order', 'desc'),
        ];
    }
}
