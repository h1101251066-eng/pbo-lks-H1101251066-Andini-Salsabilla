const routes = {
  '#/': renderDashboard,
  '#/buku': renderBooks,
  '#/kategori': () => {
    // Halaman kategori dibuat di Langkah 19
    if (typeof renderCategories === 'function') {
      renderCategories();
    } else {
      document.getElementById('app').innerHTML =
        '<p class="text-gray-500">Halaman kategori dibuat di Langkah 19.</p>';
    }
  },
};

function router() {
  const hash = window.location.hash || '#/';
  const page = routes[hash] || routes['#/'];

  document.querySelectorAll('.nav-link').forEach(link => {
    link.classList.toggle('active', link.getAttribute('href') === hash);
  });

  page();
}

window.addEventListener('hashchange', router);
window.addEventListener('DOMContentLoaded', router);