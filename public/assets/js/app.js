// Utilitaire Fetch : ajoute automatiquement le jeton CSRF et gère le JSON
function csrfToken() {
  const meta = document.querySelector('meta[name="csrf-token"]');
  return meta ? meta.content : '';
}

async function api(url, { method = 'GET', body = null } = {}) {
  const options = {
    method,
    headers: {
      'Accept': 'application/json',
      'X-CSRF-TOKEN': csrfToken(),
    },
    credentials: 'same-origin',
  };
  if (body !== null) {
    options.headers['Content-Type'] = 'application/json';
    options.body = JSON.stringify(body);
  }
  const response = await fetch(url, options);
  let data = null;
  try { data = await response.json(); } catch (e) { /* réponse sans JSON */ }
  if (!response.ok) {
    const error = new Error((data && data.error) || 'Erreur ' + response.status);
    error.status = response.status;
    throw error;
  }
  return data;
}

window.api = api;
