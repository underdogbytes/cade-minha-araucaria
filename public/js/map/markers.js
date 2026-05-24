import { escapeHtml } from './utils.js';

export function addObservationMarker(map, observation) {
  const lat = Number(observation.latitude);
  const lng = Number(observation.longitude);

  if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
    // TODO: erro coordenada inválida
    return;
  }

  const marker = L.marker([lat, lng]).addTo(map);

  const stage = escapeHtml(observation.stage || 'Não informado');
  const gender = escapeHtml(observation.gender || 'Não informado');

  const imageHtml = observation.photo_path
    ? `
      <img
        src="${escapeHtml(observation.photo_path)}"
        alt="Araucária"
        style="width:100px;margin-top:5px;border-radius:4px;"
      >
    `
    : '';

  marker.bindPopup(`
    <div>
      <b style="text-transform: capitalize;">Estágio: ${stage}</b><br>
      Gênero: ${gender}<br>
      ${imageHtml}
    </div>
  `);

  return marker;
}