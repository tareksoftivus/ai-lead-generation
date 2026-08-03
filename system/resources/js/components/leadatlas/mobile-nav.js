document.addEventListener("DOMContentLoaded", () => {
  const openBtn = document.querySelector("[data-nav-open]");
  const closeBtn = document.querySelector("[data-nav-close]");
  const drawer = document.querySelector(".mobile-nav");
  const backdrop = document.querySelector("[data-nav-backdrop]");

  if (!openBtn || !drawer || !backdrop) return;

  function openNav() {
    drawer.classList.add("is-open");
    backdrop.classList.add("is-open");
    document.body.classList.add("is-locked");
    openBtn.setAttribute("aria-expanded", "true");
  }

  function closeNav() {
    drawer.classList.remove("is-open");
    backdrop.classList.remove("is-open");
    document.body.classList.remove("is-locked");
    openBtn.setAttribute("aria-expanded", "false");
  }

  openBtn.addEventListener("click", openNav);
  closeBtn?.addEventListener("click", closeNav);
  backdrop.addEventListener("click", closeNav);

  drawer.querySelectorAll("a").forEach((link) => {
    link.addEventListener("click", closeNav);
  });

  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") closeNav();
  });
});
