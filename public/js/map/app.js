import { setupFormListener } from './form/index.js';
import { initializeEditMarker, initMap, invalidateMapSize, reloadGlobalMapPoints, destroyMap } from './map.js';
import { handleSelecaoImagem } from './form/imagem.js';

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

window.addEventListener('destroy-map', event => {
  const { mapId } = event.detail || {};
  if (mapId) {
    destroyMap(mapId);
  }
});

window.addEventListener('reload-map', async event => {
  const { mapId = 'map' } = event.detail || {};
  await reloadGlobalMapPoints(mapId);
});

window.addEventListener('process-image-exif', async event => {
  const { isChecked, file, formElement, mapId } = event.detail || {};
  if (formElement) {
    await handleSelecaoImagem(isChecked, file, formElement, mapId);
  }
});