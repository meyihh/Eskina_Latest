<?php
include '../config.php';
require_once '../auth.php';

if ($_SESSION['user']['role'] !== 'admin') {
    header("Location: ../landing.php");
    exit;
}

if (!isset($_GET['user_id'])) {
    echo "No employee selected.";
    exit;
}

$user_id = intval($_GET['user_id']); 

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
  <title>Eskina Coffee | Daily Time Record</title>
  <link rel="stylesheet" href="./css/employees_dtr.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
      <img src="./images/eslogo.jpg" class="logo-img" id="logoutLogo" alt="Logo">
      <span class="admin-name"><?= $adminName ?></span>
    </div>

    <div class="logout-popup" id="logoutPopup">Logout</div>
    <form id="logoutForm" action="logout.php" method="POST" style="display:none;"></form>
  </aside>

  <button class="menu-toggle">☰</button>

  <main class="content">
    <a href="employees.php" class="back-link">BACK</a>
    <h2>Daily Time Record</h2>

    <?php
    $sql_user = "SELECT full_name, email, contact, address FROM accounts WHERE id = $user_id";
    $result_user = $conn->query($sql_user);

    if ($result_user->num_rows > 0) {
        $user = $result_user->fetch_assoc();

        echo '<div class="employee-info">';
        echo '<div>';
        echo '<p><strong>Employee Name:</strong> ' . htmlspecialchars($user['full_name']) . '</p>';
        echo '<p><strong>Email:</strong> ' . htmlspecialchars($user['email']) . '</p>';
        echo '</div>';
        echo '<div>';
        echo '<p><strong>Contact Number:</strong> ' . htmlspecialchars($user['contact']) . '</p>';
        echo '<p><strong>Address:</strong> ' . htmlspecialchars($user['address']) . '</p>';
        echo '</div>';
        echo '</div>';

        $sql_dtr = "SELECT * FROM dtr_logs WHERE user_id = $user_id ORDER BY created_at DESC";
        $result_dtr = $conn->query($sql_dtr);

        echo '<table class="attendance-table">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>DATE</th>';
        echo '<th>TIME IN</th>';
        echo '<th>PHOTO IN</th>';
        echo '<th>TIME OUT</th>';
        echo '<th>PHOTO OUT</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        if ($result_dtr->num_rows > 0) {
            while ($dtr = $result_dtr->fetch_assoc()) {
                $date = $dtr['time_in'] ? date('Y-m-d', strtotime($dtr['time_in'])) : '';
                $time_in = $dtr['time_in'] ? date('H:i:s', strtotime($dtr['time_in'])) : '';
                $time_out = $dtr['time_out'] ? date('H:i:s', strtotime($dtr['time_out'])) : '';
                $photo_in = $dtr['photo_in'] ? '<img src="' . htmlspecialchars($dtr['photo_in']) . '" width="50">' : '';
                $photo_out = $dtr['photo_out'] ? '<img src="' . htmlspecialchars($dtr['photo_out']) . '" width="50">' : '';

                echo '<tr>';
                echo "<td>$date</td>";
                echo "<td>$time_in</td>";
                echo "<td>$photo_in</td>";
                echo "<td>$time_out</td>";
                echo "<td>$photo_out</td>";
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="5">No attendance logged yet</td></tr>';
        }

        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<p>Employee not found.</p>';
    }
    ?>
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
