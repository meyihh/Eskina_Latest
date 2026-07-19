<?php
include '../config.php';
require_once '../auth.php';

if ($_SESSION['user']['role'] !== 'barista') {
    header("Location: ../landing.php");
    exit;
}

$today = date("Y-m-d");
$adminName = htmlspecialchars($_SESSION['user']['username'] ?? 'Admin');

// Fetch admin username (id = 30)
$query = "SELECT username FROM users WHERE id = 30 LIMIT 1";
$result = mysqli_query($conn, $query);
$adminName = ($result && mysqli_num_rows($result) > 0)
    ? htmlspecialchars(mysqli_fetch_assoc($result)['username'])
    : "Unknown";

// ✅ Pending orders count for navbar badge
$pendingQuery = $conn->prepare("
    SELECT COUNT(DISTINCT o.id) AS pending_count
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    WHERE oi.status != 'DONE' AND DATE(o.created_at) = ?
");
$pendingQuery->bind_param("s", $today);
$pendingQuery->execute();
$pendingRow = $pendingQuery->get_result()->fetch_assoc();
$pendingCount = $pendingRow['pending_count'] ?? 0;

// Pagination settings
$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Total completed orders today
$totalQuery = $conn->prepare("
    SELECT COUNT(DISTINCT o.id) AS total_orders
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    WHERE oi.status = 'DONE' AND DATE(o.created_at) = ?
");
$totalQuery->bind_param("s", $today);
$totalQuery->execute();
$totalRow = $totalQuery->get_result()->fetch_assoc();
$totalOrders = $totalRow['total_orders'] ?? 0;
$totalPages = ceil($totalOrders / $limit);

// Fetch paginated completed orders
$sql = "
    SELECT o.id, o.customer_name, o.order_type, o.total_price, o.payment_method, o.created_at
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    WHERE oi.status = 'DONE' AND DATE(o.created_at) = ?
    GROUP BY o.id
    ORDER BY o.created_at DESC
    LIMIT ? OFFSET ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sii", $today, $limit, $offset);
$stmt->execute();
$orders = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Eskina Coffee | Transaction History</title>
<link rel="stylesheet" href="./css/transaction.css" />
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<!-- ✅ Navbar -->
<div class="navbar">
  <div class="brand">
    <div class="logo">
      <img src="./images/eslogo.jpg" alt="Eskina Coffee Logo" onerror="this.onerror=null; this.src='fallback.png'"/>
    </div>
    <span>Eskina Coffee</span>
  </div>

  <div class="hamburger" onclick="toggleMenu()">
    <span></span><span></span><span></span>
  </div>

  <div class="center-icons">
    <a href="main.php" class="icon-btn" title="Menu">
      <div class="circle"><img src="./images/list.png" alt="Menu" class="icon-img" /></div>
    </a>

    <a href="serve.php" class="icon-btn" title="Queue" style="position: relative;">
      <div class="circle">
        <img src="./images/cart.png" class="icon-img" alt="Cart" />
        <span class="notif-badge" id="queue-badge" style="display: <?= $pendingCount > 0 ? 'flex' : 'none' ?>;">
          <?= $pendingCount ?>
        </span>
      </div>
    </a>

    <a href="online_orders.php" class="icon-btn" title="Online Orders">
      <div class="circle"><img src="./images/bag.png" alt="Bag" class="icon-img" /></div>
    </a>

    <a href="transaction.php" class="icon-btn active" title="Transaction">
      <div class="circle"><img src="./images/file.png" alt="File" class="icon-img" /></div>
    </a>
  </div>

  <div class="nav-links">
    <form action="logout.php" method="POST" id="logoutForm" style="display:inline;">
      <a href="#" class="logout-btn" id="logoutBtn">Logout</a>
    </form>
  </div>
</div>

<!-- MAIN CONTENT -->
<main class="content">
  <div class="header-row" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap;">
    <!-- Search bar -->
    <div class="search-container">
      <input type="text" id="searchInput" class="search-input" placeholder="Search by ID, Customer, or Type..." />
    </div>
  </div>

  <div class="table-container">
    <table class="order-table" id="orderTable">
      <thead>
        <tr>
          <th>Order ID</th>
          <th>Customer</th>
          <th>Order Type</th>
          <th>Items</th>
          <th>Total</th>
          <th>Payment</th>
          <th>Date & Time</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if ($orders && $orders->num_rows > 0) {
            while ($row = $orders->fetch_assoc()) {
                $itemsSql = "SELECT product_name, quantity FROM order_items WHERE order_id = {$row['id']} AND status = 'DONE'";
                $items = $conn->query($itemsSql);
                $itemsList = '';
                if ($items && $items->num_rows > 0) {
                    while ($item = $items->fetch_assoc()) {
                        $itemsList .= "{$item['quantity']}x {$item['product_name']}<br>";
                    }
                }

                echo "
<tr class='order-row'
    data-order-id='{$row['id']}'
    data-customer='{$row['customer_name']}'
    data-type='{$row['order_type']}'
    data-total='₱{$row['total_price']}'
    data-payment='{$row['payment_method']}'
    data-date='" . date("F j, Y | g:i A", strtotime($row['created_at'])) . "'
    data-items=\"" . htmlspecialchars($itemsList, ENT_QUOTES) . "\">
    <td>{$row['id']}</td>
    <td>{$row['customer_name']}</td>
    <td>{$row['order_type']}</td>
    <td>{$itemsList}</td>
    <td>₱{$row['total_price']}</td>
    <td>{$row['payment_method']}</td>
    <td>" . date("F j, Y | g:i A", strtotime($row['created_at'])) . "</td>
</tr>";
            }
        } else {
            echo "<tr><td colspan='7' style='text-align:center;'>No completed orders found today.</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <?php if($totalPages > 1): ?>
    <div class="pagination" style="text-align:center; margin-top:15px;">
        <?php if($page > 1): ?>
            <a href="?page=<?= $page-1 ?>">&laquo; Previous</a>
        <?php endif; ?>

        <?php for($i=1; $i<=$totalPages; $i++): ?>
            <a href="?page=<?= $i ?>" class="<?= $i==$page?'active':'' ?>"><?= $i ?></a>
        <?php endfor; ?>

        <?php if($page < $totalPages): ?>
            <a href="?page=<?= $page+1 ?>">Next &raquo;</a>
        <?php endif; ?>
    </div>
  <?php endif; ?>
</main>

<script>
  // Click row to show SweetAlert modal with full details
  // ✅ Transaction Details Popup (no .swal2-popup usage)
document.querySelectorAll(".order-row").forEach(row => {
  row.addEventListener("click", () => {
    const cells = row.cells;
    Swal.fire({
      html: `
        <div class="transaction-popup">
          <h2>Transaction Details</h2>
          <p><strong>Order #:</strong> ${cells[0].textContent}</p>
          <p><strong>Customer:</strong> ${cells[1].textContent}</p>
          <p><strong>Order Type:</strong> ${cells[2].textContent}</p>
          <p><strong>Total:</strong> ${cells[4].textContent}</p>
          <p><strong>Payment:</strong> ${cells[5].textContent}</p>
          <p><strong>Date & Time:</strong> ${cells[6].textContent}</p>
          <p><strong>Items:</strong></p>
          <div class="items-box">${row.dataset.items.replace(/<br>/g, "<br>")}</div>
          <div style="text-align:center;">
            <button class="close-btn" onclick="Swal.close()">Close</button>
          </div>
        </div>
      `,
      showConfirmButton: false,
      background: "transparent",
      width: "auto",
      padding: 0
    });
  });
});


  document.getElementById("logoutBtn").addEventListener("click", function(e) {
  e.preventDefault();

  Swal.fire({
    title: "Are you sure you want to log out?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "rgba(87, 54, 7, 1)",
    cancelButtonColor: "rgba(87, 54, 7, 1)",
    confirmButtonText: "Yes",
    cancelButtonText: "No"
  }).then((result) => {
    if (result.isConfirmed) {
      document.getElementById("logoutForm").submit();
    }
  });
});

  // Live search filter
  document.getElementById("searchInput").addEventListener("keyup", function() {
    const filter = this.value.toLowerCase();
    document.querySelectorAll("#orderTable tbody tr").forEach(row => {
      row.style.display = row.textContent.toLowerCase().includes(filter) ? "" : "none";
    });
  });

  // Mobile menu toggle
  function toggleMenu() { document.getElementById("mobileMenu")?.classList.toggle("active"); }
</script>
</body>
</html>
