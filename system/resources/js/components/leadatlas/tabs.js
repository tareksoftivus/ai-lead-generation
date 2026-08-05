/* Tabs — swaps between sibling panels. */
document.addEventListener("DOMContentLoaded", () => {
  'use strict';

  const roots = document.querySelectorAll("[data-tabs]");
  if (!roots.length) return;

  roots.forEach((root) => {
    const tabs = root.querySelectorAll("[data-tab]");
    const panels = root.querySelectorAll("[data-tab-panel]");
    if (!tabs.length || !panels.length) return;

    function show(key) {
      tabs.forEach((tab) => {
        const on = tab.dataset.tab === key;
        tab.classList.toggle("is-active", on);
        tab.setAttribute("aria-selected", on ? "true" : "false");
      });

      panels.forEach((panel) => {
        panel.classList.toggle("is-hidden", panel.dataset.tabPanel !== key);
      });
    }

    root.addEventListener("click", (e) => {
      const tab = e.target.closest("[data-tab]");
      if (!tab || !root.contains(tab)) return;
      e.preventDefault();
      show(tab.dataset.tab);
    });
  });
});
