<?php
include '../config.php';
require_once '../auth.php';

// Only admin
if ($_SESSION['user']['role'] !== 'admin') {
    header("Location: ../landing.php");
    exit;
}
$adminName = htmlspecialchars($_SESSION['user']['username'] ?? 'Admin');

// Fetch admin username
$query = "SELECT username FROM users WHERE id = 30 LIMIT 1";
$result = mysqli_query($conn, $query);
$adminName = ($result && mysqli_num_rows($result) > 0) ? htmlspecialchars(mysqli_fetch_assoc($result)['username']) : "Unknown";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Eskina Coffee | Employees</title>
<link rel="stylesheet" href="./css/employee.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<div class="main-layout">
  <!-- SIDEBAR -->
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
      <img src="./images/eslogo.jpg" class="logo-img" id="logoutLogo" alt="Logo">
      <span class="admin-name"><?= $adminName ?></span>
    </div>

    <div class="logout-popup" id="logoutPopup">Logout</div>
    <form id="logoutForm" action="logout.php" method="POST" style="display:none;"></form>
  </aside>

  <button class="menu-toggle">☰</button>

  <!-- MAIN CONTENT -->
  <main class="content">
  <h2>Employees</h2>
  <div class="employee-container">
    <?php
    $sql = "SELECT id, full_name, email, contact, address FROM accounts";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<a href="employees_dtr.php?user_id=' . $row['id'] . '" class="employee-card">';
            echo '<h3>' . htmlspecialchars($row['full_name']) . '</h3>';
            echo '<p><strong>Email:</strong> ' . htmlspecialchars($row['email']) . '</p>';
            echo '<p><strong>Contact:</strong> ' . htmlspecialchars($row['contact']) . '</p>';
            echo '<p><strong>Address:</strong> ' . htmlspecialchars($row['address']) . '</p>';
            echo '</a>';
        }
    } else {
        echo '<p>No employees found.</p>';
    }
    ?>
  </div>
</main>
</div>

<script>
// Logout popup toggle
const logo = document.getElementById("logoutLogo");
const popup = document.getElementById("logoutPopup");

logo.addEventListener("click", (e) => {
  e.stopPropagation();
  popup.style.display = popup.style.display === "block" ? "none" : "block";
});

document.addEventListener("click", (e) => {
  if (!logo.contains(e.target) && !popup.contains(e.target)) {
    popup.style.display = "none";
  }
});

// Logout confirmation
popup.addEventListener("click", (e) => {
  e.stopPropagation();
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
