# Leads Module

Path: `app/Modules/Leads`

The Leads module is LeadAtlas's core product: it searches Google Places for businesses matching user-defined filters, charges credits when leads are generated, lets users save selected generated results as owned "leads," enriches each saved lead with a contact email and a lead-quality score, and gives users a working pipeline (status, tags, lists, notes, activity timeline) to manage those leads. It depends on the `Credits` module (lead generation spends credits) and `GoogleMapsSettings` (Places API key/config), declared via `module.json`'s `requires`.

## Domain model

| Model | Table | Scope | Purpose |
|---|---|---|---|
| `Search` | `searches` | per-user | A named, saved filter preset (not itself a run). |
| `SearchRun` | `search_runs` | per-user | One execution of a search — snapshots its own `filters` (independent of the parent `Search`, so later edits to the saved search don't rewrite history), tracks `status` (pending/running/done/failed), `results_count`, `credits_spent`. |
| `Place` | `places` | **global**, not user-scoped | A cached Google Place, keyed by unique `google_place_id`. Shared across all users — enrichment (Place Details, website scan) happens once per Place and benefits everyone who finds that business later. |
| `LeadBank` | `leads_bank` | **global**, not user-scoped | Searchable bank of generated lead candidates. A search checks this first using normalized business type, location, review/rating/contact filters, and requested lead count; Google Places is called only when the bank has no match or a partial match. |
| `Lead` | `leads` (soft-deletes) | per-user | A user's *owned* record of a `Place`. Unique `(user_id, place_id)` at the DB level. Carries `status`, `email`, `enriched_at`, `enrichment_credit_spent`, `score` (0–100), `score_signals` (json). |
| `LeadList` | `lead_lists` | per-user | Manual or search-derived grouping of leads (`belongsToMany` via `lead_list_lead`). |
| `Tag` | `tags` | per-user | Free-form label, unique `(user_id, name)`, max 30 chars. |
| `LeadNote` | `lead_notes` | per-lead | Free-text note, authored by a user. |
| `LeadActivity` | `lead_activities` | per-lead, append-only | Timeline entries (`found_in_search`, `contact_found`, `enrichment_failed`, `status_changed`, `tag_added/removed`, `note_added`, `scored`, `list_added/removed`), created via static `Lead Activity::log*()` helpers. |

Notable cascade behavior: deleting a `SearchRun` **nulls** (doesn't cascade) `lead.search_run_id` and `lead_list.search_run_id` — leads and lists survive their originating run being deleted. Deleting a `Tag` or `LeadList` only detaches the pivot; the `Lead` is untouched.

## Services

- **`SearchEstimateService`** — instant, local, Places-API-free cost projection. A requested lead count is honored up to the configured cap; if no count is requested, the default generation target is 5 leads.
- **`SearchService`** — `run()` checks that the user can afford the requested/default generation count before creating a run or calling Google; runs synchronously if there's a single keyword×location combination, otherwise dispatches it to a queue. `execute()` checks `leads_bank` first, calls Google Places only for missing results, dedupes/filters results, caches them as `Place`/`LeadBank` rows linked to the run, then spends credits for the actual generated result count.
- **`LeadBankService`** — normalizes lead-generation requests and searches `leads_bank` for similar cached results before an API call. New Google results are written back into the bank before they are shown in the search panel.
- **`SearchPromptParser`** — parses lightweight natural-language prompts such as "Find the dentists in Dhaka with 15 reviews atleat 20 leads" into the existing filters (`keyword`, `location`, `min_reviews_from`, `requested_count`).
- **`GooglePlaces\GooglePlacesClient`** — thin wrapper over the Places API (New) `textSearch`/`placeDetails`, with pagination (up to 3 pages), retry-once-on-5xx, and required inter-page delay.
- **`GooglePlaces\PlacesResultMapper`** / **`PlacesSearchResult`** — pure mapping of raw API payloads into `places` table attributes / a DTO.
- **`LeadService`** — `saveFromSearch()` is the generated-results → owned-leads conversion: finds-or-restores a `Lead` per selected place, enriches, scores, and logs activity inside a transaction. It does not spend credits; generation already did that.
- **`LeadEnrichmentService`** — fetches Place Details once per `Place` (cached), then looks up an email via `EmailFromWebsiteLookup`.
- **`EmailFromWebsiteLookup`** — bounded/timeout-guarded scan of a business's homepage (and `/contact`) for a `mailto:`/email pattern, filtering out known junk domains.
- **`HeuristicLeadScorer`** (implements `Contracts\LeadScorer`) — deterministic, non-AI scoring stand-in from review count/rating/website/phone presence.
- **`TemplateOutreachDraftGenerator`** (implements `Contracts\OutreachDraftGenerator`) — canned string-template outreach email, always surfaced for manual edit, never auto-sent.
- **`TagService`** — case-insensitive find-or-create per user (preserves original casing).

`LeadScorer` and `OutreachDraftGenerator` are contracts specifically so a future AI module can rebind real implementations in `LeadsServiceProvider` without touching controllers, views, or the rest of the module.

## Jobs

- **`RunPlacesSearchJob`** (queued, `tries=3`) — runs `SearchService::execute()` for multi-combination searches off the request cycle.

## Controllers & routes (`Routes/user.php`)

| Controller | Gate | Key actions |
|---|---|---|
| `SearchController` | `leads.search` | new/run/estimate/save search, history list, view/rerun/delete a run |
| `LeadsController` | `leads.view` (read) / `leads.manage` (write) | index, map, show, save-from-search, status/tags/notes, bulk tag/status/delete, destroy |
| `LeadListsController` | `leads.manage` | list index (lists+tags), create/rename/delete list |
| `TagsController` | `leads.manage` | create/rename/delete tag |

Single-lead actions do their own inline ownership check (404 on mismatch) rather than routing through `LeadPolicy`, though the policy is still registered in `Module.php` for `Lead::class`.

## Workflow: search → saved, enriched, scored lead

1. **Estimate** — user builds filters on "New search"; each change can hit the `estimate` endpoint (`SearchEstimateService`, no API calls, no spend). The estimate shows the generation target and required credits.
2. **Run** — prompt-only requests are parsed into filters first. `SearchService::run()` requires enough credits for the requested/default generation count before a `SearchRun` is created or Google is called. Single-combination searches execute synchronously, multi-combination ones are queued via `RunPlacesSearchJob`.
3. **Execute** — checks `leads_bank` for similar results first. If the bank satisfies the requested count, those cached candidates are returned immediately. If the bank has no match or a partial match, Google Places is called only for the missing results; new candidates are saved to `places` and `leads_bank` before being linked to the run. Credits are spent for the actual generated result count, and `search_runs.credits_spent` records that amount.
4. **Save** — user selects generated results and posts to `leads/save-from-search`. Saving to owned leads/lists is free because the generation was already charged. Enrichment fetches an email, `HeuristicLeadScorer` computes and persists the score, and `LeadActivity` rows log each step.
5. **Manage** — the lead now lives in "All leads"/"Map view"; status/tags/notes/lists don't touch credits or re-score. The detail page recomputes the scorer's headline/explanation and outreach draft live on every view; the persisted `score`/`score_signals` are fixed at enrichment time.

## Tests

Feature tests cover the full funnel end-to-end (`EndToEndFunnelTest`), credit-spend correctness (`EnrichmentTest`), CRUD/pipeline actions (`LeadCrudTest`), lists/tags (`ListsAndTagsTest`), and search execution/queuing (`SearchTest`). Unit tests cover the Places client (pagination, retry, mapping), the scorer/draft-generator stub bindings and bounds, and data-model invariants (dedupe, unique constraints, scopes, cascade-null behavior).
