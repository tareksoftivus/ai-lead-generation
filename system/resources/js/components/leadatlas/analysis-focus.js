/* Analysis focus — the "what you want to know" picker steers the results. */
document.addEventListener("DOMContentLoaded", () => {
  'use strict';

  const select = document.querySelector("[data-anz-focus]");
  const results = document.querySelector("[data-anz-results]");
  if (!select || !results) return;

  const MODES = ["gaps", "fit", "summary"];

  function apply() {
    const mode = MODES.includes(select.value) ? select.value : "gaps";
    MODES.forEach((m) => results.classList.toggle(`is-${m}`, m === mode));
  }

  select.addEventListener("change", apply);
  apply();
});
