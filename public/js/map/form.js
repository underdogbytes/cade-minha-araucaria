import { createObservation } from './api.js';
import { addNewObservationToMap, clearClickMarker } from './map.js';

let isSubmitting = false;

export function setupFormListener() {
  const form = document.getElementById('araucariaForm');
  if (!form) {
    return;
  }

  form.addEventListener('submit', handleSubmit);
}

async function handleSubmit(event) {
  event.preventDefault();

  if (isSubmitting) {
    return;
  }

  const form = event.currentTarget;
  const submitButton = form.querySelector('button[type="submit"]');

  try {
    isSubmitting = true;

    toggleSubmitButton(submitButton, true);

    validateImage(form);

    const response = await createObservation(form);

    const observation = response.data || response;

    if (observation) {
      addNewObservationToMap(observation);
    }

    form.reset();

    clearClickMarker();

    alert('Araucária salva com sucesso!');

  } catch (error) {
    console.error(error);
    alert(error.message || 'Erro inesperado.');
  } finally {
    isSubmitting = false;
    toggleSubmitButton(submitButton, false);
  }
}

function validateImage(form) {
  const input = form.querySelector('#photo_path') || form.querySelector('#photo');

  if (!input?.files?.length) {
    throw new Error('Selecione uma imagem.');
  }

  const file = input.files[0];

  const allowedTypes = [
    'image/jpeg',
    'image/png',
    'image/webp',
  ];

  if (!allowedTypes.includes(file.type)) {
    throw new Error('Formato de imagem inválido. Formatos aceitos: JPEG, PNG, WEBP.');
  }

  const maxSize = 20 * 1024 * 1024; // 20MB

  if (file.size > maxSize) {
    throw new Error('A imagem deve ter no máximo 20MB.');
  }
}

function toggleSubmitButton(button, loading) {
  if (!button) {
    return;
  }

  button.disabled = loading;

  button.textContent = loading
    ? 'Salvando...'
    : 'Salvar Observação';
}