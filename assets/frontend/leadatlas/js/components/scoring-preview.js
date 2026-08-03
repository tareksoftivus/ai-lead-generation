/* Scoring preview — screen 23. */
document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector("[data-scoring]");
  if (!form) return;

  const weights = [...form.querySelectorAll("[data-weight]")];
  const samples = [...form.querySelectorAll("[data-sample]")];
  if (!weights.length) return;

  const totalOut = form.querySelector("[data-weight-total]");
  const movedOut = form.querySelector("[data-preview-moved]");

  function band(score) {
    if (score >= 80) return "hi";
    if (score >= 60) return "mid";
    return "lo";
  }

  // Reads the sliders into { reviews: 30, booking: 25, ... }.
  function readWeights() {
    const out = {};
    weights.forEach((el) => {
      out[el.dataset.weight] = Number(el.value) || 0;
    });
    return out;
  }

  function parseSignals(row) {
    const out = {};
    (row.dataset.signals || "")
      .split(",")
      .map((pair) => pair.split(":"))
      .forEach(([key, val]) => {
        if (key) out[key.trim()] = Number(val) || 0;
      });
    return out;
  }

  function render() {
    const w = readWeights();
    const total = Object.values(w).reduce((a, b) => a + b, 0);

    // Mirror each slider's value into its label and feed the track fill.
    weights.forEach((el) => {
      const out = form.querySelector(
        `[data-weight-out="${el.dataset.weight}"]`,
      );
      if (out) out.textContent = el.value;

      const min = Number(el.min || 0);
      const max = Number(el.max || 100);
      const pct = ((Number(el.value) - min) / (max - min)) * 100;
      el.style.setProperty("--range-fill", `${pct}%`);
    });

    if (totalOut) totalOut.textContent = String(total);

    let moved = 0;

    samples.forEach((row) => {
      const signals = parseSignals(row);
      const now = Number(row.dataset.now) || 0;

      let next = now;
      if (total > 0) {
        const sum = Object.keys(w).reduce(
          (acc, key) => acc + (signals[key] || 0) * w[key],
          0,
        );
        next = Math.round(sum / total);
      }

      const newEl = row.querySelector("[data-sample-new]");
      const deltaEl = row.querySelector("[data-sample-delta]");

      if (newEl) {
        newEl.textContent = String(next);
        newEl.className = `score score--${band(next)} numeric`;
      }

      const diff = next - now;
      if (diff !== 0) moved += 1;

      if (deltaEl) {
        // One element, three states — CSS owns the look (§4).
        deltaEl.classList.toggle("is-up", diff > 0);
        deltaEl.classList.toggle("is-down", diff < 0);
        deltaEl.classList.toggle("is-same", diff === 0);
        deltaEl.textContent =
          diff === 0 ? "no change" : `${diff > 0 ? "+" : ""}${diff}`;
      }
    });

    if (movedOut) movedOut.textContent = String(moved);

    // Nothing to commit when the weighting produces no movement.
    const apply = form.querySelector("[data-scoring-apply]");
    if (apply) apply.disabled = total === 0;
    form.classList.toggle("is-zeroed", total === 0);
  }

  form.addEventListener("input", render);
  form.addEventListener("change", render);
  render();
});
