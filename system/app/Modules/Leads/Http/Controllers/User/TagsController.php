<?php

namespace App\Modules\Leads\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Modules\Leads\Http\Requests\StoreTagRequest;
use App\Modules\Leads\Models\Tag;
use App\Modules\Leads\Services\TagService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class TagsController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:leads.manage'),
        ];
    }

    public function __construct(
        protected TagService $tagService
    ) {}

    public function store(StoreTagRequest $request): RedirectResponse
    {
        $this->tagService->findOrCreate($request->user(), $request->validated('tag'));

        return redirect()
            ->route('user.leads.lists')
            ->with('success', __('Tag created.'));
    }

    public function update(StoreTagRequest $request, Tag $tag): RedirectResponse
    {
        $this->authorizeOwnership($request, $tag);

        $tag->update(['name' => trim($request->validated('tag'))]);

        return redirect()
            ->route('user.leads.lists')
            ->with('success', __('Tag renamed.'));
    }

    /**
     * Deletes the tag and detaches it from every lead — the leads
     * themselves are never touched.
     */
    public function destroy(Request $request, Tag $tag): RedirectResponse
    {
        $this->authorizeOwnership($request, $tag);

        $tag->leads()->detach();
        $tag->delete();

        return redirect()
            ->route('user.leads.lists')
            ->with('success', __('Tag deleted.'));
    }

    protected function authorizeOwnership(Request $request, Tag $tag): void
    {
        abort_unless($tag->user_id === $request->user()->id, 404);
    }
}
