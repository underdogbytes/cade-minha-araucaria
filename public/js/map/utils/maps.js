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
 * Configuração visual de cada status de observação da comunidade
 */
export const STATUS_CONFIG = {
  normal: {
    key: 'normal',
    label: 'Normal / Saudável',
    color: '#1b4332',
    class: 'normal',
    iconSvg: (size = 15) => `
      <svg viewBox="0 0 24 24" fill="currentColor" style="width: ${size}px; height: ${size}px; display: block;">
        <path d="M12 2C11.4 2 11 2.4 11 3V5.5C9.2 5.6 7.6 6.4 6.4 7.7C6.1 8.1 6.1 8.7 6.5 9C6.9 9.3 7.5 9.3 7.8 8.9C8.8 7.8 10.3 7.1 11.8 7.1V10C9.8 10.2 8 11.2 6.8 12.8C6.5 13.2 6.6 13.8 7 14.1C7.4 14.4 8 14.3 8.3 13.9C9.2 12.7 10.6 12 12 12C13.4 12 14.8 12.7 15.7 13.9C16 14.3 16.6 14.4 17 14.1C17.4 13.8 17.5 13.2 17.2 12.8C16 11.2 14.2 10.2 12.2 10V7.1C13.7 7.1 15.2 7.8 16.2 8.9C16.5 9.3 17.1 9.3 17.5 9C17.9 8.7 17.9 8.1 17.6 7.7C16.4 6.4 14.8 5.6 13 5.5V3C13 2.4 12.6 2 12 2ZM11 14.5V22H13V14.5H11Z"/>
      </svg>
    `,
  },
  protegida: {
    key: 'protegida',
    label: 'Protegida',
    color: '#059669',
    class: 'protegida',
    iconSvg: (size = 15) => `
      <svg viewBox="0 0 24 24" fill="currentColor" style="width: ${size}px; height: ${size}px; display: block;">
        <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-1 16l-4-4 1.41-1.41L11 14.17l6.59-6.59L19 9l-8 8z"/>
      </svg>
    `,
  },
  risco: {
    key: 'risco',
    label: 'Em Risco',
    color: '#d97706',
    class: 'risco',
    iconSvg: (size = 15) => `
      <svg viewBox="0 0 24 24" fill="currentColor" style="width: ${size}px; height: ${size}px; display: block;">
        <path d="M12 2L1 21h22L12 2zm0 3.99L19.53 19H4.47L12 5.99zM11 10v4h2v-4h-2zm0 6v2h2v-2h-2z"/>
      </svg>
    `,
  },
  doente: {
    key: 'doente',
    label: 'Doente',
    color: '#dc2626',
    class: 'doente',
    iconSvg: (size = 15) => `
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" style="width: ${size}px; height: ${size}px; display: block;">
        <path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/>
        <path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/>
      </svg>
    `,
  },
  morta: {
    key: 'morta',
    label: 'Morta',
    color: '#18181b',
    class: 'morta',
    iconSvg: (size = 15) => `
      <svg viewBox="0 0 24 24" fill="currentColor" style="width: ${size}px; height: ${size}px; display: block;">
        <path d="M12 2C6.48 2 2 6.48 2 12c0 2.85 1.2 5.41 3.12 7.24.23.22.48.42.74.6V21a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-1h4v1a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-1.16c.26-.18.51-.38.74-.6C20.8 17.41 22 14.85 22 12c0-5.52-4.48-10-10-10zm-3 11c-.83 0-1.5-.67-1.5-1.5S8.17 10 9 10s1.5.67 1.5 1.5S9.83 13 9 13zm3 4c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm3-4c-.83 0-1.5-.67-1.5-1.5S14.17 10 15 10s1.5.67 1.5 1.5S15.83 13 15 13z"/>
      </svg>
    `,
  },
};

/**
 * Gera ícone customizado de Araucária em DivIcon conforme status da comunidade.
 * Suporta estados individuais e empates bicolores 50/50 (ex: 'doente_risco', 'normal_protegida').
 * @param {string} type
 * @returns {L.DivIcon}
 */
export function generateAraucariaIcon(type = 'normal') {
  const normalizedType = String(type || 'normal').toLowerCase();

  // Verifica se é um empate com duas categorias (ex: 'doente_risco', 'normal_protegida')
  if (normalizedType.includes('_') || normalizedType.includes('-')) {
    const parts = normalizedType.split(/[_-]/);
    const key1 = parts[0];
    const key2 = parts[1];
    const cfg1 = STATUS_CONFIG[key1] || STATUS_CONFIG.normal;
    const cfg2 = STATUS_CONFIG[key2] || STATUS_CONFIG.risco;

    const html = `
      <div class="araucaria-pin araucaria-pin-bicolor"
           style="background: linear-gradient(90deg, ${cfg1.color} 0%, ${cfg1.color} 50%, ${cfg2.color} 50%, ${cfg2.color} 100%);"
           title="Empate comunitário: ${cfg1.label} / ${cfg2.label}">
        <div class="pin-half left">${cfg1.iconSvg(11)}</div>
        <div class="pin-divider"></div>
        <div class="pin-half right">${cfg2.iconSvg(11)}</div>
      </div>
    `;

    return L.divIcon({
      html,
      className: 'custom-araucaria-pin-container',
      iconSize: [32, 32],
      iconAnchor: [16, 16],
      popupAnchor: [0, -18],
    });
  }

  const cfg = STATUS_CONFIG[normalizedType] || STATUS_CONFIG.normal;
  const html = `<div class="araucaria-pin ${cfg.class}">${cfg.iconSvg(15)}</div>`;

  return L.divIcon({
    html,
    className: 'custom-araucaria-pin-container',
    iconSize: [32, 32],
    iconAnchor: [16, 16],
    popupAnchor: [0, -18],
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