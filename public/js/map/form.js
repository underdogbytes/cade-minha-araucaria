// form.js
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

    window.dispatchEvent(new CustomEvent('observation-saved', {
      detail: { message: successMessage, observation }
    }));

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
    window.dispatchEvent(new CustomEvent('observation-error', {
      detail: { message: error.message || 'Erro inesperado.' }
    }));
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
    console.log(`Disparando exclusão da araucária ID: ${id}`);

    // 1. Faz a chamada para a API do Laravel
    const response = await deleteObservation(id);
    const successMessage = response.message || 'Registro excluído com sucesso!';

    // 2. Dispara o alerta visual verde na tela (O mesmo que criamos antes!)
    window.dispatchEvent(new CustomEvent('observation-saved', {
      detail: { message: successMessage }
    }));

    // 3. Efeito visual: Faz a linha desaparecer suavemente antes de remover do HTML
    if (elementoLinha) {
      elementoLinha.style.transition = 'all 0.5s ease';
      elementoLinha.style.opacity = '0';
      elementoLinha.style.transform = 'scale(0.95)';

      setTimeout(() => {
        elementoLinha.remove();
      }, 500);
    }

  } catch (error) {
    console.error('Erro ao excluir:', error.message);
    // Dispara o alerta vermelho de erro na tela
    window.dispatchEvent(new CustomEvent('observation-error', {
      detail: { message: error.message || 'Não foi possível excluir.' }
    }));
  }
};