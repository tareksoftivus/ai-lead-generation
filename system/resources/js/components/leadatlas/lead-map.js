import L from "leaflet";

/* Lead map — the Leaflet view behind the map screen. */
document.addEventListener("DOMContentLoaded", () => {
  'use strict';

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

  function parseLeads(raw) {
    if (!raw) return [];

    try {
      const parsed = JSON.parse(raw);
      if (!Array.isArray(parsed)) return [];

      return parsed
        .map((lead) => {
          const lat = Number(lead.lat);
          const lng = Number(lead.lng);
          if (!Number.isFinite(lat) || !Number.isFinite(lng)) return null;

          return {
            ...lead,
            id: String(lead.id),
            lat,
            lng,
            score:
              lead.score === null || lead.score === undefined
                ? null
                : Number(lead.score),
          };
        })
        .filter(Boolean);
    } catch {
      return [];
    }
  }

  function parsePins(raw) {
    if (!raw) return [];
    return raw
      .split(";")
      .map((chunk) => chunk.trim())
      .filter(Boolean)
      .map((chunk, index) => {
        const parts = chunk.split(",");
        const lat = Number(parts[0]);
        const lng = Number(parts[1]);
        if (!Number.isFinite(lat) || !Number.isFinite(lng)) return null;
        const score =
          parts[2] === undefined || parts[2] === "" ? null : Number(parts[2]);
        const name = parts.slice(3).join(",").trim();
        return { id: `pin-${index}`, lat, lng, score, name };
      })
      .filter(Boolean);
  }

  function escapeHtml(value) {
    return String(value ?? "")
      .replaceAll("&", "&amp;")
      .replaceAll("<", "&lt;")
      .replaceAll(">", "&gt;")
      .replaceAll('"', "&quot;")
      .replaceAll("'", "&#039;");
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
    const pins = holder.dataset.mapLeads
      ? parseLeads(holder.dataset.mapLeads)
      : parsePins(holder.dataset.mapPins);
    const empty = holder.querySelector("[data-map-empty]");

    if (!pins.length) {
      empty?.classList.remove("is-hidden");
    }

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
      scrollWheelZoom: false,
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

    pins.forEach((pin) => {
      const marker = L.marker([pin.lat, pin.lng], { icon: markerFor(pin) });

      if (pin.name) {
        const score =
          pin.score === null
            ? "<span class='mpop__meta'>Not scored yet</span>"
            : `<span class='mpop__score numeric'>${pin.score}</span>`;

        const status = pin.status_label
          ? `<span class="status status--${escapeHtml(pin.status)}">${escapeHtml(pin.status_label)}</span>`
          : "";
        const contact = pin.contact_label
          ? `<span class="mpop__meta">${escapeHtml(pin.contact_label)}</span>`
          : "";
        const address = pin.address
          ? `<span class="mpop__addr">${escapeHtml(pin.address)}</span>`
          : "";
        const open = pin.url
          ? `<a class="mpop__link" href="${escapeHtml(pin.url)}">Open lead</a>`
          : "";
        const maps = pin.maps_url
          ? `<a class="mpop__link" href="${escapeHtml(pin.maps_url)}" target="_blank" rel="noopener">Open in Google Maps</a>`
            : "";

        marker.bindPopup(
          `<span class="mpop"><span class="mpop__head"><span class="mpop__name">${escapeHtml(pin.name)}</span>${score}</span>${address}<span class="mpop__line">${status}${contact}</span><span class="mpop__actions">${open}${maps}</span></span>`,
        );
      }

      // Keep the name on the marker so a filter can match against it.
      marker._pin = pin;
      group.addLayer(marker);
    });

    if (pins.length) {
      group.addTo(map);
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
      empty?.classList.toggle("is-hidden", shown > 0);

      if (shown > 1) {
        const visible = L.featureGroup(
          group.getLayers().filter((m) => map.hasLayer(m)),
        );
        map.fitBounds(visible.getBounds().pad(0.15), { maxZoom: 14 });
      }
      return shown;
    }

    holder._leadMap = {
      map,
      group,
      markerFor,
      filter,
      focus(id) {
        let focused = false;
        group.eachLayer((marker) => {
          if (String(marker._pin?.id) !== String(id)) return;
          map.setView(marker.getLatLng(), 15, { animate: true });
          marker.openPopup();
          focused = true;
        });
        return focused;
      },
    };
  });
});
