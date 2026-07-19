<?php
include '../config.php';
require_once '../auth.php';

// ========== AUTH ==========
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    session_unset();
    session_destroy();
    header("Location: ../login.php");
    exit;
}

$_SESSION['active_role'] = 'admin';
$adminName = htmlspecialchars($_SESSION['user']['username'] ?? 'Admin');

function redirectWithMessage($type) {
    header("Location: " . $_SERVER['PHP_SELF'] . "?status=" . $type);
    exit;
}

function sanitizeInput($v) {
    return htmlspecialchars(strip_tags(trim($v)), ENT_QUOTES, 'UTF-8');
}

// ========== ADD MENU ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_menu'])) {
    $category_id = intval($_POST['category']);
    $name = sanitizeInput($_POST['name']);
    $priceRaw = $_POST['price'];            // <-- here
    // Add validation for price
    if (!is_numeric($priceRaw) || floatval($priceRaw) < 0) {
        redirectWithMessage("invalid_price");
    }
    $price = round(floatval($priceRaw), 2);

    $best_seller = isset($_POST['best_seller']) ? 1 : 0;

     if (empty($_FILES['image']['name'])) {
        redirectWithMessage("no_image");  
    }

    $image = null;

    if (!empty($_FILES['image']['name'])) {
        $uploadDir = "uploads/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $imageName = time() . "_" . basename($_FILES['image']['name']);
        $targetFile = $uploadDir . $imageName;
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
        if (in_array(mime_content_type($_FILES['image']['tmp_name']), $allowedTypes)) {
            move_uploaded_file($_FILES['image']['tmp_name'], $targetFile);
            $image = $targetFile;
        }
    }

    $stmt = $conn->prepare("INSERT INTO products (category_id, name, price, image, best_seller) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isdsi", $category_id, $name, $price, $image, $best_seller);
    if ($stmt->execute()) redirectWithMessage("added");
    $stmt->close();
}

// ========== UPDATE MENU ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_menu'])) {
    $id = intval($_POST['product_id']);
    $category_id = intval($_POST['category']);
    $name = sanitizeInput($_POST['name']);
    $priceRaw = $_POST['price'];            // <-- here
    // Add validation for price
    if (!is_numeric($priceRaw) || floatval($priceRaw) < 0) {
        redirectWithMessage("invalid_price");
    }
    $price = round(floatval($priceRaw), 2);

    $best_seller = isset($_POST['best_seller']) ? 1 : 0;
    $image = $_POST['current_image'] ?? null;

    if (!empty($_FILES['image']['name'])) {
        $uploadDir = "uploads/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $imageName = time() . "_" . basename($_FILES['image']['name']);
        $targetFile = $uploadDir . $imageName;
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
        if (in_array(mime_content_type($_FILES['image']['tmp_name']), $allowedTypes)) {
            move_uploaded_file($_FILES['image']['tmp_name'], $targetFile);
            $image = $targetFile;
         } else {
            redirectWithMessage("invalid_image_type");
        }
    } else {
        // no new upload
        if (empty($image)) {
            // no existing image either — error
            redirectWithMessage("no_image_on_edit");
        }
    }

    $stmt = $conn->prepare("UPDATE products SET category_id=?, name=?, price=?, image=?, best_seller=? WHERE id=?");
    $stmt->bind_param("isdssi", $category_id, $name, $price, $image, $best_seller, $id);
    if ($stmt->execute()) {
        redirectWithMessage("updated");
    }
    $stmt->close();
}

// ========== DELETE MENU ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_menu'])) {
    $id = intval($_POST['delete_menu']);

    $stmt = $conn->prepare("SELECT image FROM products WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($imagePath);
    $stmt->fetch();
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        if ($imagePath && file_exists($imagePath)) unlink($imagePath);
        redirectWithMessage("deleted");
    }
    $stmt->close();
}

// ========== FETCH ==========
$categories = $conn->query("SELECT id,name FROM categories ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
$products = $conn->query("SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id=c.id ORDER BY p.id DESC")->fetch_all(MYSQLI_ASSOC);

$status = $_GET['status'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Eskina Coffee | Admin Menu Items</title>
<link rel="stylesheet" href="./css/menu_items.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.2/css/dataTables.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.1.2/js/dataTables.min.js"></script>

</head>

<body>

<!-- Sidebar -->
<aside class="sidebar">
<button class="sidebar-close">×</button>
  <ul>
    <li class="<?= basename($_SERVER['PHP_SELF']) == 'admin_dashboard.php' ? 'active' : '' ?>"><a href="admin_dashboard.php">Dashboard</a></li>
    <li class="<?= basename($_SERVER['PHP_SELF']) == 'menu_items.php' ? 'active' : '' ?>"><a href="menu_items.php">Menu Items</a></li>
    <li class="<?= basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : '' ?>"><a href="orders.php">Order Transactions</a></li>
    <li class="<?= basename($_SERVER['PHP_SELF']) == 'customers.php' ? 'active' : '' ?>"><a href="customers.php">Customers</a></li>
    <li class="<?= basename($_SERVER['PHP_SELF']) == 'employees.php' ? 'active' : '' ?>"><a href="employees.php">Employees</a></li>
    <li class="<?= basename($_SERVER['PHP_SELF']) == 'create_barista.php' ? 'active' : '' ?>"><a href="create_barista.php">Barista</a></li>
    <li class="<?= basename($_SERVER['PHP_SELF']) == 'admin_order.php' ? 'active' : '' ?>"><a href="admin_order.php">Order Taking</a></li>
    <li class="<?= basename($_SERVER['PHP_SELF']) == 'admin_serve.php' ? 'active' : '' ?>"><a href="admin_serve.php">Order Status</a></li>
  </ul>

  <div class="sidebar-logo">
    <img src="./images/eslogo.jpg" alt="Eskina Coffee" class="logo-img" id="logoutLogo">
    <span class="admin-name"><?= $adminName ?></span>
  </div>
  <div class="logout-popup" id="logoutPopup">Logout</div>
  <form id="logoutForm" action="logout.php" method="POST" style="display:none;"></form>
</aside>

<button class="menu-toggle">☰</button>

<!-- Main Content -->
<main class="main-layout">
  <div class="content">
    <div style="text-align:center; margin-bottom:15px;">
      <h2>Menu Items</h2>
    </div>

    <div style="display:flex; justify-content:flex-end; align-items:center; margin-bottom:20px;">
    <button onclick="openForm()" class="btn-primary">Add Menu</button>
    </div>

    <table id="menuTable" class="menu-table">
      <thead>
        <tr>
          <th>ID</th><th>Image</th><th>Name</th><th>Category</th><th>Price</th><th>Best Seller</th><th>Action</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach($products as $prod): ?>
        <tr>
          <td><?= $prod['id'] ?></td>
          <td><?php if($prod['image']): ?><img src="<?= $prod['image'] ?>" class="table-img"><?php else: ?>-<?php endif; ?></td>
          <td><?= $prod['name'] ?></td>
          <td><?= $prod['category_name'] ?></td>
          <td><?= number_format($prod['price'],2) ?></td>
          <td><?= $prod['best_seller'] ? 'Yes':'No' ?></td>
          <td>
            <button class="edit-btn" title="Edit" data-prod='<?= json_encode($prod) ?>' onclick="openEditForm(JSON.parse(this.dataset.prod))"><i class="fas fa-edit"></i></button>
            <button class="delete-btn" title="Delete" onclick="confirmDelete(<?= $prod['id'] ?>)"><i class="fas fa-trash-alt"></i></button>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</main>

<!-- Add/Edit Modal -->
<div class="modal" id="menuModal">
  <div class="modal-content">
    <h3 id="modalTitle">Add Menu</h3>
    <form method="POST" enctype="multipart/form-data" id="menuForm">
      <input type="hidden" name="product_id" id="product_id">
      <label>Category:</label>
      <select name="category" id="category" required>
        <option value="">Select Category</option>
        <?php foreach($categories as $cat): ?>
          <option value="<?= $cat['id'] ?>"><?= $cat['name'] ?></option>
        <?php endforeach; ?>
      </select>
      <label>Name:</label>
      <input type="text" name="name" id="name" required>
      <label>Price:</label>
      <input type="number" step="0.01" name="price" id="price" required min="0" step="0.01" placeholder="0.00" >
      <label>Image:</label>
     <input type="file" name="image" id="imageInput">
    <input type="hidden" name="current_image" id="current_image" value="">
      <div id="existingImagePreview" style="margin:8px 0;"></div>
    <label><input type="checkbox" name="best_seller" id="best_seller"> Best Seller</label>
      <div class="btn-group">
        <button type="submit" class="btn-primary" id="submitBtn" name="add_menu">Save</button>
        <button type="button" class="btn-secondary" onclick="closeForm()">Cancel</button>
      </div>
    </form>
  </div>
</div>

<script>
// Logout Popup
const logo = document.getElementById("logoutLogo");
const popup = document.getElementById("logoutPopup");
logo.addEventListener("click", () => popup.style.display = popup.style.display === "block" ? "none" : "block");
document.addEventListener("click", e => { if (!logo.contains(e.target) && !popup.contains(e.target)) popup.style.display = "none"; });
popup.addEventListener("click", () => {
  Swal.fire({ title:"Are you sure you want to log out?", icon:"warning", showCancelButton:true,
    confirmButtonColor:"#3D2419", cancelButtonColor:"#3D2419", confirmButtonText:"Yes", cancelButtonText:"No"
  }).then(result => { if(result.isConfirmed){ 
      const table = $('#menuTable').DataTable();
      table.state.clear();      // remove saved state
      table.destroy();      
      document.getElementById("logoutForm").submit(); } });
});

$(document).ready(function(){
  $('#menuTable').DataTable({
    stateSave: true,
    pageLength: 10,
    lengthMenu: [5, 10, 15, 20],
    order: [[0, 'desc']]
  });

  <?php if ($status): ?>
  table.state.clear();
  <?php endif; ?>
});

// Modal Controls
function openForm() {
  document.getElementById('menuModal').style.display = 'flex';
  document.getElementById('modalTitle').textContent = 'Add Menu';
  document.getElementById('submitBtn').name = 'add_menu';
  document.getElementById('menuForm').reset();

   // Clear editing fields
  document.getElementById('product_id').value = '';
  document.getElementById('current_image').value = '';
  document.getElementById('existingImagePreview').innerHTML = '';

   const imageInput = document.getElementById('imageInput');
  imageInput.setAttribute('required', 'required');
}
function openEditForm(data) {
  document.getElementById('menuModal').style.display = 'flex';
  document.getElementById('modalTitle').textContent = 'Edit Menu';
  document.getElementById('submitBtn').name = 'update_menu';
  submitBtn.textContent = 'Update';
  document.getElementById('product_id').value = data.id;
  document.getElementById('category').value = data.category_id;
  document.getElementById('name').value = data.name;
  document.getElementById('price').value = data.price;
  document.getElementById('current_image').value = data.image;
  document.getElementById('best_seller').checked = data.best_seller == 1;

   const imageInput = document.getElementById('imageInput');
  const currentImageField = document.getElementById('current_image');
  const previewDiv = document.getElementById('existingImagePreview');

  currentImageField.value = data.image || '';

  if (data.image) {
    // Show existing image preview
    previewDiv.innerHTML = `<img src="${data.image}" alt="Current Image" style="max-height:100px;">`;
    // Make file input optional (remove required)
    imageInput.removeAttribute('required');
  } else {
    previewDiv.innerHTML = '';
    // If no existing image, still require a new one
    imageInput.setAttribute('required', 'required');
  }
}
function closeForm() { document.getElementById('menuModal').style.display = 'none'; }

// Delete Confirmation
function confirmDelete(id) {
  Swal.fire({
    title: "Are you sure?",
    text: "Are you sure you want to delete this product?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3D2419",
    cancelButtonColor: "#3D2419",
    confirmButtonText: "Yes, delete it"
  }).then(result => {
    if (result.isConfirmed) {
      const form = document.createElement('form');
      form.method = 'POST';
      form.innerHTML = `<input type='hidden' name='delete_menu' value='${id}'>`;
      document.body.appendChild(form);
      form.submit();
    }
  });
}

<?php if ($status): ?>
Swal.fire({
  icon: 'success',
  title: 'Success',
  text: 'Menu <?= $status ?> successfully!',
  confirmButtonColor: '#3D2419'
}).then(() => {
  // Remove ?status=... from URL after alert
  const url = new URL(window.location);
  url.searchParams.delete('status');
  window.history.replaceState({}, document.title, url.pathname);
});
<?php endif; ?>
</script>

<script>
const priceInput = document.getElementById('price');

priceInput.addEventListener('input', (e) => {
  // Remove non-digit except dot
  let v = e.target.value;
  // Allow digits and optional decimal point and two decimals
  // e.g., 123.45
  const regex = /^\d+(\.\d{0,2})?$/;
  if (!regex.test(v)) {
    // If invalid, trim last character
    e.target.value = v.slice(0, -1);
  }
});
</script>

<script>
const sidebar = document.querySelector('.sidebar');
const toggleBtn = document.querySelector('.menu-toggle');
const closeBtn = document.querySelector('.sidebar-close');

toggleBtn.addEventListener('click', () => {
  sidebar.classList.add('active');
});
closeBtn.addEventListener('click', () => {
  sidebar.classList.remove('active');
});
</script>

</body>
</html>
