<?php

namespace App\Modules\Contacts\Services;

use App\Modules\Contacts\Models\Contact;
use App\Modules\Shared\Traits\HasCrudOperations;
use App\Modules\Shared\Traits\HasExport;
use App\Modules\Shared\Traits\HasImport;

class ContactsService
{
    use HasCrudOperations;
    use HasExport;
    use HasImport;

    protected string $model = Contact::class;

    /**
     * Columns to search with LIKE when 'search' filter is provided.
     */
    protected array $searchable = ['name', 'email', 'phone', 'subject', 'message'];

    /**
     * Columns that accept exact-match filters.
     */
    protected array $filterable = [];

    /**
     * Optional explicit sortable whitelist for query safety.
     * Leave empty to allow default trait behavior.
     */
    protected array $sortable = ['name', 'email', 'subject', 'read_at', 'created_at'];

    /**
     * Columns available for CSV export as ['column' => 'Label'].
     */
    protected array $exportable = [
        'id' => 'ID',
        'name' => 'Name',
        'email' => 'Email',
        'phone' => 'Phone',
        'subject' => 'Subject',
        'message' => 'Message',
        'terms_accepted' => 'Terms Accepted',
        'read_at' => 'Read At',
        'created_at' => 'Created At',
    ];

    /**
     * CSV header to database column mapping as ['CSV Header' => 'db_column'].
     */
    protected array $importable = [];

    /**
     * Validation rules per database column for import.
     */
    protected array $importRules = [];

    public function markAsRead(Contact $contact): Contact
    {
        if (! $contact->read_at) {
            $contact->forceFill(['read_at' => now()])->save();
        }

        return $contact->fresh();
    }

    public function markAsUnread(Contact $contact): Contact
    {
        $contact->forceFill(['read_at' => null])->save();

        return $contact->fresh();
    }
}
