import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

gsap.registerPlugin(ScrollTrigger);

document.addEventListener("DOMContentLoaded", () => {
  const reduced = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  const blocks = document.querySelectorAll("[data-anim]");
  if (!blocks.length) return;

  if (reduced) {
    gsap.set("[data-anim], [data-anim-item]", { opacity: 1, y: 0 });
    return;
  }

  blocks.forEach((block) => {
    const items = block.querySelectorAll("[data-anim-item]");
    const targets = items.length ? items : [block];

    if (items.length) gsap.set(block, { opacity: 1 });

    gsap.fromTo(
      targets,
      { opacity: 0, y: 24 },
      {
        opacity: 1,
        y: 0,
        duration: 0.6,
        ease: "power2.out",
        stagger: items.length ? 0.08 : 0,
        scrollTrigger: {
          trigger: block,
          start: "top 85%",
          once: true,
        },
      },
    );
  });
});
