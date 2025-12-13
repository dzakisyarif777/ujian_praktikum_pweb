<?php
require_once __DIR__ . '/config.php';
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Inventory Keren — Dashboard</title>

  <!-- Fonts & Icons -->
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  <!-- Bootstrap -->  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Custom CSS -->
  <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

  <!-- NAVBAR -->
  <nav class="navbar app-nav mx-3">
    <div class="container-fluid d-flex align-items-center justify-content-between">
      <div class="d-flex align-items-center gap-3">
        <div class="brand-badge" aria-hidden="true"></div>
        <div>
          <div class="brand-title">Inventory Saya</div>
          <div class="brand-sub"></div>
        </div>
      </div>

      <div class="d-flex align-items-center gap-2">
        <button class="btn btn-fab" id="btn-add" title="Tambah produk" aria-label="Tambah produk">
          <i class="fa fa-plus me-1"></i> Tambah Produk
        </button>
      </div>
    </div>
  </nav>

  <!-- MAIN -->
  <main class="container">
    <div class="row align-items-center mb-3 gx-3">
      <div class="col-lg-8">
        <div class="search-wrap d-flex align-items-center">
          <i class="fa fa-search search-icon" aria-hidden="true"></i>
          <input id="search" class="form-control search-input" placeholder="Cari nama / SKU / deskripsi..." aria-label="Cari produk">
          <button class="btn btn-sm ms-2 btn-outline-secondary" id="btn-clear-search" title="Clear search" aria-label="Bersihkan pencarian"><i class="fa fa-xmark"></i></button>
        </div>
      </div>

      <div class="col-lg-4 text-end d-flex justify-content-end align-items-center gap-2">
        <select id="perPage" class="form-select w-auto" aria-label="Items per page">
          <option value="5">5</option>
          <option value="10" selected>10</option>
          <option value="25">25</option>
        </select>

        <button class="btn btn-light" id="btn-toggle-view" title="Toggle view" aria-label="Toggle view"><i class="fa fa-table"></i></button>
      </div>
    </div>

    <div id="alert-area" aria-live="polite"></div>

    <section id="table-wrap" class="mb-4">
      <div id="table-area"></div>
      <nav aria-label="Pagination">
        <ul class="pagination justify-content-center" id="pagination"></ul>
      </nav>
    </section>
  </main>

  <!-- Product Modal -->
  <div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form id="productForm" autocomplete="off">
          <div class="modal-header"><h5 class="modal-title">Form Produk</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
          <div class="modal-body">
            <input type="hidden" name="id" id="product-id">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
            <div class="row g-3">
              <div class="col-md-6"><label class="form-label">Nama</label><input name="name" id="product-name" class="form-control" required></div>
              <div class="col-md-6"><label class="form-label">SKU</label><input name="sku" id="product-sku" class="form-control" required></div>
              <div class="col-md-4"><label class="form-label">Harga</label><input name="price" id="product-price" type="number" step="0.01" class="form-control"></div>
              <div class="col-md-4"><label class="form-label">Stok</label><input name="quantity" id="product-quantity" type="number" class="form-control"></div>
              <div class="col-12"><label class="form-label">Deskripsi</label><textarea name="description" id="product-desc" class="form-control" rows="4"></textarea></div>
              <div class="col-12"><label class="form-label">Gambar (URL)</label><input name="image" id="product-image" class="form-control" placeholder="https://example.com/image.jpg"><div class="form-text">Boleh kosong — sistem pakai placeholder jika kosong.</div></div>
            </div>
          </div>
          <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div>
        </form>
      </div>
    </div>
  </div>

  <!-- Detail Modal -->
  <div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
      <div class="modal-content" style="overflow:hidden;">
        <div class="modal-body p-0">
          <div class="row g-0">
            <div class="col-md-6"><div id="detail-image-wrap" style="height:100%; min-height:320px; display:flex; align-items:center; justify-content:center; background:linear-gradient(180deg,#fafafa,#fff);"><img id="detail-image" src="" alt="Gambar Produk" style="max-width:100%; max-height:80vh; object-fit:cover;"></div></div>
            <div class="col-md-6"><div class="p-4"><h3 id="detail-name" class="mb-1"></h3><div id="detail-sku" class="small-muted mb-2"></div><h4 id="detail-price" class="mb-2" style="color:#3b2b6b"></h4><div id="detail-stock" class="small-muted mb-3"></div><hr><h6>Deskripsi</h6><p id="detail-desc" class="small-muted" style="line-height:1.6;"></p><div class="mt-4 d-flex gap-2"><button id="btn-detail-edit" class="btn btn-primary">Edit</button><button id="btn-detail-close" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button><a id="btn-detail-image-link" class="btn btn-sm btn-light ms-auto" href="#" target="_blank">Buka Gambar</a></div></div></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div id="toasts" style="position:fixed;right:20px;bottom:20px;z-index:9999"></div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>const BASE_URL = "<?= rtrim(BASE_URL, '/') . '/' ?>";</script>
  <script src="assets/js/app.js"></script>
</body>
</html>
