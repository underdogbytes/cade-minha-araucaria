export async function fetchObservations() {
  const response = await fetch('/api/observations', {
    headers: {
      Accept: 'application/json',
    },
  });

  if (!response.ok) {
    throw new Error('Erro ao carregar observações.');
  }

  return response.json();
}

export async function createObservation(form) {
  const formData = new FormData(form);
  const url = form.getAttribute('action') || '/api/observations';
  const tokenInput = form.querySelector('input[name="_token"]');
  const csrfToken = tokenInput ? tokenInput.value : '';

  const response = await fetch(url, {
    method: 'POST',
    body: formData,
    headers: {
      'Accept': 'application/json',
      'X-CSRF-TOKEN': csrfToken
    },
  });

  let data = {};

  try {
    data = await response.json();
  } catch {
    throw new Error('Resposta inválida do servidor.');
  }

  if (!response.ok) {
    throw new Error(data.message || 'Erro ao salvar observação.');
  }

  return data;
}

export async function deleteObservation(id) {
  const csrfToken = document.querySelector('input[name="_token"]')?.value || '';

  const response = await fetch(`/observations/${id}`, {
    method: 'DELETE',
    headers: {
      'Accept': 'application/json',
      'X-CSRF-TOKEN': csrfToken
    },
  });

  let data = {};
  try {
    data = await response.json();
  } catch {
    throw new Error('Resposta inválida do servidor.');
  }

  if (!response.ok) {
    throw new Error(data.message || 'Erro ao excluir observação.');
  }

  return data;
}