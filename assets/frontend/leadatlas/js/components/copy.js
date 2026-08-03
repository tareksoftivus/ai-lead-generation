/* Copy to clipboard. */
document.addEventListener("DOMContentLoaded", () => {
  const RESET_MS = 1600;

  function textFor(trigger) {
    const literal = trigger.getAttribute("data-copy");
    if (literal) return literal;

    const sel = trigger.getAttribute("data-copy-target");
    if (!sel) return "";

    const el = document.querySelector(sel);
    if (!el) return "";

    return "value" in el && el.value !== undefined
      ? el.value
      : el.textContent.trim();
  }

  function legacyCopy(text) {
    const holder = document.createElement("textarea");
    holder.value = text;
    holder.setAttribute("readonly", "");
    holder.className = "sr-only";
    document.body.appendChild(holder);
    holder.select();
    try {
      document.execCommand("copy");
    } catch (err) {
      // Nothing useful to do — the button simply will not flash.
    }
    holder.remove();
  }

  function flash(trigger) {
    trigger.classList.add("is-copied");
    clearTimeout(trigger._copyTimer);
    trigger._copyTimer = setTimeout(
      () => trigger.classList.remove("is-copied"),
      RESET_MS,
    );
  }

  document.addEventListener("click", (e) => {
    const trigger = e.target.closest("[data-copy], [data-copy-target]");
    if (!trigger) return;

    if (trigger.tagName === "A") e.preventDefault();

    const text = textFor(trigger);
    if (!text) return;

    if (navigator.clipboard?.writeText) {
      navigator.clipboard
        .writeText(text)
        .then(() => flash(trigger))
        .catch(() => {
          legacyCopy(text);
          flash(trigger);
        });
    } else {
      legacyCopy(text);
      flash(trigger);
    }
  });
});
