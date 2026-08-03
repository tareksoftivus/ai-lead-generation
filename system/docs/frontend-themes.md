# Frontend Themes

Themes are code-defined contracts stored in `config/frontend-themes.php`. This system is owned by the `Frontend` module (`app/Modules/Frontend`) and renders public pages — it is unrelated to admin/user **panel** theming (`config/panels.php`'s `theme: dark|light` shell setting).

## Core concepts

- **Theme**: a named visual skin (`leadatlas`) declared in `config/frontend-themes.php`. Each owns its own layout views, its own CSS/JS bundle, its own settings schema, and a list of section types it supports. The `leadatlas` theme owns **all** of its rendering — layouts, nav partials, and section views — and never falls through to `frontend.shared.*`.
- **Page**: a row in `pages` (model `Page`) with title/slug/meta and an ordered set of `PageSection` pivots pointing at reusable `FrontendSection` records. Both the homepage and ordinary CMS pages (About, etc.) are composed this way — the homepage is not a hardcoded template.
- **Section**: a reusable content block declared in `config/frontend-sections.php`, with typed `fields` (text/textarea/number/boolean/feature/checkbox/tags/repeater) and its own `data` payload stored on the `FrontendSection` row. Sections are theme-agnostic content; only their *rendering* is theme-specific. Two families exist: the original generic types (`hero`, `feature_grid`, `cta`, `faq`, `testimonial_grid`, `rich_content`, `footer`) usable on any CMS page, and `homepage_*` types (`homepage_hero`, `homepage_stages`, `homepage_features`, `homepage_voices`, `homepage_pricing`, `homepage_faq`, `homepage_cta`) that model the specific bespoke sections of the LeadAtlas marketing homepage (funnel/stages, testimonial carousel, pricing plans, search CTA) — these don't generalize to other themes so they're named/scoped for `leadatlas` specifically.
- **Menu / menu slot**: `FrontendMenu` + `FrontendMenuItem` are admin-managed nav trees; `config/frontend-menus.php` declares named slots (header/footer) that a theme's settings bind a published menu to. Note: the `leadatlas` theme's own `navigation/{header,footer}.blade.php` partials are static (hardcoded nav links matching the product's real nav), not menu-slot-driven — they don't consume `$resolvedMenus`.

## Where things live

```
config/frontend-themes.php        theme registry (view_namespace, page_layouts, supported_section_types, theme_settings_schema)
config/frontend-sections.php      section type registry (fields, defaults, validation rules, supported_themes)
config/frontend-menus.php         menu slot registry

app/Modules/Frontend/
  Models/            Page, PageSection, FrontendSection, FrontendMenu, FrontendMenuItem, FrontendThemeSetting
  Services/
    ThemeRegistry            reads config/frontend-themes.php — options, default theme/layout, supportsSection()
    SectionRegistry          reads config/frontend-sections.php — options, fields, defaults, supportsTheme()
    ThemeSettingsService     persists per-theme settings (cached) — enabled flag, active_theme, arbitrary theme.{key}.{setting} values, type-cast by schema
    ActiveThemeResolver      resolves which theme actually renders (falls back if the requested theme is disabled)
    ThemeRenderService       resolves layout view + section view per theme (with fallback chain) + assembles themeVariables()
    PageComposerService      syncs a page's ordered sections; computes which themes are fully compatible with a page's section types
    FrontendSectionService   CRUD + field normalization for FrontendSection records
    MenuSlotRegistry / MenuService / MenuAssignmentService / MenuRenderService / MenuTreeService   menu slot config, menu CRUD, slot↔menu assignment validation, cached render tree
    PageRenderService        top-level: builds the full render payload for a Page (theme, themeVars, layoutView, resolvedMenus, resolvedSections)
  Http/Controllers/Admin/    FrontendThemesController, FrontendPagesController, FrontendSectionsController, FrontendMenusController

resources/views/frontend/
  themes/leadatlas/layouts/page.blade.php                    generic section-driven layout (inline <style>, own nav includes) — used for ordinary CMS pages (About, etc.)
  themes/leadatlas/layouts/landing.blade.php                 homepage layout — same section-rendering loop as page.blade.php, plus @vite(leadatlas.js) and the favicon/meta block; the homepage is a real composed Page, not hardcoded markup
  themes/leadatlas/navigation/{header,footer}.blade.php      theme-owned, static nav partials (hardcoded links, not menu-slot-driven)
  themes/leadatlas/sections/homepage_hero.blade.php          hero section renderer (proof stat, headline, dual CTA, dashboard screenshot card)
  themes/leadatlas/sections/homepage_stages.blade.php        "the run" funnel/stages section renderer
  themes/leadatlas/sections/homepage_features.blade.php      feature card grid renderer
  themes/leadatlas/sections/homepage_voices.blade.php         Swiper testimonial carousel renderer
  themes/leadatlas/sections/homepage_pricing.blade.php        pricing plan cards renderer
  themes/leadatlas/sections/homepage_faq.blade.php            FAQ accordion renderer
  themes/leadatlas/sections/homepage_cta.blade.php             closing CTA with inline search form renderer
  themes/leadatlas/sections/unsupported.blade.php             theme's own fallback renderer (never uses frontend.shared.*)
  (generic hero/feature_grid/cta/faq/testimonial_grid/rich_content/footer sections render straight from config/frontend-sections.php's field data with no theme-owned view yet — see note below)

resources/css/leadatlas.css          theme's Tailwind v4 entry (design tokens + component classes, ported from the demo) — @source-scans themes/leadatlas/**/*.blade.php only, not shared/
resources/js/leadatlas.js            theme's JS entry — mobile nav, sticky header, testimonial slider (Swiper), scroll animations (GSAP)
resources/js/components/leadatlas/   theme-scoped JS components (mobile-nav, header-scroll, voices-slider, scroll-animations)
assets/frontend/leadatlas/images/    static images (logo, hero screenshot, avatars, blog thumbnails) referenced via asset()
assets/frontend/leadatlas/css/       raw, unprocessed mirror of the original demo's app.css (reference only — not served or built; the real source is resources/css/leadatlas.css)
assets/frontend/leadatlas/js/        raw, unprocessed mirror of the original demo's full JS bundle, all components (reference only — the real source is resources/js/leadatlas.js + resources/js/components/leadatlas/, which only import the 4 components the homepage actually uses)
```

Note: the plain `hero`/`feature_grid`/`cta`/`faq`/`testimonial_grid`/`rich_content`/`footer` section types are declared in `config/frontend-sections.php` with `supported_themes: ['leadatlas']`, but `leadatlas` currently has no `sections/{type}.blade.php` view for them — if a page ever uses one of these generic types, it will render via the theme's `unsupported.blade.php` fallback rather than `frontend.shared.sections.*`. Add a `themes/leadatlas/sections/{type}.blade.php` view if you need one of these generic types to actually render.

## Render flow

`PageRenderService::payload(Page $page, string $themeKey)` is the entry point and returns everything a controller needs to render a page:

1. `ActiveThemeResolver` decides the effective `themeKey` (a requested theme is only honored if `ThemeSettingsService::isEnabled()` says so; otherwise falls back to the persisted `active_theme` setting).
2. `ThemeRenderService::layoutView()` picks the layout Blade view: tries `{theme.view_namespace}.layouts.{page.default_layout}`, falls back to the theme's default layout, then to `frontend.shared.layouts.page` (this last fallback is a framework-level safety net for themes that don't define layouts at all — `leadatlas` always resolves its own).
3. For each of the page's ordered sections, `ThemeRenderService::sectionView()` resolves a view with this precedence: `{theme.view_namespace}.sections.{section.type}` (theme override, if the theme supports that type) → `frontend.shared.sections.{section.type}` (shared default) → `theme.fallback_renderer`. Because `leadatlas`'s `fallback_renderer` points at its own `themes.leadatlas.sections.unsupported` (not `frontend.shared.sections.unsupported`), and because it has a `sections/homepage_*.blade.php` view for every section type actually used on its homepage, `leadatlas` never touches `frontend.shared.*` at render time.
4. `ThemeRenderService::themeVariables()` pulls the theme's editable settings (`logo_text`, `primary_color`, `accent_color`, `show_hero_kicker`) via `ThemeSettingsService`, with schema defaults as fallback.
5. `MenuRenderService::resolveForTheme()` resolves the theme's assigned menus per slot into a render-ready tree (cached) — passed to `leadatlas`'s layouts as `$resolvedMenus` but currently unused by its static nav partials.

Both `layouts/page.blade.php` and `layouts/landing.blade.php` are full HTML documents: they define the theme's CSS as an inline `<style>` block keyed off `--primary`/`--accent` CSS vars from `$themeVars` (`page.blade.php`) or load the compiled `leadatlas.css`/`leadatlas.js` bundle via `@vite` (`landing.blade.php`), `@include` the theme's own header nav, loop `$resolvedSections` rendering each `$resolved['view']`, then `@include` the theme's own footer nav.

## Why themes are code-defined

Themes are version-controlled and predictable. Editors manage content and theme settings, but they do not define rendering contracts in the database.

This keeps:

- rendering rules explicit
- future theme additions safer
- theme compatibility easier to validate

## Theme contract

Each theme entry should define:

- `key`
- `label`
- `description`
- `preview_image`
- `default_enabled`
- `view_namespace`
- `supported_section_types`
- `page_layouts`
- `fallback_renderer`
- `theme_settings_schema`

## Current theme

- `leadatlas` — the product's own theme, and the only theme in this app. Its design (hero, funnel/stages, feature grid, testimonial carousel, pricing, FAQ, CTA) is ported from `template-integration/demo-html/src/index.html`. Both `layouts/page.blade.php` (generic CMS pages like About) and `layouts/landing.blade.php` (homepage) render a `Page`'s ordered `FrontendSection` records — the homepage's demo-specific sections were modeled as their own `homepage_*` section types (see Core concepts) rather than forced into the generic `hero`/`feature_grid`/etc. types, since the demo's stages/carousel/pricing/CTA-with-search-form don't fit those generic field schemas.

All of its views live in `resources/views/frontend/themes/leadatlas/`, and it deliberately owns every view it needs (layouts, nav, sections, fallback) rather than falling back to `frontend.shared.*`.

## Active theme resolution

The active public theme is resolved by:

1. stored `active_theme`
2. first enabled theme
3. registry default fallback

`ActiveThemeResolver` owns this logic.

## Theme settings

Theme settings are stored in `frontend_theme_settings` and keyed by prefix:

- `active_theme`
- `theme.leadatlas.enabled`
- `theme.leadatlas.primary_color`
- `theme.leadatlas.logo_text`

This allows theme-scoped settings without giving themes their own database-defined schema.

## Adding a new theme

1. Add a new entry to `config/frontend-themes.php`
2. Create a view namespace under `resources/views/frontend/themes/{theme-key}/`
3. Add layouts under `layouts/`
4. Add section views under `sections/{type}.blade.php` for every section type the theme supports — reusing `frontend.shared.sections.*` is fine for a theme willing to depend on the shared defaults, but is not required (`leadatlas` owns all of its own section views instead)
5. Define theme settings schema if the theme needs configurable settings
6. Enable the theme from `Frontend Themes`

## Compatibility rules

- a theme can be enabled without becoming active
- only enabled themes can be activated
- page content remains shared across themes in v1
- unsupported sections use the configured fallback renderer
