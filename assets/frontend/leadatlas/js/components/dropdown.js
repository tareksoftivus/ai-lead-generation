document.addEventListener("DOMContentLoaded", () => {
  const menus = document.querySelectorAll("[data-dropdown]");
  if (!menus.length) return;

  const GAP = 8; // space between trigger and panel
  const MARGIN = 8; // min gap from the viewport edge
  let openMenu = null;

  function panelOf(menu) {
    return menu.querySelector("[data-dropdown-panel]");
  }

  function toggleOf(menu) {
    return menu.querySelector("[data-dropdown-toggle]");
  }

  function position(menu) {
    const panel = panelOf(menu);
    const toggle = toggleOf(menu);
    if (!panel || !toggle) return;

    const t = toggle.getBoundingClientRect();
    if (menu.hasAttribute("data-dropdown-match-width")) {
      panel.style.width = `${Math.round(t.width)}px`;
    }
    const pw = panel.offsetWidth;
    const ph = panel.offsetHeight;
    const vw = document.documentElement.clientWidth;
    const vh = document.documentElement.clientHeight;
    const align = menu.dataset.dropdownAlign === "start" ? "start" : "end";

    let top = t.bottom + GAP;
    if (top + ph > vh - MARGIN && t.top - GAP - ph >= MARGIN) {
      top = t.top - GAP - ph; // flip up
    }
    top = Math.max(MARGIN, Math.min(top, vh - ph - MARGIN));

    let left = align === "start" ? t.left : t.right - pw;
    left = Math.max(MARGIN, Math.min(left, vw - pw - MARGIN));

    const cb = panel.offsetParent;
    if (cb && cb !== document.body && cb.nodeName !== "HTML") {
      const cbRect = cb.getBoundingClientRect();
      left -= cbRect.left;
      top -= cbRect.top;
    }

    panel.style.left = `${Math.round(left)}px`;
    panel.style.top = `${Math.round(top)}px`;
  }

  function closeAll(except) {
    menus.forEach((menu) => {
      if (menu === except) return;
      menu.classList.remove("is-open");
      toggleOf(menu)?.setAttribute("aria-expanded", "false");
    });
    if (openMenu && openMenu !== except) openMenu = null;
  }

  function open(menu) {
    closeAll(menu);
    const panel = panelOf(menu);
    const toggle = toggleOf(menu);
    if (!panel) return;
    menu.classList.add("is-open");
    toggle?.setAttribute("aria-expanded", "true");
    position(menu); // measure + place now that it's rendered
    openMenu = menu;
  }

  function close(menu) {
    menu.classList.remove("is-open");
    toggleOf(menu)?.setAttribute("aria-expanded", "false");
    if (openMenu === menu) openMenu = null;
  }

  menus.forEach((menu) => {
    const toggle = toggleOf(menu);
    if (!toggle) return;

    toggle.addEventListener("click", (e) => {
      e.stopPropagation();
      if (menu.classList.contains("is-open")) {
        close(menu);
      } else {
        open(menu);
      }
    });
  });

  // Select-style menus: a toolbar FILTER rather than a navigation menu.
  document.addEventListener("click", (e) => {
    const item = e.target.closest(".menu__panel .menu__item");
    if (!item) return;

    const menu = item.closest("[data-dropdown-select]");
    if (!menu) return;

    const text = [...item.childNodes]
      .filter((n) => n.nodeType === Node.TEXT_NODE)
      .map((n) => n.textContent)
      .join("")
      .trim();

    const label = menu.querySelector("[data-dropdown-label]");
    if (label) label.textContent = text;

    menu.dataset.value = item.dataset.value ?? text;
    menu
      .querySelectorAll(".menu__item")
      .forEach((i) => i.classList.toggle("is-selected", i === item));

    menu.dispatchEvent(
      new CustomEvent("dropdown:select", {
        bubbles: true,
        detail: { value: menu.dataset.value },
      }),
    );
    close(menu);
  });

  // Picking an ACTION closes the menu it came from. Without this the outside
  // click handler below never fires (the click is inside the menu), so an
  // action menu stayed open after being used — on the pipeline the open panel
  // then travelled with the card into its new column.
  document.addEventListener("click", (e) => {
    const item = e.target.closest(".menu__panel .menu__item");
    if (!item) return;
    const menu = item.closest("[data-dropdown]");
    if (!menu || menu.hasAttribute("data-dropdown-select")) return;
    close(menu);
  });

  document.addEventListener("click", (e) => {
    if (!openMenu) return;
    if (openMenu.contains(e.target)) return;
    close(openMenu);
  });

  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && openMenu) close(openMenu);
  });

  window.addEventListener(
    "resize",
    () => {
      if (openMenu) position(openMenu);
    },
    { passive: true },
  );

  window.addEventListener(
    "scroll",
    () => {
      if (openMenu) position(openMenu);
    },
    { passive: true, capture: true },
  );
});
