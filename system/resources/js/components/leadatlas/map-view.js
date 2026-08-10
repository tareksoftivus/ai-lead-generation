/* Map view — wires the leads filters to the map and the results rail. */
document.addEventListener("DOMContentLoaded", () => {
  'use strict';

  const root = document.querySelector("[data-map-view]");
  if (!root) return;

  const holder = root.querySelector("[data-map]");
  const countEl = root.querySelector("[data-map-count]");
  if (!holder) return;

  function visibleLeadIds() {
    return new Set(
      [...root.querySelectorAll("[data-lead-id]")]
        .filter((row) => !row.classList.contains("is-hidden"))
        .map((row) => row.dataset.leadId),
    );
  }

  function apply() {
    const api = holder._leadMap;
    if (!api) return;

    const ids = visibleLeadIds();
    const shown = api.filter((pin) => ids.has(String(pin.id)));
    if (countEl) countEl.textContent = String(shown);
  }

  root.addEventListener("list:filtered", apply);

  root.addEventListener("click", (e) => {
    const row = e.target.closest("[data-lead-id]");
    if (!row || e.target.closest("a")) return;

    const api = holder._leadMap;
    if (!api) return;

    if (api.focus(row.dataset.leadId)) {
      root
        .querySelectorAll("[data-lead-id].is-active")
        .forEach((activeRow) => activeRow.classList.remove("is-active"));
      row.classList.add("is-active");
    }
  });

  apply();
});
