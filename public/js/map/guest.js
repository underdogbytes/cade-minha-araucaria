import { hideSpinner, showErrorMessage, showSpinner } from '../utils.js';
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

showSpinner('mapSpinner');
fetch(apiUrl)
  .then(response => {
    if(!response.ok) {
      throw new Error('Erro ao carregar observações');
    }
    return response.json();
  })
  .then(({data}) => {
    const observations = data;

    observations.forEach(obs => {
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

      markers.addLayer(marker);
    });

    map.addLayer(markers);
  })
  .catch(error => {
    showErrorMessage(error);  
  })
  .finally(() => {
    hideSpinner('mapSpinner');
  });