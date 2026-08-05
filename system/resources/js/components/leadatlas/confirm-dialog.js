document.addEventListener("DOMContentLoaded", () => {
  'use strict';

  const dialog = document.getElementById("confirmDialog");
  if (!dialog) return;

  const titleEl = dialog.querySelector("[data-confirm-title-target]");
  const bodyEl = dialog.querySelector("[data-confirm-body-target]");
  const acceptBtn = dialog.querySelector("[data-confirm-accept]");
  if (!titleEl || !bodyEl || !acceptBtn) return;

  let pendingTrigger = null;

  function openDialog(trigger) {
    titleEl.textContent = trigger.dataset.confirmTitle || "Are you sure?";
    bodyEl.textContent =
      trigger.dataset.confirmBody || "This action cannot be undone.";
    acceptBtn.textContent = trigger.dataset.confirmLabel || "Confirm";

    // State only — the CSS decides what a destructive confirm looks like.
    const isDestructive = trigger.dataset.confirmVariant === "error";
    dialog.classList.toggle("is-destructive", isDestructive);

    pendingTrigger = trigger;
    dialog.classList.add("is-open");
    document.body.classList.add("is-locked");
    dialog.removeAttribute("aria-hidden");
    acceptBtn.focus();
  }

  function closeDialog() {
    dialog.classList.remove("is-open");
    dialog.setAttribute("aria-hidden", "true");
    document.body.classList.remove("is-locked");
    pendingTrigger = null;
  }

  document.addEventListener("click", (e) => {
    const trigger = e.target.closest("[data-confirm]");
    if (trigger) {
      e.preventDefault();
      openDialog(trigger);
      return;
    }

    if (e.target.closest("[data-confirm-cancel]")) {
      e.preventDefault();
      closeDialog();
      return;
    }

    if (e.target.closest("[data-confirm-accept]")) {
      e.preventDefault();
      if (pendingTrigger) {
        pendingTrigger.dispatchEvent(
          new CustomEvent("confirm:accepted", { bubbles: true }),
        );
      }
      closeDialog();
    }
  });

  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && dialog.classList.contains("is-open")) {
      closeDialog();
    }
  });
});
