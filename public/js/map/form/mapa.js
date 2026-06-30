export function atualizaPinsMapa(lat, lng, mapId) {
  if (window.updateMarkerPosition) {
    window.updateMarkerPosition(lat, lng, mapId);
  }
}

export function atualizaCoordenadas(formElement, coordenadas, mapId) {
  const latInput = formElement.querySelector('[name="latitude') || document.getElementById('latitude');
  const lngInput = formElement.querySelector('[name="longitude"]') || document.getElementById('longitude');

  if (latInput) {
    latInput.value = coordenadas.lat;
    latInput.dispatchEvent(new Event('input', { bubbles: true }));
    latInput.dispatchEvent(new Event('change', { bubbles: true }));
  }
  if (lngInput) {
    lngInput.value = coordenadas.lng;
    lngInput.dispatchEvent(new Event('input', { bubbles: true }));
    lngInput.dispatchEvent(new Event('change', { bubbles: true }));
  }

  // Atualiza o pin no mapa:
  atualizaPinsMapa(coordenadas.lat, coordenadas.lng, mapId);
}

export function limparPinsMapa(mapId) {
  if (window.clearClickMarker) {
    window.clearClickMarker(mapId);
  }
}