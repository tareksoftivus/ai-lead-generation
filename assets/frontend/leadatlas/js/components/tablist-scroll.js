/* Scroll a tab row to its active tab. */
document.addEventListener("DOMContentLoaded", () => {
  const lists = document.querySelectorAll(".app-tablist");
  if (!lists.length) return;

  const ready = document.fonts?.ready ?? Promise.resolve();
  ready.then(() => run(lists));
});

function run(lists) {
  lists.forEach((list) => {
    // Nothing to do when everything is already visible.
    if (list.scrollWidth <= list.clientWidth) return;

    const active = list.querySelector(".is-active");
    if (!active) return;

    const listBox = list.getBoundingClientRect();
    const activeBox = active.getBoundingClientRect();

    // Where the tab sits along the full scrollable width.
    const offset = activeBox.left - listBox.left + list.scrollLeft;

    const centred = offset - (list.clientWidth - activeBox.width) / 2;
    const max = list.scrollWidth - list.clientWidth;

    list.scrollLeft = Math.min(max, Math.max(0, centred));
  });
}
