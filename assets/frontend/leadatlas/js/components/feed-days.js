/* Feed day headings — hides a date heading when a filter leaves nothing */
document.addEventListener("DOMContentLoaded", () => {
  const lists = document.querySelectorAll("[data-list]");
  if (!lists.length) return;

  function sync(list) {
    const days = list.querySelectorAll("[data-feed-day]");
    if (!days.length) return;

    days.forEach((day) => {
      let visible = 0;
      let el = day.nextElementSibling;

      // Walk to the next heading, counting rows still on screen.
      while (el && !el.hasAttribute("data-feed-day")) {
        if (
          el.hasAttribute("data-list-key") &&
          !el.classList.contains("is-hidden")
        ) {
          visible += 1;
        }
        el = el.nextElementSibling;
      }

      day.classList.toggle("is-hidden", visible === 0);
    });
  }

  lists.forEach((list) => {
    if (!list.querySelector("[data-feed-day]")) return;

    list.addEventListener("list:filtered", () => sync(list));

    sync(list);
  });
});
