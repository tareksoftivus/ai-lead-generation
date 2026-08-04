<?php

namespace App\Http\Controllers;

use App\Modules\Blog\Models\BlogPost;
use App\Modules\Frontend\Services\ActiveThemeResolver;
use App\Modules\Frontend\Services\FrontendPageService;
use App\Modules\Frontend\Services\PageRenderService;
use App\Modules\PricingPlan\Models\PricingPlan;
use App\Modules\Testimonials\Models\Testimonial;
use Illuminate\Support\Facades\Schema;
use Throwable;

class FrontendPageController extends Controller
{
    protected const STAGE_VARIANTS = ['find', 'ai', 'act'];

    protected const VOICE_ICONS = ['ph-map-trifold', 'ph-sparkle', 'ph-paper-plane-tilt'];

    public function __construct(
        protected FrontendPageService $pages,
        protected ActiveThemeResolver $activeThemeResolver,
        protected PageRenderService $renderer
    ) {}

    public function home()
    {
        try {
            if (! Schema::hasTable('pages')) {
                return view('welcome');
            }

            $page = $this->pages->homePage();

            if (! $page) {
                return view('welcome');
            }

            $payload = $this->renderer->payload($page, $this->activeThemeResolver->resolve());

            foreach ($payload['resolvedSections'] as $resolved) {
                if ($resolved['section']->type === 'homepage_voices') {
                    $resolved['section']->data = array_merge(
                        $resolved['section']->data,
                        ['items' => $this->voiceItems()]
                    );
                }

                if ($resolved['section']->type === 'homepage_pricing') {
                    $resolved['section']->data = array_merge(
                        $resolved['section']->data,
                        ['plans' => $this->pricingPlanItems()]
                    );
                }

                if ($resolved['section']->type === 'homepage_blog') {
                    $resolved['section']->data = array_merge(
                        $resolved['section']->data,
                        ['posts' => $this->blogPostItems()]
                    );
                }
            }

            return response()->view($payload['layoutView'], $payload);
        } catch (Throwable) {
            return view('welcome');
        }
    }

    /**
     * Map active Testimonial records into the homepage_voices section's
     * item shape, replacing the section's stored placeholder data.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function voiceItems(): array
    {
        return Testimonial::active()
            ->orderBy('sort_order')
            ->orderByDesc('rating')
            ->orderBy('client_name')
            ->get()
            ->values()
            ->map(fn (Testimonial $testimonial, int $index) => [
                'variant' => self::STAGE_VARIANTS[$index % count(self::STAGE_VARIANTS)],
                'icon' => self::VOICE_ICONS[$index % count(self::VOICE_ICONS)],
                'label' => $testimonial->designation ?: __('Customer'),
                'quote' => $testimonial->quote,
                'avatar_url' => $testimonial->avatar_url,
                'name' => $testimonial->client_name,
                'role' => trim(($testimonial->designation ?? '').($testimonial->company_name ? ', '.$testimonial->company_name : '')),
            ])
            ->all();
    }

    /**
     * Map active PricingPlan records into the homepage_pricing section's
     * plan shape, replacing the section's stored placeholder data.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function pricingPlanItems(): array
    {
        return PricingPlan::active()
            ->ordered()
            ->get()
            ->map(fn (PricingPlan $plan) => [
                'name' => $plan->name,
                'price' => '$'.number_format($plan->price_monthly),
                'period' => '/month',
                'credits' => number_format($plan->credits_monthly).' credits a month',
                'featured' => $plan->is_featured,
                'badge' => $plan->is_featured ? __('Most chosen') : '',
                'features' => implode("\n", $plan->features ?? []),
                'button_text' => $plan->cta_label,
                'button_link' => route('register'),
            ])
            ->all();
    }

    /**
     * Map the 3 newest published BlogPost records into the homepage_blog
     * section's post shape, replacing the section's stored placeholder data.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function blogPostItems(): array
    {
        return BlogPost::published()
            ->with(['category', 'author'])
            ->orderByDesc('published_at')
            ->limit(3)
            ->get()
            ->map(fn (BlogPost $post, int $index) => [
                'image_url' => $post->coverImageUrl(),
                'date_day' => $post->published_at?->format('d') ?? '',
                'date_month' => $post->published_at?->format('M') ?? '',
                'topic' => $post->category?->name ?? '',
                'variant' => self::STAGE_VARIANTS[$index % count(self::STAGE_VARIANTS)],
                'read_minutes' => (string) $this->readMinutes($post),
                'title' => $post->title,
                'excerpt' => $post->excerpt ?? '',
                'author_avatar_url' => null,
                'author_name' => $post->author?->name ?? '',
                'link' => route('blog.show', $post->slug),
            ])
            ->all();
    }

    protected function readMinutes(BlogPost $post): int
    {
        $words = str_word_count(strip_tags((string) $post->body));

        return max(1, (int) ceil($words / 200));
    }

    public function show(string $slug)
    {
        try {
            if (! Schema::hasTable('pages')) {
                abort(404);
            }

            $page = $this->pages->findBySlug($slug);
        } catch (Throwable) {
            abort(404);
        }

        abort_if(! $page, 404);

        $payload = $this->renderer->payload($page, $this->activeThemeResolver->resolve());

        return response()->view($payload['layoutView'], $payload);
    }
}
