<?php
include '../config.php';
require_once '../auth.php';

if ($_SESSION['user']['role'] !== 'admin') {
    header("Location: ../landing.php");
    exit;
}

$adminName = htmlspecialchars($_SESSION['user']['username'] ?? 'Admin');

$query = "SELECT username FROM users WHERE id = 30 LIMIT 1";
$result = mysqli_query($conn, $query);
$adminName = ($result && mysqli_num_rows($result) > 0)
    ? htmlspecialchars(mysqli_fetch_assoc($result)['username'])
    : "Unknown";

function displayOrders($conn, $type, $title)
{
    $limit = 10;
    $pageParam = strtolower(str_replace('-', '_', $type)) . '_page';
    $page = isset($_GET[$pageParam]) ? (int)$_GET[$pageParam] : 1;
    if ($page < 1) $page = 1;
    $offset = ($page - 1) * $limit;

    $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
    $conditions = "o.order_type = '$type' AND o.id IN (SELECT order_id FROM order_items WHERE status='DONE')";

    switch ($filter) {
        case 'day':
            if (isset($_GET['date'])) {
            $date = $_GET['date'];  // expect format YYYY-MM-DD
            $conditions .= " AND DATE(o.created_at) = '$date'";
            } else {
                $conditions .= " AND DATE(o.created_at) = CURDATE()";
            }
        break;
        case 'month':
            if (isset($_GET['year']) && isset($_GET['month'])) {
                $year = intval($_GET['year']);
                $month = intval($_GET['month']);
                $conditions .= " AND MONTH(o.created_at) = $month AND YEAR(o.created_at) = $year";
            } else {
                $conditions .= " AND MONTH(o.created_at) = MONTH(CURDATE()) AND YEAR(o.created_at) = YEAR(CURDATE())";
            }
            break;
        case 'year':
            if (isset($_GET['year'])) {
                $year = intval($_GET['year']);
                $conditions .= " AND YEAR(o.created_at) = $year";
            } else {
                $conditions .= " AND YEAR(o.created_at) = YEAR(CURDATE())";
            }
            break;
        default:
            break;
    }

    $countSql = "SELECT COUNT(*) as total FROM orders o WHERE $conditions";
    $countResult = $conn->query($countSql);
    $totalRows = ($countResult && $countResult->num_rows > 0) ? $countResult->fetch_assoc()['total'] : 0;
    $totalPages = ceil($totalRows / $limit);

    $sql = "SELECT o.id, o.customer_name, o.total_price, o.created_at, o.payment_method
            FROM orders o
            WHERE $conditions
            ORDER BY o.created_at DESC
            LIMIT $limit OFFSET $offset";
    $orders = $conn->query($sql);

    echo "<div class='order-column'>";
    echo "<h3>$title</h3>";

    if ($orders && $orders->num_rows > 0) {
        $currentDate = null;
        while ($row = $orders->fetch_assoc()) {
            $orderDate = date("F j, Y", strtotime($row['created_at']));
            if ($orderDate !== $currentDate) {
                if ($currentDate !== null) echo "<hr class='date-divider'>";
                echo "<h4 class='order-date-heading'>$orderDate</h4>";
                $currentDate = $orderDate;
            }

            $itemsSql = "SELECT product_name, quantity FROM order_items WHERE order_id = {$row['id']} AND status='DONE'";
            $items = $conn->query($itemsSql);
            $itemsList = "";
            if ($items && $items->num_rows > 0) {
                while ($item = $items->fetch_assoc()) {
                    $itemsList .= "{$item['quantity']}x {$item['product_name']}<br>";
                }
            }

            echo "
            <div class='order-card'
                 data-order-id='{$row['id']}'
                 data-customer='{$row['customer_name']}'
                 data-total='₱{$row['total_price']}'
                 data-payment='{$row['payment_method']}'
                 data-date='{$row['created_at']}'
                 data-items=\"" . htmlspecialchars($itemsList, ENT_QUOTES) . "\">
                <p><strong>Order #:</strong> {$row['id']}</p>
                <p><strong>Customer:</strong> {$row['customer_name']}</p>
            </div>";
        }
    } else {
        echo "<p>No $title orders found for this period.</p>";
    }

    if ($totalPages > 1) {
        echo "<div class='pagination'>";
        if ($page > 1) echo "<a href='?{$pageParam}=" . ($page - 1) . "&filter=$filter'>&laquo; Prev</a>";
        echo "<span>Page $page of $totalPages</span>";
        if ($page < $totalPages) echo "<a href='?{$pageParam}=" . ($page + 1) . "&filter=$filter'>Next &raquo;</a>";
        echo "</div>";
    }

    echo "</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Eskina Coffee | Order Transactions</title>
  <link rel="stylesheet" href="./css/orders.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
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
.logout-popup:hover { background-color: #5b3322; }
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

  <main class="content">
    <div class="header-row">
      <h2>Order Transactions</h2>
      <div class="filter-container">
        <label for="filter">Filter by:</label>
        <select id="filter" onchange="applyFilter()">
          <option value="all" <?= (!isset($_GET['filter']) || $_GET['filter']=='all') ? 'selected' : '' ?>>All</option>
          <option value="day" <?= (isset($_GET['filter']) && $_GET['filter']=='day') ? 'selected' : '' ?>>Day</option>
          <option value="month" <?= (isset($_GET['filter']) && $_GET['filter']=='month') ? 'selected' : '' ?>>Month</option>
          <option value="year" <?= (isset($_GET['filter']) && $_GET['filter']=='year') ? 'selected' : '' ?>>Year</option>
        </select>
      </div>
    </div>

    <div id="orders-container" class="orders-columns">
      <?php
      displayOrders($conn, "DINE-IN", "DINE-IN");
      displayOrders($conn, "TAKE-OUT", "TAKE OUT");
      displayOrders($conn, "ONLINE", "ONLINE ORDERS");
      ?>
    </div>

    <div id="orderModal" class="order-modal">
      <div class="order-modal-content">
        <span class="order-modal-close">&times;</span>
        <h3>Order History</h3>
        <div id="orderDetail"></div>
      </div>
    </div>
  </main>
</div>

<script>
const filterSelect = document.getElementById("filter");
let previousFilter = filterSelect.value;

function applyFilter() {
  const filter = filterSelect.value;

  // If user selects the same filter again (like Month → Month)
  if (filter === previousFilter) {
    if (filter === "month") {
      Swal.fire({
        title: "Select a month",
        html: `<input type="month" id="monthPicker" class="swal2-input">`,
        showCancelButton: true,
        confirmButtonText: "Apply",
        cancelButtonText: "Cancel",
        confirmButtonColor: "#3D2419",
        cancelButtonColor: "#3D2419",
        preConfirm: () => document.getElementById("monthPicker").value
      }).then(result => {
        if (result.isConfirmed && result.value) {
          const [year, month] = result.value.split("-");
          window.location.href = `orders.php?filter=month&year=${year}&month=${month}`;
        }
      });
    }
    return; // Stop further execution
  }

  previousFilter = filter;

  // Keep your original applyFilter logic below this
  if (filter === "all") {
    window.location.href = "orders.php?filter=all";
    return;
  }

  if (filter === "day") {
    Swal.fire({
      title: "Select a date",
      input: "date",
      showCancelButton: true,
      confirmButtonText: "Apply",
      cancelButtonText: "Cancel",
      confirmButtonColor: "#3D2419",
      cancelButtonColor: "#3D2419",
    }).then(result => {
      if (result.isConfirmed && result.value) {
        window.location.href = `orders.php?filter=day&date=${result.value}`;
      }
    });
  }

  else if (filter === "month") {
    Swal.fire({
      title: "Select a month",
      html: `<input type="month" id="monthPicker" class="swal2-input">`,
      showCancelButton: true,
      confirmButtonText: "Apply",
      cancelButtonText: "Cancel",
      confirmButtonColor: "#3D2419",
      cancelButtonColor: "#3D2419",
      preConfirm: () => document.getElementById("monthPicker").value
    }).then(result => {
      if (result.isConfirmed && result.value) {
        const [year, month] = result.value.split("-");
        window.location.href = `orders.php?filter=month&year=${year}&month=${month}`;
      }
    });
  }

  else if (filter === "year") {
    Swal.fire({
      title: "Select a year",
      html: `<input type="number" id="yearPicker" class="swal2-input" 
               min="2020" max="${new Date().getFullYear()}" 
               value="${new Date().getFullYear()}">`,
      showCancelButton: true,
      confirmButtonText: "Apply",
      cancelButtonText: "Cancel",
      confirmButtonColor: "#3D2419",
      cancelButtonColor: "#3D2419",
      preConfirm: () => document.getElementById("yearPicker").value
    }).then(result => {
      if (result.isConfirmed && result.value) {
        window.location.href = `orders.php?filter=year&year=${result.value}`;
      }
    });
  }
}


function getWeekNumber(d) {
  d = new Date(Date.UTC(d.getFullYear(), d.getMonth(), d.getDate()));
  const dayNum = d.getUTCDay() || 7;
  d.setUTCDate(d.getUTCDate() + 4 - dayNum);
  const yearStart = new Date(Date.UTC(d.getUTCFullYear(), 0, 1));
  return Math.ceil((((d - yearStart) / 86400000) + 1) / 7);
}

const logo = document.getElementById("logoutLogo");
const popup = document.getElementById("logoutPopup");

logo.addEventListener("click", e => {
  e.stopPropagation();
  popup.style.display = popup.style.display === "block" ? "none" : "block";
});

document.addEventListener("click", e => {
  if (!logo.contains(e.target) && !popup.contains(e.target)) popup.style.display = "none";
});

popup.addEventListener("click", () => {
  Swal.fire({
    title: "Are you sure you want to log out?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3D2419",
    cancelButtonColor: "#3D2419",
    confirmButtonText: "Yes",
    cancelButtonText: "No"
  }).then(result => {
    if (result.isConfirmed) document.getElementById("logoutForm").submit();
    else popup.style.display = "none";
  });
});

const orderCards = document.querySelectorAll(".order-card");
const orderModal = document.getElementById("orderModal");
const orderDetail = document.getElementById("orderDetail");
const orderModalClose = document.querySelector(".order-modal-close");

orderCards.forEach(card => {
  card.addEventListener("click", () => {
    const { orderId, customer, total, payment, date, items } = card.dataset;
    orderDetail.innerHTML = `
      <p><strong>Order #:</strong> ${orderId}</p>
      <p><strong>Customer:</strong> ${customer}</p>
      <p><strong>Total:</strong> ${total}</p>
      <p><strong>Payment:</strong> ${payment}</p>
      <p><strong>Date & Time:</strong> ${date}</p>
      <p><strong>Items:</strong><br>${items.replace(/<br>/g, "<br>")}</p>`;
    orderModal.style.display = "flex";
  });
});

orderModalClose.addEventListener("click", () => orderModal.style.display = "none");
window.addEventListener("click", e => { if (e.target === orderModal) orderModal.style.display = "none"; });
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
