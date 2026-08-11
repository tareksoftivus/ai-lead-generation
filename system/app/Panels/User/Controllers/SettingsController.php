<?php

namespace App\Panels\User\Controllers;

use App\Http\Controllers\Controller;
use App\Models\UserSetting;
use App\Modules\AiTools\Models\BusinessAnalysisItem;
use App\Modules\Leads\Models\Lead;
use App\Modules\Leads\Models\Search;
use App\Modules\Leads\Models\SearchRun;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * The "Account settings" screen — workspace display, search defaults,
     * email preferences, and the danger zone.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $settings = UserSetting::forUser($user);

        return view('panels.user.settings.index', [
            'user' => $user,
            'settings' => $settings,
            'searchDefaults' => $settings->mergedSearchDefaults(),
            'emailPreferences' => $settings->mergedEmailPreferences(),
            'timezones' => $this->timezoneOptions($settings->timezone),
            'workspaceStats' => $this->workspaceStats($user),
        ]);
    }

    public function updateGeneral(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'workspace_name' => ['required', 'string', 'max:120'],
            'timezone' => ['required', 'timezone'],
        ]);

        UserSetting::forUser($request->user())->update($validated);

        return redirect()
            ->to(route('user.settings.index').'#general')
            ->with('success', __('Workspace settings updated.'));
    }

    public function updateSearchDefaults(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'default_location' => ['nullable', 'string', 'max:255'],
            'default_radius' => ['required', 'integer', 'min:1', 'max:50'],
            'min_rating' => ['nullable', Rule::in(['', '3', '4', '4.5'])],
            'min_reviews' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'skip_no_phone' => ['nullable', 'boolean'],
            'skip_closed' => ['nullable', 'boolean'],
            'skip_seen' => ['nullable', 'boolean'],
        ]);

        UserSetting::forUser($request->user())->update([
            'search_defaults' => [
                'default_location' => trim((string) ($validated['default_location'] ?? '')),
                'default_radius' => (int) $validated['default_radius'],
                'min_rating' => ($validated['min_rating'] ?? '') !== '' ? (string) $validated['min_rating'] : null,
                'min_reviews' => isset($validated['min_reviews']) ? (int) $validated['min_reviews'] : null,
                'skip_no_phone' => $request->boolean('skip_no_phone'),
                'skip_closed' => $request->boolean('skip_closed'),
                'skip_seen' => $request->boolean('skip_seen'),
            ],
        ]);

        return redirect()
            ->to(route('user.settings.index').'#defaults')
            ->with('success', __('Search defaults updated.'));
    }

    public function updateEmailPreferences(Request $request): RedirectResponse
    {
        $request->validate([
            'email_search_done' => ['nullable', 'boolean'],
            'email_low_credits' => ['nullable', 'boolean'],
            'email_weekly' => ['nullable', 'boolean'],
            'email_product' => ['nullable', 'boolean'],
        ]);

        UserSetting::forUser($request->user())->update([
            'email_preferences' => [
                'email_search_done' => $request->boolean('email_search_done'),
                'email_low_credits' => $request->boolean('email_low_credits'),
                'email_weekly' => $request->boolean('email_weekly'),
                'email_product' => $request->boolean('email_product'),
            ],
        ]);

        return redirect()
            ->to(route('user.settings.index').'#email')
            ->with('success', __('Email preferences updated.'));
    }

    public function destroyWorkspace(Request $request): RedirectResponse
    {
        $user = $request->user();
        $settings = UserSetting::forUser($user);

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'confirm_workspace' => ['required', Rule::in([$settings->workspace_name])],
        ]);

        Auth::guard('web')->logout();

        DB::transaction(fn () => $user->forceDelete());

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', __('Your workspace and account have been deleted.'));
    }

    /**
     * @return array<int, string>
     */
    protected function timezoneOptions(?string $selected = null): array
    {
        $preferred = [
            'UTC',
            'America/Chicago',
            'America/New_York',
            'America/Denver',
            'America/Los_Angeles',
            'Europe/London',
            'Asia/Dhaka',
        ];

        if ($selected && ! in_array($selected, $preferred, true)) {
            array_unshift($preferred, $selected);
        }

        return array_values(array_unique($preferred));
    }

    /**
     * @return array{leads: int, searches: int, credits: int, analyses: int}
     */
    protected function workspaceStats($user): array
    {
        return [
            'leads' => Lead::query()->where('user_id', $user->id)->count(),
            'searches' => Search::query()->where('user_id', $user->id)->count() + SearchRun::query()->where('user_id', $user->id)->count(),
            'credits' => (int) $user->credits_balance,
            'analyses' => BusinessAnalysisItem::query()->where('user_id', $user->id)->count(),
        ];
    }
}
