import { hideSpinner } from '../utils.js';
import { addUserLocationMarker, flyToUserLocation, getUserLocation } from './utils/geolocation.js';
import {
  DEFAULT_CENTER,
  gender,
  generateAraucariaIcon,
  generateMap,
  generatePhotoPath,
  lifeStage,
  makeTiles,
} from './utils/maps.js';

const API_URL = '/api/observations?all=1';
const FALLBACK_IMAGE = '/images/auth-bg.jpeg';
const VALID_PIN_TYPES = new Set(['normal', 'risco', 'protegida']);
const CITY_LOCATIONS = new Map([
  ['curitiba', [-25.4323, -49.2712, 12]],
  ['araucaria', [-25.5925, -49.4072, 12]],
  ['sao jose', [-25.5347, -49.2064, 12]],
  ['sao jose dos pinhais', [-25.5347, -49.2064, 12]],
  ['campo largo', [-25.4594, -49.5278, 12]],
  ['colombo', [-25.2925, -49.2247, 12]],
  ['pinhais', [-25.4331, -49.1919, 12]],
  ['barigui', [-25.4262, -49.3082, 14]],
  ['botanico', [-25.4428, -49.2394, 14]],
  ['tangua', [-25.3789, -49.2825, 14]],
  ['batel', [-25.4419, -49.2882, 14]],
]);

let currentTileStyle = 'osm';
let currentTileLayer = makeTiles(currentTileStyle);
const map = generateMap('map', L.latLng(DEFAULT_CENTER[0], DEFAULT_CENTER[1]), currentTileLayer);
let allMarkers = [];
let resizeObserver;
let loadController;

const markersGroup = L.markerClusterGroup({
  showCoverageOnHover: false,
  maxClusterRadius: 40,
  iconCreateFunction(cluster) {
    const count = document.createElement('span');
    count.textContent = String(cluster.getChildCount());
    const wrapper = document.createElement('div');
    wrapper.className = 'araucaria-cluster';
    wrapper.append(count);
    return L.divIcon({
      html: wrapper.outerHTML,
      className: 'custom-cluster-container',
      iconSize: new L.Point(46, 46),
    });
  },
});

function invalidateMapSize() {
  map?.invalidateSize();
}

[100, 400, 1000].forEach(delay => window.setTimeout(invalidateMapSize, delay));

if ('ResizeObserver' in window) {
  const mapElement = document.getElementById('map');
  if (mapElement) {
    resizeObserver = new ResizeObserver(invalidateMapSize);
    resizeObserver.observe(mapElement);
  }
}

function isValidCoordinate(value, min, max) {
  return typeof value === 'number' && Number.isFinite(value) && value >= min && value <= max;
}

function getCoordinates(observation) {
  const lat = typeof observation?.latitude === 'number' ? observation.latitude : Number(observation?.latitude);
  const lng = typeof observation?.longitude === 'number' ? observation.longitude : Number(observation?.longitude);
  if (!isValidCoordinate(lat, -90, 90) || !isValidCoordinate(lng, -180, 180)) return null;
  return [lat, lng];
}

function getObservationPhoto(observation) {
  const photos = Array.isArray(observation?.photos) ? observation.photos : [];
  const candidate = observation?.photo_path || photos[0]?.url || photos[0]?.photo_path;
  if (typeof candidate !== 'string' || candidate.trim() === '') return FALLBACK_IMAGE;

  // O helper deve produzir um caminho local conhecido. Não aceitamos data:, javascript: ou URLs externas.
  const generatedPath = generatePhotoPath(candidate.trim());
  if (typeof generatedPath !== 'string') return FALLBACK_IMAGE;
  try {
    const url = new URL(generatedPath, window.location.origin);
    if (url.origin !== window.location.origin || !['http:', 'https:'].includes(url.protocol)) {
      return FALLBACK_IMAGE;
    }
    return `${url.pathname}${url.search}`;
  } catch {
    return FALLBACK_IMAGE;
  }
}

function safeText(value, fallback = '') {
  return typeof value === 'string' && value.trim() ? value.trim() : fallback;
}

function createPopupContent(observation, photoPath) {
  const container = document.createElement('div');
  container.className = 'araucaria-popup';

  const image = document.createElement('img');
  image.src = photoPath;
  image.alt = 'Foto da observação';
  image.loading = 'lazy';
  image.referrerPolicy = 'no-referrer';
  image.addEventListener('error', () => { image.src = FALLBACK_IMAGE; }, { once: true });
  container.append(image);

  const title = document.createElement('strong');
  title.textContent = safeText(observation?.species_name, 'Araucária angustifolia');
  container.append(title);

  const details = document.createElement('p');
  const stage = lifeStage[observation?.stage] || safeText(observation?.stage, 'Estado não informado');
  const observer = safeText(observation?.observer, 'Colaborador comunitário');
  details.textContent = `${stage} • ${observer}`;
  container.append(details);

  if (isValidObservationId(observation?.id)) {
    const link = document.createElement('a');
    link.href = `/observations/${encodeURIComponent(String(observation.id))}`;
    link.textContent = 'Ver observação';
    container.append(link);
  }

  return container;
}

function isValidObservationId(id) {
  return (typeof id === 'string' && id.length > 0 && id.length <= 128)
    || (typeof id === 'number' && Number.isSafeInteger(id) && id >= 0);
}

function updateFloatingCard(observation) {
  const card = document.getElementById('hero-floating-obs');
  if (!card || !observation || typeof observation !== 'object') return;

  const imageElement = document.getElementById('floating-obs-img');
  const titleElement = document.getElementById('floating-obs-title');
  const subtitleElement = document.getElementById('floating-obs-subtitle');
  const badgeElement = document.getElementById('floating-obs-badge');

  if (titleElement) titleElement.textContent = safeText(observation.species_name, 'Araucária angustifolia');

  if (subtitleElement) {
    const parsedDate = observation.observed_at ? new Date(observation.observed_at) : null;
    const dateText = parsedDate && !Number.isNaN(parsedDate.getTime())
      ? parsedDate.toLocaleDateString('pt-BR')
      : 'Data não informada';
    const observer = safeText(observation.observer, 'Colaborador comunitário');
    subtitleElement.textContent = `${observer.startsWith('@') ? observer : `@${observer}`} • ${dateText}`;
  }

  if (badgeElement) {
    badgeElement.textContent = lifeStage[observation.stage] || safeText(observation.stage, 'Estado não informado');
    badgeElement.className = 'inline-block rounded-full px-2 py-0.5 text-[10px] font-semibold mt-0.5 ' +
      (observation.stage === 'dead'
        ? 'bg-red-100 text-red-800'
        : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300');
  }

  if (imageElement) {
    imageElement.src = getObservationPhoto(observation);
    imageElement.onerror = () => {
      imageElement.onerror = null;
      imageElement.src = FALLBACK_IMAGE;
    };
  }

  card.onclick = () => {
    if (isValidObservationId(observation.id)) {
      window.location.assign(`/observations/${encodeURIComponent(String(observation.id))}`);
    }
  };
  card.setAttribute('role', 'link');
  card.setAttribute('tabindex', '0');
  card.onkeydown = event => {
    if (event.key === 'Enter' || event.key === ' ') {
      event.preventDefault();
      card.click();
    }
  };
}

async function loadObservations() {
  loadController?.abort();
  loadController = new AbortController();

  try {
    const response = await fetch(API_URL, {
      headers: { Accept: 'application/json' },
      credentials: 'same-origin',
      signal: loadController.signal,
    });
    if (!response.ok) throw new Error(`Falha na API (${response.status})`);

    const payload = await response.json();
    const observations = Array.isArray(payload?.data) ? payload.data : [];
    const markerList = [];

    for (const observation of observations) {
      if (!observation || typeof observation !== 'object') continue;
      const coordinates = getCoordinates(observation);
      if (!coordinates) continue;

      const photoPath = getObservationPhoto(observation);
      const pinType = observation.stage === 'dead'
        ? 'risco'
        : (VALID_PIN_TYPES.has(observation.type) ? observation.type : 'normal');
      const stage = lifeStage[observation.stage] || safeText(observation.stage, 'Não informado');
      const observedGender = gender[observation.gender] || safeText(observation.gender, 'Não informado');
      const marker = L.marker(coordinates, { icon: generateAraucariaIcon(pinType) })
        .bindPopup(createPopupContent(observation, photoPath));

      marker.obsData = observation;
      marker.pinType = pinType;
      marker.on('click', () => updateFloatingCard(observation));
      markerList.push(marker);
    }

    allMarkers = markerList;
    markersGroup.clearLayers();
    markersGroup.addLayers(markerList);
    if (!map.hasLayer(markersGroup)) map.addLayer(markersGroup);

    if (observations.length > 0) updateFloatingCard(observations[0]);
    invalidateMapSize();
  } catch (error) {
    if (error?.name !== 'AbortError') {
      console.error('[Mapa] Não foi possível carregar as observações.');
      if (!map.hasLayer(markersGroup)) map.addLayer(markersGroup);
    }
  } finally {
    hideSpinner('mapSpinner');
  }
}

loadObservations();

function onClick(id, callback) {
  document.getElementById(id)?.addEventListener('click', callback);
}

onClick('btn-map-zoom-in', () => map.zoomIn());
onClick('btn-map-zoom-out', () => map.zoomOut());
onClick('btn-map-locate', async () => {
  const button = document.getElementById('btn-map-locate');
  if (!button) return;
  button.classList.add('animate-pulse');
  try {
    await flyToUserLocation(map);
  } finally {
    button.classList.remove('animate-pulse');
  }
});

onClick('btn-explorar-mapa', event => {
  const heroMap = document.getElementById('mapa-hero');
  if (!heroMap) return;
  event.preventDefault();
  heroMap.scrollIntoView({ behavior: 'smooth', block: 'center' });
  window.setTimeout(invalidateMapSize, 300);
});

onClick('btn-toggle-tiles', event => {
  map.removeLayer(currentTileLayer);
  currentTileStyle = currentTileStyle === 'osm' ? 'topo' : currentTileStyle === 'topo' ? 'satellite' : 'osm';
  currentTileLayer = makeTiles(currentTileStyle);
  const label = event.currentTarget.querySelector('span:last-child');
  if (label) label.textContent = currentTileStyle === 'osm' ? 'OSM' : currentTileStyle === 'topo' ? 'Topo' : 'Satélite';
  currentTileLayer.addTo(map).bringToBack();
});

function filterMarkers() {
  const showAll = document.getElementById('layer-toggle-all')?.checked ?? true;
  const showAraucarias = document.getElementById('layer-toggle-araucarias')?.checked ?? true;
  const showRisk = document.getElementById('layer-toggle-risco')?.checked ?? true;
  const showProtected = document.getElementById('layer-toggle-protegidas')?.checked ?? true;

  markersGroup.clearLayers();
  if (!showAll) return;
  markersGroup.addLayers(allMarkers.filter(marker => {
    if (marker.pinType === 'risco') return showRisk;
    if (marker.pinType === 'protegida') return showProtected;
    return showAraucarias;
  }));
}

['layer-toggle-all', 'layer-toggle-araucarias', 'layer-toggle-risco', 'layer-toggle-protegidas'].forEach(id => {
  document.getElementById(id)?.addEventListener('change', event => {
    if (id === 'layer-toggle-all') {
      for (const childId of ['layer-toggle-araucarias', 'layer-toggle-risco', 'layer-toggle-protegidas']) {
        const child = document.getElementById(childId);
        if (child) child.checked = event.currentTarget.checked;
      }
    }
    filterMarkers();
  });
});

function normalizeSearchTerm(value) {
  return value.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().trim();
}

const searchInput = document.getElementById('map-search-input');
searchInput?.addEventListener('keydown', event => {
  if (event.key !== 'Enter') return;
  event.preventDefault();
  const term = normalizeSearchTerm(searchInput.value);
  if (!term) return;

  const match = CITY_LOCATIONS.get(term);
  if (match) {
    map.flyTo([match[0], match[1]], match[2], { duration: 1.5 });
  } else {
    map.flyTo(DEFAULT_CENTER, 12, { duration: 1.5 });
  }
});

getUserLocation()
  .then(location => {
    if (isValidCoordinate(location?.lat, -90, 90) && isValidCoordinate(location?.lng, -180, 180)) {
      addUserLocationMarker(map, location.lat, location.lng);
    }
  })
  .catch(() => {
    // Localização é opcional; não interrompe a experiência se for negada ou indisponível.
  });

window.addEventListener('pagehide', () => {
  loadController?.abort();
  resizeObserver?.disconnect();
}, { once: true });