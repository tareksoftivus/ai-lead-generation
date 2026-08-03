document.addEventListener("DOMContentLoaded", () => {
  const sidebar = document.querySelector("[data-sidebar]");
  const backdrop = document.querySelector("[data-sidebar-backdrop]");
  const openBtn = document.querySelector("[data-sidebar-open]");
  const closeBtn = document.querySelector("[data-sidebar-close]");

  // The nav scrolls, and it always starts at scrollTop 0 — so on the screens
  // low down the list (Settings, Credits, API) the lit item sat below the fold
  // and the sidebar looked like nothing was selected. Centre it, but only when
  // it is genuinely out of view, so the common case does not jump.
  //
  // Waits on fonts: measured before they load, the links are still in fallback
  // metrics, the scroll range is smaller than it will be, and the result lands
  // short. Same trap as tablist-scroll.js.
  function revealActiveLink() {
    const active = sidebar?.querySelector(".sidebar-link.is-active");
    const scroller = active?.closest("nav");
    if (!active || !scroller) return;

    const linkBox = active.getBoundingClientRect();
    const navBox = scroller.getBoundingClientRect();
    if (linkBox.top >= navBox.top && linkBox.bottom <= navBox.bottom) return;

    scroller.scrollTop +=
      linkBox.top - navBox.top - scroller.clientHeight / 2 + linkBox.height / 2;
  }

  if (document.fonts?.ready) {
    document.fonts.ready.then(revealActiveLink);
  } else {
    revealActiveLink();
  }

  if (!sidebar || !backdrop || !openBtn) return;

  function openSidebar() {
    sidebar.classList.add("is-open");
    backdrop.classList.add("is-open");
    document.body.classList.add("is-locked");
    openBtn.setAttribute("aria-expanded", "true");
  }

  function closeSidebar() {
    sidebar.classList.remove("is-open");
    backdrop.classList.remove("is-open");
    document.body.classList.remove("is-locked");
    openBtn.setAttribute("aria-expanded", "false");
  }

  openBtn.addEventListener("click", openSidebar);
  closeBtn?.addEventListener("click", closeSidebar);
  backdrop.addEventListener("click", closeSidebar);

  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") closeSidebar();
  });
});
