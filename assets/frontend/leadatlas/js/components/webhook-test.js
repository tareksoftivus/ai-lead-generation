/* Webhook test — screen 37's "Send test" button. */
document.addEventListener("DOMContentLoaded", () => {
  const SENDING_MS = 900;

  function setPill(pill, ok) {
    if (!pill) return;

    pill.classList.toggle("status--done", ok);
    pill.classList.toggle("status--failed", !ok);

    pill.textContent = "";

    const icon = document.createElement("i");
    icon.className = ok ? "ph ph-check" : "ph ph-warning";
    icon.setAttribute("aria-hidden", "true");

    pill.append(icon, document.createTextNode(ok ? "Healthy" : "Failing"));
  }

  function setLastCall(cell, ok) {
    if (!cell) return;

    const now = new Date();
    const hh = String(now.getHours()).padStart(2, "0");
    const mm = String(now.getMinutes()).padStart(2, "0");

    cell.textContent = "";

    const time = document.createElement("time");
    time.setAttribute("datetime", now.toISOString());
    time.textContent = `${hh}:${mm}`;

    cell.append(time, document.createTextNode(ok ? " · 200" : " · 500"));
  }

  document.addEventListener("click", (e) => {
    const btn = e.target.closest("[data-webhook-test]");
    if (!btn || btn.classList.contains("is-testing")) return;

    const row = btn.closest("[data-webhook-row]");
    if (!row) return;

    const ok = row.dataset.testResult !== "fail";

    btn.classList.add("is-testing");
    btn.disabled = true;

    setTimeout(() => {
      setPill(row.querySelector("[data-webhook-status]"), ok);
      setLastCall(row.querySelector("[data-webhook-last]"), ok);

      btn.classList.remove("is-testing");
      btn.disabled = false;

      const live = document.querySelector("[data-webhook-live]");
      if (live) {
        live.textContent = ok
          ? "Test delivered. The endpoint replied 200."
          : "Test failed. The endpoint replied 500.";
      }
    }, SENDING_MS);
  });
});
