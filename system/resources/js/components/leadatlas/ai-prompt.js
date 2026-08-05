/* AI search — reads a sentence and fills the filter rail from it.

   It sets CRITERIA only. It never runs the search: running is what spends
   credits, and that stays an explicit act behind the Run button (rule 1).

   The parsing here is a front-end stand-in so the control is demonstrable.
   The backend replaces it by posting the prompt to the model and returning
   the same shape: {keyword[], location[], min_rating, min_reviews}. */
document.addEventListener("DOMContentLoaded", () => {
  'use strict';

  const btn = document.querySelector("[data-ai-fill]");
  const input = document.querySelector("[data-ai-prompt]");
  if (!btn || !input) return;

  const form = btn.closest("form");
  if (!form) return;

  function fieldFor(name) {
    return form.querySelector(`[data-filter-name="${name}"]`);
  }

  // Chips are the shape filter-field.js already renders, so the two agree
  // about what a set value looks like.
  function addChip(field, value) {
    if (!field) return;
    const holder = field.querySelector("[data-filter-chips]");
    if (!holder) return;

    const name = field.dataset.filterName;
    const exists = [...holder.querySelectorAll(".ffield__chip")].some(
      (c) => c.dataset.value?.toLowerCase() === value.toLowerCase(),
    );
    if (exists) return;

    const chip = document.createElement("span");
    chip.className = "ffield__chip";
    chip.dataset.value = value;
    chip.innerHTML =
      `<span></span>` +
      `<input type="hidden" name="${name}" />` +
      `<button type="button" class="ffield__x" aria-label="Remove">` +
      `<i class="ph ph-x" aria-hidden="true"></i></button>`;
    // textContent, not innerHTML, so a typed value can never inject markup.
    chip.querySelector("span").textContent = value;
    chip.querySelector("input").value = value;
    holder.appendChild(chip);
    field.classList.add("is-filled");
  }

  function pickRadio(name, value) {
    const el = form.querySelector(`input[name="${name}"][value="${value}"]`);
    if (el) el.checked = true;
  }

  // Open a filter whose value we just set, so the change is visible rather
  // than hidden behind a collapsed row.
  function reveal(el) {
    const item = el?.closest("[data-accordion]");
    if (!item || item.classList.contains("is-open")) return;
    item.classList.add("is-open");
    item
      .querySelector("[data-accordion-toggle]")
      ?.setAttribute("aria-expanded", "true");
  }

  function parse(text) {
    const out = { keywords: [], locations: [], rating: null, reviews: null };

    // "<type> in <place>" — the shape every Maps search takes.
    const inMatch = text.match(/^\s*(.+?)\s+in\s+(.+?)\s*$/i);
    let head = text;
    if (inMatch) {
      head = inMatch[1];
      out.locations.push(
        inMatch[2]
          .replace(/\s+with\s+.*$/i, "")
          .replace(/\s+that\s+.*$/i, "")
          .replace(/[.,]\s*$/, "")
          .trim(),
      );
    }
    const kw = head
      .replace(/\s+with\s+.*$/i, "")
      .replace(/[.,]\s*$/, "")
      .trim();
    if (kw) out.keywords.push(kw);

    const rating = text.match(/([0-9](?:\.[0-9])?)\s*\+?\s*stars?/i);
    if (rating) out.rating = String(Math.floor(Number(rating[1])));

    const reviews = text.match(/([0-9]{1,5})\s*\+?\s*reviews?/i);
    if (reviews) out.reviews = reviews[1];

    return out;
  }

  // Snap a free number to the bucket the form actually offers.
  function nearestBucket(name, n) {
    const values = [...form.querySelectorAll(`input[name="${name}"]`)]
      .map((el) => el.value)
      .filter(Boolean)
      .map(Number)
      .sort((a, b) => a - b);
    if (!values.length) return null;
    const under = values.filter((v) => v <= n);
    return String(under.length ? under[under.length - 1] : values[0]);
  }

  btn.addEventListener("click", () => {
    const text = input.value.trim();
    if (!text) {
      input.focus();
      return;
    }

    const got = parse(text);

    got.keywords.forEach((k) => addChip(fieldFor("keyword[]"), k));
    got.locations.forEach((l) => addChip(fieldFor("location[]"), l));
    if (got.keywords.length) reveal(fieldFor("keyword[]"));
    if (got.locations.length) reveal(fieldFor("location[]"));

    if (got.rating) {
      const v = nearestBucket("min_rating", Number(got.rating));
      if (v) {
        pickRadio("min_rating", v);
        reveal(form.querySelector("input[name='min_rating']"));
      }
    }
    if (got.reviews) {
      const v = nearestBucket("min_reviews", Number(got.reviews));
      if (v) {
        pickRadio("min_reviews", v);
        reveal(form.querySelector("input[name='min_reviews']"));
      }
    }

    input.value = "";

    // One dispatch drives the count, the estimate, and the results together.
    form.dispatchEvent(new Event("change", { bubbles: true }));
    form.dispatchEvent(new Event("input", { bubbles: true }));
  });

  input.addEventListener("keydown", (e) => {
    if (e.key !== "Enter") return;
    e.preventDefault(); // never submit the form from here
    btn.click();
  });
});
