document.addEventListener("DOMContentLoaded", () => {
  'use strict';

  function openModal(modal) {
    if (!modal) return;
    modal.classList.add("is-open");
    document.body.classList.add("is-locked");
    modal.removeAttribute("aria-hidden");
  }

  function closeModal(modal) {
    if (!modal) return;
    modal.classList.remove("is-open");
    modal.setAttribute("aria-hidden", "true");
    if (!document.querySelector(".modal.is-open")) {
      document.body.classList.remove("is-locked");
    }
  }

  // Delegated so server-rendered rows added later still work.
  document.addEventListener("click", (e) => {
    const opener = e.target.closest("[data-modal-open]");
    if (opener) {
      const modal = document.getElementById(opener.dataset.modalOpen);
      if (modal) {
        e.preventDefault();
        openModal(modal);
      }
      return;
    }

    const closer = e.target.closest("[data-modal-close]");
    if (closer) {
      e.preventDefault();
      closeModal(closer.closest(".modal"));
      return;
    }

    const backdrop = e.target.closest(".modal__backdrop");
    if (backdrop) closeModal(backdrop.closest(".modal"));
  });

  document.addEventListener("keydown", (e) => {
    if (e.key !== "Escape") return;
    closeModal(document.querySelector(".modal.is-open"));
  });
});
