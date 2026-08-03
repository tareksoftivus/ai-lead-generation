/* Filter field — typeahead multi-select with removable chips.
   Each chosen value becomes a real named input so the backend reads it
   straight off the POST, and every change re-fires the estimate. */
document.addEventListener("DOMContentLoaded", () => {
  const fields = document.querySelectorAll("[data-filter-field]");
  if (!fields.length) return;

  function notify(field) {
    field.dispatchEvent(new Event("input", { bubbles: true }));
    field.dispatchEvent(new Event("change", { bubbles: true }));
  }

  function values(field) {
    return [...field.querySelectorAll(".ffield__chip")].map(
      (chip) => chip.dataset.value,
    );
  }

  function buildChip(field, value) {
    const chip = document.createElement("span");
    chip.className = "ffield__chip";
    chip.dataset.value = value;

    const label = document.createElement("span");
    label.textContent = value;
    chip.appendChild(label);

    const hidden = document.createElement("input");
    hidden.type = "hidden";
    hidden.name = field.dataset.filterName || "";
    hidden.value = value;
    chip.appendChild(hidden);

    const x = document.createElement("button");
    x.type = "button";
    x.className = "ffield__x";
    x.setAttribute("aria-label", `Remove ${value}`);
    x.innerHTML = '<i class="ph ph-x" aria-hidden="true"></i>';
    chip.appendChild(x);

    return chip;
  }

  function add(field, value) {
    const clean = value.trim();
    if (!clean) return;
    // Case-insensitive dedupe: "Dentist" and "dentist" are one filter.
    const taken = values(field).some(
      (v) => v.toLowerCase() === clean.toLowerCase(),
    );
    const list = field.querySelector("[data-filter-chips]");
    if (taken || !list) return;

    list.appendChild(buildChip(field, clean));
    field.classList.add("is-filled");
    notify(field);
  }

  function remove(chip) {
    const field = chip.closest("[data-filter-field]");
    chip.remove();
    if (!field) return;
    field.classList.toggle("is-filled", values(field).length > 0);
    notify(field);
  }

  function openPanel(field) {
    field.classList.add("is-open");
    field
      .querySelector("[data-filter-input]")
      ?.setAttribute("aria-expanded", "true");
  }

  function closePanel(field) {
    field.classList.remove("is-open");
    field
      .querySelector("[data-filter-input]")
      ?.setAttribute("aria-expanded", "false");
    const panel = field.querySelector("[data-filter-panel]");
    panel?.querySelectorAll("[data-filter-option]").forEach((opt) => {
      opt.classList.remove("is-hidden");
    });
    const custom = panel?.querySelector("[data-filter-custom]");
    if (custom) custom.textContent = "";
  }

  // Panels are position:fixed so a container query can't clip them (§6.1).
  function place(field) {
    const panel = field.querySelector("[data-filter-panel]");
    const input = field.querySelector("[data-filter-input]");
    if (!panel || !input) return;

    // Align to the FIELD, not the input — the input is only the leftover
    // space beside the chips, so on a narrow screen it shrinks to a stub
    // and a panel matching it would be unreadably narrow.
    const box = field.getBoundingClientRect();
    panel.style.setProperty("--panel-x", `${box.left}px`);
    panel.style.setProperty("--panel-y", `${box.bottom + 6}px`);
    panel.style.setProperty("--panel-w", `${box.width}px`);
  }

  function filterOptions(field, term) {
    const panel = field.querySelector("[data-filter-panel]");
    if (!panel) return;

    const q = term.trim().toLowerCase();
    let shown = 0;

    panel.querySelectorAll("[data-filter-option]").forEach((opt) => {
      const hit = !q || opt.dataset.filterOption.toLowerCase().includes(q);
      opt.classList.toggle("is-hidden", !hit);
      if (hit) shown++;
    });

    // Instantly offers the raw term as the first option — that matters here
    // because Maps categories are open-ended, not a fixed vocabulary.
    const custom = panel.querySelector("[data-filter-custom]");
    if (custom) {
      custom.textContent = q ? term.trim() : "";
      custom.classList.toggle("is-hidden", !q);
    }
    const empty = panel.querySelector("[data-filter-empty]");
    if (empty) empty.classList.toggle("is-hidden", shown > 0 || !!q);
  }

  fields.forEach((field) => {
    const input = field.querySelector("[data-filter-input]");
    if (!input) return;

    input.addEventListener("focus", () => {
      place(field);
      openPanel(field);
      filterOptions(field, input.value);
    });

    input.addEventListener("input", () => {
      place(field);
      openPanel(field);
      filterOptions(field, input.value);
    });

    input.addEventListener("keydown", (e) => {
      if (e.key === "Enter") {
        e.preventDefault();
        add(field, input.value);
        input.value = "";
        filterOptions(field, "");
        return;
      }
      // Backspace on an empty box removes the last chip — the behaviour
      // every chip input has, and its absence is felt immediately.
      if (e.key === "Backspace" && !input.value) {
        const chips = field.querySelectorAll(".ffield__chip");
        if (chips.length) remove(chips[chips.length - 1]);
        return;
      }
      if (e.key === "Escape") closePanel(field);
    });
  });

  // Delegated: options and chips are both created and destroyed at runtime.
  document.addEventListener("click", (e) => {
    const x = e.target.closest(".ffield__x");
    if (x) {
      remove(x.closest(".ffield__chip"));
      return;
    }

    const opt = e.target.closest("[data-filter-option], [data-filter-custom]");
    if (opt) {
      const field = opt.closest("[data-filter-field]");
      const input = field?.querySelector("[data-filter-input]");
      if (!field) return;
      add(field, opt.dataset.filterOption || opt.textContent.trim());
      if (input) {
        input.value = "";
        input.focus();
      }
      filterOptions(field, "");
      return;
    }

    document
      .querySelectorAll("[data-filter-field].is-open")
      .forEach((field) => {
        if (!field.contains(e.target)) closePanel(field);
      });
  });

  // ⚠️ Close on scroll, never reposition. The panel is position:fixed, so
  // following the field means it detaches and paints over the topbar once
  // the field scrolls past it. Closing is what a native select does too.
  window.addEventListener(
    "scroll",
    () => {
      document
        .querySelectorAll("[data-filter-field].is-open")
        .forEach((field) => closePanel(field));
    },
    true,
  );

  window.addEventListener("resize", () => {
    document
      .querySelectorAll("[data-filter-field].is-open")
      .forEach((field) => closePanel(field));
  });
});
