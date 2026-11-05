async function renderDashboard() {
  const app = document.getElementById('app');
  app.innerHTML = '<p class="text-gray-500">Memuat dashboard...</p>';

  try {
    const [books, categories] = await Promise.all([
      BookService.list(),
      CategoryService.list(),
    ]);

    // Buku diurutkan dari ID terbesar, jadi 5 pertama = terbaru
    const latest = books.slice(0, 5);

    app.innerHTML = `
      <h2 class="text-2xl font-bold mb-4">Dashboard</h2>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="bg-blue-600 text-white rounded-lg p-6 shadow">
          <p class="text-sm opacity-80">Total Buku</p>
          <p class="text-4xl font-bold">${books.length}</p>
        </div>
        <div class="bg-orange-500 text-white rounded-lg p-6 shadow">
          <p class="text-sm opacity-80">Kategori</p>
          <p class="text-4xl font-bold">${categories.length}</p>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-4">
        <h3 class="font-semibold mb-3">Buku Terbaru</h3>
        <ul class="divide-y">
          ${latest.length === 0
            ? '<li class="py-2 text-gray-500">Belum ada buku.</li>'
            : latest.map(b => `
              <li class="py-2 flex justify-between">
                <span>${escapeHtml(b.judul)} <span class="text-gray-500">- ${escapeHtml(b.penulis)}</span></span>
                <span class="text-sm text-gray-500">Stok: ${b.stok}</span>
              </li>`).join('')}
        </ul>
      </div>
    `;
  } catch (err) {
    app.innerHTML = `<p class="text-red-600">Gagal memuat dashboard: ${escapeHtml(err.message)}</p>`;
  }
}