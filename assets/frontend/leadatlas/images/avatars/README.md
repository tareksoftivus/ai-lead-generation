# Hero avatar photos

The hero social-proof stack (`src/index.html`) expects **5 files** here:

    avatar-1.jpg  avatar-2.jpg  avatar-3.jpg  avatar-4.jpg  avatar-5.jpg

## Requirements

- **Square.** They are rendered as circles at 32px (64px on a 2x screen).
- **256x256 recommended** — enough for 2x/3x, still a few KB each.
- Face roughly centred; `object-cover` will crop the rest.
- Keep them small. Five avatars should total well under ~40KB.

## Before you add them

These are **real faces shown as customers**. Only use photos you have the right
to distribute in a product sold on CodeCanyon:

- your own team / real customers who agreed, or
- a stock licence that permits redistribution in a template, or
- an AI-generated / illustrated set with a clear licence.

⚠️ Do **not** copy the avatars from `calendar-html/` — that project is a frozen
style reference and its assets are not ours to ship (CLAUDE.md).

## Once added

Nothing to wire up — `src/index.html` already points at these paths. Rebuild and
they appear. Then update the `alt` text and delete the placeholder TODO in the
hero if the names/figures become real.
