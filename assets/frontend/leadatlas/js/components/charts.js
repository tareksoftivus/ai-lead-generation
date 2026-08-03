import Chart from "chart.js/auto";

/**
 * Charts — every chart on every screen.
 *
 * The backend fills ONE element and this draws it (§14.1). Never size a
 * div to fake a bar:
 *
 *   <canvas data-chart="bar"
 *           data-chart-values="12,19,8,24"
 *           data-chart-labels="Mon,Tue,Wed,Thu"></canvas>
 *
 * Supported: bar, line, doughnut.
 *
 * Colours are read from the @theme tokens at runtime rather than
 * hardcoded, so a palette change in app.css reaches the charts too.
 * Optional data-chart-color picks the role: discover (map data), ai
 * (machine output), accent (commercial), success. Defaults to discover,
 * because most charts here count things that came off the map.
 */
document.addEventListener("DOMContentLoaded", () => {
  const canvases = document.querySelectorAll("[data-chart]");
  if (!canvases.length) return;

  const css = getComputedStyle(document.documentElement);
  const token = (name, fallback) =>
    css.getPropertyValue(`--color-${name}`).trim() || fallback;

  const roles = {
    discover: token("discover", "#4f39f6"),
    ai: token("ai", "#7c3aed"),
    accent: token("accent", "#f97316"),
    success: token("success", "#047857"),
  };
  const ink = token("body", "#475569");
  const grid = token("neutral-200", "#e2e8f0");

  // Chart.js draws its own text, so it cannot inherit our font stack.
  Chart.defaults.font.family =
    "Inter, ui-sans-serif, system-ui, -apple-system, sans-serif";
  Chart.defaults.font.size = 11;
  Chart.defaults.color = ink;

  const list = (el, key) =>
    (el.dataset[key] || "")
      .split(",")
      .map((v) => v.trim())
      .filter(Boolean);

  function fade(ctx, area, color) {
    if (!area) return "transparent";
    const g = ctx.createLinearGradient(0, area.top, 0, area.bottom);
    g.addColorStop(0, `${color}33`);
    g.addColorStop(1, `${color}00`);
    return g;
  }

  canvases.forEach((canvas) => {
    const type = canvas.dataset.chart;
    const labels = list(canvas, "chartLabels");
    const values = list(canvas, "chartValues").map(Number);
    if (!values.length) return;

    const color = roles[canvas.dataset.chartColor] || roles.discover;

    // A doughnut needs one colour per slice; bar and line take one.
    const palette = [roles.ai, roles.discover, roles.accent, roles.success];

    const base = {
      labels,
      datasets: [
        {
          data: values,
          borderColor: color,
          backgroundColor:
            type === "doughnut"
              ? values.map((_, i) => palette[i % palette.length])
              : type === "line"
                ? (c) => fade(c.chart.ctx, c.chart.chartArea, color)
                : color,
          borderWidth: type === "doughnut" ? 0 : 2,
          borderRadius: type === "bar" ? 6 : 0,
          fill: type === "line",
          tension: 0.35,
          pointRadius: 0,
          pointHoverRadius: 4,
          pointBackgroundColor: color,
        },
      ],
    };

    const axes =
      type === "doughnut"
        ? {}
        : {
            x: {
              grid: { display: false },
              border: { color: grid },
              ticks: { maxRotation: 0, autoSkipPadding: 12 },
            },
            y: {
              beginAtZero: true,
              border: { display: false },
              grid: { color: grid },
              ticks: { maxTicksLimit: 5, padding: 8 },
            },
          };

    new Chart(canvas, {
      type,
      data: base,
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: type === "doughnut" ? "68%" : undefined,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: token("title", "#171a3d"),
            padding: 10,
            cornerRadius: 8,
            displayColors: false,
          },
        },
        scales: axes,
        interaction: { intersect: false, mode: "index" },
      },
    });
  });
});
