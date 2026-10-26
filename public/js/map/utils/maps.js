/**
 * Constantes padrão do mapa
 */
export const DEFAULT_CENTER = [-25.4323, -49.2712];
export const DEFAULT_ZOOM = 12;

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
 * @param {string} style - 'osm' | 'topo' | 'satellite'
 * @returns Leaflet tile layer
 */
export function makeTiles(style = 'osm') {
  if (style === 'satellite') {
    return L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
      maxZoom: 19,
      attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'
    });
  }
  if (style === 'topo') {
    // Mapa topográfico/verde suave do Esri, destaca relevo e vegetação perfeitamente
    return L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}', {
      maxZoom: 19,
      attribution: 'Tiles &copy; Esri'
    });
  }
  // OpenStreetMap padrão: gratuito, estável, sem necessidade de API key
  return L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
  });
}

/**
 * Gera ícone customizado de Araucária em DivIcon conforme mockup
 * @param {'normal' | 'risco' | 'protegida'} type
 * @returns {L.DivIcon}
 */
export function generateAraucariaIcon(type = 'normal') {
  let modifierClass = '';
  let iconSvg = '';

  if (type === 'risco') {
    modifierClass = 'risco';
    // Ícone de alerta / risco
    iconSvg = `
      <svg viewBox="0 0 24 24" fill="currentColor" style="width: 15px; height: 15px; display: block;">
        <path d="M12 2L1 21h22L12 2zm0 3.99L19.53 19H4.47L12 5.99zM11 10v4h2v-4h-2zm0 6v2h2v-2h-2z"/>
      </svg>
    `;
  } else if (type === 'protegida') {
    modifierClass = 'protegida';
    // Ícone de escudo protetor
    iconSvg = `
      <svg viewBox="0 0 24 24" fill="currentColor" style="width: 15px; height: 15px; display: block;">
        <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-1 16l-4-4 1.41-1.41L11 14.17l6.59-6.59L19 9l-8 8z"/>
      </svg>
    `;
  } else {
    // Silhueta clássica de copa em candelabro da Araucária
    iconSvg = `
      <svg viewBox="0 0 24 24" fill="currentColor" style="width: 15px; height: 15px; display: block;">
        <path d="M12 2C11.4 2 11 2.4 11 3V5.5C9.2 5.6 7.6 6.4 6.4 7.7C6.1 8.1 6.1 8.7 6.5 9C6.9 9.3 7.5 9.3 7.8 8.9C8.8 7.8 10.3 7.1 11.8 7.1V10C9.8 10.2 8 11.2 6.8 12.8C6.5 13.2 6.6 13.8 7 14.1C7.4 14.4 8 14.3 8.3 13.9C9.2 12.7 10.6 12 12 12C13.4 12 14.8 12.7 15.7 13.9C16 14.3 16.6 14.4 17 14.1C17.4 13.8 17.5 13.2 17.2 12.8C16 11.2 14.2 10.2 12.2 10V7.1C13.7 7.1 15.2 7.8 16.2 8.9C16.5 9.3 17.1 9.3 17.5 9C17.9 8.7 17.9 8.1 17.6 7.7C16.4 6.4 14.8 5.6 13 5.5V3C13 2.4 12.6 2 12 2ZM11 14.5V22H13V14.5H11Z"/>
      </svg>
    `;
  }

  return L.divIcon({
    html: `<div class="araucaria-pin ${modifierClass}">${iconSvg}</div>`,
    className: 'custom-araucaria-pin-container',
    iconSize: [32, 32],
    iconAnchor: [16, 16],
    popupAnchor: [0, -18]
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
    zoom: DEFAULT_ZOOM,
    zoomControl: false, // Usamos controles customizados na interface
    layers: [tiles]
  });
}

/**
 * Gera HTML para popup do marker
 * @param string stage 
 * @param string gender
 * @param int araucariaId
 * @param string imageHtml
 * @param string|null observer
 * @returns string HTML do popup
 */
export function generateMarkerHTML(stage, gender, araucariaId, imageHtml, observer = null) {
  const observerHtml = observer
    ? `<div style="margin: 2px 0 6px 0; font-size: 11px; color: #4b5563;">
         <b>Colaborador:</b> <span style="color: #1b4332; font-weight: 700;">@${observer}</span>
       </div>`
    : '';

  return `
    <div style="font-family: sans-serif; min-width: 160px; line-height: 1.4;">
      <h4 style="margin: 0 0 4px 0; color: #1b4332; font-size: 14px; font-weight: bold;">Araucária Registrada</h4>
      ${observerHtml}
      <div style="font-size: 12px; margin-bottom: 6px;">
        <b>Estágio:</b> ${stage}<br>
        <b>Gênero:</b> ${gender}
      </div>
      <a href="/observations/${araucariaId}"
        class="inline-block bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-3 py-1.5 rounded transition text-center w-full"
        style="color: white!important; text-decoration: none; display: block; margin-bottom: 6px;">
        Ver detalhes completos →
      </a>
      ${imageHtml}
    </div>
  `;
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
  } else if (!photoPath.startsWith('http') && !photoPath.startsWith('/')) {
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