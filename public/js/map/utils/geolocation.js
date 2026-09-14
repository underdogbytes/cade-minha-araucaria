import { DEFAULT_CENTER, DEFAULT_ZOOM } from './maps.js';

/**
 * Constantes de geolocalização
 */
const USER_ZOOM = 14;
const GEOLOCATION_TIMEOUT = 8000;
const LOCALSTORAGE_KEY = 'user_last_location';

/**
 * Tenta obter a localização atual do usuário via navigator.geolocation.
 * @returns {Promise<{lat: number, lng: number} | null>}
 */
export function getUserLocation() {
  return new Promise((resolve) => {
    if (!navigator.geolocation) {
      console.warn('[Geolocation] navigator.geolocation não disponível.');
      resolve(null);
      return;
    }

    navigator.geolocation.getCurrentPosition(
      (position) => {
        const location = {
          lat: position.coords.latitude,
          lng: position.coords.longitude,
        };

        // Salvar no localStorage para carregamentos futuros
        try {
          localStorage.setItem(LOCALSTORAGE_KEY, JSON.stringify(location));
        } catch (e) {
          // localStorage pode estar indisponível (modo privado, etc.)
        }

        console.info(`[Geolocation] Localização obtida: ${location.lat}, ${location.lng}`);
        resolve(location);
      },
      (error) => {
        console.warn('[Geolocation] Permissão negada ou erro:', error.message);
        resolve(null);
      },
      {
        enableHighAccuracy: false,
        timeout: GEOLOCATION_TIMEOUT,
        maximumAge: 300000, // 5 minutos de cache
      }
    );
  });
}

/**
 * Retorna a última localização salva no localStorage, ou null.
 * @returns {{lat: number, lng: number} | null}
 */
export function getCachedLocation() {
  try {
    const cached = localStorage.getItem(LOCALSTORAGE_KEY);
    if (cached) {
      const parsed = JSON.parse(cached);
      if (parsed && typeof parsed.lat === 'number' && typeof parsed.lng === 'number') {
        return parsed;
      }
    }
  } catch (e) {
    // localStorage indisponível ou dados corrompidos
  }
  return null;
}

/**
 * Determina o centro inicial do mapa.
 * Prioridade: 1) cache localStorage (instantâneo), 2) geolocalização (async), 3) Curitiba (fallback).
 * @returns {Promise<{center: [number, number], zoom: number, isUserLocation: boolean}>}
 */
export async function getInitialCenter() {
  // Tenta localização em tempo real
  const location = await getUserLocation();

  if (location) {
    return {
      center: [location.lat, location.lng],
      zoom: USER_ZOOM,
      isUserLocation: true,
    };
  }

  return {
    center: DEFAULT_CENTER,
    zoom: DEFAULT_ZOOM,
    isUserLocation: false,
  };
}

/**
 * Adiciona um marcador circular azul pulsante na posição do usuário.
 * @param {L.Map} map
 * @param {number} lat
 * @param {number} lng
 * @returns {L.LayerGroup}
 */
export function addUserLocationMarker(map, lat, lng) {
  const userLocationGroup = L.layerGroup();

  // Círculo de precisão (halo externo)
  const pulseCircle = L.circleMarker([lat, lng], {
    radius: 18,
    color: '#4285F4',
    fillColor: '#4285F4',
    fillOpacity: 0.15,
    weight: 1,
    opacity: 0.3,
    className: 'user-location-pulse',
  });

  // Ponto central sólido
  const centerDot = L.circleMarker([lat, lng], {
    radius: 7,
    color: '#ffffff',
    fillColor: '#4285F4',
    fillOpacity: 1,
    weight: 2.5,
    opacity: 1,
  });

  centerDot.bindTooltip('Você está aqui', {
    direction: 'top',
    offset: [0, -10],
    className: 'user-location-tooltip',
  });

  userLocationGroup.addLayer(pulseCircle);
  userLocationGroup.addLayer(centerDot);
  userLocationGroup.addTo(map);

  return userLocationGroup;
}

/**
 * Adiciona botão de controle "Minha Localização" no mapa.
 * @param {L.Map} map
 */
export function addLocateControl(map) {
  const LocateControl = L.Control.extend({
    options: {
      position: 'topright',
    },

    onAdd: function () {
      const container = L.DomUtil.create('div', 'leaflet-bar leaflet-control user-locate-control');
      const button = L.DomUtil.create('a', 'user-locate-button', container);

      button.href = '#';
      button.title = 'Ir para minha localização';
      button.innerHTML = '📍';
      button.setAttribute('role', 'button');
      button.setAttribute('aria-label', 'Ir para minha localização');

      L.DomEvent.disableClickPropagation(container);
      L.DomEvent.on(button, 'click', async function (e) {
        L.DomEvent.preventDefault(e);

        button.innerHTML = '⏳';
        const location = await getUserLocation();

        if (location) {
          map.flyTo([location.lat, location.lng], USER_ZOOM, {
            duration: 1.5,
          });
          // Remover marcador anterior se existir
          map.eachLayer((layer) => {
            if (layer._userLocationGroup) {
              map.removeLayer(layer);
            }
          });
          const markerGroup = addUserLocationMarker(map, location.lat, location.lng);
          markerGroup._userLocationGroup = true;
        } else {
          console.warn('[Geolocation] Não foi possível obter a localização.');
        }

        button.innerHTML = '📍';
      });

      return container;
    },
  });

  map.addControl(new LocateControl());
}

/**
 * Centraliza o mapa na localização do usuário com animação flyTo.
 * Se conseguir, adiciona marcador azul e retorna true.
 * @param {L.Map} map
 * @returns {Promise<boolean>}
 */
export async function flyToUserLocation(map) {
  // Primeiro tenta cache para posicionar rapidamente
  const cached = getCachedLocation();
  if (cached) {
    map.setView([cached.lat, cached.lng], USER_ZOOM);
    addUserLocationMarker(map, cached.lat, cached.lng);
  }

  // Depois tenta localização real (mais precisa)
  const location = await getUserLocation();

  if (location) {
    if (cached) {
      // Se já moveu para cache, fazer flyTo suave para posição real
      map.flyTo([location.lat, location.lng], USER_ZOOM, { duration: 1 });
    } else {
      // Primeira vez — animação mais visível
      map.flyTo([location.lat, location.lng], USER_ZOOM, { duration: 1.5 });
    }

    // Limpar marcador do cache e adicionar na posição real
    map.eachLayer((layer) => {
      if (layer instanceof L.CircleMarker) {
        map.removeLayer(layer);
      }
    });
    addUserLocationMarker(map, location.lat, location.lng);
    return true;
  }

  return !!cached;
}
