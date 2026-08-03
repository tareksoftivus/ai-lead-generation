document.addEventListener("DOMContentLoaded", () => {
  document.addEventListener("submit", (e) => {
    const form = e.target;
    if (!(form instanceof HTMLFormElement)) return;

    const action = form.getAttribute("action");
    if (action && action !== "#") return;

    e.preventDefault();
    form.dispatchEvent(new CustomEvent("form:guarded", { bubbles: true }));
  });
});
