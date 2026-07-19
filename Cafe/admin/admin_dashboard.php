<?php
include '../config.php';
require_once '../auth.php';

if (
    !isset($_SESSION['user']) ||
    empty($_SESSION['user']['role']) ||
    $_SESSION['user']['role'] !== 'admin'
) {
    session_unset();
    session_destroy();
    header("Location: ../landing.php");
    exit;
}

if (isset($_SESSION['active_role']) && $_SESSION['active_role'] !== 'admin') {
    
    session_unset();
    session_destroy();
    header("Location: ../login.php");
    exit;
} else {
    
    $_SESSION['active_role'] = 'admin';
}

$query = "SELECT username FROM users WHERE id = 30 LIMIT 1";
$result = mysqli_query($conn, $query);
$adminName = ($result && mysqli_num_rows($result) > 0)
    ? htmlspecialchars(mysqli_fetch_assoc($result)['username'])
    : "Unknown";

$topQuery = "
    SELECT product_name, SUM(quantity) AS total_sold
    FROM order_items
    GROUP BY product_name
    ORDER BY total_sold DESC
    LIMIT 3
";
$topResult = mysqli_query($conn, $topQuery);

$topItems = [];
if ($topResult && mysqli_num_rows($topResult) > 0) {
    while ($row = mysqli_fetch_assoc($topResult)) {
        $topItems[] = [
            'name' => htmlspecialchars($row['product_name']),
            'sold' => (int)$row['total_sold']
        ];
    }
}

$salesQuery = "
    SELECT DATE_FORMAT(created_at, '%M') AS month, 
           SUM(price * quantity) AS total_sales
    FROM order_items
    GROUP BY MONTH(created_at)
    ORDER BY MONTH(created_at)
";
$salesResult = mysqli_query($conn, $salesQuery);

$months = [];
$sales = [];
if ($salesResult && mysqli_num_rows($salesResult) > 0) {
    while ($row = mysqli_fetch_assoc($salesResult)) {
        $months[] = $row['month'];
        $sales[] = round($row['total_sales'], 2);
    }
}

$employeeQuery = "SELECT COUNT(*) AS total_employees FROM accounts";
$employeeResult = mysqli_query($conn, $employeeQuery);
$employeeCount = ($employeeResult && mysqli_num_rows($employeeResult) > 0)
    ? (int)mysqli_fetch_assoc($employeeResult)['total_employees']
    : 0;

$customerQuery = "SELECT COUNT(DISTINCT id) AS total_customers FROM orders";
$customerResult = mysqli_query($conn, $customerQuery);
$customerCount = ($customerResult && mysqli_num_rows($customerResult) > 0)
    ? (int)mysqli_fetch_assoc($customerResult)['total_customers']
    : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Eskina Coffee | Admin Dashboard</title>
  <link rel="stylesheet" href="./css/admin_dashboard.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    /* Logout popup */
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
      z-index: 10;
      transition: all 0.2s ease;
    }
    .logout-popup:hover { background-color: #5b3322; 
    }

    .summary-container {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 15px;
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

    <div class="logout-popup" id="logoutPopup">Logout</div>
    <form id="logoutForm" action="logout.php" method="POST" style="display:none;"></form>
  </aside>

  <button class="menu-toggle">☰</button>

<div class="dashboard-container">
  <h2>Dashboard</h2>

  <!-- Top Summary Cards -->
  <div class="summary-row">
    <a href="employees.php" class="summary-card">
      <h3>Employees</h3>
      <p><?= $employeeCount ?></p>
    </a>
    <a href="customers.php" class="summary-card">
      <h3>Customers</h3>
      <p><?= $customerCount ?></p>
    </a>
    <div class="summary-card" style="background-color:#BFAB99; color:#3D2419;">
      <h3>Total Sales</h3>
      <p>₱<?= number_format(array_sum($sales), 2) ?></p>
    </div>
  </div>

  <!-- Charts Row -->
  <div class="charts-row">
    <div class="sales-summary">
      <h2>Monthly Sales Summary</h2>
      <canvas id="salesChart"></canvas>
    </div>

    <div class="top-products-chart">
      <h2>Top 3 Best-Selling Products</h2>
      <canvas id="topProductsChart"></canvas>
    </div>
  </div>
</div>

</div>

<script>

const logo = document.getElementById("logoutLogo");
const popup = document.getElementById("logoutPopup");
logo.addEventListener("click", () => {
  popup.style.display = popup.style.display === "block" ? "none" : "block";
});
document.addEventListener("click", (e) => {
  if (!logo.contains(e.target) && !popup.contains(e.target)) {
    popup.style.display = "none";
  }
});
popup.addEventListener("click", () => {
  Swal.fire({
    html: '<div style="text-align: center; font-size: 30px; font-weight: bold;">Are you sure you want to log out?</div>',
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


const ctx = document.getElementById('salesChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($months) ?>,
        datasets: [{
            label: 'Total Sales (₱)',
            data: <?= json_encode($sales) ?>,
            backgroundColor: '#3D2419',
            borderColor: '#BFAB99',
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: { color: '#3D2419' }
            },
            x: {
                ticks: { color: '#3D2419' }
            }
        },
        plugins: {
            legend: { display: false },
            tooltip: {
              callbacks: {
                label: function(context) {
                  return '₱' + context.formattedValue;
                }
              }
            }
        }
    }
});

const pieCtx = document.getElementById('topProductsChart').getContext('2d');
new Chart(pieCtx, {
  type: 'pie',
  data: {
    labels: <?= json_encode(array_column($topItems, 'name')) ?>,
    datasets: [{
      data: <?= json_encode(array_column($topItems, 'sold')) ?>,
      backgroundColor: ['#3D2419', '#BFAB99', '#8B5E3C'],
      borderColor: '#fff',
      borderWidth: 2
    }]
  },
  options: {
    responsive: true,
    plugins: {
      legend: {
        position: 'bottom',
        labels: { color: '#3D2419', font: { size: 14 } }
      },
      tooltip: {
        callbacks: {
          label: function(context) {
            return context.label + ': ' + context.formattedValue + ' sold';
          }
        }
      }
    }
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
