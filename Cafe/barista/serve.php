<?php
include '../config.php';
require_once '../auth.php';

if ($_SESSION['user']['role'] !== 'barista') {
    header("Location: ../landing.php");
    exit;
}

$today = date("Y-m-d");

// Count pending orders for notif badge (orders with at least one item not DONE)
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
$limit = 10; // orders per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Fetch total orders for pagination (excluding fully DONE orders)
$totalQuery = $conn->prepare("
    SELECT COUNT(DISTINCT o.id) AS total_orders
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    WHERE DATE(o.created_at) = ? AND oi.status != 'DONE'
");
$totalQuery->bind_param("s", $today);
$totalQuery->execute();
$totalRow = $totalQuery->get_result()->fetch_assoc();
$totalOrders = $totalRow['total_orders'] ?? 0;
$totalPages = ceil($totalOrders / $limit);

// Fetch today’s orders with pagination (excluding fully DONE orders)
$sql = "
    SELECT o.id, o.customer_name, o.order_type, o.total_price, o.payment_method, o.created_at
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    WHERE DATE(o.created_at) = ?
      AND EXISTS (
          SELECT 1 FROM order_items oi2
          WHERE oi2.order_id = o.id AND oi2.status != 'DONE'
      )
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
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Eskina Coffee | Queue</title>
<link rel="stylesheet" href="./css/serve.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<!-- NAVBAR -->
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

      <a href="serve.php" class="icon-btn active" title="Queue" style="position: relative;">
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

      <a href="transaction.php" class="icon-btn" title="Transaction">
        <div class="circle"><img src="./images/file.png" alt="File" class="icon-img" /></div>
      </a>
  </div>

  <div class="nav-links">
    <form action="logout.php" method="POST" id="logoutForm" style="display:inline;">
      <a href="#" class="logout-btn" id="logoutBtn">Logout</a>
    </form>
  </div>
</div>

<!-- Mobile menu -->
<div class="mobile-menu" id="mobileMenu">
  <div class="mobile-icons">
    <a href="main.php" class="icon-btn"><div class="circle"><img src="./images/list.png"></div></a>
    <a href="serve.php" class="icon-btn" style="position: relative;">
      <div class="circle">
        <img src="./images/cart.png">
        <span class="notif-badge" id="queue-badge-mobile" style="display: <?= $pendingCount > 0 ? 'flex' : 'none' ?>;"><?= $pendingCount ?></span>
      </div>
    </a>
    <a href="online_orders.php" class="icon-btn"><div class="circle"><img src="./images/bag.png"></div></a>
  </div>
  <a href="#" id="logoutBtnMobile" class="logout-btn">Logout</a>
</div>

<!-- MAIN CONTENT -->
<div class="container">
  <div class="search-container">
    <input type="text" id="searchInput" class="search-input" placeholder="Search by ID, Customer, or Type...">
  </div>

  <div class="table-wrapper">
    <table id="transactionTable">
      <thead>
        <tr>
          <th>Order ID</th>
          <th>Customer</th>
          <th>Order Type</th>
          <th>Items</th>
          <th>Total</th>
          <th>Payment</th>
          <th>Date & Time</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if ($orders && $orders->num_rows > 0) {
            while ($row = $orders->fetch_assoc()) {
                $itemsSql = "SELECT product_name, quantity, status FROM order_items WHERE order_id = {$row['id']}";
                $itemsResult = $conn->query($itemsSql);
                $itemsList = '';
                while ($item = $itemsResult->fetch_assoc()) {
                    $itemsList .= htmlspecialchars($item['quantity'] . "x " . $item['product_name']) . "<br>";
                }
                $statusClass = 'pending';
                $rowStatus = 'PREPARING';
                $actionBtn = "<button class='done-btn' data-order-id='{$row['id']}' title='Mark as Done'>
                                <img src='./images/correct.png' alt='Done'/>
                              </button>";

                echo "<tr class='order-row' data-items=\"" . htmlspecialchars($itemsList, ENT_QUOTES) . "\">
                        <td>{$row['id']}</td>
                        <td>{$row['customer_name']}</td>
                        <td>{$row['order_type']}</td>
                        <td>{$itemsList}</td>
                        <td>₱" . number_format($row['total_price'], 2) . "</td>
                        <td>{$row['payment_method']}</td>
                        <td>" . date("F j, Y | g:i A", strtotime($row['created_at'])) . "</td>
                        <td><span class='status $statusClass'>{$rowStatus}</span></td>
                        <td class='action-cell'>$actionBtn</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='9' style='text-align:center;'>No orders for today.</td></tr>";
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
</div>

<script>
// Click row to show details
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

// Live search
document.getElementById("searchInput").addEventListener("keyup", function() {
  const filter = this.value.toLowerCase();
  document.querySelectorAll("#transactionTable tbody tr").forEach(row => {
    row.style.display = row.textContent.toLowerCase().includes(filter) ? "" : "none";
  });
});

// ✅ Logout (Desktop)
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

// ✅ Logout (Mobile)
document.getElementById("logoutBtnMobile").addEventListener("click", function(e) {
  e.preventDefault();
  Swal.fire({
    title: "Are you sure you want to log out?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "rgba(87, 54, 7, 1)",
    cancelButtonColor: "rgba(87, 54, 7, 1)",
    confirmButtonText: "Yes",
    cancelButtonText: "No"
  }).then(result => {
    if (result.isConfirmed) {
      document.getElementById("logoutForm").submit();
    }
  });
});

// ✅ Toggle mobile menu
function toggleMenu() {
  document.getElementById("mobileMenu").classList.toggle("active");
}

// ✅ Mark order as DONE
document.querySelectorAll(".done-btn").forEach(btn => {
  btn.addEventListener("click", function(e) {
    e.stopPropagation();
    const orderId = this.dataset.orderId;
    Swal.fire({
      title: "Mark this order as DONE?",
      icon: "question",
      showCancelButton: true,
      confirmButtonColor: "rgba(87, 54, 7, 1)",
      cancelButtonColor: "#888",
      confirmButtonText: "Yes",
      cancelButtonText: "No"
    }).then(result => {
      if (result.isConfirmed) {
        fetch("mark_done.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: "order_id=" + orderId
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            const row = btn.closest("tr");
            row.parentNode.removeChild(row);

            const badge = document.getElementById("queue-badge");
            if (badge) {
              let count = parseInt(badge.textContent) - 1;
              badge.textContent = count > 0 ? count : "";
              badge.style.display = count > 0 ? "flex" : "none";
            }
            const mobileBadge = document.getElementById("queue-badge-mobile");
            if (mobileBadge) {
              let count = parseInt(mobileBadge.textContent) - 1;
              mobileBadge.textContent = count > 0 ? count : "";
              mobileBadge.style.display = count > 0 ? "flex" : "none";
            }

            Swal.fire("Done!", "Ready to Pick-up item.", "success");
          } else {
            Swal.fire("Error", data.message, "error");
          }
        })
        .catch(err => Swal.fire("Error", "Something went wrong.", "error"));
      }
    });
  });
});
</script>
</body>
</html>
