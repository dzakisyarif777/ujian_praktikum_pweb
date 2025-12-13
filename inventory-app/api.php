<?php
// api.php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {

  /* =====================================================
     LIST DATA
  ===================================================== */
  if ($method === 'GET' && $action === 'list') {

    $page     = max(1, (int)($_GET['page'] ?? 1));
    $perPage  = max(1, (int)($_GET['per_page'] ?? 10));
    $q        = trim($_GET['q'] ?? '');
    $sort     = $_GET['sort'] ?? 'created_at';
    $dir      = ($_GET['dir'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

    $allowedSort = ['name','sku','price','quantity','created_at'];
    if (!in_array($sort, $allowedSort)) {
      $sort = 'created_at';
    }

    $offset = ($page - 1) * $perPage;

    $where = '';
    $params = [];

    if ($q !== '') {
      $where = "WHERE name LIKE :q OR sku LIKE :q OR description LIKE :q";
      $params[':q'] = "%$q%";
    }

    // total
    $stmt = pdo()->prepare("SELECT COUNT(*) FROM products $where");
    $stmt->execute($params);
    $total = (int)$stmt->fetchColumn();

    // data
    $sql = "
      SELECT *
      FROM products
      $where
      ORDER BY $sort $dir
      LIMIT :limit OFFSET :offset
    ";

    $stmt = pdo()->prepare($sql);

    foreach ($params as $k => $v) {
      $stmt->bindValue($k, $v);
    }

    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();
    $rows = $stmt->fetchAll();

    echo json_encode([
      'success' => true,
      'data'    => $rows,
      'meta'    => [
        'total'     => $total,
        'page'      => $page,
        'per_page'  => $perPage
      ]
    ]);
    exit;
  }

  /* =====================================================
     GET SINGLE
  ===================================================== */
  if ($method === 'GET' && $action === 'get') {

    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) throw new Exception('ID tidak valid');

    $stmt = pdo()->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();

    echo json_encode([
      'success' => true,
      'data'    => $row
    ]);
    exit;
  }

  /* =====================================================
     CREATE
  ===================================================== */
  if ($method === 'POST' && $action === 'create') {

    $name        = trim($_POST['name'] ?? '');
    $sku         = trim($_POST['sku'] ?? '');
    $price       = (float)($_POST['price'] ?? 0);
    $quantity    = (int)($_POST['quantity'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $image       = trim($_POST['image'] ?? '');

    if ($name === '' || $sku === '') {
      throw new Exception('Nama dan SKU wajib diisi');
    }

    $stmt = pdo()->prepare("
      INSERT INTO products
      (name, sku, price, quantity, description, image)
      VALUES
      (:name, :sku, :price, :qty, :desc, :image)
    ");

    $stmt->execute([
      ':name'  => $name,
      ':sku'   => $sku,
      ':price'=> $price,
      ':qty'   => $quantity,
      ':desc'  => $description,
      ':image' => $image
    ]);

    echo json_encode([
      'success' => true,
      'id'      => pdo()->lastInsertId()
    ]);
    exit;
  }

  /* =====================================================
     UPDATE
  ===================================================== */
  if ($method === 'POST' && $action === 'update') {

    $id          = (int)($_POST['id'] ?? 0);
    $name        = trim($_POST['name'] ?? '');
    $sku         = trim($_POST['sku'] ?? '');
    $price       = (float)($_POST['price'] ?? 0);
    $quantity    = (int)($_POST['quantity'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $image       = trim($_POST['image'] ?? '');

    if ($id <= 0) throw new Exception('ID tidak valid');

    $stmt = pdo()->prepare("
      UPDATE products SET
        name = :name,
        sku = :sku,
        price = :price,
        quantity = :qty,
        description = :desc,
        image = :image
      WHERE id = :id
    ");

    $stmt->execute([
      ':name'  => $name,
      ':sku'   => $sku,
      ':price'=> $price,
      ':qty'   => $quantity,
      ':desc'  => $description,
      ':image' => $image,
      ':id'    => $id
    ]);

    echo json_encode(['success' => true]);
    exit;
  }

  /* =====================================================
     DELETE
  ===================================================== */
  if ($method === 'POST' && $action === 'delete') {

    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) throw new Exception('ID tidak valid');

    $stmt = pdo()->prepare("DELETE FROM products WHERE id = :id");
    $stmt->execute([':id' => $id]);

    echo json_encode(['success' => true]);
    exit;
  }

  /* =====================================================
     DEFAULT
  ===================================================== */
  http_response_code(400);
  echo json_encode([
    'success' => false,
    'message' => 'Action tidak dikenali'
  ]);

} catch (Exception $e) {
  http_response_code(500);
  echo json_encode([
    'success' => false,
    'message' => $e->getMessage()
  ]);
}
