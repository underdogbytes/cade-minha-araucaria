import { fetchObservations } from './api.js';
import { addObservationMarker } from './markers.js';
import { makeTiles } from './utils/maps.js';
import { dispatchAlert } from './utils/alerts.js';

let maps = {};
let clickMarkers = {};
let observationLayers = {};
let pendingObservationsPromise = null;

async function fetchObservationsDeduped() {
  if (!pendingObservationsPromise) {
    pendingObservationsPromise = fetchObservations().finally(() => {
      setTimeout(() => {
        pendingObservationsPromise = null;
      }, 1000);
    });
  }
  return pendingObservationsPromise;
}

export async function initMap(mapId = 'map') {
  if (maps[mapId]) { return maps[mapId]; }

  const mapElement = document.getElementById(mapId);
  if (!mapElement) {
    return null;
  }

  const map = L.map(mapId).setView([-25.4323, -49.2712], 12);
  const tiles = makeTiles();

  tiles.addTo(map);

  map.on('click', (event) => handleMapClick(event, mapId));

  maps[mapId] = map;

  if (mapId !== 'map-create' && mapId !== 'map-edit') {
    await loadExistingPoints(map, mapId);
  }

  return map;
}

export function destroyMap(mapId = 'map') {
  if (clickMarkers[mapId]) {
    if (maps[mapId]) {
      maps[mapId].removeLayer(clickMarkers[mapId]);
    }
    delete clickMarkers[mapId];
  }
  if (observationLayers[mapId]) {
    if (maps[mapId]) {
      maps[mapId].removeLayer(observationLayers[mapId]);
    }
    delete observationLayers[mapId];
  }
  if (maps[mapId]) {
    maps[mapId].remove();
    delete maps[mapId];
  }
}

function handleMapClick(event, mapId) {
  const map = maps[mapId];
  if (!map) return;

  const { lat, lng } = event.latlng;

  updateCoordinates(lat, lng, mapId);

  if (clickMarkers[mapId]) {
    clickMarkers[mapId].setLatLng(event.latlng);
    return;
  }

  const clickMarker = L.marker(event.latlng, {
    draggable: true,
  }).addTo(map);

  clickMarker.on('moveend', (e) => handleMarkerMove(e, mapId));
  clickMarkers[mapId] = clickMarker;
}

function handleMarkerMove(event, mapId) {
  const position = event.target.getLatLng();

  updateCoordinates(position.lat, position.lng, mapId);
}

function updateCoordinates(lat, lng, mapId) {
  const sufixo = mapId === 'map-edit' ? 'edit' : (mapId === 'map-create' ? 'create' : 'create');
  const form = document.getElementById(`araucariaForm-${sufixo}`) || document.getElementById('araucariaForm');

  if (!form) return;

  const latInput = form.querySelector(`#latitude-${sufixo}`) || form.querySelector('[name="latitude"]') || form.querySelector('[id^="latitude"]');
  const lngInput = form.querySelector(`#longitude-${sufixo}`) || form.querySelector('[name="longitude"]') || form.querySelector('[id^="longitude"]');

  if (!latInput || !lngInput) return;

  const latValue = Number(lat).toFixed(6);
  const lngValue = Number(lng).toFixed(6);

  latInput.value = latValue;
  lngInput.value = lngValue;

  latInput.dispatchEvent(new Event('input', { bubbles: true }));
  lngInput.dispatchEvent(new Event('input', { bubbles: true }));
}

async function loadExistingPoints(map, mapId = 'map') {
  await reloadGlobalMapPoints(mapId);
}

export async function reloadGlobalMapPoints(mapId = 'map') {
  const map = maps[mapId];
  if (!map) return;

  if (observationLayers[mapId]) {
    observationLayers[mapId].clearLayers();
  } else {
    observationLayers[mapId] = L.layerGroup().addTo(map);
  }

  try {
    const response = await fetchObservationsDeduped();
    const observations = response.data || response;

    if (Array.isArray(observations)) {
      observations.forEach(observation => {
        const marker = addObservationMarker(map, observation);
        if (marker && observationLayers[mapId]) {
          observationLayers[mapId].addLayer(marker);
        }
      });
    }
  } catch (error) {
    console.error(`[Map Service Error] Falha ao carregar/recarregar observações existentes no mapa '${mapId}':`, error);
    dispatchAlert('error', 'Não foi possível carregar os pontos das observações.');
  }
}

export function addNewObservationToMap(observation, mapId = 'map') {
  if (mapId === 'map-create') return;

  const map = maps[mapId];
  if (!map) return;

  const marker = addObservationMarker(map, observation);
  if (marker && observationLayers[mapId]) {
    observationLayers[mapId].addLayer(marker);
  }
}

export function clearClickMarker(mapId = 'map-create') {
  const map = maps[mapId];
  if (!map) return;

  if (clickMarkers[mapId]) {
    map.removeLayer(clickMarkers[mapId]);
    clickMarkers[mapId] = null;
  }

  if (observationLayers[mapId]) {
    observationLayers[mapId].clearLayers();
  }

  // Garante a remoção de qualquer marcador remanescente no mapa especificado
  map.eachLayer((layer) => {
    if (layer instanceof L.Marker) {
      map.removeLayer(layer);
    }
  });
}

export function invalidateMapSize(mapId = 'map') {
  const map = maps[mapId];
  if (!map) { return; }

  requestAnimationFrame(() => {
    requestAnimationFrame(() => {
      map.invalidateSize();
    });
  });
}

export function initializeEditMarker(mapId = 'map-edit') {
  const map = maps[mapId];
  if (!map) return;

  const formEdit = document.getElementById('araucariaForm-edit');
  if (!formEdit) return;

  const latInput = formEdit.querySelector('#latitude-edit') || formEdit.querySelector('[name="latitude"]') || formEdit.querySelector('[id^="latitude"]');
  const lngInput = formEdit.querySelector('#longitude-edit') || formEdit.querySelector('[name="longitude"]') || formEdit.querySelector('[id^="longitude"]');

  if (!latInput || !lngInput || !latInput.value || !lngInput.value) {
    return;
  }

  const lat = Number(latInput.value);
  const lng = Number(lngInput.value);

  if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
    return;
  }

  map.setView([lat, lng], 15);

  if (clickMarkers[mapId]) {
    map.removeLayer(clickMarkers[mapId]);
  }

  const editMarker = L.marker([lat, lng], {
    draggable: true,
  }).addTo(map);

  editMarker.on('moveend', (e) => handleMarkerMove(e, mapId));
  clickMarkers[mapId] = editMarker;
}

export function updateMarkerFromInputs(mapId) {
  const map = maps[mapId];
  if (!map) return;

  const sufixo = mapId === 'map-edit' ? 'edit' : (mapId === 'map-create' ? 'create' : 'create');
  const form = document.getElementById(`araucariaForm-${sufixo}`);
  if (!form) return;

  const latInput = form.querySelector(`#latitude-${sufixo}`) || form.querySelector('[name="latitude"]') || form.querySelector('[id^="latitude"]');
  const lngInput = form.querySelector(`#longitude-${sufixo}`) || form.querySelector('[name="longitude"]') || form.querySelector('[id^="longitude"]');
  if (!latInput || !lngInput || !latInput.value || !lngInput.value) return;

  const lat = Number(latInput.value);
  const lng = Number(lngInput.value);

  if (!Number.isFinite(lat) || !Number.isFinite(lng)) return;

  const latlng = L.latLng(lat, lng);
  map.setView(latlng, 15);

  if (clickMarkers[mapId]) {
    clickMarkers[mapId].setLatLng(latlng);
  } else {
    const clickMarker = L.marker(latlng, {
      draggable: true,
    }).addTo(map);
    clickMarker.on('moveend', (e) => handleMarkerMove(e, mapId));
    clickMarkers[mapId] = clickMarker;
  }
}

export function updateMarkerPosition(lat, lng, mapId) {
  const map = maps[mapId];
  if (!map) return;

  const latlng = L.latLng(Number(lat), Number(lng));
  map.setView(latlng, 15);

  if (clickMarkers[mapId]) {
    clickMarkers[mapId].setLatLng(latlng);
  } else {
    const clickMarker = L.marker(latlng, {
      draggable: true,
    }).addTo(map);
    clickMarker.on('moveend', (e) => handleMarkerMove(e, mapId));
    clickMarkers[mapId] = clickMarker;
  }
}