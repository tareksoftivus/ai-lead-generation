/* Campaign review gate — screen 29's detail page. */
document.addEventListener("DOMContentLoaded", () => {
  const gate = document.querySelector("[data-review-gate]");
  if (!gate) return;

  const items = [...document.querySelectorAll("[data-review-item]")];
  const send = gate.querySelector("[data-review-send]");
  const doneEl = gate.querySelector("[data-review-done]");
  const totalEl = gate.querySelector("[data-review-total]");
  const fill = gate.querySelector("[data-review-fill]");
  const hint = gate.querySelector("[data-review-hint]");
  if (!items.length || !send) return;

  const total = items.length;
  if (totalEl) totalEl.textContent = total;

  function update() {
    const read = items.filter((item) => {
      const box = item.querySelector("[data-review-check]");
      const on = !!box && box.checked;
      item.classList.toggle("is-read", on);
      return on;
    }).length;

    if (doneEl) doneEl.textContent = read;
    if (fill)
      fill.style.setProperty("--review-pct", `${(read / total) * 100}%`);

    const complete = read === total;
    send.disabled = !complete;
    gate.classList.toggle("is-complete", complete);

    if (hint) {
      hint.textContent = complete
        ? "Every message read. You can schedule this campaign."
        : `${total - read} left to read before this campaign can send.`;
    }
  }

  items.forEach((item) => {
    const box = item.querySelector("[data-review-check]");
    box?.addEventListener("change", update);
  });

  update();
});
