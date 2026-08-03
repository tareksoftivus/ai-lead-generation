document.addEventListener("DOMContentLoaded", () => {
  const header = document.querySelector(".site-header");
  if (!header) return;

  const THRESHOLD = 12;

  function sync() {
    header.classList.toggle("is-stuck", window.scrollY > THRESHOLD);
  }

  sync();
  window.addEventListener("scroll", sync, { passive: true });
  window.addEventListener("resize", sync, { passive: true });
});
