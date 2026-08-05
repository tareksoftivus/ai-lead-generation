/* Applied filters — the strip above the results.

   Rendered FROM the form on every change rather than tracked separately, so
   the strip and the rail can never disagree about what is applied. Removing
   a chip unsets the real control and lets the existing change handlers
   recount, re-estimate, and refresh the results. */
document.addEventListener("DOMContentLoaded", () => {
  'use strict';

  const strip = document.querySelector("[data-applied-filters]");
  if (!strip) return;

  const form = strip.closest("form");
  if (!form) return;

  // Reads the label off the filter's own accordion, so a renamed filter
  // renames its chip with no second list to maintain.
  function labelOf(el) {
    const item = el.closest("[data-accordion]");
    return item?.querySelector(".fac__label")?.textContent.trim() || "";
  }

  function collect() {
    const out = [];

    form.querySelectorAll("[data-filter-field]").forEach((field) => {
      field.querySelectorAll(".ffield__chip").forEach((chip) => {
        out.push({
          text: chip.dataset.value || "",
          group: labelOf(field),
          remove: () => chip.remove(),
        });
      });
    });

    form.querySelectorAll("[data-range-group]").forEach((group) => {
      const picked = group.querySelector("input[type='radio']:checked");
      if (!picked || picked.value === "") return;
      const text = picked.closest("label")?.textContent.trim() || picked.value;
      out.push({
        text,
        group: labelOf(group),
        remove: () => {
          const any = group.querySelector("input[type='radio'][value='']");
          if (any) any.checked = true;
        },
      });
    });

    form
      .querySelectorAll("input[type='checkbox'][data-filter-counts]")
      .forEach((box) => {
        if (!box.checked) return;
        // First span only — the label also carries a paragraph of help text.
        // A switch outside any accordion (One lead per business) has no
        // fac__label above it, so fall back to its own <label for=...>.
        const own = box.closest("label")?.querySelector("span span");
        const linked = box.id
          ? form.querySelector(`label[for="${box.id}"]`)
          : null;
        const text =
          own?.textContent.trim() || linked?.textContent.trim() || box.name;
        out.push({
          text,
          group: labelOf(box),
          remove: () => {
            box.checked = false;
          },
        });
      });

    return out;
  }

  function render() {
    const applied = collect();
    strip.textContent = "";
    strip.classList.toggle("is-hidden", applied.length === 0);
    if (!applied.length) return;

    applied.forEach((item) => {
      const chip = document.createElement("button");
      chip.type = "button";
      chip.className = "achip";

      if (item.group) {
        const g = document.createElement("span");
        g.className = "achip__group";
        g.textContent = item.group;
        chip.appendChild(g);
      }

      // textContent throughout: a typed keyword must never become markup.
      const v = document.createElement("span");
      v.textContent = item.text;
      chip.appendChild(v);

      const x = document.createElement("i");
      x.className = "ph ph-x achip__x";
      x.setAttribute("aria-hidden", "true");
      chip.appendChild(x);

      const sr = document.createElement("span");
      sr.className = "sr-only";
      sr.textContent = `Remove ${item.group} ${item.text}`;
      chip.appendChild(sr);

      chip.addEventListener("click", () => {
        item.remove();
        form.dispatchEvent(new Event("change", { bubbles: true }));
        form.dispatchEvent(new Event("input", { bubbles: true }));
      });

      strip.appendChild(chip);
    });

    const clear = document.createElement("button");
    clear.type = "button";
    clear.className = "achip__clear";
    clear.textContent = "Clear all";
    clear.addEventListener("click", () => {
      form.querySelector("[data-filter-clear]")?.click();
    });
    strip.appendChild(clear);
  }

  form.addEventListener("change", render);
  form.addEventListener("input", render);
  render();
});
