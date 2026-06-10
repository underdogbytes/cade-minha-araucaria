/**
 * Mapas e dicionários
 */
export const lifeStage = {
  seedling: 'Muda',
  sapling: 'Jovem',
  adult: 'Adulta',
  dead: 'Morta/Cortada'
};

export const gender = {
  unknown: 'Desconhecido',
  male: 'Macho',
  female: 'Fêmea'
};

/**
 * @returns Leaflet tile layer do OpenStreetMap
 */
export function makeTiles() {
  return L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 18,
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
  });
}

/**
 * Gera instância do mapa Leaflet
 * @param string mapId 
 * @param array latlng 
 * @param int tiles 
 * @returns 
 */
export function generateMap(mapId, latlng, tiles) {
  return L.map(mapId, {
    center: latlng,
    zoom: 12,
    layers: [tiles]
  });
}

/**
 * Gera HTML para popup do marker
 * @param string stage 
 * @param string gender
 * @param int araucariaId
 * @param string imageHtml
 * @returns string HTML do popup
 */
export function generateMarkerHTML(stage, gender, araucariaId, imageHtml) {
  return `
    <div style="font-family: sans-serif; min-width: 150px;">
      <h4 style="margin: 0 0 5px 0; color: #1b4332;">Araucária Registrada</h4>
      <b>Estágio:</b> ${stage}<br>
      <b>Gênero:</b> ${gender}<br>
      <a href="/observations/${araucariaId}"
        class="inline-block bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-2 py-1 rounded transition text-center w-full"
        style="color: white!important">
        Ver detalhes completos →
      </a>
      ${imageHtml}
    </div>
  `
}

/**
 * Gera caminho para foto
 * @param string photoPath
 * @returns string caminho para foto
 */
export function generatePhotoPath(photoPath) {
  if (photoPath.includes('data:image')) {
    const indiceBase64 = photoPath.indexOf('data:image');
    photoPath = photoPath.substring(indiceBase64);
  } else if (!photoPath.startsWith('http') && !photoPath.startsWith('/storage')) {
    photoPath = '/storage/' + photoPath;
  }

  // TODO: transformar style em classe
  return `
    <img src="${photoPath}"
      alt="Araucária"
      style="width: 100%;
        max-width: 140px;
        margin-top: 8px;
        border-radius: 4px;
        border: 1px solid #ddd;"
    >
  `;
}