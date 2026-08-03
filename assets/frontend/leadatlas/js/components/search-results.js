document.addEventListener("DOMContentLoaded", () => {
  const results = document.querySelector("[data-search-results]");
  if (!results) return;

  const form = results.closest("form") || document.querySelector("form");
  const hero = document.querySelector(".srch__hero");

  // Live, like the reference: the moment the query names something to look
  // for, the workspace shows what comes back. There is no "run" step to
  // preview — running is what SPENDS credits, and that stays on the button.
  function hasQuery() {
    if (!form) return false;
    const chips = form.querySelectorAll(
      '[data-filter-field] input[type="hidden"]',
    );
    return chips.length > 0;
  }

  function sync() {
    const on = hasQuery();
    results.classList.toggle("is-hidden", !on);
    hero?.classList.toggle("is-hidden", on);
  }

  form?.addEventListener("change", sync);
  form?.addEventListener("input", sync);

  // Clear drops back to the empty state by emptying the query itself, so the
  // rail and the workspace never disagree about what is being searched.
  document.addEventListener("click", (e) => {
    if (!e.target.closest("[data-results-hide]")) return;
    e.preventDefault();
    form?.querySelectorAll("[data-filter-field]").forEach((field) => {
      field.querySelectorAll(".ffield__chip").forEach((chip) => chip.remove());
      field.classList.remove("is-filled");
    });
    form?.dispatchEvent(new Event("change", { bubbles: true }));
    form?.dispatchEvent(new Event("input", { bubbles: true }));
  });

  sync();
});
