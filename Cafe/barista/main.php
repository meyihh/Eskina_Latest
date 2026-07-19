<?php
include '../config.php';
require_once '../auth.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$loggedInStaff = isset($_SESSION['username']) ? $_SESSION['username'] : 'STAFF NAME';

$products = [];

$query = "
    SELECT 
        p.name,
        c.slug AS sectionId,
        p.meta,
        p.price,
        p.best_seller
    FROM products p
    INNER JOIN categories c ON p.category_id = c.id
    ORDER BY c.id, p.name
";

$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = [
            'sectionId' => strtolower(trim($row['sectionId'])),
            'name' => $row['name'],
            'meta' => $row['meta'] ?? '',
            'price' => $row['price'],
            'bestSeller' => (bool)$row['best_seller']
        ];
    }
} else {
    echo "<script>console.warn('⚠️ No products found in the database');</script>";
}

$today = date("Y-m-d");

$pendingQuery = $conn->prepare("
    SELECT COUNT(*) AS pending_count
    FROM order_items oi
    JOIN orders o ON oi.order_id = o.id
    WHERE oi.status != 'DONE' AND DATE(o.created_at) = ?
");

$pendingQuery->bind_param("s", $today);
$pendingQuery->execute();
$pendingRow = $pendingQuery->get_result()->fetch_assoc();
$pendingCount = $pendingRow['pending_count'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <title>Eskina Coffee | Dashboard</title>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="./css/style.css" />
  
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
    <a href="main.php" class="icon-btn active" title="Menu">
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


  <!-- Hidden dropdown for mobile -->
<div class="mobile-menu" id="mobileMenu">
  <div class="mobile-icons">
    <a href="main.php" class="icon-btn" title="Menu">
      <div class="circle"><img src="./images/list.png" alt="Menu" class="icon-img" /></div>
    </a>

    <!-- 🧾 QUEUE ICON with notification -->
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
  </div>
  <a href="#" id="logoutBtnMobile" class="logout-btn">Logout</a>
</div>

    <div class="container">

      <aside class="sidebar">
        <div class="section" data-section="drinks">
          <div class="section-header">
            <div>Drinks</div>
            <div>&#9662;</div>
          </div>
          <ul class="section-list expanded" id="drinks-list">
            <li><a data-target="classics-section">Classics</a></li>
            <li><a data-target="specials-section">Specials</a></li>
            <li><a data-target="iceblendedcoffee">Iced Blended Coffee Based</a></li>
            <li><a data-target="iceblendedcream">Iced Blended Cream Based</a></li>
            <li><a data-target="tea">Tea</a></li>
            <li><a data-target="refreshers">Refreshers</a></li>
            <li><a data-target="anticoffee">Anti-Coffee</a></li>
            <li><a data-target="extras">Extras</a></li>
          </ul>
        </div>

        <div class="section" data-section="foods">
          <div class="section-header">
            <div>Foods</div>
            <div>&#9662;</div>
          </div>
          <ul class="section-list" id="foods-list">
            <li><a data-target="ricebowls">Rice Bowls</a></li>
            <li><a data-target="munchies">Munchies</a></li>
            <li><a data-target="pasta">Pasta</a></li>
            <li><a data-target="wraps">Wraps & Sandwiches</a></li>
          </ul>
        </div>
      </aside>  

      <div class="main">
        <div class="top-row">
          <div class="search-box">
            <input id="globalSearch" type="text" placeholder="Search classics..." aria-label="Search classics">
            <button id="searchBtn" aria-label="Search">
              <img src="./images/search.png" alt="Search" class="search-icon" onerror="this.onerror=null; this.src='🔍';" />
            </button>
          </div>


          <!-- Floating Checkout Button -->
        <div class="floating-checkout-btn" title="Checkout">
          <div class="circle">
            <i class="fa-solid fa-cart-shopping icon-img"></i>
            <span class="cart-badge" id="checkoutCount">0</span>
          </div>
        </div>
        </div>

<!-- Order Modal -->
<div id="orderModal" class="order-modal hidden">
  <div class="order-modal-content">
    <span class="close-btn" onclick="closeOrderModal()">&times;</span>
    <h2 class="modal-title">Order Summary</h2>

    <!-- Items -->
    <div id="orderItems" class="order-items-list"></div>

    <!-- Dining Option -->
    <div class="dining-option">
      <label for="diningType">Order Type:</label>
      <select id="diningType">
        <option value="DINE_IN">Dine In</option>
        <option value="TAKE_OUT">Take Out</option>
      </select>
    </div>

    <!-- Customer Name -->
      <div class="customer-name">
        <label for="customerName">Customer Name:</label>
        <input type="text" id="customerName" placeholder="Enter Customer Name" required>
      </div>


    <!-- Payment Option -->
    <div class="payment-options">
      <label for="paymentMethod">Payment Method:</label>
      <select id="paymentMethod">
        <option value="CASH">Cash</option>
        <option value="GCASH">GCash</option>
      </select>
    </div>

  
   <!-- Total with Add More button -->
  <div class="total-price" style="display:flex; justify-content:space-between; align-items:center;">
  <button type="button" id="addMoreBtn" class="add-more-btn">Add More</button>
  <span>Total: <span id="totalAmount">₱0.00</span></span>
  </div>


    <!-- Confirm -->
    <button class="confirm-btn" onclick="confirmOrder()">Confirm Order</button>
  </div>
</div>
        <!-- SECTIONS -->
        <div class="section-title" id="classics-section">Classics</div>
        <div class="product-grid" id="classics-grid"></div>

        <div class="section-title" id="specials-section">Specials</div>
        <div class="product-grid" id="specials-grid"></div>

        <div class="section-title" id="iceblendedcoffee">Iced Blended Coffee Based</div>
        <div class="product-grid" id="iceblendedcoffee-grid"></div>

        <div class="section-title" id="iceblendedcream">Iced Blended Cream Based</div>
        <div class="product-grid" id="iceblendedcream-grid"></div>

        <div class="section-title" id="tea">Tea</div>
        <div class="product-grid" id="tea-grid"></div>

        <div class="section-title" id="refreshers">Refreshers</div>
        <div class="product-grid" id="refreshers-grid"></div>

        <div class="section-title" id="anticoffee">Anti-Coffee</div>
        <div class="product-grid" id="anticoffee-grid"></div>

        <div class="section-title" id="extras">Extras</div>
        <div class="product-grid" id="extras-grid"></div>

                <!-- Foods Sections -->
        <div class="section-title" id="ricebowls">Rice Bowls</div>
        <div class="product-grid" id="ricebowls-grid"></div>

        <div class="section-title" id="munchies">Munchies</div>
        <div class="product-grid" id="munchies-grid"></div>

        <div class="section-title" id="pasta">Pasta</div>
        <div class="product-grid" id="pasta-grid"></div>

        <div class="section-title" id="wraps">Wraps & Sandwiches</div>
        <div class="product-grid" id="wraps-grid"></div>
      </div>
    </div>
    
<form method="POST" id="orderForm">
  <input type="hidden" name="cart_data" id="cartData">
  <button type="submit" onclick="saveCart()">Confirm Order</button>
</form>

<script src="./scripts/cart.js"></script>
<script src="./scripts/sidebar_navigation.js"></script>

<script>

  
  const products = <?= json_encode($products, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>.map(p => ({
    sectionId: p.sectionId,
    name: p.name,
    meta: p.meta || '',
    price: '₱' + parseFloat(p.price.replace(/,/g, '')).toFixed(2),
    bestSeller: p.bestSeller
  }));

  // ---------- PRODUCT CARDS ----------
  function createProductCard(p) {
    const card = document.createElement('div');
    card.className = 'product-card';

    let selectedSize = 'Grande';
    let selectedTemp = p.meta.toLowerCase().includes('iced') ? 'Iced' : 'Hot';
    const metaHTML = '';

    const isFood = ['ricebowls', 'munchies', 'pasta', 'wraps'].includes(p.sectionId);

    card.innerHTML = `
      <div class="image"></div>
      <div class="info">
        <div class="name" style="font-size:14px; margin-bottom:4px;">${p.name}</div>
        ${
          isFood
            ? ''
            : `<div class="controls" style="display:flex; flex-wrap:wrap; gap:6px;">
                 <select class="size-select" aria-label="Size">
                   <option value="Tall">Tall</option>
                   <option value="Grande" selected>Grande</option>
                   <option value="Venti">Venti</option>
                 </select>
                 <div class="temp-toggle" aria-label="Temperature">
                   <button type="button" class="temp-btn" data-temp="Hot">Hot</button>
                   <button type="button" class="temp-btn" data-temp="Iced">Iced</button>
                 </div>
               </div>`
        }
        ${metaHTML}
      </div>
      <div class="bottom">
        <div class="price">${p.price}</div>
        <button class="add-btn" aria-label="Add to cart">
          <img src="./images/add.png" alt="Add" onerror="this.onerror=null; this.src='+';" />
        </button>
      </div>
    `;

    if (!isFood) {
      const sizeSelect = card.querySelector('.size-select');
      sizeSelect.addEventListener('change', () => {
        selectedSize = sizeSelect.value;
      });

      const tempButtons = card.querySelectorAll('.temp-btn');
      function refreshTempUI() {
        tempButtons.forEach(btn => {
          btn.classList.toggle('active', btn.dataset.temp === selectedTemp);
        });
      }
      tempButtons.forEach(btn => {
        btn.addEventListener('click', () => {
          selectedTemp = btn.dataset.temp;
          refreshTempUI();
        });
      });
      refreshTempUI();
    }

    const addBtn = card.querySelector('.add-btn');
    addBtn.addEventListener('click', () => {
      const existingIndex = cart.findIndex(item =>
        item.name === p.name &&
        (isFood || item.size === selectedSize) &&
        (isFood || item.temp === selectedTemp)
      );

      if (existingIndex >= 0) {
        cart[existingIndex].quantity += 1;
      } else {
        cart.push({
          name: p.name,
          price: p.price,
          size: isFood ? null : selectedSize,
          temp: isFood ? null : selectedTemp,
          quantity: 1
        });
      }

      updateCheckoutBadge(getTotalCartQuantity());
      saveCartToLocalStorage();
      showToast(`Added ${p.name}${isFood ? '' : ` (${selectedSize}, ${selectedTemp})`} to cart`);
    });

    if (p.bestSeller) {
      const badge = document.createElement('img');
      badge.src = './images/reward.png';
      badge.alt = 'Best Seller';
      badge.className = 'badge';
      badge.title = 'Best Seller';
      badge.setAttribute('aria-label', 'Best Seller');
      card.appendChild(badge);
    }

    return card;
  }

  const sectionMap = {
    'classics-section': document.getElementById('classics-grid'),
    'specials-section': document.getElementById('specials-grid'),
    'iceblendedcoffee': document.getElementById('iceblendedcoffee-grid'),
    'iceblendedcream': document.getElementById('iceblendedcream-grid'),
    'tea': document.getElementById('tea-grid'),
    'refreshers': document.getElementById('refreshers-grid'),
    'anticoffee': document.getElementById('anticoffee-grid'),
    'extras': document.getElementById('extras-grid'),
    'ricebowls': document.getElementById('ricebowls-grid'),
    'munchies': document.getElementById('munchies-grid'),
    'pasta': document.getElementById('pasta-grid'),
    'wraps': document.getElementById('wraps-grid'),
  };

  products.forEach(p => {
    const container = sectionMap[p.sectionId];
    if (container) container.appendChild(createProductCard(p));
  });

  // ---------- TOAST ----------
  function showToast(message) {
    let container = document.getElementById("toast-container");
    if (!container) {
      container = document.createElement("div");
      container.id = "toast-container";
      document.body.appendChild(container);
    }

    const toast = document.createElement("div");
    toast.className = "toast";
    toast.textContent = message;

    container.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
  }

  // ---------- SEARCH ----------
const searchInput = document.getElementById('globalSearch');

searchInput.addEventListener('input', () => {
  const keyword = searchInput.value.trim().toLowerCase();
  const productCards = document.querySelectorAll('.product-card');
  const sectionTitles = document.querySelectorAll('.section-title');
  const productGrids = document.querySelectorAll('.product-grid');

  // If search is empty, show all products and sections
  if (keyword === '') {
    productCards.forEach(card => (card.style.display = 'flex'));
    sectionTitles.forEach(title => (title.style.display = 'block'));
    productGrids.forEach(grid => (grid.style.display = 'grid'));
    return;
  }

  // Hide all by default
  productCards.forEach(card => (card.style.display = 'none'));
  sectionTitles.forEach(title => (title.style.display = 'none'));
  productGrids.forEach(grid => (grid.style.display = 'none'));

  // Show only matching cards and their sections
  productCards.forEach(card => {
    const name = card.querySelector('.name')?.textContent.toLowerCase() || '';
    if (name.includes(keyword)) {
      card.style.display = 'flex';
      const grid = card.closest('.product-grid');
      if (grid) {
        grid.style.display = 'grid';
        const sectionTitle = grid.previousElementSibling;
        if (sectionTitle && sectionTitle.classList.contains('section-title')) {
          sectionTitle.style.display = 'block';
        }
      }
    }
  });
});


  // ---------- ORDER MODAL ----------
  document.querySelector('.floating-checkout-btn')?.addEventListener('click', openOrderModal);
  let selectedIndexes = [];

  function openOrderModal() {
    const modal = document.getElementById("orderModal");
    const orderItems = document.getElementById("orderItems");
    const totalAmountEl = document.getElementById("totalAmount");

    orderItems.innerHTML = "";
    selectedIndexes = [];
    let total = 0;

    cart.forEach((item, index) => {
      const price = parseFloat(item.price.replace(/[₱,]/g, "")) || 0;
      const itemTotal = price * item.quantity;
      total += itemTotal;

      const div = document.createElement("div");
      div.className = "order-item";
      div.dataset.index = index;

      div.innerHTML = `
        <div class="item-left">
          <span class="name">${item.name}</span>
          <div class="qty-controls">
            <button class="qty-btn minus">−</button>
            <span class="qty">${item.quantity}</span>
            <button class="qty-btn plus">+</button>
          </div>
        </div>
        <div class="item-right">
          <span class="price">₱${itemTotal.toFixed(2)}</span>
          <button class="remove-x">&times;</button>
        </div>
      `;

      div.querySelector(".minus").addEventListener("click", (e) => {
        e.stopPropagation();
        if (cart[index].quantity > 1) {
          cart[index].quantity--;
        } else {
          cart.splice(index, 1);
        }
        saveCartToLocalStorage();
        updateCheckoutBadge(getTotalCartQuantity());
        openOrderModal();
      });

      div.querySelector(".plus").addEventListener("click", (e) => {
        e.stopPropagation();
        cart[index].quantity++;
        saveCartToLocalStorage();
        updateCheckoutBadge(getTotalCartQuantity());
        openOrderModal();
      });

      div.querySelector(".remove-x").addEventListener("click", (e) => {
        e.stopPropagation();
        cart.splice(index, 1);
        saveCartToLocalStorage();
        updateCheckoutBadge(getTotalCartQuantity());
        openOrderModal();
      });

      orderItems.appendChild(div);
    });

    totalAmountEl.textContent = `₱${total.toFixed(2)}`;
    modal.classList.remove("hidden");
  }

  function closeOrderModal() {
    document.getElementById("orderModal").classList.add("hidden");
  }

  document.getElementById("addMoreBtn").addEventListener("click", () => {
  closeOrderModal(); // Close the order modal
  showToast("You can continue adding items."); // Optional toast
});

 
 // ---------- CONFIRM ORDER ----------
async function confirmOrder() {
  if (cart.length === 0) {
    showToast("Cart is empty!");
    return;
  }

  const customerNameInput = document.getElementById("customerName");
  const customerName = customerNameInput.value.trim();

  // Remove previous error if any
  let errorMsg = document.getElementById("customerNameError");
  if (errorMsg) errorMsg.remove();
  customerNameInput.style.borderColor = "";

  if (customerName === "") {
    customerNameInput.style.borderColor = "red";
    errorMsg = document.createElement("div");
    errorMsg.id = "customerNameError";
    errorMsg.style.color = "red";
    errorMsg.style.fontSize = "12px";
    errorMsg.style.marginTop = "4px";
    errorMsg.textContent = "Customer Name is required!";
    customerNameInput.parentNode.appendChild(errorMsg);
    customerNameInput.focus();
    return;
  }

  const paymentMethod = document.getElementById("paymentMethod").value;
  let orderType = document.getElementById("diningType").value;
  orderType = orderType.replace("_", "-").toUpperCase();

  const payload = {
    cart,
    payment: paymentMethod,
    orderType: orderType,
    customerName: customerName,
  };

  const total = cart.reduce((sum, item) => {
    const priceNum = parseFloat(item.price.replace(/[₱,]/g, "")) || 0;
    return sum + priceNum * item.quantity;
  }, 0);

  const itemsHtml = cart.map(item => {
    const priceNum = parseFloat(item.price.replace(/[₱,]/g, "")) || 0;
    const itemTotal = (priceNum * item.quantity).toFixed(2);
    let descriptor = item.name;
    if (item.size) descriptor += ` (${item.size})`;
    if (item.temp) descriptor += ` (${item.temp})`;
    return `<li>${item.quantity} × ${descriptor} — ₱${itemTotal}</li>`;
  }).join("");

  const previewHtml = `
    <div style="text-align:left; font-size:14px; color:#543310;">
      <h3 style="text-align:center; margin-bottom:10px; font-size:24px; color:#3D2419;">Order Preview</h3>
      <p><strong>Customer:</strong> ${customerName}</p>
      <p><strong>Order Type:</strong> ${orderType === "DINE-IN" ? "Dine In" : (orderType === "TAKE-OUT" ? "Take Out" : orderType)}</p>
      <p><strong>Payment Method:</strong> ${paymentMethod}</p>
      <hr/>
      <div><strong>Items:</strong></div>
      <ul style="list-style:none; padding-left:0; margin:8px 0;">
        ${itemsHtml}
      </ul>
      <hr/>
      <p style="font-weight:600; text-align:right; font-size:16px;">Total: ₱${total.toFixed(2)}</p>
    </div>
  `;
// ✅ Close the modal first before showing SweetAlert
closeOrderModal();
  Swal.fire({
    html: previewHtml,
    showCancelButton: true,
    confirmButtonText: 'Proceed & Confirm',
    cancelButtonText: 'Edit Order',
    confirmButtonColor: '#74512D',
    cancelButtonColor: '#999',
    width: '600px'
  }).then(async (result) => {
    if (result.isConfirmed) {
      try {
        const res = await fetch("save_order.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(payload),
        });
        const data = await res.json();

        if (data.status === "success") {
          generateReceipt(
            data.order_id,
            payload.payment,
            cart,
            data.total,
            "<?php echo $loggedInStaff; ?>",
            payload.orderType
          );

          Swal.fire({
            html: `
              <div style="text-align:center;">
                <div style="font-size:28px; font-weight:700; color:#3D2419;">Order Saved!</div>
                <div style="margin-top:10px; font-size:18px;">
                  Order #${data.order_id} — Total: <b>₱${parseFloat(data.total).toFixed(2)}</b>
                </div>
              </div>
            `,
            icon: "success",
            confirmButtonColor: "#74512D",
          });

          cart = [];
          saveCartToLocalStorage();
          updateCheckoutBadge(0);
          closeOrderModal();
        } else {
          showToast(data.message || "Something went wrong.");
        }
      } catch (err) {
        console.error("Error:", err);
        showToast("Could not save order.");
      }
    }
  });
}

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

<script>
function toggleMenu() {
  document.getElementById("mobileMenu").classList.toggle("active");
}

// Handle mobile logout
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



function updateNotifBadge() {
  fetch('get_pending_count.php', { cache: 'no-store' })
    .then(res => res.json())
    .then(data => {
      const count = data.count || 0;
      const badge = document.getElementById('queue-badge');
      const mobileBadge = document.getElementById('queue-badge-mobile');

      if (badge && mobileBadge) {
        if (count > 0) {
          badge.textContent = count;
          badge.style.display = 'flex';
          mobileBadge.textContent = count;
          mobileBadge.style.display = 'flex';
        } else {
          badge.style.display = 'none';
          mobileBadge.style.display = 'none';
        }
      }

      // Immediately call again after 1 second (continuous update)
      setTimeout(updateNotifBadge, 1000);
    })
    .catch(err => {
      console.error('Error updating badge:', err);
      // Retry after 3 seconds if fetch fails
      setTimeout(updateNotifBadge, 3000);
    });
}

// Start live updating
updateNotifBadge();


</script>
</body>
</html>