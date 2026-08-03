<?php

namespace App\Modules\Blog\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Blog\Models\BlogCategory;
use App\Modules\Blog\Models\BlogPost;
use App\Modules\Blog\Services\BlogPostService;
use App\Modules\Frontend\Services\ActiveThemeResolver;
use App\Modules\Frontend\Services\FrontendPageService;
use App\Modules\Frontend\Services\PageRenderService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class BlogController extends Controller
{
    protected const VARIANTS = ['find', 'ai', 'act', 'legal'];

    public function __construct(
        protected BlogPostService $service,
        protected FrontendPageService $pages,
        protected ActiveThemeResolver $activeThemeResolver,
        protected PageRenderService $renderer
    ) {}

    /**
     * Public blog listing, optionally filtered by category slug. Renders
     * through the leadatlas theme's CMS page pipeline (the "blog" Page),
     * with the blogpage_grid section's data replaced in memory by real
     * BlogPost/BlogCategory records instead of its stored placeholder data.
     */
    public function index(Request $request)
    {
        $activeCategory = null;

        if ($slug = $request->query('category')) {
            $activeCategory = BlogCategory::active()->where('slug', $slug)->first();
        }

        $posts = $this->service->publishedForPublic($activeCategory?->id);
        $categories = BlogCategory::active()->orderBy('sort_order')->orderBy('name')->get();

        if (Schema::hasTable('pages') && $page = $this->pages->findBySlug('blog')) {
            $payload = $this->renderer->payload($page, $this->activeThemeResolver->resolve());

            foreach ($payload['resolvedSections'] as $resolved) {
                if ($resolved['section']->type === 'blogpage_grid') {
                    $resolved['section']->data = $this->gridData($posts, $categories, $activeCategory);
                }
            }

            return response()->view($payload['layoutView'], $payload);
        }

        return view('blog::public.index', [
            'posts' => $posts,
            'categories' => $categories,
            'activeCategory' => $activeCategory,
        ]);
    }

    /**
     * Build the blogpage_grid section data array from real posts/categories:
     * a lead article (the newest post), a grid of the rest, category filter
     * pills, and pagination page numbers.
     */
    protected function gridData($posts, $categories, ?BlogCategory $activeCategory): array
    {
        $items = collect($posts->items());
        $lead = $items->first();
        $rest = $items->slice($lead ? 1 : 0);

        return [
            'topics' => $categories->isEmpty() ? [] : collect([
                ['label' => __('All posts'), 'value' => '', 'active' => ! $activeCategory],
            ])->concat($categories->map(fn (BlogCategory $category) => [
                'label' => $category->name,
                'value' => $category->slug,
                'active' => $activeCategory?->id === $category->id,
            ]))->all(),
            'lead_image' => $lead?->coverImageUrl(),
            'lead_topic' => $lead?->category?->name ?? '',
            'lead_variant' => $this->variantFor($lead),
            'lead_date' => $lead?->published_at?->toDateString() ?? '',
            'lead_date_display' => $lead?->published_at?->format('d F Y') ?? '',
            'lead_read_minutes' => $lead ? (string) $this->readMinutes($lead) : '',
            'lead_title' => $lead?->title ?? '',
            'lead_excerpt' => $lead?->excerpt ?? '',
            'lead_author_avatar' => null,
            'lead_author_name' => $lead?->author?->name ?? '',
            'lead_link' => $lead ? route('blog.show', $lead->slug) : '#',
            'posts' => $rest->map(fn (BlogPost $post) => [
                'image' => $post->coverImageUrl(),
                'date_day' => $post->published_at?->format('d') ?? '',
                'date_month' => $post->published_at?->format('M') ?? '',
                'topic' => $post->category?->name ?? '',
                'variant' => $this->variantFor($post),
                'read_minutes' => (string) $this->readMinutes($post),
                'title' => $post->title,
                'excerpt' => $post->excerpt ?? '',
                'author_avatar' => null,
                'author_name' => $post->author?->name ?? '',
                'link' => route('blog.show', $post->slug),
            ])->values()->all(),
            'pages' => $posts->lastPage() > 1 ? collect(range(1, $posts->lastPage()))->map(fn (int $number) => [
                'number' => (string) $number,
                'active' => $number === $posts->currentPage(),
            ])->all() : [],
        ];
    }

    protected function variantFor(?BlogPost $post): string
    {
        if (! $post || ! $post->category) {
            return 'find';
        }

        return self::VARIANTS[$post->category->id % count(self::VARIANTS)];
    }

    protected function readMinutes(BlogPost $post): int
    {
        $words = str_word_count(strip_tags((string) $post->body));

        return max(1, (int) ceil($words / 200));
    }

    /**
     * XML sitemap of the blog index and every published post.
     */
    public function sitemap(): Response
    {
        $posts = BlogPost::published()
            ->orderByDesc('published_at')
            ->get(['slug', 'updated_at', 'published_at']);

        return response()
            ->view('blog::public.sitemap', ['posts' => $posts])
            ->header('Content-Type', 'application/xml');
    }

    /**
     * Public single-post page.
     */
    public function show(string $slug): View
    {
        $post = BlogPost::published()
            ->with(['category', 'author'])
            ->where('slug', $slug)
            ->firstOrFail();

        $related = BlogPost::published()
            ->where('blog_category_id', $post->blog_category_id)
            ->whereKeyNot($post->id)
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('blog::public.show', [
            'post' => $post,
            'related' => $related,
        ]);
    }
}
