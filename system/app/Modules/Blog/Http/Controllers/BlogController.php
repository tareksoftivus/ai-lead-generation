<?php

namespace App\Modules\Blog\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Blog\Models\BlogCategory;
use App\Modules\Blog\Models\BlogPost;
use App\Modules\Blog\Services\BlogPostService;
use App\Modules\Frontend\Models\FrontendSection;
use App\Modules\Frontend\Models\Page;
use App\Modules\Frontend\Services\ActiveThemeResolver;
use App\Modules\Frontend\Services\FrontendPageService;
use App\Modules\Frontend\Services\MenuRenderService;
use App\Modules\Frontend\Services\PageRenderService;
use App\Modules\Frontend\Services\ThemeRegistry;
use App\Modules\Frontend\Services\ThemeRenderService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BlogController extends Controller
{
    protected const VARIANTS = ['find', 'ai', 'act', 'legal'];

    public function __construct(
        protected BlogPostService $service,
        protected FrontendPageService $pages,
        protected ActiveThemeResolver $activeThemeResolver,
        protected PageRenderService $renderer,
        protected ThemeRegistry $themes,
        protected ThemeRenderService $themeRender,
        protected MenuRenderService $menus
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

        if ($slug = $request->query('category', $request->query('topic'))) {
            $activeCategory = BlogCategory::active()->where('slug', $slug)->first();
        }

        $posts = $this->service->publishedForPublic($activeCategory?->id);

        if ($activeCategory) {
            $posts->appends(['category' => $activeCategory->slug]);
        }

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

        return $this->fallbackIndexView($posts, $categories, $activeCategory);
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
                [
                    'label' => __('All posts'),
                    'value' => '',
                    'url' => route('blog.index'),
                    'active' => ! $activeCategory,
                ],
            ])->concat($categories->map(fn (BlogCategory $category) => [
                'label' => $category->name,
                'value' => $category->slug,
                'url' => route('blog.index', ['category' => $category->slug]),
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
                'url' => $posts->url($number),
                'active' => $number === $posts->currentPage(),
            ])->all() : [],
            'previous_page_url' => $posts->previousPageUrl(),
            'next_page_url' => $posts->nextPageUrl(),
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

    protected function fallbackIndexView($posts, $categories, ?BlogCategory $activeCategory): View
    {
        $themeKey = 'leadatlas';
        $page = new Page([
            'title' => __('Blog'),
            'slug' => 'blog',
            'meta_title' => __('Blog'),
            'meta_description' => __('Notes on finding local businesses, scoring them, and getting a reply - written for people doing the work.'),
            'default_layout' => 'default',
        ]);

        $hero = new FrontendSection([
            'type' => 'featurespage_hero',
            'data' => [
                'title' => __('Blog'),
                'lead' => __('Notes on finding local businesses, scoring them, and getting a reply - written for people doing the work.'),
            ],
        ]);

        $grid = new FrontendSection([
            'type' => 'blogpage_grid',
            'data' => $this->gridData($posts, $categories, $activeCategory),
        ]);

        return view('frontend.themes.leadatlas.layouts.page', [
            'themeKey' => $themeKey,
            'theme' => $this->themes->get($themeKey),
            'themeVars' => $this->themeRender->themeVariables($themeKey),
            'page' => $page,
            'resolvedMenus' => $this->menus->resolveForTheme($themeKey),
            'resolvedSections' => [
                [
                    'view' => 'frontend.themes.leadatlas.sections.featurespage_hero',
                    'supported' => true,
                    'section' => $hero,
                ],
                [
                    'view' => 'frontend.themes.leadatlas.sections.blogpage_grid',
                    'supported' => true,
                    'section' => $grid,
                ],
            ],
        ]);
    }

    protected function relatedPostsFor(BlogPost $post)
    {
        $related = collect();

        if ($post->blog_category_id) {
            $related = BlogPost::published()
                ->with(['category', 'author'])
                ->where('blog_category_id', $post->blog_category_id)
                ->whereKeyNot($post->id)
                ->latest('published_at')
                ->limit(3)
                ->get();
        }

        if ($related->count() < 3) {
            $topUp = BlogPost::published()
                ->with(['category', 'author'])
                ->whereKeyNot($post->id)
                ->when($related->isNotEmpty(), fn ($query) => $query->whereKeyNot($related->pluck('id')->all()))
                ->latest('published_at')
                ->limit(3 - $related->count())
                ->get();

            $related = $related->concat($topUp);
        }

        return $related->values();
    }

    protected function blogDetailPage(BlogPost $post): Page
    {
        return new Page([
            'title' => $post->title,
            'slug' => 'blog/'.$post->slug,
            'meta_title' => $post->seoTitle(),
            'meta_description' => $post->seoDescription(),
            'default_layout' => 'default',
        ]);
    }

    protected function blogDetailSections(BlogPost $post, $related): array
    {
        return [
            [
                'view' => 'frontend.themes.leadatlas.sections.blog_details_hero',
                'supported' => true,
                'section' => new FrontendSection([
                    'type' => 'blog_details_hero',
                    'data' => [
                        'title' => $post->title,
                    ],
                ]),
            ],
            [
                'view' => 'frontend.themes.leadatlas.sections.blog_details_article',
                'supported' => true,
                'section' => new FrontendSection([
                    'type' => 'blog_details_article',
                    'data' => $this->blogDetailArticleData($post),
                ]),
            ],
            [
                'view' => 'frontend.themes.leadatlas.sections.blog_details_related',
                'supported' => true,
                'section' => new FrontendSection([
                    'type' => 'blog_details_related',
                    'data' => [
                        'posts' => $related
                            ->map(fn (BlogPost $relatedPost, int $index) => $this->blogRelatedCardData($relatedPost, $index))
                            ->all(),
                    ],
                ]),
            ],
        ];
    }

    protected function blogDetailArticleData(BlogPost $post): array
    {
        return [
            'title' => $post->title,
            'body' => $post->body,
            'cover_image' => $post->cover_image,
            'cover_image_url' => $this->coverImageFor($post),
            'author_name' => $this->authorNameFor($post),
            'author_avatar' => asset('assets/frontend/leadatlas/images/avatars/avatar-3.jpg'),
            'published_date' => $post->published_at?->toDateString(),
            'published_date_display' => $post->published_at?->format('d F Y'),
            'read_minutes' => $this->readMinutes($post),
            'category' => $post->category?->name,
            'variant' => $this->variantFor($post),
        ];
    }

    protected function blogRelatedCardData(BlogPost $post, int $index): array
    {
        return [
            'title' => $post->title,
            'excerpt' => $post->excerpt,
            'url' => route('blog.show', $post->slug),
            'image' => $this->coverImageFor($post, $index + 1),
            'date_day' => $post->published_at?->format('d'),
            'date_month' => $post->published_at?->format('M'),
            'category' => $post->category?->name,
            'variant' => $this->variantFor($post),
            'read_minutes' => $this->readMinutes($post),
            'author_name' => $this->authorNameFor($post),
            'author_avatar' => asset('assets/frontend/leadatlas/images/avatars/avatar-'.($index % 2 ? '3' : '4').'.jpg'),
        ];
    }

    protected function blogSeoData(BlogPost $post, array $themeVars): array
    {
        $description = $post->seoDescription();
        $canonicalUrl = route('blog.show', $post->slug);
        $coverImage = $this->coverImageFor($post);
        $authorName = $this->authorNameFor($post);

        return [
            'title' => $post->seoTitle(),
            'description' => $description,
            'canonical' => $canonicalUrl,
            'og_type' => 'article',
            'image' => $coverImage,
            'published_time' => $post->published_at?->toAtomString(),
            'modified_time' => $post->updated_at?->toAtomString(),
            'author' => $authorName,
            'json_ld' => [
                '@context' => 'https://schema.org',
                '@type' => 'BlogPosting',
                'headline' => $post->seoTitle(),
                'description' => $description,
                'url' => $canonicalUrl,
                'datePublished' => $post->published_at?->toAtomString(),
                'dateModified' => $post->updated_at?->toAtomString(),
                'author' => [
                    '@type' => 'Person',
                    'name' => $authorName,
                ],
                'publisher' => [
                    '@type' => 'Organization',
                    'name' => $themeVars['logo_text'] ?? 'LeadAtlas',
                ],
                'image' => $coverImage,
                'mainEntityOfPage' => [
                    '@type' => 'WebPage',
                    '@id' => $canonicalUrl,
                ],
            ],
        ];
    }

    protected function authorNameFor(BlogPost $post): string
    {
        return $post->author?->name ?: __('LeadAtlas Editorial');
    }

    protected function coverImageFor(BlogPost $post, int $fallbackIndex = 0): string
    {
        $coverImage = trim((string) $post->cover_image);

        if ($coverImage !== '') {
            if (filter_var($coverImage, FILTER_VALIDATE_URL) || str_starts_with($coverImage, '/')) {
                return $coverImage;
            }

            if (is_numeric($coverImage) && $mediaUrl = media_url($coverImage)) {
                return $mediaUrl;
            }

            return Storage::disk('public')->url($coverImage);
        }

        $seed = $post->getKey() ?: abs(crc32($post->slug ?: $post->title));
        $imageNumber = (($seed - 1 + $fallbackIndex) % 8) + 1;

        return asset('assets/frontend/leadatlas/images/blog/blog-thumb-0'.$imageNumber.'.jpg');
    }

    /**
     * XML sitemap of the blog index and every published post.
     */
    public function sitemap(): Response
    {
        $posts = BlogPost::published()
            ->orderByDesc('published_at')
            ->get(['slug', 'updated_at', 'published_at']);

        $escape = fn (?string $value): string => htmlspecialchars((string) $value, ENT_XML1 | ENT_COMPAT, 'UTF-8');
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
        $xml .= '    <url>'."\n";
        $xml .= '        <loc>'.$escape(route('blog.index')).'</loc>'."\n";
        $xml .= '    </url>'."\n";

        foreach ($posts as $post) {
            $xml .= '    <url>'."\n";
            $xml .= '        <loc>'.$escape(route('blog.show', $post->slug)).'</loc>'."\n";
            $xml .= '        <lastmod>'.$escape(($post->updated_at ?: $post->published_at)?->toAtomString()).'</lastmod>'."\n";
            $xml .= '    </url>'."\n";
        }

        $xml .= '</urlset>'."\n";

        return response($xml)->header('Content-Type', 'application/xml');
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

        $themeKey = 'leadatlas';
        $themeVars = $this->themeRender->themeVariables($themeKey);
        $related = $this->relatedPostsFor($post);

        return view('frontend.themes.leadatlas.layouts.page', [
            'post' => $post,
            'related' => $related,
            'themeKey' => $themeKey,
            'theme' => $this->themes->get($themeKey),
            'themeVars' => $themeVars,
            'page' => $this->blogDetailPage($post),
            'resolvedMenus' => $this->menus->resolveForTheme($themeKey),
            'resolvedSections' => $this->blogDetailSections($post, $related),
            'seo' => $this->blogSeoData($post, $themeVars),
        ]);
    }
}
