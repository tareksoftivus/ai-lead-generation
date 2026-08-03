import L from "leaflet";

/**
 * Lead map — the Leaflet view behind screens 16 and 20.
 *
 * The backend fills ONE element and this draws it, the same contract the
 * charts use (§14.1):
 *
 *   <div data-map
 *        data-map-center="30.2672,-97.7431"
 *        data-map-zoom="12"
 *        data-map-pins="30.26,-97.74,92,Barton Springs Dental;..."></div>
 *
 * Each pin is `lat,lng,score,name`. Pins are semicolon-separated so a
 * business name can safely contain a comma.
 *
 * ⚠️ Pin colour encodes the SCORE, and score is AI output — so pins run
 * on the violet ramp, not the discover azure the map chrome uses. Azure is
 * where the data came from; violet is what the AI made of it. An
 * unscored pin (score omitted) stays azure, because at that point it is
 * still just a map result.
 *
 * Markers are divIcons, not image files: they take our @theme colours,
 * stay sharp at any zoom, and need no sprite to ship.
 */
document.addEventListener("DOMContentLoaded", () => {
  const holders = document.querySelectorAll("[data-map]");
  if (!holders.length) return;

  const css = getComputedStyle(document.documentElement);
  const token = (name, fallback) =>
    css.getPropertyValue(`--color-${name}`).trim() || fallback;

  const HI = token("ai-deep", "#5734b4");
  const MID = token("ai", "#6d45d9");
  const LO = token("neutral-500", "#6b7288");
  const RAW = token("discover-deep", "#0a7387");

  function colorFor(score) {
    if (score === null) return RAW;
    if (score >= 80) return HI;
    if (score >= 60) return MID;
    return LO;
  }

  function parsePins(raw) {
    if (!raw) return [];
    return raw
      .split(";")
      .map((chunk) => chunk.trim())
      .filter(Boolean)
      .map((chunk) => {
        const parts = chunk.split(",");
        const lat = Number(parts[0]);
        const lng = Number(parts[1]);
        if (!Number.isFinite(lat) || !Number.isFinite(lng)) return null;
        const score =
          parts[2] === undefined || parts[2] === "" ? null : Number(parts[2]);
        const name = parts.slice(3).join(",").trim();
        return { lat, lng, score, name };
      })
      .filter(Boolean);
  }

  function markerFor(pin) {
    const color = colorFor(pin.score);
    const label = pin.score === null ? "" : pin.score;

    return L.divIcon({
      className: "",
      html: `<span class="mpin" style="--mpin-color:${color}">${label}</span>`,
      iconSize: [30, 30],
      iconAnchor: [15, 15],
      popupAnchor: [0, -14],
    });
  }

  holders.forEach((holder) => {
    const pins = parsePins(holder.dataset.mapPins);

    const centerAttr = (holder.dataset.mapCenter || "").split(",");
    const center =
      centerAttr.length === 2 && Number.isFinite(Number(centerAttr[0]))
        ? [Number(centerAttr[0]), Number(centerAttr[1])]
        : pins.length
          ? [pins[0].lat, pins[0].lng]
          : [30.2672, -97.7431];

    const map = L.map(holder, {
      center,
      zoom: Number(holder.dataset.mapZoom) || 12,
      scrollWheelZoom: false, // a page-scroll trap otherwise
      attributionControl: true,
    });

    L.tileLayer(
      "https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png",
      {
        maxZoom: 19,
        attribution:
          '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
      },
    ).addTo(map);

    map.on("click", () => map.scrollWheelZoom.enable());
    map.on("mouseout", () => map.scrollWheelZoom.disable());

    const group = L.featureGroup();

    const detailHref = holder.dataset.mapDetail;

    pins.forEach((pin) => {
      const marker = L.marker([pin.lat, pin.lng], { icon: markerFor(pin) });

      if (pin.name) {
        const score =
          pin.score === null
            ? "<span class='mpop__meta'>Not scored yet</span>"
            : `<span class='mpop__score numeric'>${pin.score}</span>`;

        const open = detailHref
          ? `<a class="mpop__link" href="${detailHref}">Open lead</a>`
          : "";

        marker.bindPopup(
          `<span class="mpop"><span class="mpop__head"><span class="mpop__name">${pin.name}</span>${score}</span>${open}</span>`,
        );
      }

      // Keep the name on the marker so a filter can match against it.
      marker._pin = pin;
      group.addLayer(marker);
    });

    if (pins.length) {
      group.addTo(map);
      // Fit to the data rather than trusting a hardcoded zoom.
      if (pins.length > 1) {
        map.fitBounds(group.getBounds().pad(0.15), { maxZoom: 14 });
      }
    }

    function filter(predicate) {
      let shown = 0;
      group.eachLayer((marker) => {
        const on = predicate(marker._pin);
        if (on) {
          if (!map.hasLayer(marker)) marker.addTo(map);
          shown += 1;
        } else if (map.hasLayer(marker)) {
          map.removeLayer(marker);
        }
      });
      if (shown > 1) {
        const visible = L.featureGroup(
          group.getLayers().filter((m) => map.hasLayer(m)),
        );
        map.fitBounds(visible.getBounds().pad(0.15));
      }
      return shown;
    }

    holder._leadMap = { map, group, markerFor, filter };
  });
});
