import { setupFormListener } from './form/index.js';
import { initializeEditMarker, initMap, invalidateMapSize, reloadGlobalMapPoints } from './map.js';

document.addEventListener('DOMContentLoaded', () => {
  setupFormListener();
});

window.addEventListener('mudar-aba', async event => {
  const abaAtiva = event.detail;
  let mapId = null;

  if (abaAtiva === 'mapa-mundi') {
    mapId = 'map';
  } else if (abaAtiva === 'create') {
    if (document.getElementById('map-create')) {
      mapId = 'map-create';
    } else if (document.getElementById('map')) {
      mapId = 'map';
    }
  } else if (abaAtiva === 'edit') {
    if (document.getElementById('map-edit')) {
      mapId = 'map-edit';
    }
  }

  if (mapId) {
    await initMap(mapId);
    requestAnimationFrame(() => {
      invalidateMapSize(mapId);
      if (abaAtiva === 'mapa-mundi') {
        reloadGlobalMapPoints(mapId);
      } else if (abaAtiva === 'edit') {
        initializeEditMarker(mapId);
      }
    });
  }

  setTimeout(() => {
    setupFormListener();
  }, 100);
});