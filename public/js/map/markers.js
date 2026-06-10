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
  const araucariaId = observation.id;
  const observerName = escapeHtml(observation.observer || 'Desconecido');
  const araucariaPhoto = observation.photo_path ? escapeHtml(observation.photo_path) : null;

  const imageHtml = observation.photo_path
    ? `
      <img
        src="${escapeHtml(observation.photo_path)}"
        alt="Araucária"
        style="width: 100%; max-width: 140px; margin-top: 8px; border-radius: 4px; border: 1px solid #ddd;"
      >
    `
    : '';

  marker.bindPopup(`
    <div style="font-family: sans-serif; min-width: 150px;">
      <h4 style="margin: 0 0 5px 0; color: #1b4332;">Araucária Registrada</h4>
      <b>Estágio:</b> ${stage}<br>
      <b>Gênero:</b> ${gender}<br>
      <a href="/observations/${araucariaId}"
        class="inline-block bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-2 py-1 rounded transition text-center w-full"
        style="color: white!important">
        Ver detalhes completos →
      </a>
      <small style="color: #666;">Por: ${observerName}</small><br>
      ${ imageHtml }
    </div>
  `);

  return marker;
}