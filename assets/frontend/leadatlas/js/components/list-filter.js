/* List filter — tabs + search + dropdown filters over a set of rows. */
document.addEventListener("DOMContentLoaded", () => {
  const lists = document.querySelectorAll("[data-list]");
  if (!lists.length) return;

  lists.forEach((list) => {
    const rows = list.querySelectorAll("[data-list-key]");
    if (!rows.length) return;

    const tabs = list.querySelectorAll("[data-list-tab]");
    const search = list.querySelector("[data-list-search]");
    const empty = list.querySelector("[data-list-empty]");
    const table = list.querySelector("[data-list-table]");
    const count = list.querySelector("[data-list-count]");
    const filters = list.querySelectorAll("[data-list-filter]");

    let key = "all";
    let query = "";

    function apply() {
      let shown = 0;

      rows.forEach((row) => {
        const matchKey = key === "all" || row.dataset.listKey === key;
        const matchQuery =
          !query || row.textContent.toLowerCase().includes(query);

        let matchFilters = true;
        filters.forEach((filter) => {
          const value = filter.dataset.value;
          if (!value || value === "all") return;
          if (row.dataset[filter.dataset.listFilter] !== value) {
            matchFilters = false;
          }
        });

        const on = matchKey && matchQuery && matchFilters;
        row.classList.toggle("is-hidden", !on);
        if (on) shown += 1;
      });

      empty?.classList.toggle("is-hidden", shown > 0);
      table?.classList.toggle("is-hidden", shown === 0);
      if (count) count.textContent = String(shown);

      list.dispatchEvent(
        new CustomEvent("list:filtered", { bubbles: true, detail: { shown } }),
      );
    }

    if (tabs.length) {
      // Delegated, so server-rendered tabs work without rebinding.
      list.addEventListener("click", (e) => {
        const tab = e.target.closest("[data-list-tab]");
        if (!tab || !list.contains(tab)) return;

        e.preventDefault();
        key = tab.dataset.listTab;
        tabs.forEach((t) => {
          const on = t === tab;
          t.classList.toggle("is-active", on);
          t.setAttribute("aria-current", on ? "page" : "false");
        });
        apply();
      });
    }

    search?.addEventListener("input", () => {
      query = search.value.trim().toLowerCase();
      apply();
    });

    // Dropdown filters announce their choice; dropdown.js dispatches it.
    filters.forEach((filter) => {
      filter.addEventListener("dropdown:select", apply);
    });

    apply();
  });
});
