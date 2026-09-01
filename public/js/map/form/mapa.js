import { updateMarkerPosition, clearClickMarker } from '../map.js';

export function atualizaPinsMapa(lat, lng, mapId) {
  updateMarkerPosition(lat, lng, mapId);
}

export function atualizaCoordenadas(formElement, coordenadas, mapId) {
  if (!formElement) return;

  const latInput = formElement.querySelector('[name="latitude"]') || formElement.querySelector('[id^="latitude"]');
  const lngInput = formElement.querySelector('[name="longitude"]') || formElement.querySelector('[id^="longitude"]');

  if (latInput) {
    latInput.value = coordenadas.lat;
    latInput.dispatchEvent(new Event('input', { bubbles: true }));
  }
  if (lngInput) {
    lngInput.value = coordenadas.lng;
    lngInput.dispatchEvent(new Event('input', { bubbles: true }));
  }

  // Atualiza o pin no mapa:
  atualizaPinsMapa(coordenadas.lat, coordenadas.lng, mapId);
}

export function limparPinsMapa(mapId) {
  clearClickMarker(mapId);
}