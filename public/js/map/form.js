// form.js
import { dispatchAlert } from './alerts.js';
import { createObservation, deleteObservation } from './api.js';
import { addNewObservationToMap, clearClickMarker } from './map.js';

let isSubmitting = false;

export function setupFormListener() {
  window.removeEventListener('submit', handleGlobalSubmit);
  window.addEventListener('submit', handleGlobalSubmit);
}

async function handleGlobalSubmit(event) {
  const form = event.target.closest('form');

  if (!form || !form.id || !form.id.startsWith('araucariaForm-')) {
    return;
  }

  event.preventDefault();

  if (isSubmitting) {
    // TODO: mostrar mensagem de "Aguarde, salvando..." ou "Envio bloqueado: já existe uma requisição em andamento."
    return;
  }

  const submitButton = form.querySelector('button[type="submit"]');

  try {
    isSubmitting = true;
    toggleSubmitButton(submitButton, true);
    validateImage(form);

    const response = await createObservation(form);
    const observation = response.data || response;
    const successMessage = response.message || 'Observação salva com sucesso!';

    if (observation) {
      const currentMapId = form.id === 'araucariaForm-edit' ? 'map-edit' : 'map-create';
      addNewObservationToMap(observation, currentMapId);
    }

    dispatchAlert('saved', successMessage);

    // ✨ Se for CRIAÇÃO, limpa tudo para o próximo registro
    if (form.id === 'araucariaForm-create') {
      form.reset();
      clearClickMarker('map-create');

      const latInput = form.querySelector('#latitude');
      const lngInput = form.querySelector('#longitude');
      if (latInput) latInput.value = '';
      if (lngInput) lngInput.value = '';
    }
    // Se for EDIÇÃO, não resetamos o form! 
    // Limpa o estado de clique antigo se houver, mantendo os dados salvos na tela

  } catch (error) {
    let message = error.message || 'Erro inesperado.';
    dispatchAlert('error', message);
  } finally {
    isSubmitting = false;
    toggleSubmitButton(submitButton, false);
  }
}

function validateImage(form) {
  const input = form.querySelector('#photo_path') || form.querySelector('#photo');
  const isEdit = form.id === 'araucariaForm-edit';

  if (!input?.files?.length) {
    if (isEdit) return;
    throw new Error('Selecione uma imagem.');
  }

  const file = input.files[0];
  const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

  if (!allowedTypes.includes(file.type)) {
    throw new Error('Formato de imagem inválido. Formatos aceitos: JPEG, PNG, WEBP.');
  }

  const maxSize = 20 * 1024 * 1024; // 20MB
  if (file.size > maxSize) {
    throw new Error('A imagem deve ter no máximo 20MB.');
  }
}

function toggleSubmitButton(button, loading) {
  if (!button) return;
  button.disabled = loading;
  button.textContent = loading ? 'Salvando...' : 'Salvar Observação';
}

window.deletarObservacao = async function (id, elementoLinha) {
  try {
    const response = await deleteObservation(id);
    const successMessage = response.message || 'Registro excluído com sucesso!';

    dispatchAlert('saved', successMessage);

    if (elementoLinha) {
      elementoLinha.style.transition = 'all 0.5s ease';
      elementoLinha.style.opacity = '0';
      elementoLinha.style.transform = 'scale(0.95)';

      setTimeout(() => {
        elementoLinha.remove();
      }, 500);
    }

  } catch (error) {
    let message = error.message || 'Não foi possível excluir.';
    dispatchAlert('error', message);
  }
};

export function limparCamposLocalizacao(mapId) {
  const latInput = document.getElementById('latitude');
  const lngInput = document.getElementById('longitude');
  if (latInput) latInput.value = '';
  if (lngInput) lngInput.value = '';

  latInput?.dispatchEvent(new Event('input', { bubbles: true }));
  lngInput?.dispatchEvent(new Event('input', { bubbles: true }));

  if (window.clearClickMarker) {
    window.clearClickMarker(mapId);
  }
}

export function limparCampoDataHora() {
  const dateInput = document.getElementById('observed_at');
  if (dateInput) {
    dateInput.value = '';
    dateInput.dispatchEvent(new Event('input', { bubbles: true }));
    dateInput.dispatchEvent(new Event('change', { bubbles: true }));
  }
}

export function limparPinsMapa(mapId) {
  if (window.clearClickMarker) {
    window.clearClickMarker(mapId);
  }
}
export async function processPhotoExif(isChecked, file, mapId) {
  // Quando desmarcado:
  if (!isChecked) {
    limparCamposLocalizacao(mapId);
    limparCampoDataHora();
    limparPinsMapa(mapId);

    return
  }

  if (!file) return;

  try {
    if (!window.ExifReader) return

    const tags = await window.ExifReader.load(file, { expanded: true });
    
    // Data e hora da foto
    const dateTimeTag = tags.exif?.DateTimeOriginal || tags.DateTimeOriginal;
    if (dateTimeTag && dateTimeTag.description) {
      const dateString = dateTimeTag.description;
      const isoDateString = dateString.replace(/^(\d{4}):(\d{2}):(\d{2})/, '$1-$2-$3').replace(' ', 'T');
      if (isoDateString.length >= 16) {
        const dateInput = document.getElementById('observed_at');
        if (dateInput) {
          dateInput.value = isoDateString.substring(0, 16);
          dateInput.dispatchEvent(new Event('input', { bubbles: true }));
          dateInput.dispatchEvent(new Event('change', { bubbles: true }));
        }
      }
    }

    // Localização GPS
    if (tags.gps && tags.gps.Latitude !== undefined && tags.gps.Longitude !== undefined) {
      const lat = Number(tags.gps.Latitude).toFixed(6);
      const lng = Number(tags.gps.Longitude).toFixed(6);
      
      const latInput = document.getElementById('latitude');
      const lngInput = document.getElementById('longitude');
      if (latInput) {
        latInput.value = lat;
        latInput.dispatchEvent(new Event('input', { bubbles: true }));
        latInput.dispatchEvent(new Event('change', { bubbles: true }));
      }
      if (lngInput) {
          lngInput.value = lng;
          lngInput.dispatchEvent(new Event('input', { bubbles: true }));
          lngInput.dispatchEvent(new Event('change', { bubbles: true }));
      }
      if (window.updateMarkerPosition) {
        window.updateMarkerPosition(lat, lng, mapId);
      }

    } else {
      dispatchAlert('error', 'Esta foto não contém dados de GPS (localização).');
      limparCamposLocalizacao(mapId);
      limparCampoDataHora();
      limparPinsMapa(mapId)
    }
  } catch (error) {
    dispatchAlert('error', 'Erro inesperado, tente mais tarde.');
  }
}

window.processPhotoExif = processPhotoExif;