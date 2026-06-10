import { hideSpinner, showErrorMessage } from '../utils.js';
import { gender, generateMap, generateMarkerHTML, generatePhotoPath, lifeStage, makeTiles } from './utils/maps.js';

const apiUrl = '/api/observations';
const tiles = makeTiles();
const latlng = L.latLng(-25.4323, -49.2712);
const map = generateMap('map', latlng, tiles);
const markers = L.markerClusterGroup({
  iconCreateFunction: function (cluster) {
    const childCount = cluster.getChildCount();

    return new L.DivIcon({
      html: `<div class="araucaria-cluster"><span>${childCount}</span></div>`,
      className: 'custom-cluster-container',
      iconSize: new L.Point(50, 50)
    });
  }
});

async function loadObservations() {
  try {
    const response = await fetch(apiUrl);
    if (!response.ok) throw new Error('Erro na requisição');

    const data = await response.json();
    const observations = data.data;
    const markerList = [];
    

    observations.forEach(obs => {
      // Ignorar por falta de coordenadas:
      if (!obs.latitude || !obs.longitude) { return; }

      const formattedLifeStage = lifeStage[obs.stage] || obs.stage;
      const formattedGender = gender[obs.gender] || obs.gender;
      const araucariaId = obs.id;
      let photoPath = obs.photo_path;
      let imageHtml = '';

      if (photoPath) {
        let newPhotoPath = generatePhotoPath(photoPath);
        imageHtml = newPhotoPath;
      }

      const markerHTML = generateMarkerHTML(formattedLifeStage, formattedGender, araucariaId, imageHtml);
      const marker = L.marker([obs.latitude, obs.longitude]).bindPopup(markerHTML);

      markerList.push(marker);
    });

    markers.addLayers(markerList);
    map.addLayer(markers);
  } catch (error) {
    showErrorMessage(error);
  } finally {
    hideSpinner('mapSpinner');
  }
}

loadObservations();