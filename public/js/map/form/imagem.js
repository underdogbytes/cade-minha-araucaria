import { dispatchAlert } from '../utils/alerts.js';
import { extrairDadosEXIF, limparDadosEXIF } from './exif.js';
import { atualizaCoordenadas } from './mapa.js';
import { atualizaDataHora } from './validacao.js';

export async function handleSelecaoImagem(isChecked, file, formElement, mapId) {
  // Limpa o estado no formulário e no mapa para garantir consistência
  limparDadosEXIF(formElement, mapId);

  // Se o usuário não marcou a checkbox ou não selecionou arquivo, encerra limpo
  if (!isChecked || !file) return;

  try {
    const data = await extrairDadosEXIF(file);
    if (!data || (!data.coords && !data.date)) {
      dispatchAlert('error', 'Esta foto não contém dados de localização ou data.');
      return;
    }

    // Data e hora:
    if (data.date) { atualizaDataHora(formElement, data.date); }

    // Coordenadas:
    if (data.coords) { atualizaCoordenadas(formElement, data.coords, mapId); }
  } catch (error) {
    console.error('[EXIF Image Handler Error]:', error);
    dispatchAlert('error', error.message || 'Erro ao processar metadados da imagem.');
  }
}

export function validarImagem(form) {
  const input = form.querySelector('[name="photos[]"]') || form.querySelector('[name="photo_path"]') || form.querySelector('input[type="file"]');
  const isEdit = form.id === 'araucariaForm-edit';

  if (!input?.files?.length) {
    if (isEdit) return;
    throw new Error('Selecione pelo menos uma imagem.');
  }

  const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
  const maxSize = 20 * 1024 * 1024; // 20MB

  for (const file of input.files) {
    if (!allowedTypes.includes(file.type)) {
      throw new Error(`Formato de imagem inválido em "${file.name}". Formatos aceitos: JPEG, PNG, WEBP.`);
    }

    if (file.size > maxSize) {
      throw new Error(`A imagem "${file.name}" excede o limite máximo de 20MB.`);
    }
  }
}