/* Editor Component (Quill) */

export function initEditor() {
  const editorElement = document.getElementById("quill-editor");

  if (editorElement) {
    if (typeof Quill !== "undefined") {
      try {
        new Quill(editorElement, {
          theme: "snow",
          placeholder: "Type your content here...",
          modules: {
            toolbar: [
              [{ header: [1, 2, 3, false] }],
              ["bold", "italic", "underline", "strike"],
              [{ list: "ordered" }, { list: "bullet" }],
              [{ direction: "rtl" }],
              [{ color: [] }, { background: [] }],
              [{ align: [] }],
              ["link", "image", "code-block", "clean"],
            ],
          },
        });
      } catch {
        // Leave the textarea fallback usable if the rich editor cannot mount.
      }
    }
  }
}

// Initialize Editor when DOM is ready
// Initialize Editor when DOM is ready or immediately if already ready
if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", () => {
    'use strict';

    initEditor();
  });
} else {
  initEditor();
}
