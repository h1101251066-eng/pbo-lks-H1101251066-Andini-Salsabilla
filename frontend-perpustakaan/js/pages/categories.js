async function renderCategories() {
  const app = document.getElementById('app');
  app.innerHTML = '<p class="text-gray-500">Memuat data kategori...</p>';

  try {
    const categories = await CategoryService.list();

    app.innerHTML = `
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-2xl font-bold">Kategori</h2>
        <button id="btn-add" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">+ Tambah Kategori</button>
      </div>

      <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-left">
          <thead class="bg-gray-50 text-gray-600 text-sm">
            <tr>
              <th class="p-3">ID</th>
              <th class="p-3">Nama</th>
              <th class="p-3">Deskripsi</th>
              <th class="p-3">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            ${categories.length === 0
              ? '<tr><td colspan="4" class="p-4 text-center text-gray-500">Belum ada data.</td></tr>'
              : categories.map(c => `
                <tr>
                  <td class="p-3">${c.id}</td>
                  <td class="p-3">${escapeHtml(c.nama)}</td>
                  <td class="p-3">${escapeHtml(c.deskripsi)}</td>
                  <td class="p-3 space-x-2">
                    <button class="btn-edit text-blue-600 hover:underline" data-id="${c.id}">Edit</button>
                    <button class="btn-delete text-red-600 hover:underline" data-id="${c.id}">Hapus</button>
                  </td>
                </tr>`).join('')}
          </tbody>
        </table>
      </div>
    `;

    document.getElementById('btn-add').addEventListener('click', () => openCategoryModal());
    app.querySelectorAll('.btn-edit').forEach(btn => {
      btn.addEventListener('click', () => {
        openCategoryModal(categories.find(c => c.id === Number(btn.dataset.id)));
      });
    });
    app.querySelectorAll('.btn-delete').forEach(btn => {
      btn.addEventListener('click', () => deleteCategory(Number(btn.dataset.id)));
    });
  } catch (err) {
    app.innerHTML = `<p class="text-red-600">Gagal memuat kategori: ${escapeHtml(err.message)}</p>`;
  }
}

function openCategoryModal(category = null) {
  const isEdit = category !== null;
  const modal = document.createElement('div');
  modal.className = 'modal-backdrop';
  modal.innerHTML = `
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
      <h3 class="text-lg font-bold mb-4">${isEdit ? 'Edit Kategori' : 'Tambah Kategori'}</h3>
      <div class="space-y-3">
        <input id="f-nama" class="w-full border rounded p-2" placeholder="Nama kategori" value="${isEdit ? escapeHtml(category.nama) : ''}">
        <textarea id="f-deskripsi" class="w-full border rounded p-2" rows="3" placeholder="Deskripsi">${isEdit ? escapeHtml(category.deskripsi) : ''}</textarea>
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
      nama: modal.querySelector('#f-nama').value,
      deskripsi: modal.querySelector('#f-deskripsi').value,
    };

    try {
      if (isEdit) {
        await CategoryService.update(category.id, data);
        showToast('Kategori berhasil diperbarui');
      } else {
        await CategoryService.create(data);
        showToast('Kategori berhasil ditambahkan');
      }
      modal.remove();
      renderCategories();
    } catch (err) {
      showToast(err.message, 'error');
    }
  });
}

async function deleteCategory(id) {
  if (!confirm('Yakin ingin menghapus kategori ini?')) return;

  try {
    await CategoryService.remove(id);
    showToast('Kategori berhasil dihapus');
    renderCategories();
  } catch (err) {
    showToast(err.message, 'error');
  }
}