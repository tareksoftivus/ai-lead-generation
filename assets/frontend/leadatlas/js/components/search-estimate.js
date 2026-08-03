/* Search estimate — the cost preview on the New search screen. */
document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector("[data-estimate-form]");
  if (!form) return;

  // The count is echoed in more than one place (the action bar and the
  // results header), so update every instance, not just the first.
  const countEls = form.querySelectorAll("[data-estimate-count]");
  const countEl = countEls[0];
  const costEl = form.querySelector("[data-estimate-cost]");
  const leftEl = form.querySelector("[data-estimate-left]");
  const warnEl = form.querySelector("[data-estimate-warning]");
  const submit = form.querySelector("[data-estimate-submit]");
  if (!countEl || !costEl || !submit) return;

  const balance = Number(form.dataset.balance || 0);

  const fmt = (n) => n.toLocaleString("en-US");

  // Chip fields post as name[] arrays, so count the hidden inputs rather
  // than reading a single .value — form.elements[name] is a list once two
  // or more chips exist, and its .value is only ever the first one.
  function chipCount(name) {
    return form.querySelectorAll(`input[type="hidden"][name="${name}"]`).length;
  }

  function pickedValue(name) {
    const el = form.elements[name];
    if (!el) return "";
    // A radio set exposes the checked value directly.
    return el.value || "";
  }

  function estimateSearch() {
    const radius = Number(form.elements.radius?.value || 10);
    const keywords = chipCount("keyword[]");
    const locations = chipCount("location[]");

    if (!keywords || !locations) return 0;

    // Each extra term and each extra place widens the search.
    let n = Math.round(18 * radius * keywords * locations);

    if (pickedValue("min_rating")) n = Math.round(n * 0.7);

    const reviews = pickedValue("min_reviews");
    if (reviews === "custom") {
      const from = Number(form.elements.min_reviews_from?.value || 0);
      const to = Number(form.elements.min_reviews_to?.value || 0);
      if (from || to) n = Math.round(n * 0.6);
    } else if (reviews) {
      n = Math.round(n * 0.6);
    }

    if (form.elements.has_website?.checked) n = Math.round(n * 0.75);

    // Excluding terms drops listings before we spend on them.
    const excluded = chipCount("exclude_keyword[]");
    if (excluded) n = Math.round(n * Math.pow(0.88, excluded));

    return n;
  }

  function estimateSelection() {
    const picker = form.elements.source;
    if (!picker) return 0;

    const opt = picker.selectedOptions
      ? picker.selectedOptions[0]
      : picker.options[picker.selectedIndex];
    if (!opt) return 0;

    const total = Number(opt.dataset.count || 0);
    const done = Number(opt.dataset.analysed || 0);
    const skip = form.elements.skip_analysed?.checked;

    return skip ? Math.max(0, total - done) : total;
  }

  const estimate =
    form.dataset.estimateMode === "selection"
      ? estimateSelection
      : estimateSearch;

  const radiusOut = form.querySelector("[data-radius-out]");

  function render() {
    const n = estimate();

    // Mirror the range value into its label, and feed the track fill.
    // Kept here rather than an inline oninput= so no JS lives in markup.
    const range = form.elements.radius;
    if (range) {
      if (radiusOut) radiusOut.textContent = range.value;

      const min = Number(range.min || 0);
      const max = Number(range.max || 100);
      const pct = ((Number(range.value) - min) / (max - min)) * 100;
      range.style.setProperty("--range-fill", `${pct}%`);
    }
    const cost = n; // one credit per enriched business
    const after = balance - cost;
    const tooExpensive = cost > balance;

    countEls.forEach((el) => {
      el.textContent = fmt(n);
    });
    costEl.textContent = fmt(cost);
    if (leftEl) leftEl.textContent = fmt(Math.max(0, after));

    // Semantic state class only — CSS owns the look (§4).
    if (warnEl) warnEl.classList.toggle("is-shown", tooExpensive);
    form.classList.toggle("is-overbudget", tooExpensive);

    submit.disabled = n === 0 || tooExpensive;
  }

  form.addEventListener("input", render);
  form.addEventListener("change", render);
  render();
});
