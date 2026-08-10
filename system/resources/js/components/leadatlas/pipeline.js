/* Pipeline board — drag cards between stages, move via the menu, remove on confirm. */
document.addEventListener("DOMContentLoaded", () => {
  const board = document.querySelector("[data-pipeline]");
  if (!board) return;

  const emptyClass = "is-empty";
  const csrf = document.querySelector('meta[name="csrf-token"]')?.content || "";
  let dragCard = null;

  const columns = () => [...board.querySelectorAll("[data-stage]")];

  function recount() {
    columns().forEach((col) => {
      const drop = col.querySelector("[data-stage-drop]");
      const countEl = col.querySelector("[data-stage-count]");
      if (!drop) return;

      const n = drop.querySelectorAll("[data-card]").length;
      if (countEl) countEl.textContent = String(n);

      // The column's own empty state, not a page-level one.
      col.classList.toggle(emptyClass, n === 0);
    });
  }

  function restamp(card, stage) {
    const pill = card.querySelector("[data-card-status]");
    if (!pill) return;

    const label = board.querySelector(
      `[data-stage="${stage}"] [data-stage-name]`,
    );
    pill.className = `status status--${stage}`;
    if (label) pill.textContent = label.textContent.trim();
  }

  async function persist(card, stage) {
    const url = card?.dataset.updateUrl;
    if (!url) return true;

    const response = await fetch(url, {
      method: "PATCH",
      headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": csrf,
      },
      body: JSON.stringify({ status: stage }),
    });

    return response.ok;
  }

  async function remove(card) {
    const url = card?.dataset.removeUrl;
    if (!url) return true;

    const response = await fetch(url, {
      method: "DELETE",
      headers: {
        Accept: "application/json",
        "X-CSRF-TOKEN": csrf,
      },
    });

    return response.ok;
  }

  async function moveTo(card, stage) {
    const target = board.querySelector(
      `[data-stage="${stage}"] [data-stage-drop]`,
    );
    if (!target || !card) return;

    const previous = card.closest("[data-stage-drop]");
    const previousStage = card.closest("[data-stage]")?.dataset.stage;
    if (previousStage === stage) return;

    target.appendChild(card);
    restamp(card, stage);
    recount();

    try {
      const ok = await persist(card, stage);
      if (!ok) throw new Error("Pipeline update failed");
      window.showToast?.("Pipeline updated", "Lead stage saved.", "success");
    } catch (error) {
      previous?.appendChild(card);
      if (previousStage) restamp(card, previousStage);
      recount();
      window.showToast?.("Could not update", "Please try again.", "error");
    }
  }

  /* --- Drag ---------------------------------------------------------- */

  board.addEventListener("dragstart", (e) => {
    const card = e.target.closest("[data-card]");
    if (!card) return;
    dragCard = card;
    card.classList.add("is-dragging");
    e.dataTransfer.effectAllowed = "move";
    // Firefox refuses to start a drag without data set.
    e.dataTransfer.setData("text/plain", card.dataset.lead || "");
  });

  board.addEventListener("dragend", () => {
    if (dragCard) dragCard.classList.remove("is-dragging");
    columns().forEach((c) => c.classList.remove("is-over"));
    dragCard = null;
  });

  board.addEventListener("dragover", (e) => {
    if (!dragCard) return;
    const col = e.target.closest("[data-stage]");
    if (!col) return;
    e.preventDefault(); // this is what permits the drop
    e.dataTransfer.dropEffect = "move";
    columns().forEach((c) => c.classList.toggle("is-over", c === col));
  });

  board.addEventListener("dragleave", (e) => {
    const col = e.target.closest("[data-stage]");
    if (col && !col.contains(e.relatedTarget)) col.classList.remove("is-over");
  });

  board.addEventListener("drop", (e) => {
    if (!dragCard) return;
    const col = e.target.closest("[data-stage]");
    if (!col) return;
    e.preventDefault();
    moveTo(dragCard, col.dataset.stage);
    columns().forEach((c) => c.classList.remove("is-over"));
  });

  /* --- The menu ------------------------------------------------------ */

  // Delegated, so cards the backend renders later need no binding.
  board.addEventListener("click", (e) => {
    const item = e.target.closest("[data-move-to]");
    if (!item) return;
    const card = item.closest("[data-card]");
    if (!card) return;
    moveTo(card, item.dataset.moveTo);
  });

  // Removal happens only after the confirm dialog is accepted — it fires
  // confirm:accepted on the trigger, which bubbles up to here. The card
  // leaves the board; the lead itself is untouched (see the dialog body).
  board.addEventListener("confirm:accepted", (e) => {
    const trigger = e.target.closest("[data-remove-card]");
    if (!trigger) return;
    const card = trigger.closest("[data-card]");
    if (!card) return;

    remove(card)
      .then((ok) => {
        if (!ok) throw new Error("Pipeline remove failed");
        card.remove();
        recount();
        window.showToast?.("Pipeline updated", "Lead removed from the board.", "success");
      })
      .catch(() => {
        window.showToast?.("Could not remove", "Please try again.", "error");
      });
  });

  recount();
});
