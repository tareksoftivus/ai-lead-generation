document.addEventListener("DOMContentLoaded", () => {
  const groups = document.querySelectorAll("[data-accordion]");
  if (!groups.length) return;

  // Delegated: the backend may render these rows, so nothing is bound per node.
  document.addEventListener("click", (e) => {
    const toggle = e.target.closest("[data-accordion-toggle]");
    if (!toggle) return;

    const item = toggle.closest("[data-accordion]");
    if (!item) return;

    const open = !item.classList.contains("is-open");
    item.classList.toggle("is-open", open);
    toggle.setAttribute("aria-expanded", open ? "true" : "false");
  });

  // How many choices a closed filter is holding, so a set filter is visible
  // without opening it. Chips count themselves; a bucket counts as one only
  // when it is off its "Any" default; a checkbox counts when checked.
  function countOf(item) {
    const chips = item.querySelectorAll(
      '[data-filter-chips] input[type="hidden"]',
    );
    if (chips.length) return chips.length;

    const radio = item.querySelector('input[type="radio"]:checked');
    if (radio) return radio.value === "" ? 0 : 1;

    const box = item.querySelector('input[type="checkbox"]:checked');
    if (box) return 1;

    const range = item.querySelector('input[type="range"]');
    if (range) return range.value === range.defaultValue ? 0 : 1;

    return 0;
  }

  function sync(item) {
    const mark = item.querySelector("[data-accordion-count]");
    if (!mark) return;
    const n = countOf(item);
    mark.textContent = String(n);
    mark.hidden = n === 0;
  }

  function syncAll() {
    groups.forEach(sync);
  }

  // The chip fields and range buckets already fire these as they change.
  document.addEventListener("change", syncAll);
  document.addEventListener("input", syncAll);
  document.addEventListener("filters:change", syncAll);

  syncAll();
});
