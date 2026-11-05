async function renderBooks() {
  const app = document.getElementById('app');
  app.innerHTML = '<p class="text-gray-500">Memuat data buku...</p>';

  try {
    const books = await BookService.list();

    app.innerHTML = `
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-2xl font-bold">Buku</h2>
        <button id="btn-add" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">+ Tambah Buku</button>
      </div>

      <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-left">
          <thead class="bg-gray-50 text-gray-600 text-sm">
            <tr>
              <th class="p-3">ID</th>
              <th class="p-3">Judul</th>
              <th class="p-3">Penulis</th>
              <th class="p-3">Tahun</th>
              <th class="p-3">Stok</th>
              <th class="p-3">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            ${books.length === 0
              ? '<tr><td colspan="6" class="p-4 text-center text-gray-500">Belum ada data.</td></tr>'
              : books.map(b => `
                <tr>
                  <td class="p-3">${b.id}</td>
                  <td class="p-3">${escapeHtml(b.judul)}</td>
                  <td class="p-3">${escapeHtml(b.penulis)}</td>
                  <td class="p-3">${b.tahun}</td>
                  <td class="p-3">${b.stok}</td>
                  <td class="p-3 space-x-2">
                    <button class="btn-edit text-blue-600 hover:underline" data-id="${b.id}">Edit</button>
                    <button class="btn-delete text-red-600 hover:underline" data-id="${b.id}">Hapus</button>
                  </td>
                </tr>`).join('')}
          </tbody>
        </table>
      </div>
    `;

    document.getElementById('btn-add').addEventListener('click', () => openBookModal());
    app.querySelectorAll('.btn-edit').forEach(btn => {
      btn.addEventListener('click', () => {
        openBookModal(books.find(b => b.id === Number(btn.dataset.id)));
      });
    });
    app.querySelectorAll('.btn-delete').forEach(btn => {
      btn.addEventListener('click', () => deleteBook(Number(btn.dataset.id)));
    });
  } catch (err) {
    app.innerHTML = `<p class="text-red-600">Gagal memuat buku: ${escapeHtml(err.message)}</p>`;
  }
}

function openBookModal(book = null) {
  const isEdit = book !== null;
  const modal = document.createElement('div');
  modal.className = 'modal-backdrop';
  modal.innerHTML = `
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
      <h3 class="text-lg font-bold mb-4">${isEdit ? 'Edit Buku' : 'Tambah Buku'}</h3>
      <div class="space-y-3">
        <input id="f-judul" class="w-full border rounded p-2" placeholder="Judul" value="${isEdit ? escapeHtml(book.judul) : ''}">
        <input id="f-penulis" class="w-full border rounded p-2" placeholder="Penulis" value="${isEdit ? escapeHtml(book.penulis) : ''}">
        <input id="f-tahun" type="number" class="w-full border rounded p-2" placeholder="Tahun" value="${isEdit ? book.tahun : ''}">
        <input id="f-stok" type="number" class="w-full border rounded p-2" placeholder="Stok" value="${isEdit ? book.stok : ''}">
      </div>
      <div class="flex justify-end gap-2 mt-5">
        <button id="f-cancel" class="px-4 py-2 rounded border">Batal</button>
        <button id="f-save" class="px-4 py-2 rounded bg-blue-600 text-white">Simpan</button>
      </div>
    </div>
  `;
  document.body.appendChild(modal);

  modal.querySelector('#f-cancel').addEventListener('click', () => modal.remove());
  modal.querySelector('#f-save').addEventListener('click', async () => {
    const data = {
      judul: modal.querySelector('#f-judul').value,
      penulis: modal.querySelector('#f-penulis').value,
      tahun: Number(modal.querySelector('#f-tahun').value),
      stok: Number(modal.querySelector('#f-stok').value),
    };

    try {
      if (isEdit) {
        await BookService.update(book.id, data);
        showToast('Buku berhasil diperbarui');
      } else {
        await BookService.create(data);
        showToast('Buku berhasil ditambahkan');
      }
      modal.remove();
      renderBooks();
    } catch (err) {
      showToast(err.message, 'error');
    }
  });
}

async function deleteBook(id) {
  if (!confirm('Yakin ingin menghapus buku ini?')) return;

  try {
    await BookService.remove(id);
    showToast('Buku berhasil dihapus');
    renderBooks();
  } catch (err) {
    showToast(err.message, 'error');
  }
}