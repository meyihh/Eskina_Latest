<?php
include '../config.php';
require_once '../auth.php';

$adminName = htmlspecialchars($_SESSION['user']['username'] ?? 'Admin');

// --- Handle AJAX Update (Mark as Done) ---
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_status"])) {
    $ids = json_decode($_POST["ids"], true);
    $status = strtoupper(trim($_POST["status"]));

    if (is_array($ids) && count($ids) > 0) {
        $placeholders = implode(",", array_fill(0, count($ids), "?"));
        $types = str_repeat("i", count($ids));
        $sql = "UPDATE order_items SET status = ? WHERE id IN ($placeholders)";
        $stmt = $conn->prepare($sql);
        $params = array_merge([$status], $ids);
        $stmt->bind_param("s" . $types, ...$params);
        $stmt->execute();

        echo json_encode(["status" => $stmt->affected_rows > 0 ? "success" : "error"]);
    }
    exit;
}

// --- Get Today’s Orders ---
$today = date("Y-m-d");
$sql = "SELECT 
            oi.id, oi.order_id, oi.product_name, oi.quantity, oi.price, oi.status, oi.created_at, 
            o.order_type, o.customer_name, o.points
        FROM order_items oi
        JOIN orders o ON oi.order_id = o.id
        WHERE DATE(o.created_at) = ?
        ORDER BY 
            CASE WHEN oi.status = 'DONE' THEN 1 ELSE 0 END, 
            oi.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $today);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
while ($row = $result->fetch_assoc()) {
    $row["order_type"] = strtoupper(trim($row["order_type"]));
    $items[] = $row;
}

// --- Group Items by Order ID ---
$grouped = [];
foreach ($items as $row) {
    $key = $row['order_id'];
    if (!isset($grouped[$key])) {
        $grouped[$key] = [
            "order_type"    => $row['order_type'],
            "customer_name" => $row['customer_name'],
            "points"        => $row['points'],
            "orders"        => [],
            "created_at"    => $row['created_at'],
            "status"        => $row['status'],
        ];
    }
    $grouped[$key]["orders"][] = $row;
}

foreach ($grouped as $orderId => $group) {
    if (strtoupper($group['status']) === 'DONE') {
        unset($grouped[$orderId]);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Eskina Coffee | Order Status</title>
  <link rel="stylesheet" href="./css/admin_serve.css?v=<?= time() ?>" />
  <link rel="stylesheet" href="./css/admin_dashboard.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
<div class="main-layout">
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

  <h2>Order Status</h2>

    <div class="serve-container">
      <div class="serve-columns">
        <!-- 🟤 DINE-IN -->
        <div class="serve-column">
          <h3><i class="fa-solid fa-mug-saucer"></i> DINE-IN</h3>
          <div class="serve-list">
            <?php foreach ($grouped as $group): if ($group['order_type'] === 'DINE-IN'):
              $total = 0; ?>
              <div class="serve-card" data-order-id="<?= $group['customer_name'] ?>">
                <div class="serve-header">
                  <h3><?= htmlspecialchars($group['customer_name']) ?></h3>
                  <span class="status <?= strtolower($group['status']) ?>"><?= $group['status'] ?></span>
                </div>
                <div class="serve-items">
                  <?php foreach ($group['orders'] as $order):
                        $total += $order['price'] * $order['quantity']; ?>
                    <div class="serve-item" data-id="<?= $order['id'] ?>">
                      <span><?= $order['quantity'] ?>x</span>
                      <span><?= htmlspecialchars($order['product_name']) ?></span>
                    </div>
                  <?php endforeach; ?>
                </div>
                <div class="serve-footer">
                  <p>Total: ₱<?= number_format($total, 2) ?></p>
                  <p><?= date("h:i A", strtotime($group['created_at'])) ?></p>
                </div>
              </div>
            <?php endif; endforeach; ?>
          </div>
        </div>

        <!-- 🟤 TAKE-OUT -->
        <div class="serve-column">
          <h3><i class="fa-solid fa-bag-shopping"></i> TAKE OUT</h3>
          <div class="serve-list">
            <?php foreach ($grouped as $group): if ($group['order_type'] === 'TAKE-OUT'):
              $total = 0; ?>
              <div class="serve-card" data-order-id="<?= $group['customer_name'] ?>">
                <div class="serve-header">
                  <h3><?= htmlspecialchars($group['customer_name']) ?></h3>
                  <span class="status <?= strtolower($group['status']) ?>"><?= $group['status'] ?></span>
                </div>
                <div class="serve-items">
                  <?php foreach ($group['orders'] as $order):
                        $total += $order['price'] * $order['quantity']; ?>
                    <div class="serve-item" data-id="<?= $order['id'] ?>">
                      <span><?= $order['quantity'] ?>x</span>
                      <span><?= htmlspecialchars($order['product_name']) ?></span>
                    </div>
                  <?php endforeach; ?>
                </div>
                <div class="serve-footer">
                  <p>Total: ₱<?= number_format($total, 2) ?></p>
                  <p><?= date("h:i A", strtotime($group['created_at'])) ?></p>
                </div>
              </div>
            <?php endif; endforeach; ?>
          </div>
        </div>
      </div>

      <button class="done-btn" onclick="markAsDone()">Mark as Done</button>
    </div>
  </div>
</div>

<script>
let selected = null;
document.querySelectorAll(".serve-card").forEach(card => {
  card.addEventListener("click", () => {
    if (selected) selected.classList.remove("selected");
    card.classList.add("selected");
    selected = card;
  });
});

function markAsDone() {
  if (!selected) {
    Swal.fire({
            html: `
              <div style="text-align:center;">
                <div style="font-size:28px; font-weight:700; color:#3D2419;">No order selected</div>
                <div style="margin-top:10px; font-size:18px;">
                  Please select a card first.
                </div>
              </div>
            `,
            icon: "warning",
            confirmButtonColor: "#74512D",
          });
    return;
  }

  const ids = Array.from(selected.querySelectorAll(".serve-item")).map(i => i.dataset.id);
  const formData = new FormData();
  formData.append("update_status", "1");
  formData.append("ids", JSON.stringify(ids));
  formData.append("status", "DONE");

  fetch("admin_serve.php", { method: "POST", body: formData })
    .then(r => r.json())
    .then(data => {
      if (data.status === "success") {
        selected.querySelector(".status").textContent = "DONE";
        selected.querySelector(".status").classList.add("done");
       Swal.fire({
            html: `
              <div style="text-align:center;">
                <div style="font-size:28px; font-weight:700; color:#3D2419;">Success!</div>
                <div style="margin-top:10px; font-size:18px;">
                  Order transaction complete!
                </div>
              </div>
            `,
            icon: "success",
            confirmButtonColor: "#74512D",
          });
     } else {
      Swal.fire({
        html: `
          <div style="text-align:center;">
            <div style="font-size:28px; font-weight:700; color:#3D2419;">Error</div>
            <div style="margin-top:10px; font-size:18px;">
              No record updated.
            </div>
          </div>
        `,
        icon: "error",
        confirmButtonColor: "#74512D",
      });
    }
  })
  .catch(() => {
    Swal.fire({
      html: `
        <div style="text-align:center;">
          <div style="font-size:28px; font-weight:700; color:#3D2419;">Error</div>
          <div style="margin-top:10px; font-size:18px;">
            Failed to update order.
          </div>
        </div>
      `,
      icon: "error",
      confirmButtonColor: "#74512D",
    });
  });
}

// --- Logout Behavior ---
const logo = document.getElementById("logoutLogo");
const popup = document.getElementById("logoutPopup");
logo.addEventListener("click", () => popup.style.display = popup.style.display === "block" ? "none" : "block");
document.addEventListener("click", (e) => {
  if (!logo.contains(e.target) && !popup.contains(e.target)) popup.style.display = "none";
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
  }).then(r => { if (r.isConfirmed) document.getElementById("logoutForm").submit(); });
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
