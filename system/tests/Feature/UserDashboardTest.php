<?php

use App\Models\User;
use App\Modules\Credits\Services\CreditLedger;
use App\Modules\Leads\Models\Lead;
use App\Modules\Leads\Models\LeadActivity;
use App\Modules\Leads\Models\LeadList;
use App\Modules\Leads\Models\Place;
use App\Modules\Leads\Models\SearchRun;
use App\Modules\PricingPlan\Models\PricingPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

it('renders the LeadAtlas user dashboard', function () {
    $user = User::factory()->create([
        'name' => 'Amara',
        'is_active' => true,
        'email_verified_at' => now(),
        'credits_balance' => 2480,
    ]);

    $response = $this->actingAs($user)->get(route('user.dashboard'));

    $response->assertSuccessful();

    // Reference layout chrome (app shell) is used for the user panel.
    $response->assertSee('app-sidebar', false);
    $response->assertSee('app-content', false);
    $response->assertSee('data-sidebar', false);

    // Greeting uses the authenticated user's name.
    $response->assertSee('Amara');

    // Dashboard panels from the reference markup.
    $response->assertSee('Credits remaining');
    $response->assertSee('Running now');
    $response->assertSee('Leads found');
    $response->assertSee('Top scoring');
    $response->assertSee('Recent searches');

    // The chart canvas is data-driven (Chart.js) rather than a hand-rolled image.
    $response->assertSee('data-chart="line"', false);

    // Dashboard links resolve to real screens, not dead "#" anchors.
    $response->assertSee(route('user.search.history'));
    $response->assertSee(route('user.leads.index'));
    $response->assertSee(route('user.search.new'));
});

it('renders every account option in the topbar profile dropdown', function () {
    $user = User::factory()->create([
        'name' => 'Amara',
        'is_active' => true,
        'email_verified_at' => now(),
    ]);

    $response = $this->actingAs($user)->get(route('user.dashboard'));

    $response->assertSuccessful();

    // The account dropdown carries every option from the reference topbar.
    $response->assertSee('data-dropdown-panel', false);
    $response->assertSee(route('user.profile.edit'));
    $response->assertSee('Account settings');
    $response->assertSee(route('user.settings.index'));
    $response->assertSee('Credits & billing');
    $response->assertSee(route('user.credits.index'));
    $response->assertSee('badge-discover', false);
    $response->assertSee('2,480');
    $response->assertSee('Support');
    $response->assertSee(route('user.support-tickets.index'));
    $response->assertSee('Sign out');

    // The sidebar Account group carries the Support entry.
    $response->assertSee('sidebar-group', false);
    $response->assertSee(route('user.support-tickets.index'));

    // Icons from the reference topbar markup.
    $response->assertSee('ph-user', false);
    $response->assertSee('ph-gear-six', false);
    $response->assertSee('ph-coins', false);
    $response->assertSee('ph-lifebuoy', false);
    $response->assertSee('ph-sign-out', false);
});

it('renders profile and notifications pages under the reference layout', function () {
    $user = User::factory()->create([
        'name' => 'Amara',
        'is_active' => true,
        'email_verified_at' => now(),
    ]);

    $profile = $this->actingAs($user)->get(route('user.profile.edit'));
    $profile->assertSuccessful();
    $profile->assertSee('app-sidebar', false);
    $profile->assertSee('app-content', false);
    $profile->assertSee('pro__main', false);
    $profile->assertSee('pro__grid', false);
    $profile->assertSee('pro__photo', false);
    $profile->assertSee('pro__avatar', false);
    $profile->assertSee('pro__conns', false);
    $profile->assertSee('pro__sessions', false);
    $profile->assertSee('data-preview-swap="#p-avatar-img"', false);
    $profile->assertSee('data-password-toggle', false);
    $profile->assertSee('id="p-avatar"', false);
    $profile->assertSee('form="profile-form"', false);
    $profile->assertSee(route('user.profile.update'));
    $profile->assertDontSee(route('user.profile.sessions.revoke-all'));

    $this->assertTrue(
        DB::table('sessions')->insert([
            'id' => 'other-session-id',
            'user_id' => $user->id,
            'ip_address' => '10.0.0.4',
            'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.0 Mobile/15E148 Safari/604.1',
            'payload' => 'dummy',
            'last_activity' => now()->timestamp,
        ])
    );

    $withOther = $this->actingAs($user)->get(route('user.profile.edit'));
    $withOther->assertSee(route('user.profile.sessions.revoke-all'));
    $withOther->assertSee(route('user.profile.sessions.revoke', 'other-session-id'));

    $notifications = $this->actingAs($user)->get(route('user.system-notifications.index'));
    $notifications->assertSuccessful();
    $notifications->assertSee('app-sidebar', false);
    $notifications->assertSee('app-content', false);
});

it('rejects a profile update when the current password is wrong', function () {
    $user = User::factory()->create([
        'name' => 'Amara',
        'email' => 'amara@riveragrowth.co',
        'password' => 'old-password',
        'is_active' => true,
        'email_verified_at' => now(),
    ]);

    $this->actingAs($user)
        ->put(route('user.profile.update'), [
            'name' => 'Amara',
            'email' => 'amara@riveragrowth.co',
            'current_password' => 'wrong-password',
            'password' => 'new-secret-123',
            'password_confirmation' => 'new-secret-123',
        ])
        ->assertSessionHasErrors('current_password');

    $this->assertFalse(Hash::check('new-secret-123', $user->fresh()->password));
});

it('updates the password when the current password is correct', function () {
    $user = User::factory()->create([
        'name' => 'Amara',
        'email' => 'amara@riveragrowth.co',
        'password' => 'old-password',
        'is_active' => true,
        'email_verified_at' => now(),
    ]);

    $this->actingAs($user)
        ->put(route('user.profile.update'), [
            'name' => 'Amara',
            'email' => 'amara@riveragrowth.co',
            'current_password' => 'old-password',
            'password' => 'new-secret-123',
            'password_confirmation' => 'new-secret-123',
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $this->assertTrue(Hash::check('new-secret-123', $user->fresh()->password));
});

it('renders the New search screen with the filter rail and cost estimate', function () {
    Permission::findOrCreate('leads.search', 'web');

    $user = User::factory()->create([
        'name' => 'Amara',
        'is_active' => true,
        'email_verified_at' => now(),
    ]);
    $user->givePermissionTo('leads.search');

    $response = $this->actingAs($user)->get(route('user.search.new'));

    $response->assertSuccessful();

    // Reference layout chrome.
    $response->assertSee('app-sidebar', false);
    $response->assertSee('app-content', false);

    // The search screen is a form carrying the estimate + filter rail.
    $response->assertSee('data-estimate-form', false);
    $response->assertSee('Filters');
    $response->assertSee('Business type');
    $response->assertSee('Start your search');
    $response->assertSee('Run search');

    // The save-search modal is present.
    $response->assertSee('id="saveSearchModal"', false);
    $response->assertSee('Save this search');

    // The sidebar carries the new "Lead Generation" group + item.
    $response->assertSee('Lead Generation');
    $response->assertSee(route('user.search.new'));
});

it('renders the Search history screen with tabs, filters, and real rows including re-run/delete actions', function () {
    Permission::findOrCreate('leads.search', 'web');

    $user = User::factory()->create([
        'name' => 'Amara',
        'is_active' => true,
        'email_verified_at' => now(),
    ]);
    $user->givePermissionTo('leads.search');

    SearchRun::query()->create([
        'user_id' => $user->id,
        'filters' => ['keyword' => ['dentists'], 'location' => ['Austin, TX'], 'radius' => 10],
        'status' => 'done',
        'results_count' => 184,
        'credits_spent' => 172,
    ]);

    $response = $this->actingAs($user)->get(route('user.search.history'));

    $response->assertSuccessful();

    // Reference layout chrome.
    $response->assertSee('app-sidebar', false);
    $response->assertSee('app-content', false);

    // The page header links back to the new-search screen.
    $response->assertSee(route('user.search.new'));

    // Tabbed list + keyword search + period dropdown.
    $response->assertSee('data-list-tab="all"', false);
    $response->assertSee('data-list-search', false);
    $response->assertSee('data-list-filter="period"', false);

    // A real history row with its re-run/delete confirm actions.
    $response->assertSee('dentists');
    $response->assertSee('Austin, TX');
    $response->assertSee('status--done', false);
    $response->assertSee('data-confirm', false);

    // The confirm dialog is available for row actions.
    $response->assertSee('id="confirmDialog"', false);
});

it('renders the All leads screen with tabs, filters, bulk actions and modals', function () {
    Permission::findOrCreate('leads.view', 'web');
    Permission::findOrCreate('leads.manage', 'web');

    $user = User::factory()->create([
        'name' => 'Amara',
        'is_active' => true,
        'email_verified_at' => now(),
    ]);
    $user->givePermissionTo('leads.view', 'leads.manage');

    $place = Place::query()->create([
        'google_place_id' => 'p-barton-springs',
        'name' => 'Barton Springs Dental',
        'formatted_address' => '1401 S Lamar Blvd, Austin, TX',
    ]);
    Lead::query()->create([
        'user_id' => $user->id,
        'place_id' => $place->id,
        'status' => 'qualified',
        'score' => 92,
    ]);

    $response = $this->actingAs($user)->get(route('user.leads.index'));

    $response->assertSuccessful();

    // Reference layout chrome.
    $response->assertSee('app-sidebar', false);
    $response->assertSee('app-content', false);

    // The page header links back to the new-search screen.
    $response->assertSee(route('user.search.new'));

    // Tabbed list + keyword search + score/contact dropdown filters.
    $response->assertSee('data-list-tab="all"', false);
    $response->assertSee('data-list-search', false);
    $response->assertSee('data-list-filter="score"', false);
    $response->assertSee('data-list-filter="contact"', false);

    // Bulk selection scaffolding + a real row + confirm delete.
    $response->assertSee('data-bulk', false);
    $response->assertSee('Barton Springs Dental');
    $response->assertSee('status--qualified', false);

    // Bulk tag/status modals are present.
    $response->assertSee('id="tagModal"', false);
    $response->assertSee('id="statusModal"', false);

    // The sidebar carries the new "Leads" group + item.
    $response->assertSee('Leads');
    $response->assertSee(route('user.leads.index'));
});

it('renders the Lead details screen with the verdict, signals, draft, timeline and side facts', function () {
    Permission::findOrCreate('leads.view', 'web');
    Permission::findOrCreate('leads.manage', 'web');

    $user = User::factory()->create([
        'name' => 'Amara',
        'is_active' => true,
        'email_verified_at' => now(),
    ]);
    $user->givePermissionTo('leads.view', 'leads.manage');

    $place = Place::query()->create([
        'google_place_id' => 'p-barton-springs',
        'name' => 'Barton Springs Dental',
        'formatted_address' => '1401 S Lamar Blvd, Austin, TX',
        'lat' => 30.2540,
        'lng' => -97.7660,
        'rating' => 4.7,
        'review_count' => 312,
        'website' => 'https://bartonspringsdental.com',
    ]);
    $lead = Lead::query()->create([
        'user_id' => $user->id,
        'place_id' => $place->id,
        'status' => 'new',
        'score' => 92,
    ]);
    LeadActivity::logScored($lead);
    LeadActivity::logStatusChanged($lead, 'new', 'new', $user);

    $response = $this->actingAs($user)->get(route('user.leads.show', $lead));

    $response->assertSuccessful();

    // Reference layout chrome.
    $response->assertSee('app-sidebar', false);
    $response->assertSee('app-content', false);

    // The header links back to the All leads table.
    $response->assertSee('back-link', false);
    $response->assertSee(route('user.leads.index'));

    // The lead head with mark, name, status chip and actions.
    $response->assertSee('lead-head', false);
    $response->assertSee('Barton Springs Dental');
    $response->assertSee('status--new', false);
    $response->assertSee('data-modal-open="noteModal"', false);
    $response->assertSee('data-modal-open="statusModal"', false);

    // The AI verdict + score-signal breakdown.
    $response->assertSee('verdict-card__score', false);
    $response->assertSee('signal-row', false);

    // The drafted email with copy button.
    $response->assertSee('data-copy-target="#draft-body"', false);

    // Activity timeline.
    $response->assertSee('timeline__item', false);
    $response->assertSee('timeline__dot--ai', false);

    // Side column: map card + facts + tags.
    $response->assertSee('mapcard', false);
    $response->assertSee('data-map-pins', false);
    $response->assertSee('Where it is');
    $response->assertSee('Category');
    $response->assertSee('Rating');
    $response->assertSee('data-modal-open="tagModal"', false);

    // All three CRUD modals + confirm dialog are present.
    $response->assertSee('id="noteModal"', false);
    $response->assertSee('id="statusModal"', false);
    $response->assertSee('id="tagModal"', false);
    $response->assertSee('id="confirmDialog"', false);
});

it('renders the Map view screen with the map and results rail', function () {
    Permission::findOrCreate('leads.view', 'web');

    $user = User::factory()->create([
        'name' => 'Amara',
        'is_active' => true,
        'email_verified_at' => now(),
    ]);
    $user->givePermissionTo('leads.view');

    $place = Place::query()->create([
        'google_place_id' => 'p-barton-springs',
        'name' => 'Barton Springs Dental',
        'lat' => 30.2540,
        'lng' => -97.7660,
    ]);
    Lead::query()->create([
        'user_id' => $user->id,
        'place_id' => $place->id,
        'score' => 92,
    ]);

    $response = $this->actingAs($user)->get(route('user.leads.map'));

    $response->assertSuccessful();

    // Reference layout chrome.
    $response->assertSee('app-sidebar', false);
    $response->assertSee('app-content', false);

    // Map scaffolding fed by the pins data attribute.
    $response->assertSee('data-map', false);
    $response->assertSee('data-map-pins', false);
    $response->assertSee('data-map-center', false);

    // Keyword search + score filter drive the map and rail.
    $response->assertSee('data-map-view', false);
    $response->assertSee('data-list-search', false);
    $response->assertSee('data-map-count', false);

    // A real rail row keyed for the map filter.
    $response->assertSee('Barton Springs Dental');
    $response->assertSee('data-lead-name', false);

    // The rail links through to the table view.
    $response->assertSee(route('user.leads.index'));
});

it('renders the Lists & tags screen with tab panels and CRUD modals', function () {
    Permission::findOrCreate('leads.manage', 'web');

    $user = User::factory()->create([
        'name' => 'Amara',
        'is_active' => true,
        'email_verified_at' => now(),
    ]);
    $user->givePermissionTo('leads.manage');

    LeadList::query()->create([
        'user_id' => $user->id,
        'name' => 'Austin dentists — Q3',
        'source' => 'manual',
    ]);

    $response = $this->actingAs($user)->get(route('user.leads.lists'));

    $response->assertSuccessful();

    // Reference layout chrome.
    $response->assertSee('app-sidebar', false);
    $response->assertSee('app-content', false);

    // Tab panel scaffolding for Lists / Tags.
    $response->assertSee('data-tabs', false);
    $response->assertSee('data-tab="lists"', false);
    $response->assertSee('data-tab="tags"', false);
    $response->assertSee('data-tab-panel="list', false);

    // A real list row with delete confirm.
    $response->assertSee('Austin dentists — Q3');
    $response->assertSee('data-confirm', false);

    // List + tag CRUD modals are present.
    $response->assertSee('id="listModal"', false);
    $response->assertSee('id="tagModal"', false);
    $response->assertSee('id="confirmDialog"', false);

    // Row links open the leads table.
    $response->assertSee(route('user.leads.index'));

    // The sidebar carries the "Lists & tags" item.
    $response->assertSee(route('user.leads.lists'));
});

it('renders the Business analysis screen with the selection estimate and focus picker', function () {
    $user = User::factory()->create([
        'name' => 'Amara',
        'is_active' => true,
        'email_verified_at' => now(),
    ]);

    $response = $this->actingAs($user)->get(route('user.analysis.index'));

    $response->assertSuccessful();

    // Reference layout chrome.
    $response->assertSee('app-sidebar', false);
    $response->assertSee('app-content', false);

    // Selection-mode estimate form driven by the list picker.
    $response->assertSee('data-estimate-form', false);
    $response->assertSee('data-estimate-mode="selection"', false);
    $response->assertSee('data-balance="2480"', false);
    $response->assertSee('data-count="142"', false);
    $response->assertSee('data-analysed="96"', false);

    // Focus picker drives the results class states.
    $response->assertSee('data-anz-focus', false);
    $response->assertSee('data-anz-results', false);

    // A sampled analysis card.
    $response->assertSee('Barton Springs Dental');
    $response->assertSee('anzr__gap', false);

    // The list manager link + table view link resolve.
    $response->assertSee(route('user.leads.lists'));
    $response->assertSee(route('user.leads.index'));

    // The sidebar carries the "Business analysis" item.
    $response->assertSee(route('user.analysis.index'));
});

it('renders the Lead scoring screen with the weighting preview and apply bar', function () {
    $user = User::factory()->create([
        'name' => 'Amara',
        'is_active' => true,
        'email_verified_at' => now(),
    ]);

    $response = $this->actingAs($user)->get(route('user.scoring.index'));

    $response->assertSuccessful();

    // Reference layout chrome.
    $response->assertSee('app-sidebar', false);
    $response->assertSee('app-content', false);

    // The weighting form with its four signal sliders.
    $response->assertSee('data-scoring', false);
    $response->assertSee('data-weight="reviews"', false);
    $response->assertSee('data-weight="booking"', false);
    $response->assertSee('data-weight="age"', false);
    $response->assertSee('data-weight="competition"', false);
    $response->assertSee('data-weight-total', false);

    // The sample preview wires up the live re-score.
    $response->assertSee('data-sample', false);
    $response->assertSee('data-sample-new', false);
    $response->assertSee('data-sample-delta', false);
    $response->assertSee('data-preview-moved', false);

    // The apply bar carries the commit button and the reset confirm.
    $response->assertSee('data-scoring-apply', false);
    $response->assertSee('data-confirm', false);
    $response->assertSee('confirmDialog', false);

    // A sampled lead.
    $response->assertSee('Barton Springs Dental');

    // The sidebar carries the "Lead scoring" item.
    $response->assertSee(route('user.scoring.index'));
});

it('renders the Email generator screen with the draft and copy targets', function () {
    $user = User::factory()->create([
        'name' => 'Amara',
        'is_active' => true,
        'email_verified_at' => now(),
    ]);

    $response = $this->actingAs($user)->get(route('user.email.index'));

    $response->assertSuccessful();

    // Reference layout chrome.
    $response->assertSee('app-sidebar', false);
    $response->assertSee('app-content', false);

    // The generator controls.
    $response->assertSee('gen-lead', false);
    $response->assertSee('gen-tone', false);
    $response->assertSee('gen-length', false);
    $response->assertSee('gen-open', false);

    // The draft with its copy targets.
    $response->assertSee('gen-subject', false);
    $response->assertSee('gen-body', false);
    $response->assertSee('data-copy-target="#gen-body"', false);
    $response->assertSee('Drafted by AI');

    // Where it can go — copy, template modal, campaign.
    $response->assertSee('data-modal-open="templateModal"', false);
    $response->assertSee('templateModal', false);

    // The sidebar carries the "Email generator" item.
    $response->assertSee(route('user.email.index'));
});

it('renders the Sales pipeline screen with the stage board and move controls', function () {
    $user = User::factory()->create([
        'name' => 'Amara',
        'is_active' => true,
        'email_verified_at' => now(),
    ]);

    $response = $this->actingAs($user)->get(route('user.pipeline.index'));

    $response->assertSuccessful();

    // Reference layout chrome.
    $response->assertSee('app-sidebar', false);
    $response->assertSee('app-content', false);

    // The filter toolbar (search + score + tag) drives the board.
    $response->assertSee('data-list-search', false);
    $response->assertSee('data-list-filter="score"', false);
    $response->assertSee('data-list-filter="tag"', false);

    // The board with its six stages.
    $response->assertSee('data-pipeline', false);
    foreach (['new', 'contacted', 'replied', 'qualified', 'won', 'lost'] as $stage) {
        $response->assertSee('data-stage="'.$stage.'"', false);
    }

    // A sampled card with its drag + move + remove hooks.
    $response->assertSee('data-card', false);
    $response->assertSee('data-move-to', false);
    $response->assertSee('data-remove-card', false);
    $response->assertSee('Barton Springs Dental');

    // The "Add from leads" button resolves.
    $response->assertSee(route('user.leads.index'));

    // The sidebar carries the "Sales pipeline" item.
    $response->assertSee(route('user.pipeline.index'));
});

it('renders the Contacts screen with the role filter and contact modal', function () {
    $user = User::factory()->create([
        'name' => 'Amara',
        'is_active' => true,
        'email_verified_at' => now(),
    ]);

    $response = $this->actingAs($user)->get(route('user.contacts.index'));

    $response->assertSuccessful();

    // Reference layout chrome.
    $response->assertSee('app-sidebar', false);
    $response->assertSee('app-content', false);

    // Filter toolbar: tabs + search + role dropdown.
    $response->assertSee('data-list-tab', false);
    $response->assertSee('data-list-search', false);
    $response->assertSee('data-list-filter="role"', false);
    $response->assertSee('data-list-table', false);
    $response->assertSee('data-list-empty', false);

    // A sampled contact row with its hooks.
    $response->assertSee('data-role="manager"', false);
    $response->assertSee('ct__avatar', false);
    $response->assertSee('Dana Whitfield');
    $response->assertSee('data-confirm', false);

    // The add/edit modal opens via data-modal-open.
    $response->assertSee('data-modal-open="contactModal"', false);
    $response->assertSee('contactModal', false);

    // The sidebar carries the "Contacts" item.
    $response->assertSee(route('user.contacts.index'));
});

it('renders the Notes & activities screen with the timeline feed', function () {
    $user = User::factory()->create([
        'name' => 'Amara',
        'is_active' => true,
        'email_verified_at' => now(),
    ]);

    $response = $this->actingAs($user)->get(route('user.activities.index'));

    $response->assertSuccessful();

    // Reference layout chrome.
    $response->assertSee('app-sidebar', false);
    $response->assertSee('app-content', false);

    // Filter toolbar: tabs + search + kind dropdown.
    $response->assertSee('data-list-tab', false);
    $response->assertSee('data-list-search', false);
    $response->assertSee('data-list-filter="kind"', false);
    $response->assertSee('data-list-table', false);
    $response->assertSee('data-list-empty', false);

    // Day headings + a sampled feed row.
    $response->assertSee('data-feed-day', false);
    $response->assertSee('data-kind="call"', false);
    $response->assertSee('act__dot', false);
    $response->assertSee('Called the front desk');

    // The add-note modal opens via data-modal-open.
    $response->assertSee('data-modal-open="activityModal"', false);
    $response->assertSee('activityModal', false);

    // The sidebar carries the "Notes & activities" item.
    $response->assertSee(route('user.activities.index'));
});

it('renders the Email campaigns screen with the KPIs and campaign table', function () {
    $user = User::factory()->create([
        'name' => 'Amara',
        'is_active' => true,
        'email_verified_at' => now(),
    ]);

    $response = $this->actingAs($user)->get(route('user.campaigns.index'));

    $response->assertSuccessful();

    // Reference layout chrome.
    $response->assertSee('app-sidebar', false);
    $response->assertSee('app-content', false);

    // The KPI strip.
    $response->assertSee('kpi__value', false);
    $response->assertSee('Awaiting your review');
    $response->assertSee('kpi__note', false);

    // Filter toolbar: tabs + search.
    $response->assertSee('data-list-tab', false);
    $response->assertSee('data-list-search', false);
    $response->assertSee('data-list-table', false);
    $response->assertSee('data-list-empty', false);

    // A sampled campaign row with its status and row actions.
    $response->assertSee('data-list-key="active"', false);
    $response->assertSee('status--running', false);
    $response->assertSee('live-dot', false);
    $response->assertSee('data-confirm', false);

    // The note about credits links to the draft writer.
    $response->assertSee('camp__note', false);
    $response->assertSee(route('user.email.index'));

    // The sidebar carries the "Email campaigns" item.
    $response->assertSee(route('user.campaigns.index'));
});

it('renders the Export center screen with the choices and past exports', function () {
    $user = User::factory()->create([
        'name' => 'Amara',
        'is_active' => true,
        'email_verified_at' => now(),
    ]);

    $response = $this->actingAs($user)->get(route('user.export.index'));

    $response->assertSuccessful();

    // Reference layout chrome.
    $response->assertSee('app-sidebar', false);
    $response->assertSee('app-content', false);

    // The source picker (selection / all / list / search) and the column grid.
    $response->assertSee('cnew__sum', false);
    $response->assertSee('What to export');
    $response->assertSee('The leads you selected');
    $response->assertSee('exp__col', false);
    $response->assertSee('Business name');
    $response->assertSee('Lead score');
    $response->assertSee('Why it scored that');

    // The file format picker and the no-email switch.
    $response->assertSee('name="format"', false);
    $response->assertSee('x-noemail', false);
    $response->assertSee('Leave out leads with no email');

    // The summary card with the download submit.
    $response->assertSee('This export');
    $response->assertSee('No credits.', false);
    $response->assertSee('Download');

    // Past exports table with download + delete confirm rows.
    $response->assertSee('Past exports');
    $response->assertSee('table-scroll', false);
    $response->assertSee('austin-dentists-q3.csv');
    $response->assertSee('data-confirm', false);
    $response->assertSee('data-confirm-body', false);
    $response->assertSee('id="confirmDialog"', false);

    // The sidebar carries the "Export center" item.
    $response->assertSee(route('user.export.index'));
});

it('renders the Credits & billing screen with the balance and itemised ledger', function () {
    Permission::findOrCreate('credits.view', 'web');

    $user = User::factory()->create([
        'name' => 'Amara',
        'is_active' => true,
        'email_verified_at' => now(),
    ]);
    $user->givePermissionTo('credits.view');

    app(CreditLedger::class)->grant($user, 100, 'starter_grant');

    $response = $this->actingAs($user)->get(route('user.credits.index'));

    $response->assertSuccessful();

    // Reference layout chrome.
    $response->assertSee('app-sidebar', false);
    $response->assertSee('app-content', false);

    // The balance KPI strip.
    $response->assertSee('kpi__value', false);
    $response->assertSee('Credits remaining');
    $response->assertSee('100');

    // The itemised ledger table.
    $response->assertSee('data-list-table', false);
    $response->assertSee('ledger__total', false);
    $response->assertSee('Remaining');

    // The sidebar carries the "Credits & billing" item under Account.
    $response->assertSee('Credits & billing');
    $response->assertSee(route('user.credits.index'));
});

it('renders the Buy credits screen with the balance and top-up packs', function () {
    Permission::findOrCreate('credits.view', 'web');

    PricingPlan::query()->create([
        'name' => 'Growth',
        'slug' => 'growth',
        'tagline' => 'For teams ready to turn scored leads into outreach.',
        'icon' => 'ph-sparkle',
        'price_monthly' => 89,
        'price_yearly' => 890,
        'credits_monthly' => 5000,
        'features' => ['AI summaries and drafted emails'],
        'cta_label' => 'Start free',
        'is_active' => true,
        'is_featured' => true,
        'sort_order' => 1,
    ]);

    $user = User::factory()->create([
        'name' => 'Amara',
        'is_active' => true,
        'email_verified_at' => now(),
    ]);
    $user->givePermissionTo('credits.view');

    $response = $this->actingAs($user)->get(route('user.credits.buy'));

    $response->assertSuccessful();

    // Reference layout chrome.
    $response->assertSee('app-sidebar', false);
    $response->assertSee('app-content', false);

    // Balance strip.
    $response->assertSee('bal', false);
    $response->assertSee('Credits remaining');

    // Pricing-plan cards with radio selection and per-credit pricing.
    $response->assertSee('Available plans');
    $response->assertSee('packs', false);
    $response->assertSee('pack__radio', false);
    $response->assertSee('Growth');
    $response->assertSee('5,000');
    $response->assertSee('$89');
    $response->assertSee('Checkout');
    $response->assertSee('Most bought');
    $response->assertDontSee('Coming soon');
});

it('renders the API keys screen with the plan lock and key modal', function () {
    $user = User::factory()->create([
        'name' => 'Amara',
        'is_active' => true,
        'email_verified_at' => now(),
    ]);

    $response = $this->actingAs($user)->get(route('user.api.keys'));

    $response->assertSuccessful();

    // Reference layout chrome.
    $response->assertSee('app-sidebar', false);
    $response->assertSee('app-content', false);

    // The shared API nav links across the three screens.
    $response->assertSee(route('user.api.keys'));
    $response->assertSee(route('user.api.docs'));
    $response->assertSee(route('user.api.integrations'));

    // The Scale-plan lock with its benefits and actions.
    $response->assertSee('apik-lock__icon', false);
    $response->assertSee('The API is on the Scale plan');
    $response->assertSee('apik-lock__list', false);
    $response->assertSee('Compare plans');
    $response->assertSee(route('user.credits.index'));

    // The unlocked keys section + copy/revoke hooks + modal.
    $response->assertSee('id="apiUnlocked"', false);
    $response->assertSee('data-copy', false);
    $response->assertSee('data-confirm', false);
    $response->assertSee('id="keyModal"', false);
    $response->assertSee('id="confirmDialog"', false);

    // The sidebar carries the "API & integrations" item under Account.
    $response->assertSee('API & integrations');
});

it('renders the API documentation screen with code blocks and language tabs', function () {
    $user = User::factory()->create([
        'name' => 'Amara',
        'is_active' => true,
        'email_verified_at' => now(),
    ]);

    $response = $this->actingAs($user)->get(route('user.api.docs'));

    $response->assertSuccessful();

    // Reference layout chrome.
    $response->assertSee('app-sidebar', false);
    $response->assertSee('app-content', false);

    // The reference sections + TOC.
    $response->assertSee('doc__facts', false);
    $response->assertSee('Getting started');
    $response->assertSee('Authentication');
    $response->assertSee('doc__nav', false);
    $response->assertSee('On this page');

    // Language tabs switch the request snippets (tabs.js).
    $response->assertSee('data-tabs', false);
    $response->assertSee('data-tab="curl"', false);
    $response->assertSee('data-tab="php"', false);
    $response->assertSee('data-tab="node"', false);

    // Copy targets + endpoint verb badges.
    $response->assertSee('data-copy-target', false);
    $response->assertSee('doc__verb--get', false);
    $response->assertSee('doc__verb--post', false);
    $response->assertSee('Spends credits');

    // A sampled lead payload + error table.
    $response->assertSee('Barton Springs Dental');
    $response->assertSee('insufficient_credits');
    $response->assertSee(route('user.api.keys'));
});

it('renders the Integrations screen with the connection grid and webhooks lock', function () {
    $user = User::factory()->create([
        'name' => 'Amara',
        'is_active' => true,
        'email_verified_at' => now(),
    ]);

    $response = $this->actingAs($user)->get(route('user.api.integrations'));

    $response->assertSuccessful();

    // Reference layout chrome.
    $response->assertSee('app-sidebar', false);
    $response->assertSee('app-content', false);

    // The connections grid: connected cards + available connectors.
    $response->assertSee('integs', false);
    $response->assertSee('integ__logo--hubspot', false);
    $response->assertSee('integ__logo--zapier', false);
    $response->assertSee('Connected');
    $response->assertSee('Available');
    $response->assertSee('HubSpot');
    $response->assertSee('Pipedrive');

    // Connect modal + disconnect confirms.
    $response->assertSee('data-modal-open="connectModal"', false);
    $response->assertSee('connectModal', false);
    $response->assertSee('data-confirm', false);
    $response->assertSee('id="confirmDialog"', false);

    // The webhooks lock + hidden unlocked panel.
    $response->assertSee('Webhooks are on the Scale plan');
    $response->assertSee('id="webhooksUnlocked"', false);
    $response->assertSee(route('user.api.docs'));
});

it('renders the Account settings screen with its five tabs', function () {
    $user = User::factory()->create([
        'name' => 'Amara',
        'is_active' => true,
        'email_verified_at' => now(),
    ]);

    $response = $this->actingAs($user)->get(route('user.settings.index'));

    $response->assertSuccessful();

    // Reference layout chrome.
    $response->assertSee('app-sidebar', false);
    $response->assertSee('app-content', false);

    // The settings tablist drives the five panels (tabs.js).
    $response->assertSee('data-tabs', false);
    $response->assertSee('data-tab="general"', false);
    $response->assertSee('data-tab="team"', false);
    $response->assertSee('data-tab="defaults"', false);
    $response->assertSee('data-tab="email"', false);
    $response->assertSee('data-tab="danger"', false);
    $response->assertSee('data-tab-panel="danger"', false);

    // General tab — the workspace form.
    $response->assertSee('Workspace');
    $response->assertSee('name="workspace_name"', false);
    $response->assertSee('Rivera Growth Studio');

    // Team tab — member rows, role selects, and the roles legend.
    $response->assertSee('Invite member');
    $response->assertSee('data-modal-open="inviteModal"', false);
    $response->assertSee('name="role[2]"', false);
    $response->assertSee('set__roles', false);
    $response->assertSee('That is you');

    // Search defaults tab — skip switches.
    $response->assertSee('name="default_radius"', false);
    $response->assertSee('name="skip_no_phone"', false);
    $response->assertSee('name="skip_seen"', false);

    // Email tab — preference switches.
    $response->assertSee('name="email_search_done"', false);
    $response->assertSee('name="email_weekly"', false);

    // Danger zone — the delete card links to the export center.
    $response->assertSee('Delete this workspace');
    $response->assertSee('data-confirm', false);
    $response->assertSee(route('user.export.index'));
    $response->assertSee('Export leads first');

    // The invite modal + confirm dialog.
    $response->assertSee('id="inviteModal"', false);
    $response->assertSee('id="confirmDialog"', false);

    // The sidebar carries the "Settings" item under Account.
    $response->assertSee('Settings');
    $response->assertSee(route('user.settings.index'));
});
