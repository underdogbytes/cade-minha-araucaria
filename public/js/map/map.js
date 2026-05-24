import { fetchObservations } from './api.js';
import { addObservationMarker } from './markers.js';

let map = null;
let clickMarker = null;

export async function initMap() {
  if (map) { return map; }

  map = L.map('map').setView([-25.4323, -49.2712], 12);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
  }).addTo(map);

  map.on('click', handleMapClick);

  await loadExistingPoints();

  return map;
}

function handleMapClick(event) {
  const { lat, lng } = event.latlng;

  updateCoordinates(lat, lng);

  if (clickMarker) {
    clickMarker.setLatLng(event.latlng);
    return;
  }

  clickMarker = L.marker(event.latlng, {
    draggable: true,
  }).addTo(map);

  clickMarker.on('moveend', handleMarkerMove);
}

function handleMarkerMove(event) {
  const position = event.target.getLatLng();

  updateCoordinates(position.lat, position.lng);
}

function updateCoordinates(lat, lng) {
  const latInput = document.getElementById('latitude');
  const lngInput = document.getElementById('longitude');

  if (!latInput || !lngInput) { return; }

  latInput.value = Number(lat).toFixed(6);
  lngInput.value = Number(lng).toFixed(6);
}

async function loadExistingPoints() {
  try {
    const response = await fetchObservations();
    const observations = response.data || response;

    observations.forEach(observation => {
      addObservationMarker(map, observation);
    });

  } catch (error) {
    console.error(error);
  }
}

export function addNewObservationToMap(observation) {
  if (!map) { return; }
  addObservationMarker(map, observation);
}

export function clearClickMarker() {
  if (!clickMarker || !map) { return; }

  map.removeLayer(clickMarker);

  clickMarker = null;
}

export function invalidateMapSize() {
  if (!map) { return; }

  requestAnimationFrame(() => {
    map.invalidateSize();
  });
}