// TODO: refatorar isso para virar lib sla como ainda
export function showErrorMessage(error) {
  const alertDiv = document.createElement('div');

  alertDiv.className = `
    fixed top-5 right-5 z-50 text-white px-6 py-3 rounded-lg shadow-xl font-semibold 
    flex items-center gap-2 bg-red-500 
    transition-all duration-300 transform translate-y-[-20px] opacity-0
  `.replace(/\s+/g, ' ').trim();

  alertDiv.innerHTML = `
    <span>❌</span>
    <span>${error.message || 'Erro inesperado.'}</span>
  `;

  document.body.appendChild(alertDiv);

  setTimeout(() => {
    alertDiv.classList.remove('translate-y-[-20px]', 'opacity-0');
  }, 50);

  setTimeout(() => {
    alertDiv.classList.add('translate-y-[-20px]', 'opacity-0');

    setTimeout(() => {
      alertDiv.remove();
    }, 300);
  }, 5000);
}

export function hideSpinner(spinnerId) {
  const loader = document.getElementById(spinnerId);
  if (loader) {
    loader.classList.add('opacity-0');

    setTimeout(() => {
      loader.style.display = 'none';
    }, 300);
  }
}