import gsap from "gsap";

document.addEventListener("DOMContentLoaded", () => {
  const hero = document.querySelector(".hero");
  if (!hero) return;

  const reduced = window.matchMedia("(prefers-reduced-motion: reduce)");
  const desktop = window.matchMedia("(min-width: 992px)");

  const frame = hero.querySelector(".shot__frame");
  const card = hero.querySelector(".lead-card");
  const sweep = hero.querySelector(".hero__sweep");

  function settle() {
    gsap.set([frame, card].filter(Boolean), { clearProps: "all" });
    sweep?.classList.remove("is-running");
  }

  if (reduced.matches || !desktop.matches) {
    settle();
    return;
  }

  const tl = gsap.timeline({ delay: 0.2 });

  if (frame) {
    tl.fromTo(
      frame,
      { opacity: 0, y: 28 },
      { opacity: 1, y: 0, duration: 0.8, ease: "power3.out" },
      0,
    );
  }

  if (card) {
    tl.fromTo(
      card,
      { opacity: 0, y: 16, x: -10 },
      { opacity: 1, y: 0, x: 0, duration: 0.55, ease: "power2.out" },
      0.45,
    );
  }

  if (sweep) {
    tl.add(() => sweep.classList.add("is-running"), 0.15);
  }

  const bail = () => {
    if (reduced.matches || !desktop.matches) {
      tl.progress(1);
      settle();
    }
  };
  reduced.addEventListener("change", bail);
  desktop.addEventListener("change", bail);
});
