document.addEventListener("DOMContentLoaded", () => {
  document.addEventListener("click", (e) => {
    const toggle = e.target.closest("[data-password-toggle]");
    if (!toggle) return;

    const field = toggle.closest(".password-field");
    const input = field?.querySelector("input");
    if (!field || !input) return;

    const showing = input.type === "text";
    input.type = showing ? "password" : "text";
    field.classList.toggle("is-visible", !showing);
    toggle.setAttribute(
      "aria-label",
      showing ? "Show password" : "Hide password",
    );
  });
});
