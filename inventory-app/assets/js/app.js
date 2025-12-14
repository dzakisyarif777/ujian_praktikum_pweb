document.addEventListener('DOMContentLoaded', () => {

  /* =====================================================
     ELEMENTS
  ===================================================== */
  const tableArea       = document.getElementById('table-area');
  const pagination      = document.getElementById('pagination');
  const searchInput     = document.getElementById('search');
  const perPageSelect   = document.getElementById('perPage');
  const btnAdd          = document.getElementById('btn-add');
  const btnToggleView   = document.getElementById('btn-toggle-view');
  const btnClearSearch  = document.getElementById('btn-clear-search');
  const toasts          = document.getElementById('toasts');

  // Modals
  const productModalEl  = document.getElementById('productModal');
  const detailModalEl   = document.getElementById('detailModal');
  const bsProductModal  = productModalEl ? new bootstrap.Modal(productModalEl) : null;
  const bsDetailModal   = detailModalEl ? new bootstrap.Modal(detailModalEl) : null;
  const productForm     = document.getElementById('productForm');

  // Detail modal elements
  const detailImage     = document.getElementById('detail-image');
  const detailName      = document.getElementById('detail-name');
  const detailSku       = document.getElementById('detail-sku');
  const detailPrice     = document.getElementById('detail-price');
  const detailStock     = document.getElementById('detail-stock');
  const detailDesc      = document.getElementById('detail-desc');
  const btnDetailEdit   = document.getElementById('btn-detail-edit');
  const btnDetailImageLink = document.getElementById('btn-detail-image-link');

  /* =====================================================
     STATE
  ===================================================== */
  const state = {
    page     : 1,
    per_page: parseInt(perPageSelect?.value || 10, 10),
    q        : '',
    sort     : 'created_at',
    dir      : 'desc',
    view     : 'table'
  };

  /* =====================================================
     UTILITIES
  ===================================================== */
  const num = v => (Number(v) || 0).toLocaleString('id-ID');

  const escapeHTML = (s = '') =>
    String(s)
      .replaceAll('&', '&amp;')
      .replaceAll('<', '&lt;')
      .replaceAll('>', '&gt;');

  function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-bg-${type} border-0 show`;
    toast.style.minWidth = '220px';

    toast.innerHTML = `
      <div class="d-flex">
        <div class="toast-body">${escapeHTML(message)}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto"></button>
      </div>
    `;

    toasts.appendChild(toast);
    setTimeout(() => toast.remove(), 4200);
  }

  function showSkeleton() {
    tableArea.innerHTML = `
      <div class="card p-4">
        <div class="skeleton mb-3" style="height:14px;width:50%"></div>
        <div class="skeleton mb-2" style="height:12px;width:80%"></div>
        <div class="skeleton mb-2" style="height:12px;width:70%"></div>
      </div>
    `;
  }

  function getDefaultImageByName(name = '') {
    const n = name.toLowerCase();
    if (n.includes('mouse'))  return 'https://images.unsplash.com/photo-1587825140708-dfaf72ae4b04';
    if (n.includes('laptop')) return 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8';
    if (n.includes('kopi') || n.includes('coffee'))
      return 'https://images.unsplash.com/photo-1509042239860-f550ce710b93';
    if (n.includes('mug') || n.includes('gelas'))
      return 'https://images.unsplash.com/photo-1517705008128-361805f42e86';
    return 'https://via.placeholder.com/600x400?text=No+Image';
  }

  /* =====================================================
     FETCH LIST
  ===================================================== */
  async function fetchList() {
    showSkeleton();
    state.per_page = parseInt(perPageSelect.value || state.per_page, 10);

    const params = new URLSearchParams({
      action   : 'list',
      page     : state.page,
      per_page : state.per_page,
      q        : state.q,
      sort     : state.sort,
      dir      : state.dir
    });

    try {
      const res  = await fetch(BASE_URL + 'api.php?' + params);
      const json = await res.json();

      if (!json.success) {
        tableArea.innerHTML = `<div class="center-muted">Gagal memuat data</div>`;
        showToast(json.message || 'Gagal memuat', 'danger');
        return;
      }

      renderTable(json.data);
      renderPagination(json.meta);

    } catch (err) {
      console.error(err);
      showToast('Kesalahan jaringan', 'danger');
    }
  }

  /* =====================================================
     RENDER TABLE
  ===================================================== */
  function renderTable(rows) {
    if (!rows || !rows.length) {
      tableArea.innerHTML = `<div class="center-muted">Tidak ada data</div>`;
      return;
    }

    let html = `
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>Nama</th>
              <th>SKU</th>
              <th class="text-center">Harga</th>
              <th class="text-center">Stok</th>
              <th class="text-center">Created</th>
              <th class="text-end"></th>
            </tr>
          </thead>
          <tbody>
    `;

    rows.forEach(r => {
      html += `
        <tr data-id="${r.id}">
          <td>${escapeHTML(r.name)}</td>
          <td>${escapeHTML(r.sku)}</td>
          <td class="text-center">Rp ${num(r.price)}</td>
          <td class="text-center">${r.quantity}</td>
          <td class="text-center">${escapeHTML(r.created_at)}</td>
          <td class="text-end">
            <button class="btn btn-sm btn-edit" data-id="${r.id}">Edit</button>
            <button class="btn btn-sm btn-delete" data-id="${r.id}">Hapus</button>
          </td>
        </tr>
      `;
    });

    html += `</tbody></table></div>`;
    tableArea.innerHTML = html;

    document.querySelectorAll('.btn-edit').forEach(b => b.onclick = onEdit);
    document.querySelectorAll('.btn-delete').forEach(b => b.onclick = onDelete);
    bindRowClicks();
  }

  /* =====================================================
     PAGINATION
  ===================================================== */
  function renderPagination(meta) {
    if (!pagination) return;
    const total = Math.max(1, Math.ceil((meta.total || 0) / meta.per_page));

    pagination.innerHTML = Array.from({ length: total }, (_, i) =>
      `<li class="page-item ${i + 1 === meta.page ? 'active' : ''}">
        <a href="#" class="page-link" data-page="${i + 1}">${i + 1}</a>
      </li>`
    ).join('');

    pagination.querySelectorAll('.page-link').forEach(a =>
      a.onclick = e => {
        e.preventDefault();
        state.page = parseInt(a.dataset.page, 10);
        fetchList();
      }
    );
  }

  /* =====================================================
     INLINE DETAIL
  ===================================================== */
  async function toggleInlineDetail(id, tr) {
    document.querySelectorAll('.detail-row').forEach(r => r.remove());

    const res  = await fetch(BASE_URL + 'api.php?action=get&id=' + id);
    const json = await res.json();
    const p    = json.data;

    const imageUrl = p.image?.trim() || getDefaultImageByName(p.name);
    const detailTr = document.createElement('tr');
    detailTr.className = 'detail-row';

    detailTr.innerHTML = `
      <td colspan="${tr.children.length}">
        <div class="detail-content">
          <img src="${imageUrl}">
          <div class="detail-meta">
            <h5>${escapeHTML(p.name)}</h5>
            <div class="small-muted">SKU: ${escapeHTML(p.sku)} • Stok: ${p.quantity}</div>
            <h4>Rp ${num(p.price)}</h4>
            <p>${escapeHTML(p.description || 'Tidak ada deskripsi')}</p>
            <button class="detail-close">Tutup</button>
          </div>
        </div>
      </td>
    `;

    tr.after(detailTr);
    detailTr.querySelector('.detail-close').onclick = () => detailTr.remove();
  }

  function bindRowClicks() {
    document.querySelectorAll('tr[data-id]').forEach(tr => {
      if (tr._bound) return;
      tr._bound = true;

      tr.onclick = e => {
        if (e.target.closest('button')) return;
        toggleInlineDetail(tr.dataset.id, tr);
      };
    });
  }

  /* =====================================================
     CRUD HANDLERS
  ===================================================== */
  async function onEdit(e) {
    const id = e.target.dataset.id;
    const res = await fetch(BASE_URL + 'api.php?action=get&id=' + id);
    fillProductForm((await res.json()).data);
    bsProductModal.show();
  }

  async function onDelete(e) {
    if (!confirm('Hapus produk ini?')) return;
    const fd = new FormData();
    fd.append('id', e.target.dataset.id);
    await fetch(BASE_URL + 'api.php?action=delete', { method: 'POST', body: fd });
    fetchList();
  }

  function fillProductForm(p = {}) {
    productForm['id'].value        = p.id || '';
    productForm['name'].value      = p.name || '';
    productForm['sku'].value       = p.sku || '';
    productForm['price'].value     = p.price || '';
    productForm['quantity'].value  = p.quantity || '';
    productForm['description'].value = p.description || '';
    productForm['image'].value     = p.image || '';
  }

productForm.addEventListener('submit', async (ev) => {
  ev.preventDefault(); // ← WAJIB

  const fd = new FormData(productForm);
  const id = fd.get('id') || '';
  const action = id ? 'update' : 'create';

  const res = await fetch(BASE_URL + 'api.php?action=' + action, {
    method: 'POST',
    body: fd
  });

  const json = await res.json();

  if (json.success) {
    showToast('Sukses menyimpan data');
    bsProductModal.hide();
    productForm.reset();
    fetchList();
  } else {
    showToast(json.message || 'Gagal menyimpan', 'danger');
  }
});

  /* =====================================================
     SEARCH & INIT
  ===================================================== */
  searchInput?.addEventListener('input', e => {
    state.q = e.target.value.trim();
    state.page = 1;
    fetchList();
  });

  btnAdd?.addEventListener('click', () => {
    fillProductForm({});
    bsProductModal.show();
  });

  fetchList();
});
