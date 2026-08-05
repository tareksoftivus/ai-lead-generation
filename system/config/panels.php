<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Registered Panels
    |--------------------------------------------------------------------------
    |
    | Add a panel  → add one line here + run make:panel
    | Remove panel → delete the line + delete the folder from app/Panels/
    |
    | Navigation items support these keys:
    |   - label:      Display text
    |   - icon:       Phosphor icon class (e.g. 'ph-house')
    |   - route:      Route name pattern for active-state matching (e.g. 'admin.users.*')
    |   - permission: Optional permission string – item hidden if user lacks it
    |   - group:      Sidebar group heading (default: 'Main Menu')
    |   - children:   Optional array of sub-items [['label' => '', 'route' => '']]
    |
    */

    'admin' => [
        'name' => 'Admin Panel',
        'prefix' => 'admin',                          // URL: yoursite.com/admin/*
        'middleware' => ['web', 'auth:admin', '2fa', 'panel:admin'],
        'roles' => [],                               // Empty = any authenticated admin user
        'guard' => 'admin',                          // Uses admin guard → admins table
        'theme' => 'dark',
        'components' => 'default',                        // Fixed design, uses shared components
        'active' => true,

        'navigation' => [
            [
                'label' => 'Dashboard',
                'icon' => 'ph-house',
                'route' => 'admin.dashboard',
                'group' => 'Main Menu',
            ],
            [
                'label' => 'Users',
                'icon' => 'ph-users',
                'route' => 'admin.users.*',
                'group' => 'Management',
                'permission' => 'users.view',
            ],
        ],
    ],

    'user' => [
        'name' => 'User Dashboard',
        'prefix' => 'dashboard',                      // URL: yoursite.com/dashboard/*
        'middleware' => ['web', 'auth', 'verified', 'phone.verified', '2fa', 'panel:user'],
        'roles' => [],                               // Empty = all authenticated users
        'guard' => 'web',                            // Uses web guard → users table
        'theme' => 'light',
        'components' => 'default',
        'active' => true,

        'navigation' => [
            [
                'label' => 'Dashboard',
                'icon' => 'ph-squares-four',
                'route' => 'user.dashboard',
            ],
            [
                'label' => 'New search',
                'icon' => 'ph-magnifying-glass',
                'route' => 'user.search.new',
                'group' => 'Lead Generation',
            ],
            [
                'label' => 'Search history',
                'icon' => 'ph-clock-counter-clockwise',
                'route' => 'user.search.history',
                'group' => 'Lead Generation',
            ],
            [
                'label' => 'All leads',
                'icon' => 'ph-users-three',
                'route' => 'user.leads.index',
                'group' => 'Leads',
            ],
            [
                'label' => 'Map view',
                'icon' => 'ph-map-pin',
                'route' => 'user.leads.map',
                'group' => 'Leads',
            ],
            [
                'label' => 'Lists & tags',
                'icon' => 'ph-folders',
                'route' => 'user.leads.lists',
                'group' => 'Leads',
            ],
            [
                'label' => 'Business analysis',
                'icon' => 'ph-sparkle',
                'route' => 'user.analysis.index',
                'group' => 'AI Tools',
            ],
            [
                'label' => 'Lead scoring',
                'icon' => 'ph-gauge',
                'route' => 'user.scoring.index',
                'group' => 'AI Tools',
            ],
            [
                'label' => 'Email generator',
                'icon' => 'ph-envelope-simple',
                'route' => 'user.email.index',
                'group' => 'AI Tools',
            ],
            [
                'label' => 'Sales pipeline',
                'icon' => 'ph-kanban',
                'route' => 'user.pipeline.index',
                'group' => 'CRM',
            ],
            [
                'label' => 'Contacts',
                'icon' => 'ph-address-book',
                'route' => 'user.contacts.index',
                'group' => 'CRM',
            ],
            [
                'label' => 'Notes & activities',
                'icon' => 'ph-note-pencil',
                'route' => 'user.activities.index',
                'group' => 'CRM',
            ],
            [
                'label' => 'Email campaigns',
                'icon' => 'ph-paper-plane-tilt',
                'route' => 'user.campaigns.index',
                'group' => 'Outreach',
            ],
            [
                'label' => 'Export center',
                'icon' => 'ph-download-simple',
                'route' => 'user.export.index',
                'group' => 'Outreach',
            ],
            [
                'label' => 'Credits & billing',
                'icon' => 'ph-coins',
                'route' => 'user.credits.index',
                'group' => 'Account',
            ],
            [
                'label' => 'API & integrations',
                'icon' => 'ph-plugs-connected',
                'route' => 'user.api.*',
                'group' => 'Account',
            ],
            [
                'label' => 'Settings',
                'icon' => 'ph-gear-six',
                'route' => 'user.settings.index',
                'group' => 'Account',
            ],
        ],
    ],

];
