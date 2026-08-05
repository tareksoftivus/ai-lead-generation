/* Map view — wires the leads filters to the map and the results rail. */
document.addEventListener("DOMContentLoaded", () => {
  'use strict';

  const root = document.querySelector("[data-map-view]");
  if (!root) return;

  const holder = root.querySelector("[data-map]");
  const countEl = root.querySelector("[data-map-count]");
  if (!holder) return;

  function visibleNames() {
    return new Set(
      [...root.querySelectorAll("[data-lead-name]")]
        .filter((row) => !row.classList.contains("is-hidden"))
        .map((row) => row.dataset.leadName),
    );
  }

  function apply() {
    const api = holder._leadMap;
    if (!api) return;

    const names = visibleNames();
    const shown = api.filter((pin) => names.has(pin.name));
    if (countEl) countEl.textContent = String(shown);
  }

  root.addEventListener("list:filtered", apply);

  root.addEventListener("click", (e) => {
    const row = e.target.closest("[data-lead-name]");
    if (!row || e.target.closest("a")) return;

    const api = holder._leadMap;
    if (!api) return;

    api.group.eachLayer((marker) => {
      if (marker._pin?.name !== row.dataset.leadName) return;
      api.map.setView(marker.getLatLng(), 15, { animate: true });
      marker.openPopup();
    });
  });

  apply();
});
