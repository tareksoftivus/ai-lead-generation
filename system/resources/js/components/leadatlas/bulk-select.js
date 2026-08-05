/* Bulk select — the checkbox column and the "N selected" action strip. */
document.addEventListener("DOMContentLoaded", () => {
  'use strict';

  const roots = document.querySelectorAll("[data-bulk]");
  if (!roots.length) return;

  roots.forEach((root) => {
    const bar = root.querySelector("[data-bulk-bar]");
    const countEl = root.querySelector("[data-bulk-count]");
    const selectAll = root.querySelector("[data-bulk-all]");

    const items = () => [...root.querySelectorAll("[data-bulk-item]")];

    // A row is selectable only while it is on screen.
    const visible = () =>
      items().filter((cb) => {
        const row = cb.closest("tr, [data-list-key]");
        return !row || !row.classList.contains("is-hidden");
      });

    function sync() {
      const checked = items().filter((cb) => cb.checked);
      if (countEl) countEl.textContent = String(checked.length);
      bar?.classList.toggle("is-hidden", checked.length === 0);

      if (selectAll) {
        const vis = visible();
        const all = vis.length > 0 && vis.every((cb) => cb.checked);
        selectAll.checked = all;
        selectAll.indeterminate = !all && vis.some((cb) => cb.checked);
      }
    }

    root.addEventListener("change", (e) => {
      if (e.target.closest("[data-bulk-all]")) {
        const on = e.target.checked;
        visible().forEach((cb) => (cb.checked = on));
        sync();
        return;
      }
      if (e.target.closest("[data-bulk-item]")) sync();
    });

    root.addEventListener("list:filtered", sync);

    sync();
  });
});
