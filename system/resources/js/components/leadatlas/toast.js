/**
 * Toast notifications for the public site. Mirrors the admin toast component
 * (resources/js/components/toast.js) so both surfaces share the same
 * interaction, but renders against the leadatlas theme's own tokens/markup.
 */
export function showToast(title, message, type = "success") {
  const container = document.getElementById("toastContainer");
  if (!container) return;

  const icons = {
    success: "ph-check-circle",
    error: "ph-warning-circle",
    warning: "ph-warning",
    info: "ph-info",
  };
  const icon = icons[type] || icons.success;

  const toast = document.createElement("div");
  toast.className = `toast toast--${type}`;
  toast.innerHTML = `
    <div class="toast__icon">
      <i class="ph ${icon}" aria-hidden="true"></i>
    </div>
    <div class="toast__body">
      <p class="toast__title">${title}</p>
      ${message ? `<p class="toast__message">${message}</p>` : ""}
    </div>
    <button type="button" class="toast__close" aria-label="Dismiss">
      <i class="ph ph-x" aria-hidden="true"></i>
    </button>
  `;

  toast.querySelector(".toast__close").addEventListener("click", () => dismiss(toast));

  container.appendChild(toast);
  requestAnimationFrame(() => toast.classList.add("is-shown"));

  const timer = setTimeout(() => dismiss(toast), 5000);
  toast.addEventListener("mouseenter", () => clearTimeout(timer));
}

function dismiss(toast) {
  toast.classList.remove("is-shown");
  setTimeout(() => toast.remove(), 300);
}

window.showToast = showToast;
