import { setupFormListener } from './form.js';
import { initMap, invalidateMapSize } from './map.js';

document.addEventListener('DOMContentLoaded', () => {
  setupFormListener();
});

window.addEventListener('mudar-aba', async event => {
  const abaAtiva = event.detail;

  if (abaAtiva !== 'create') { return; }
  if (!document.getElementById('map')) { return; }

  await initMap();

  invalidateMapSize();
});