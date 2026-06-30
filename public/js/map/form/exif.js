import { limparPinsMapa } from "./mapa.js";
import { limparCampo } from "./validacao.js";

export async function extrairDadosEXIF(file) {
  if (!file) return;

  const tags = await window.ExifReader.load(file, { expanded: true });
  const resultado = { coords: null, date: null };

  // Data e Hora:
  const dataHora = tags.exif?.DateTimeOriginal || tags.DateTimeOriginal;
  if (dataHora?.description) {
    const isoDate = dataHora.description.replace(/^(\d{4}):(\d{2}):(\d{2})/, '$1-$2-$3').replace(' ', 'T');
    if (isoDate.length >= 16) resultado.date = isoDate.substring(0, 16);
  }

  // Localzação:
  if (tags.gps?.Latitude !== undefined && tags.gps?.Longitude !== undefined) {
    resultado.coords = {
      lat: Number(tags.gps.Latitude).toFixed(6),
      lng: Number(tags.gps.Longitude).toFixed(6)
    };
  }

  return resultado;
}

export function limparDadosEXIF(formElement, mapId) {
  limparCampo(formElement, 'observed_at');
  limparCampo(formElement, 'latitude');
  limparCampo(formElement, 'longitude');
  limparPinsMapa(mapId);
}