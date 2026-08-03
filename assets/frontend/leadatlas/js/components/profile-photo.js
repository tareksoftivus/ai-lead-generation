/* Profile photo — live preview of a chosen avatar before it uploads. */
document.addEventListener("DOMContentLoaded", () => {
  const inputs = document.querySelectorAll("[data-preview-swap]");
  if (!inputs.length) return;

  function swapImage(input) {
    const img = document.querySelector(input.getAttribute("data-preview-swap"));
    const file = input.files && input.files[0];
    if (!img || !file) return;
    if (!file.type.startsWith("image/")) return;

    const url = URL.createObjectURL(file);
    if (img.dataset.objectUrl) URL.revokeObjectURL(img.dataset.objectUrl);
    img.src = url;
    img.dataset.objectUrl = url;
  }

  inputs.forEach((input) => {
    input.addEventListener("change", () => swapImage(input));
  });
});
