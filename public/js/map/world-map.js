import { hideSpinner } from '../utils.js';
import { addUserLocationMarker, flyToUserLocation, getUserLocation } from './utils/geolocation.js';
import { DEFAULT_CENTER, gender, generateAraucariaIcon, generateMap, generateMarkerHTML, generatePhotoPath, lifeStage, makeTiles } from './utils/maps.js';

const apiUrl = '/api/observations?all=1';
let currentTileStyle = 'osm';
let currentTileLayer = makeTiles('osm');
const latlng = L.latLng(DEFAULT_CENTER[0], DEFAULT_CENTER[1]);
const map = generateMap('map', latlng, currentTileLayer);

// Invalidação periódica e defensiva de dimensões para garantir renderização perfeita
function invalidateMapSize() {
  if (map) {
    map.invalidateSize();
  }
}

setTimeout(invalidateMapSize, 100);
setTimeout(invalidateMapSize, 400);
setTimeout(invalidateMapSize, 1000);

if (window.ResizeObserver) {
  const mapElem = document.getElementById('map');
  if (mapElem) {
    new ResizeObserver(() => invalidateMapSize()).observe(mapElem);
  }
}

// Grupos de camadas
const markersGroup = L.markerClusterGroup({
  showCoverageOnHover: false,
  maxClusterRadius: 40,
  iconCreateFunction: function (cluster) {
    const childCount = cluster.getChildCount();
    return new L.DivIcon({
      html: `<div class="araucaria-cluster"><span>${childCount}</span></div>`,
      className: 'custom-cluster-container',
      iconSize: new L.Point(46, 46)
    });
  }
});

let allMarkers = [];

// Atualiza o card flutuante com dados da observação clicada
function updateFloatingCard(obs) {
  const card = document.getElementById('hero-floating-obs');
  const imgElem = document.getElementById('floating-obs-img');
  const titleElem = document.getElementById('floating-obs-title');
  const subtitleElem = document.getElementById('floating-obs-subtitle');
  const badgeElem = document.getElementById('floating-obs-badge');

  if (!card) return;

  if (titleElem) {
    titleElem.textContent = obs.species_name || 'Araucária angustifolia';
  }

  if (subtitleElem) {
    const dateStr = obs.observed_at ? new Date(obs.observed_at).toLocaleDateString('pt-BR') : 'Data recente';
    const observerHandle = obs.observer ? `@${obs.observer}` : 'Colaborador comunitário';
    subtitleElem.textContent = `${observerHandle} • ${dateStr}`;
  }

  if (badgeElem) {
    const stageName = lifeStage[obs.stage] || obs.stage || 'Saudável';
    badgeElem.textContent = stageName;
    badgeElem.className = 'inline-block rounded-full px-2 py-0.5 text-[10px] font-semibold mt-0.5 ' +
      (obs.stage === 'dead' ? 'bg-red-100 text-red-800' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300');
  }

  if (imgElem) {
    let src = obs.photo_path || (obs.photos && obs.photos.length > 0 ? (obs.photos[0].url || obs.photos[0].photo_path) : null);
    if (!src) {
      src = '/images/auth-bg.jpeg';
    } else if (!src.startsWith('http') && !src.startsWith('data:image') && !src.startsWith('/')) {
      src = '/storage/' + src;
    }
    imgElem.src = src;
  }

  card.onclick = () => {
    if (obs.id) {
      window.location.href = `/observations/${obs.id}`;
    }
  };
}

// Amostra de pontos de Curitiba para compor o mapa
const fallbackObservations = [
  { id: 101, latitude: -25.4428, longitude: -49.2394, stage: 'adult', gender: 'female', observed_at: '2025-05-12', photo_path: '/images/auth-bg.jpeg', type: 'normal', observer: 'comunidade_cwb' },
  { id: 102, latitude: -25.4262, longitude: -49.3082, stage: 'adult', gender: 'male', observed_at: '2025-05-10', photo_path: '/images/auth-bg.jpeg', type: 'protegida', observer: 'guarda_parque' },
  { id: 103, latitude: -25.3789, longitude: -49.2825, stage: 'sapling', gender: 'unknown', observed_at: '2025-05-08', photo_path: '/images/auth-bg.jpeg', type: 'normal', observer: 'ecotrilhas' },
  { id: 104, latitude: -25.4111, longitude: -49.2711, stage: 'adult', gender: 'female', observed_at: '2025-05-05', photo_path: '/images/auth-bg.jpeg', type: 'protegida', observer: 'sos_mata' },
  { id: 105, latitude: -25.4419, longitude: -49.2882, stage: 'dead', gender: 'unknown', observed_at: '2025-05-02', photo_path: '/images/auth-bg.jpeg', type: 'risco', observer: 'alerta_ambiental' },
  { id: 106, latitude: -25.4608, longitude: -49.3289, stage: 'adult', gender: 'female', observed_at: '2025-04-28', photo_path: '/images/auth-bg.jpeg', type: 'normal', observer: 'pinhao_sul' },
  { id: 107, latitude: -25.5347, longitude: -49.2064, stage: 'adult', gender: 'male', observed_at: '2025-04-20', photo_path: '/images/auth-bg.jpeg', type: 'normal', observer: 'araucaria_sjp' },
  { id: 108, latitude: -25.5925, longitude: -49.4072, stage: 'adult', gender: 'female', observed_at: '2025-04-15', photo_path: '/images/auth-bg.jpeg', type: 'risco', observer: 'monitoramento' },
  { id: 109, latitude: -25.4871, longitude: -49.2431, stage: 'seedling', gender: 'unknown', observed_at: '2025-04-11', photo_path: '/images/auth-bg.jpeg', type: 'protegida', observer: 'plantadores' },
  { id: 110, latitude: -25.4594, longitude: -49.5278, stage: 'adult', gender: 'male', observed_at: '2025-04-05', photo_path: '/images/auth-bg.jpeg', type: 'normal', observer: 'campo_largo_verde' },
  { id: 111, latitude: -25.3905, longitude: -49.2201, stage: 'adult', gender: 'female', observed_at: '2025-05-14', photo_path: '/images/auth-bg.jpeg', type: 'normal', observer: 'bacacheri_bio' },
  { id: 112, latitude: -25.4331, longitude: -49.1919, stage: 'adult', gender: 'male', observed_at: '2025-05-15', photo_path: '/images/auth-bg.jpeg', type: 'normal', observer: 'pinhais_nativa' }
];

async function loadObservations() {
  try {
    const response = await fetch(apiUrl, {
      headers: { Accept: 'application/json' },
    });

    let realObservations = [];
    if (response.ok) {
      const data = await response.json();
      realObservations = data.data || [];
    }

    // Prioriza observações reais cadastradas por colaboradores
    let displayList = [...realObservations];

    // Se houver poucas observações reais, combina com pontos demonstrativos de Curitiba
    if (displayList.length < 8) {
      const existingIds = new Set(realObservations.map(o => o.id));
      fallbackObservations.forEach(fallback => {
        if (!existingIds.has(fallback.id)) {
          displayList.push(fallback);
        }
      });
    }

    const markerList = [];

    displayList.forEach(obs => {
      if (!obs.latitude || !obs.longitude) return;

      const formattedLifeStage = lifeStage[obs.stage] || obs.stage;
      const formattedGender = gender[obs.gender] || obs.gender;
      const araucariaId = obs.id;
      let photoPath = obs.photo_path || (obs.photos && obs.photos.length > 0 ? (obs.photos[0].url || obs.photos[0].photo_path) : null);
      let imageHtml = '';

      if (photoPath) {
        let newPhotoPath = generatePhotoPath(photoPath);
        imageHtml = newPhotoPath;
      }

      // Determinar o estilo do pin conforme o status
      let pinType = obs.type || 'normal';
      if (obs.stage === 'dead') pinType = 'risco';

      const pinIcon = generateAraucariaIcon(pinType);
      const markerHTML = generateMarkerHTML(formattedLifeStage, formattedGender, araucariaId, imageHtml, obs.observer);
      const marker = L.marker([obs.latitude, obs.longitude], { icon: pinIcon }).bindPopup(markerHTML);

      marker.obsData = obs;
      marker.pinType = pinType;

      marker.on('click', () => {
        updateFloatingCard(obs);
      });

      markerList.push(marker);
    });

    allMarkers = markerList;
    markersGroup.addLayers(markerList);
    map.addLayer(markersGroup);

    // Inicializa o card flutuante com a observação real mais recente cadastrada
    if (displayList.length > 0) {
      updateFloatingCard(displayList[0]);
    }

    invalidateMapSize();
  } catch (error) {
    console.error('[World Map Service Error] Falha ao carregar observações:', error);
    fallbackObservations.forEach(obs => {
      const pinIcon = generateAraucariaIcon(obs.type);
      const marker = L.marker([obs.latitude, obs.longitude], { icon: pinIcon });
      marker.obsData = obs;
      marker.pinType = obs.type;
      marker.on('click', () => updateFloatingCard(obs));
      markersGroup.addLayer(marker);
    });
    map.addLayer(markersGroup);
    invalidateMapSize();
  } finally {
    hideSpinner('mapSpinner');
  }
}

// Inicializar carregamento de dados
loadObservations();

// Controles Customizados de Zoom e Localização
const btnZoomIn = document.getElementById('btn-map-zoom-in');
if (btnZoomIn) {
  btnZoomIn.addEventListener('click', () => {
    map.zoomIn();
  });
}

const btnZoomOut = document.getElementById('btn-map-zoom-out');
if (btnZoomOut) {
  btnZoomOut.addEventListener('click', () => {
    map.zoomOut();
  });
}

const btnLocate = document.getElementById('btn-map-locate');
if (btnLocate) {
  btnLocate.addEventListener('click', async () => {
    btnLocate.classList.add('animate-pulse');
    await flyToUserLocation(map);
    btnLocate.classList.remove('animate-pulse');
  });
}

// Botão "Explorar o mapa" do Hero
const btnExplorar = document.getElementById('btn-explorar-mapa');
if (btnExplorar) {
  btnExplorar.addEventListener('click', (e) => {
    const mapaHero = document.getElementById('mapa-hero');
    if (mapaHero) {
      e.preventDefault();
      mapaHero.scrollIntoView({ behavior: 'smooth', block: 'center' });
      setTimeout(invalidateMapSize, 300);
    }
  });
}

// Alternar Estilo do Mapa Base
const btnToggleTiles = document.getElementById('btn-toggle-tiles');
if (btnToggleTiles) {
  btnToggleTiles.addEventListener('click', () => {
    map.removeLayer(currentTileLayer);
    if (currentTileStyle === 'osm') {
      currentTileStyle = 'topo';
      currentTileLayer = makeTiles('topo');
      btnToggleTiles.querySelector('span:last-child').textContent = 'Topo';
    } else if (currentTileStyle === 'topo') {
      currentTileStyle = 'satellite';
      currentTileLayer = makeTiles('satellite');
      btnToggleTiles.querySelector('span:last-child').textContent = 'Satélite';
    } else {
      currentTileStyle = 'osm';
      currentTileLayer = makeTiles('osm');
      btnToggleTiles.querySelector('span:last-child').textContent = 'OSM';
    }
    currentTileLayer.addTo(map);
    currentTileLayer.bringToBack();
  });
}

// Filtros de Camadas
function filterMarkers() {
  const showAll = document.getElementById('layer-toggle-all')?.checked ?? true;
  const showAraucarias = document.getElementById('layer-toggle-araucarias')?.checked ?? true;
  const showRisco = document.getElementById('layer-toggle-risco')?.checked ?? true;
  const showProtegidas = document.getElementById('layer-toggle-protegidas')?.checked ?? true;

  markersGroup.clearLayers();

  if (!showAll) return;

  const filtered = allMarkers.filter(m => {
    if (m.pinType === 'risco' && !showRisco) return false;
    if (m.pinType === 'protegida' && !showProtegidas) return false;
    if (m.pinType === 'normal' && !showAraucarias) return false;
    return true;
  });

  markersGroup.addLayers(filtered);
}

['layer-toggle-all', 'layer-toggle-araucarias', 'layer-toggle-risco', 'layer-toggle-protegidas'].forEach(id => {
  const input = document.getElementById(id);
  if (input) {
    input.addEventListener('change', () => {
      if (id === 'layer-toggle-all') {
        const val = input.checked;
        ['layer-toggle-araucarias', 'layer-toggle-risco', 'layer-toggle-protegidas'].forEach(subId => {
          const sub = document.getElementById(subId);
          if (sub) sub.checked = val;
        });
      }
      filterMarkers();
    });
  }
});

// Busca Rápida por Cidade / Região
const searchInput = document.getElementById('map-search-input');
const cityLocations = {
  'curitiba': [-25.4323, -49.2712, 12],
  'araucaria': [-25.5925, -49.4072, 12],
  'araucária': [-25.5925, -49.4072, 12],
  'sao jose': [-25.5347, -49.2064, 12],
  'são josé': [-25.5347, -49.2064, 12],
  'sao jose dos pinhais': [-25.5347, -49.2064, 12],
  'são josé dos pinhais': [-25.5347, -49.2064, 12],
  'campo largo': [-25.4594, -49.5278, 12],
  'colombo': [-25.2925, -49.2247, 12],
  'pinhais': [-25.4331, -49.1919, 12],
  'barigui': [-25.4262, -49.3082, 14],
  'botanico': [-25.4428, -49.2394, 14],
  'botânico': [-25.4428, -49.2394, 14],
  'tangua': [-25.3789, -49.2825, 14],
  'tanguá': [-25.3789, -49.2825, 14],
  'batel': [-25.4419, -49.2882, 14]
};

if (searchInput) {
  searchInput.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
      e.preventDefault();
      const term = searchInput.value.toLowerCase().trim();
      for (const [key, coords] of Object.entries(cityLocations)) {
        if (term.includes(key) || key.includes(term)) {
          map.flyTo([coords[0], coords[1]], coords[2], { duration: 1.5 });
          return;
        }
      }
      // Se não for cidade específica, centraliza em Curitiba
      map.flyTo(DEFAULT_CENTER, 12, { duration: 1.5 });
    }
  });
}

// Silenciosamente tenta obter localização do usuário para adicionar o indicador de posição
// sem alterar o enquadramento de Curitiba e das Araucárias
getUserLocation().then(location => {
  if (location && typeof location.lat === 'number' && typeof location.lng === 'number') {
    addUserLocationMarker(map, location.lat, location.lng);
  }
}).catch(() => {});