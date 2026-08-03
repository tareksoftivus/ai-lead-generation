/* Filter range — preset buckets with a Custom escape hatch.
   The buckets are radios, so the backend reads one value; Custom swaps in
   a min/max pair instead. */
document.addEventListener("DOMContentLoaded", () => {
  const groups = document.querySelectorAll("[data-range-group]");
  if (!groups.length) return;

  groups.forEach((group) => {
    const custom = group.querySelector("[data-range-custom]");
    if (!custom) return;

    function sync() {
      const picked = group.querySelector("input[type='radio']:checked");
      const on = picked?.value === "custom";

      custom.classList.toggle("is-shown", on);
      // Disabled inputs are not submitted, so the backend never receives
      // both a bucket and a stale custom pair.
      custom.querySelectorAll("input").forEach((el) => {
        el.disabled = !on;
      });
    }

    group.addEventListener("change", sync);
    sync();
  });
});
