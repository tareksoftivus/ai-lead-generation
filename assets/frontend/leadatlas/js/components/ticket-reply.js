/* Ticket reply — appends your reply to the thread on screen 42's detail */
document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector("[data-ticket-reply]");
  if (!form) return;

  const thread = document.querySelector("[data-ticket-thread]");
  const field = form.querySelector("textarea");
  const count = document.querySelector("[data-ticket-count]");
  if (!thread || !field) return;

  function initials() {
    const mark = document.querySelector("[data-account-initials]");
    const text = mark ? mark.textContent.trim() : "";
    return /^[A-Za-z]{1,3}$/.test(text) ? text.toUpperCase() : "You";
  }

  function timestamp() {
    const now = new Date();
    const hh = String(now.getHours()).padStart(2, "0");
    const mm = String(now.getMinutes()).padStart(2, "0");
    return {
      iso: now.toISOString().slice(0, 10),
      label: `Today, ${hh}:${mm}`,
    };
  }

  function addMessage(body) {
    const when = timestamp();

    const article = document.createElement("article");
    article.className = "msg";

    const mark = document.createElement("span");
    mark.className = "msg__mark";
    mark.setAttribute("aria-hidden", "true");
    mark.textContent = initials();

    const wrap = document.createElement("div");
    wrap.className = "msg__body";

    const head = document.createElement("p");
    head.className = "msg__head";

    const who = document.createElement("span");
    who.className = "msg__who";
    who.textContent = "You";

    const time = document.createElement("time");
    time.className = "msg__when";
    time.setAttribute("datetime", when.iso);
    time.textContent = when.label;

    head.append(who, time);

    const text = document.createElement("div");
    text.className = "msg__text";

    // One <p> per paragraph, and textContent throughout — never innerHTML.
    body
      .split(/\n{2,}/)
      .map((part) => part.trim())
      .filter(Boolean)
      .forEach((part) => {
        const p = document.createElement("p");
        p.textContent = part;
        text.appendChild(p);
      });

    wrap.append(head, text);
    article.append(mark, wrap);
    thread.appendChild(article);

    return article;
  }

  function updateCount() {
    if (!count) return;
    const total = thread.querySelectorAll(".msg").length;
    count.textContent = total;
  }

  form.addEventListener("form:guarded", () => {
    const body = field.value.trim();
    if (!body) return;

    const added = addMessage(body);
    updateCount();

    field.value = "";
    added.setAttribute("tabindex", "-1");
    added.focus({ preventScroll: true });
    added.scrollIntoView({ behavior: "smooth", block: "nearest" });
  });
});
