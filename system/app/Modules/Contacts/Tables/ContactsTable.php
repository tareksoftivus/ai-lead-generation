<?php

namespace App\Modules\Contacts\Tables;

use App\Modules\Contacts\Models\Contact;
use App\Modules\Shared\Support\Tables\TableAction;
use App\Modules\Shared\Support\Tables\TableColumn;
use App\Modules\Shared\Support\Tables\TableDefinition;

class ContactsTable
{
    public static function make(): TableDefinition
    {
        $prefix = self::routePrefix();

        return TableDefinition::make('contacts')
            ->emptyMessage(__('No contact requests found.'))
            ->searchPlaceholder(__('Search contact requests...'))
            ->exportUrl(route("{$prefix}.contacts.export"))
            ->columns([
                TableColumn::text('name', 'Name')
                    ->sortable()
                    ->link(fn (Contact $contact) => route("{$prefix}.contacts.show", $contact))
                    ->cellClass('text-sm font-medium text-neutral-900'),
                TableColumn::text('email', 'Email')
                    ->sortable()
                    ->cellClass('text-sm text-neutral-600'),
                TableColumn::text('phone', 'Phone')
                    ->cellClass('text-sm text-neutral-500'),
                TableColumn::text('subject', 'Subject')
                    ->sortable()
                    ->value(fn (Contact $contact) => $contact->subject ?: __('General inquiry'))
                    ->cellClass('text-sm text-neutral-700'),
                TableColumn::badge('read_at', 'Status')
                    ->sortable()
                    ->value(fn (Contact $contact) => $contact->isRead() ? 'read' : 'unread')
                    ->meta([
                        'badge_map' => [
                            'read' => ['label' => __('Read'), 'variant' => 'success'],
                            'unread' => ['label' => __('Unread'), 'variant' => 'warning'],
                        ],
                    ]),
                TableColumn::date('created_at', 'Created')
                    ->sortable()
                    ->cellClass('text-sm text-neutral-500'),
            ])
            ->actions([
                TableAction::link('view', fn (Contact $contact) => route("{$prefix}.contacts.show", $contact), 'View')
                    ->icon('eye'),
                TableAction::submit('mark_read', fn (Contact $contact) => route("{$prefix}.contacts.mark-read", $contact), 'Mark read')
                    ->icon('check-circle')
                    ->visible(fn (Contact $contact) => ! $contact->isRead()),
                TableAction::submit('mark_unread', fn (Contact $contact) => route("{$prefix}.contacts.mark-unread", $contact), 'Mark unread')
                    ->icon('circle')
                    ->visible(fn (Contact $contact) => $contact->isRead()),
                TableAction::delete(href: fn (Contact $contact) => route("{$prefix}.contacts.destroy", $contact))
                    ->icon('trash')
                    ->confirmTitle(__('Delete Contact Request?'))
                    ->confirmMessage(fn (Contact $contact) => __('Are you sure you want to delete the contact request from :name?', ['name' => $contact->name])),
            ]);
    }

    protected static function routePrefix(): string
    {
        if (! app()->bound('current.panel')) {
            return 'admin';
        }

        $panel = app('current.panel');

        return is_array($panel) ? ($panel['key'] ?? 'admin') : 'admin';
    }
}
