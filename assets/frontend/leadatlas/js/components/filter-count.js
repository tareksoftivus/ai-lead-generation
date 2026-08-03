/* Filter count — the running "N filters" badge and its clear-all.
   Counts what the user actually narrowed by, so the two required fields
   (business type, location) are deliberately not counted. */
document.addEventListener("DOMContentLoaded", () => {
  const bar = document.querySelector("[data-filter-count-bar]");
  if (!bar) return;

  const form = bar.closest("form");
  const out = bar.querySelector("[data-filter-count]");
  // The clear control may BE the bar (the rail badge is both) or sit
  // inside it — check itself first, or clear-all silently does nothing.
  const clear = bar.matches("[data-filter-clear]")
    ? bar
    : bar.querySelector("[data-filter-clear]");
  if (!form || !out) return;

  const scope = form.querySelectorAll("[data-filter-counts]");

  function countOne(el) {
    if (el.matches("[data-filter-field]")) {
      return el.querySelectorAll(".ffield__chip").length;
    }
    if (el.matches("[data-range-group]")) {
      const picked = el.querySelector("input[type='radio']:checked");
      return picked && picked.value !== "" ? 1 : 0;
    }
    if (el.matches("input[type='checkbox']")) return el.checked ? 1 : 0;
    if (el.matches("select")) return el.value ? 1 : 0;
    return 0;
  }

  function render() {
    let n = 0;
    scope.forEach((el) => {
      n += countOne(el);
    });

    out.textContent = String(n);
    bar.classList.toggle("is-active", n > 0);
  }

  clear?.addEventListener("click", () => {
    scope.forEach((el) => {
      if (el.matches("[data-filter-field]")) {
        el.querySelectorAll(".ffield__chip").forEach((chip) => chip.remove());
        el.classList.remove("is-filled");
        return;
      }
      if (el.matches("[data-range-group]")) {
        const any = el.querySelector("input[type='radio'][value='']");
        if (any) any.checked = true;
        return;
      }
      if (el.matches("input[type='checkbox']")) el.checked = false;
      else if (el.matches("select")) el.value = "";
    });

    // One dispatch drives the count, the buckets, and the estimate together.
    form.dispatchEvent(new Event("change", { bubbles: true }));
    form.dispatchEvent(new Event("input", { bubbles: true }));
  });

  form.addEventListener("input", render);
  form.addEventListener("change", render);
  render();
});
