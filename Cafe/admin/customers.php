<?php
include '../config.php';
require_once '../auth.php';

// Ensure user is admin
if ($_SESSION['user']['role'] !== 'admin') {
    header("Location: ../landing.php");
    exit;
}

$adminName = htmlspecialchars($_SESSION['user']['username'] ?? 'Admin');
// ✅ Fetch username from the 'users' table where id = 30
$query = "SELECT username FROM users WHERE id = 30 LIMIT 1";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $adminName = htmlspecialchars($row['username']);
} else {
    $adminName = "Unknown"; // fallback if no user found
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Eskina Coffee | Customers</title>
  <link rel="stylesheet" href="./css/customers.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    /* Small logout popup style */
    .logout-popup {
      display: none;
      position: absolute;
      bottom: 80px;
      left: 25px;
      background-color: #3D2419;
      color: #fff;
      border-radius: 8px;
      padding: 8px 14px;
      font-size: 0.9rem;
      cursor: pointer;
      box-shadow: 0 2px 10px rgba(0,0,0,0.2);
      transition: all 0.2s ease;
      z-index: 10;
    }

    .logout-popup:hover {
      background-color: #5b3322;
    }
  </style>
</head>
<body>

<div class="main-layout">
  <aside class="sidebar">
    <button class="sidebar-close">×</button>
    <ul>
      <li class="<?= basename($_SERVER['PHP_SELF']) == 'admin_dashboard.php' ? 'active' : '' ?>">
        <a href="admin_dashboard.php">Dashboard</a>
      </li>
      <li class="<?= basename($_SERVER['PHP_SELF']) == 'menu_items.php' ? 'active' : '' ?>">
        <a href="menu_items.php">Menu Items</a>
      </li>
      <li class="<?= basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : '' ?>">
        <a href="orders.php">Order Transactions</a>
      </li>
      <li class="<?= basename($_SERVER['PHP_SELF']) == 'customers.php' ? 'active' : '' ?>">
        <a href="customers.php">Customers</a>
      </li>
      <li class="<?= basename($_SERVER['PHP_SELF']) == 'employees.php' ? 'active' : '' ?>">
        <a href="employees.php">Employees</a>
      </li>
      <li class="<?= basename($_SERVER['PHP_SELF']) == 'create_barista.php' ? 'active' : '' ?>">
        <a href="create_barista.php">Barista</a>
      </li>
      <li class="<?= basename($_SERVER['PHP_SELF']) == 'admin_order.php' ? 'active' : '' ?>">
        <a href="admin_order.php">Order Taking</a>
      </li>
      <li class="<?= basename($_SERVER['PHP_SELF']) == 'admin_serve.php' ? 'active' : '' ?>">
        <a href="admin_serve.php">Order Status</a>
      </li>
    </ul>

    <div class="sidebar-logo">
      <img src="./images/eslogo.jpg" alt="Eskina Coffee" class="logo-img" id="logoutLogo">
      <span class="admin-name"><?= $adminName ?></span>
    </div>

    <!-- Small Logout Popup -->
    <div class="logout-popup" id="logoutPopup">Logout</div>

    <!-- Hidden logout form -->
    <form id="logoutForm" action="logout.php" method="POST" style="display:none;"></form>
  </aside>

  <button class="menu-toggle">☰</button>

  <main class="content">
    <h2>Customers</h2>
    <p>Welcome, <?= $adminName ?>! Here you can manage customer information.</p>
  </main>
</div>

<script>
const logo = document.getElementById("logoutLogo");
const popup = document.getElementById("logoutPopup");

// 🔹 Toggle popup visibility when clicking logo
logo.addEventListener("click", (e) => {
  e.stopPropagation();
  popup.style.display = popup.style.display === "block" ? "none" : "block";
});

// 🔹 Hide popup when clicking outside
document.addEventListener("click", (e) => {
  if (!popup.contains(e.target) && !logo.contains(e.target)) {
    popup.style.display = "none";
  }
});

// 🔹 SweetAlert confirmation when clicking Logout
popup.addEventListener("click", (e) => {
  e.stopPropagation(); // prevent triggering document click
  Swal.fire({
    title: "Are you sure you want to log out?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3D2419",
    cancelButtonColor: "#3D2419",
    confirmButtonText: "Yes",
    cancelButtonText: "No"
  }).then((result) => {
    if (result.isConfirmed) {
      document.getElementById("logoutForm").submit();
    } else {
      popup.style.display = "none";
    }
  });
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
