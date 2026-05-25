import { fetchObservations } from './api.js';
import { addObservationMarker } from './markers.js';

let maps = {};
let clickMarkers = {};

export async function initMap(mapId = 'map') {
  if (maps[mapId]) { return maps[mapId]; }

  const mapElement = document.getElementById(mapId);
  if (!mapElement) {
    return null;
  }

  const map = L.map(mapId).setView([-25.4323, -49.2712], 12);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
  }).addTo(map);

  map.on('click', (event) => handleMapClick(event, mapId));

  maps[mapId] = map;

  if (mapId !== 'map-create' && mapId !== 'map-edit') {
    await loadExistingPoints(map);
  }

  return map;
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

  updateCoordinates(position.lat, position.lng);
}

function updateCoordinates(lat, lng, mapId) {
  // Descobre qual o sufixo do formulário correspondente ao mapa clicado
  const sufixo = mapId === 'map-edit' ? 'edit' : 'create';
  const form = document.getElementById(`araucariaForm-${sufixo}`);

  if (!form) return;

  // Busca os inputs especificamente de dentro daquele formulário
  const latInput = form.querySelector('#latitude');
  const lngInput = form.querySelector('#longitude');

  if (!latInput || !lngInput) return;

  const latValue = Number(lat).toFixed(6);
  const lngValue = Number(lng).toFixed(6);

  latInput.value = latValue;
  lngInput.value = lngValue;

  latInput.dispatchEvent(new Event('input', { bubbles: true }));
  lngInput.dispatchEvent(new Event('input', { bubbles: true }));
}

async function loadExistingPoints(map) {
  try {
    const response = await fetchObservations();
    const observations = response.data || response;

    if (Array.isArray(observations)) {
      observations.forEach(observation => {
        addObservationMarker(map, observation);
      });
    }

  } catch (error) {
    // TODO
  }
}

export function addNewObservationToMap(observation, mapId = 'map-create') { 
  const map = maps[mapId] || maps['map'] || Object.values(maps)[0];
  if (!map) { return; }
  addObservationMarker(map, observation);
}

export function clearClickMarker(mapId = 'map-create') {
  const map = maps[mapId];
  const clickMarker = clickMarkers[mapId];

  if (!clickMarker || !map) { return; }

  map.removeLayer(clickMarker);
  clickMarkers[mapId] = null;
}

export function invalidateMapSize(mapId = 'map') {
  const map = maps[mapId];
  if (!map) { return; }

  requestAnimationFrame(() => {
    map.invalidateSize();
  });
}

export function initializeEditMarker(mapId = 'map-edit') {
  const map = maps[mapId];
  if (!map) return;

  const formEdit = document.getElementById('araucariaForm-edit');
  if (!formEdit) return;

  const latInput = formEdit.querySelector('#latitude');
  const lngInput = formEdit.querySelector('#longitude');

  if (!latInput || !lngInput || !latInput.value || !lngInput.value) {
    return;
  }

  const lat = Number(latInput.value);
  const lng = Number(lngInput.value);

  if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
    return;
  }

  // Center map to the edit location
  map.setView([lat, lng], 15);

  // Place the click marker at the edit location
  if (clickMarkers[mapId]) {
    map.removeLayer(clickMarkers[mapId]);
  }

  const editMarker = L.marker([lat, lng], {
    draggable: true,
  }).addTo(map);

  editMarker.on('moveend', (e) => handleMarkerMove(e, mapId));
  clickMarkers[mapId] = editMarker;
}