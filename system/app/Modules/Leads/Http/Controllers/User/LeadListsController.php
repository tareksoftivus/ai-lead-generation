<?php

namespace App\Modules\Leads\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Modules\Leads\Http\Requests\StoreLeadListRequest;
use App\Modules\Leads\Models\LeadList;
use App\Modules\Leads\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class LeadListsController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:leads.manage'),
        ];
    }

    /**
     * The "Lists & tags" screen — the account's groupings of leads.
     */
    public function index(Request $request): View
    {
        $lists = LeadList::query()
            ->forUser($request->user()->id)
            ->withCount('leads')
            ->latest('updated_at')
            ->get();

        $tags = Tag::query()
            ->forUser($request->user()->id)
            ->withCount('leads')
            ->latest()
            ->get();

        return view('leads::user.leads.lists', [
            'lists' => $lists,
            'tags' => $tags,
        ]);
    }

    public function store(StoreLeadListRequest $request): RedirectResponse
    {
        LeadList::query()->create([
            'user_id' => $request->user()->id,
            'name' => $request->validated('name'),
            'note' => $request->validated('note'),
            'source' => LeadList::SOURCE_MANUAL,
        ]);

        return redirect()
            ->route('user.leads.lists')
            ->with('success', __('List created.'));
    }

    public function update(StoreLeadListRequest $request, LeadList $list): RedirectResponse
    {
        $this->authorizeOwnership($request, $list);

        $list->update([
            'name' => $request->validated('name'),
            'note' => $request->validated('note'),
        ]);

        return redirect()
            ->route('user.leads.lists')
            ->with('success', __('List updated.'));
    }

    public function destroy(Request $request, LeadList $list): RedirectResponse
    {
        $this->authorizeOwnership($request, $list);

        $list->delete();

        return redirect()
            ->route('user.leads.lists')
            ->with('success', __('List deleted.'));
    }

    protected function authorizeOwnership(Request $request, LeadList $list): void
    {
        abort_unless($list->user_id === $request->user()->id, 404);
    }
}
