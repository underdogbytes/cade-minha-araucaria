import { hideSpinner } from '../utils.js';
import { addUserLocationMarker, flyToUserLocation, getUserLocation } from './utils/geolocation.js';
import {
  DEFAULT_CENTER,
  gender,
  generateAraucariaIcon,
  generateMap,
  lifeStage,
  makeTiles,
} from './utils/maps.js';

const API_URL = '/api/observations?all=1';
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
  spiderfyDistanceMultiplier: 1.25,
  spiderLegPolylineOptions: {
    weight: 2.5,
    color: '#1b4332',
    opacity: 0.85,
    dashArray: '5, 5',
    lineCap: 'round',
  },
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

markersGroup.on('spiderfied', event => {
  if (event.cluster && event.cluster._icon) {
    event.cluster._icon.classList.add('araucaria-cluster-spiderfied');
  }
  if (Array.isArray(event.markers)) {
    event.markers.forEach(marker => {
      marker._icon?.classList.add('araucaria-subpin');
    });
  }
});

markersGroup.on('unspiderfied', event => {
  if (event.cluster && event.cluster._icon) {
    event.cluster._icon.classList.remove('araucaria-cluster-spiderfied');
  }
  if (Array.isArray(event.markers)) {
    event.markers.forEach(marker => {
      marker._icon?.classList.remove('araucaria-subpin');
    });
  }
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

/**
 * Obtém a URL da foto real cadastrada na observação (Base64, storage ou URL).
 * Retorna null se não houver foto, sem imagens fictícias.
 */
function getObservationPhotoUrl(observation) {
  if (!observation || typeof observation !== 'object') return null;

  let raw = observation.photo_path;
  if (!raw && Array.isArray(observation.photos) && observation.photos.length > 0) {
    raw = observation.photos[0]?.url || observation.photos[0]?.photo_path;
  }

  if (typeof raw !== 'string') return null;
  const trimmed = raw.trim();
  if (!trimmed) return null;

  // Imagem em formato Base64 enviada pelo usuário
  if (trimmed.startsWith('data:image/')) {
    return trimmed;
  }
  // URL absoluta externa
  if (trimmed.startsWith('http://') || trimmed.startsWith('https://')) {
    return trimmed;
  }
  // Caminho absoluto no servidor
  if (trimmed.startsWith('/')) {
    return trimmed;
  }
  // Caminho relativo no storage do Laravel
  return '/storage/' + trimmed;
}

function safeText(value, fallback = '') {
  return typeof value === 'string' && value.trim() ? value.trim() : fallback;
}

function isValidObservationId(id) {
  return (typeof id === 'string' && id.length > 0 && id.length <= 128)
    || (typeof id === 'number' && Number.isSafeInteger(id) && id >= 0);
}

/**
 * Cria o conteúdo do popup com os dados REAIS da observação.
 */
function createPopupContent(observation) {
  const container = document.createElement('div');
  container.className = 'araucaria-popup';
  container.style.cssText = 'font-family: sans-serif; min-width: 170px; line-height: 1.4;';

  const photoUrl = getObservationPhotoUrl(observation);
  if (photoUrl) {
    const image = document.createElement('img');
    image.src = photoUrl;
    image.alt = 'Foto da observação';
    image.loading = 'lazy';
    image.style.cssText = 'width: 100%; max-width: 180px; max-height: 130px; object-fit: cover; border-radius: 8px; margin-bottom: 8px; display: block; border: 1px solid #e5e7eb;';
    container.append(image);
  }

  const title = document.createElement('h4');
  title.style.cssText = 'margin: 0 0 4px 0; color: #1b4332; font-size: 14px; font-weight: 700;';
  title.textContent = safeText(observation?.species_name, 'Araucária angustifolia');
  container.append(title);

  if (observation?.observer) {
    const observerDiv = document.createElement('div');
    observerDiv.style.cssText = 'font-size: 11px; color: #4b5563; margin-bottom: 4px;';
    const cleanObserver = safeText(observation.observer);
    observerDiv.innerHTML = `<b>Colaborador:</b> <span style="color: #1b4332; font-weight: 700;">${cleanObserver.startsWith('@') ? cleanObserver : `@${cleanObserver}`}</span>`;
    container.append(observerDiv);
  }

  const stage = lifeStage[observation?.stage] || safeText(observation?.stage, 'Não informado');
  const observedGender = gender[observation?.gender] || safeText(observation?.gender, 'Não informado');

  const details = document.createElement('div');
  details.style.cssText = 'font-size: 12px; color: #374151; margin-bottom: 8px;';
  details.innerHTML = `<b>Estágio:</b> ${stage}<br><b>Gênero:</b> ${observedGender}`;
  container.append(details);

  if (isValidObservationId(observation?.id)) {
    const link = document.createElement('a');
    link.href = `/observations/${encodeURIComponent(String(observation.id))}`;
    link.textContent = 'Ver detalhes completos →';
    link.style.cssText = 'display: block; background: #059669; color: white !important; font-size: 11px; font-weight: bold; text-align: center; padding: 6px 10px; border-radius: 6px; text-decoration: none;';
    container.append(link);
  }

  return container;
}

/**
 * Atualiza o card flutuante do Hero com a observação real clicada/selecionada.
 */
function updateFloatingCard(observation) {
  const card = document.getElementById('hero-floating-obs');
  if (!card) return;

  if (!observation || typeof observation !== 'object') {
    card.style.display = 'none';
    return;
  }
  card.style.display = 'flex';

  const imageElement = document.getElementById('floating-obs-img');
  const placeholderElement = document.getElementById('floating-obs-placeholder');
  const titleElement = document.getElementById('floating-obs-title');
  const subtitleElement = document.getElementById('floating-obs-subtitle');
  const badgeElement = document.getElementById('floating-obs-badge');

  if (titleElement) {
    titleElement.textContent = safeText(observation.species_name, 'Araucária angustifolia');
  }

  if (subtitleElement) {
    const parsedDate = observation.observed_at ? new Date(observation.observed_at) : null;
    const dateText = parsedDate && !Number.isNaN(parsedDate.getTime())
      ? parsedDate.toLocaleDateString('pt-BR')
      : 'Data recente';
    const observer = safeText(observation.observer, 'Colaborador');
    subtitleElement.textContent = `${observer.startsWith('@') ? observer : `@${observer}`} • ${dateText}`;
  }

  if (badgeElement) {
    badgeElement.textContent = lifeStage[observation.stage] || safeText(observation.stage, 'Saudável');
    badgeElement.className = 'inline-block rounded-full px-2 py-0.5 text-[10px] font-semibold mt-0.5 ' +
      (observation.stage === 'dead'
        ? 'bg-red-100 text-red-800'
        : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300');
  }

  const photoUrl = getObservationPhotoUrl(observation);
  if (photoUrl && imageElement) {
    imageElement.src = photoUrl;
    imageElement.style.display = 'block';
    if (placeholderElement) placeholderElement.style.display = 'none';
  } else {
    if (imageElement) imageElement.style.display = 'none';
    if (placeholderElement) placeholderElement.style.display = 'flex';
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

/**
 * Carrega EXCLUSIVAMENTE as observações reais cadastradas na base de dados.
 */
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

      const pinType = observation.stage === 'dead'
        ? 'risco'
        : (observation.is_shared ? 'protegida' : (VALID_PIN_TYPES.has(observation.type) ? observation.type : 'normal'));

      const marker = L.marker(coordinates, { icon: generateAraucariaIcon(pinType) })
        .bindPopup(createPopupContent(observation));

      marker.obsData = observation;
      marker.pinType = pinType;
      marker.on('click', () => updateFloatingCard(observation));
      markerList.push(marker);
    }

    allMarkers = markerList;
    markersGroup.clearLayers();
    markersGroup.addLayers(markerList);
    if (!map.hasLayer(markersGroup)) map.addLayer(markersGroup);

    if (observations.length > 0) {
      // Exibe a observação real cadastrada mais recente
      updateFloatingCard(observations[0]);

      // Enquadra o mapa nas observações reais cadastradas
      if (markerList.length === 1) {
        const coords = getCoordinates(observations[0]);
        if (coords) map.setView(coords, 13);
      } else if (markerList.length > 1) {
        const group = L.featureGroup(markerList);
        map.fitBounds(group.getBounds().pad(0.15));
      }
    } else {
      // Se não há observações cadastradas, o card flutuante fica oculto
      const card = document.getElementById('hero-floating-obs');
      if (card) card.style.display = 'none';
    }

    invalidateMapSize();
  } catch (error) {
    if (error?.name !== 'AbortError') {
      console.error('[Mapa] Falha ao carregar observações:', error);
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
    // Localização opcional; não interrompe a experiência se for negada ou indisponível.
  });

window.addEventListener('pagehide', () => {
  loadController?.abort();
  resizeObserver?.disconnect();
}, { once: true });