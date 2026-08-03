import Swiper from "swiper";
import { A11y, Autoplay } from "swiper/modules";
import "swiper/css";

/**
 * Voices slider — the "Who uses it" section on the home page.
 *
 * Structure follows whatsapp-store's testimonial slider (§16): a
 * continuous marquee of cards rather than a static grid, so the section
 * reads as a stream of voices and holds any number of quotes without a
 * layout decision. Its tokens and its product framing are NOT taken —
 * our cards keep the discover/AI/act stage labels (§5), which theirs has
 * no equivalent of.
 *
 * ⚠️ Three accessibility guards, all load-bearing:
 *   - prefers-reduced-motion kills the autoplay outright.
 *   - pauseOnMouseEnter stops it so a quote can actually be read
 *     (WCAG 2.2.2 — moving content must be pausable).
 *   - A drag pauses rather than killing it, so the section does not
 *     freeze the first time someone nudges it.
 */
document.addEventListener("DOMContentLoaded", () => {
  const el = document.querySelector("[data-voices-slider]");
  if (!el) return;

  const reduced = window.matchMedia("(prefers-reduced-motion: reduce)");

  new Swiper(el, {
    modules: [A11y, Autoplay],

    slidesPerView: "auto",
    spaceBetween: 16,
    breakpoints: { 576: { spaceBetween: 20 } },
    grabCursor: true,

    speed: 6000,
    autoplay: reduced.matches
      ? false
      : {
          delay: 0,
          disableOnInteraction: false,
          pauseOnMouseEnter: true,
        },

    // ⚠️ Exactly whatsapp-store's loop config (§16), option for option.
    loop: true,
    loopAdditionalSlides: 2,
  });
});
