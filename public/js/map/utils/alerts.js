export function dispatchAlert(type, message) {
  return window.dispatchEvent(new CustomEvent(`observation-${type}`, {
    detail: { message: message }
  }));
}