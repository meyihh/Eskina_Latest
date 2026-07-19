<?php
include '../config.php';
require_once '../auth.php';

$today = date("Y-m-d");

$pendingQuery = $conn->prepare("
    SELECT COUNT(DISTINCT o.customer_name) AS pending_count
    FROM order_items oi
    JOIN orders o ON oi.order_id = o.id
    WHERE oi.status != 'DONE' AND DATE(o.created_at) = ?
");
$pendingQuery->bind_param("s", $today);
$pendingQuery->execute();
$pendingRow = $pendingQuery->get_result()->fetch_assoc();
$pendingCount = $pendingRow['pending_count'] ?? 0;
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Online Orders - Eskina Coffee</title>
<link rel="stylesheet" href="./css/online_orders.css" />
</head>
<body>
 <div class="navbar">
  <div class="brand">
    <div class="logo">
      <img src="./images/eslogo.jpg" alt="Eskina Coffee Logo" onerror="this.onerror=null; this.src='fallback.png'"/>
    </div>
    <span>Eskina Coffee</span>
  </div>

  <div class="hamburger" onclick="toggleMenu()">
    <span></span>
    <span></span>
    <span></span>
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

    <a href="online_orders.php" class="icon-btn active" title="Online Orders">
      <div class="circle"><img src="./images/bag.png" alt="Bag" class="icon-img" /></div>
    </a>

    <a href="transaction.php" class="icon-btn" title="Transaction">
      <div class="circle">
        <img src="./images/file.png" alt="File" class="icon-img" />
      </div>
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
  <div class="orders-wrapper">
    <!-- LEFT: ORDER DETAILS -->
    <div class="order-details">
      <h3>Customer Orders</h3>

      <!-- Example Orders -->
      <div class="order-card">
        <div class="status-dropdown">
          <select name="status">
            <option value="Pending" selected>Pending</option>
            <option value="Preparing">Preparing</option>
            <option value="Ready for Pick Up">Ready for Pick Up</option>
            <option value="Out for Delivery">Out for Delivery</option>
            <option value="Completed">Completed</option>
            <option value="Cancelled">Cancelled</option>
          </select>
        </div>
        <div class="detail-item"><strong>Name:</strong> Juan Dela Cruz</div>
        <div class="detail-item"><strong>Contact:</strong> 09123456789</div>
        <div class="detail-item"><strong>Order:</strong> Iced Latte x2</div>
        <div class="detail-item"><strong>Address:</strong> Brgy. 123, Caloocan City</div>
        <div class="detail-item"><strong>Total:</strong> ₱200.00</div>
        <div class="detail-item"><strong>Points:</strong> 20</div>
        <div class="detail-item"><strong>Time:</strong> 5 mins ago</div>
      </div>

      <div class="order-card">
        <div class="status-dropdown">
          <select name="status">
            <option value="Pending">Pending</option>
            <option value="Preparing" selected>Preparing</option>
            <option value="Ready for Pick Up">Ready for Pick Up</option>
            <option value="Out for Delivery">Out for Delivery</option>
            <option value="Completed">Completed</option>
            <option value="Cancelled">Cancelled</option>
          </select>
        </div>
        <div class="detail-item"><strong>Name:</strong> Maria Santos</div>
        <div class="detail-item"><strong>Contact:</strong> 09987654321</div>
        <div class="detail-item"><strong>Order:</strong> Hot Americano x1, Caramel Frappe x1</div>
        <div class="detail-item"><strong>Address:</strong> Bagong Barrio, Caloocan City</div>
        <div class="detail-item"><strong>Total:</strong> ₱310.00</div>
        <div class="detail-item"><strong>Points:</strong> 31</div>
        <div class="detail-item"><strong>Time:</strong> 10 mins ago</div>
      </div>

      <div class="order-card">
        <div class="status-dropdown">
          <select name="status">
            <option value="Pending">Pending</option>
            <option value="Preparing">Preparing</option>
            <option value="Ready for Pick Up" selected>Ready for Pick Up</option>
            <option value="Out for Delivery">Out for Delivery</option>
            <option value="Completed">Completed</option>
            <option value="Cancelled">Cancelled</option>
          </select>
        </div>
        <div class="detail-item"><strong>Name:</strong> Carlo Reyes</div>
        <div class="detail-item"><strong>Contact:</strong> 09150001111</div>
        <div class="detail-item"><strong>Order:</strong> Iced Spanish Latte x2</div>
        <div class="detail-item"><strong>Address:</strong> 9th Ave, East Grace Park</div>
        <div class="detail-item"><strong>Total:</strong> ₱260.00</div>
        <div class="detail-item"><strong>Points:</strong> 26</div>
        <div class="detail-item"><strong>Time:</strong> 20 mins ago</div>
      </div> 
    </div>

    <!-- RIGHT: MAP -->
    <div class="order-map">
      <iframe
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3861.171980431056!2d120.98282157582392!3d14.58743498590197!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397b5edc8e0a9b3%3A0x1a0cfa4db5cfb3f4!2s203%2011th%20Ave%20Cor%204th%20St%2C%20East%20Grace%20Park%2C%20Caloocan%2C%20Philippines!5e0!3m2!1sen!2sph!4v1730647899999!5m2!1sen!2sph"
        width="100%"
        height="100%"
        style="border:0; border-radius: 12px;"
        allowfullscreen=""
        loading="lazy"
        referrerpolicy="no-referrer-when-downgrade">
      </iframe>
    </div>
  </div>
</main>



<!-- SWEETALERT2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
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
</script>
</body>
</html>
