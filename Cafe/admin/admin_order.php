<?php
include '../config.php';
require_once '../auth.php';

$adminName = htmlspecialchars($_SESSION['user']['username'] ?? 'Admin');
$loggedInStaff = isset($_SESSION['username']) ? $_SESSION['username'] : 'STAFF NAME';

$products = [];

$query = "
    SELECT 
        p.name,
        c.slug AS sectionId,
        p.meta,
        p.price,
        p.best_seller,
        p.image
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
            'bestSeller' => (bool)$row['best_seller'],
            'image' => $row['image'] ?? ''
        ];
    }
} else {
    echo "<script>console.warn('⚠️ No products found in the database');</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Eskina Coffee | Admin Order</title>
  <link rel="stylesheet" href="./css/admin_order.css" />
  <link rel="stylesheet" href="./css/admin_dashboard.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
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
      <span class="admin-name"><?= htmlspecialchars($adminName) ?></span>
    </div>

    <div class="logout-popup" id="logoutPopup">Logout</div>
    <form id="logoutForm" action="logout.php" method="POST" style="display:none;"></form>
  </aside>

  <button class="menu-toggle">☰</button>

  <h2>Order Taking</h2>

    <div class="order-controls">
      <div class="search-box">
        <input id="globalSearch" type="text" placeholder="Search products..." aria-label="Search products">
        <button id="searchBtn" aria-label="Search">
          <img src="./images/search.png" alt="Search" class="search-icon" />
        </button>
      </div>

      <div class="floating-checkout-btn" title="Checkout">
        <div class="circle">
          <i class="fa-solid fa-cart-shopping icon-img"></i>
          <span class="cart-badge" id="checkoutCount">0</span>
        </div>
      </div>
    </div>
    
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

    <div class="section-title" id="ricebowls">Rice Bowls</div>
    <div class="product-grid" id="ricebowls-grid"></div>

    <div class="section-title" id="munchies">Munchies</div>
    <div class="product-grid" id="munchies-grid"></div>

    <div class="section-title" id="pasta">Pasta</div>
    <div class="product-grid" id="pasta-grid"></div>

    <div class="section-title" id="wraps">Wraps & Sandwiches</div>
    <div class="product-grid" id="wraps-grid"></div>

    <div id="orderModal" class="order-modal hidden">
      <div class="order-modal-content">
        <span class="close-btn" onclick="closeOrderModal()">&times;</span>
        <h2 class="modal-title">Order Summary</h2>

        <div id="orderItems" class="order-items-list"></div>

        <div class="dining-option">
          <label for="diningType">Order Type:</label>
          <select id="diningType">
            <option value="DINE_IN">Dine In</option>
            <option value="TAKE_OUT">Take Out</option>
          </select>
        </div>


        <div class="customer-name">
          <label for="customerName">Customer Name:</label>
          <input type="text" id="customerName" placeholder="Enter Customer Name">
        </div>

        <!-- Payment Option -->
        <div class="payment-options">
          <label for="paymentMethod">Payment Method:</label>
          <select id="paymentMethod">
            <option value="CASH">Cash</option>
            <option value="GCASH">GCash</option>
          </select>
        </div>
        <div class="total-price">Total: <span id="totalAmount">₱0.00</span></div>
        <button class="confirm-btn" onclick="confirmOrder()">Confirm Order</button>
      </div>
    </div>
  </div> 
</div>


<script src="./script/cart.js"></script>
<script src="./script/sidebar_navigation.js"></script>

    
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
  
  const products = <?= json_encode($products, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>.map(p => ({
    sectionId: p.sectionId,
    name: p.name,
    meta: p.meta || '',
    price: '₱' + parseFloat(p.price.replace(/,/g, '')).toFixed(2),
    bestSeller: p.bestSeller,
    image: p.image || './images/no-image.png'
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
    <div class="image">
      <img src="${p.image}" alt="${p.name}">
    </div>

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

  // For each section (with title + grid)
  const sections = [
    { titleEl: document.getElementById('classics-section'), gridEl: document.getElementById('classics-grid') },
    { titleEl: document.getElementById('specials-section'), gridEl: document.getElementById('specials-grid') },
    { titleEl: document.getElementById('iceblendedcoffee'), gridEl: document.getElementById('iceblendedcoffee-grid') },
    { titleEl: document.getElementById('iceblendedcream'), gridEl: document.getElementById('iceblendedcream-grid') },
    { titleEl: document.getElementById('tea'), gridEl: document.getElementById('tea-grid') },
    { titleEl: document.getElementById('refreshers'), gridEl: document.getElementById('refreshers-grid') },
    { titleEl: document.getElementById('anticoffee'), gridEl: document.getElementById('anticoffee-grid') },
    { titleEl: document.getElementById('extras'), gridEl: document.getElementById('extras-grid') },
    { titleEl: document.getElementById('ricebowls'), gridEl: document.getElementById('ricebowls-grid') },
    { titleEl: document.getElementById('munchies'), gridEl: document.getElementById('munchies-grid') },
    { titleEl: document.getElementById('pasta'), gridEl: document.getElementById('pasta-grid') },
    { titleEl: document.getElementById('wraps'), gridEl: document.getElementById('wraps-grid') }
  ];

  sections.forEach(section => {
    let anyVisibleInSection = false;

    const cards = section.gridEl.querySelectorAll('.product-card');
    cards.forEach(card => {
      const name = card.querySelector('.name')?.textContent.toLowerCase() || '';
      const sectionName = section.titleEl.textContent.toLowerCase();
      const matches = name.includes(keyword) || sectionName.includes(keyword);

      card.style.display = matches ? 'flex' : 'none';
      if (matches) {
        anyVisibleInSection = true;
      }
    });

    // Show/hide the section title + grid based on whether any visible product
    section.titleEl.style.display = anyVisibleInSection ? '' : 'none';
    section.gridEl.style.display = anyVisibleInSection ? '' : 'none';
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

  // ---------- CONFIRM ORDER ----------
  async function confirmOrder() {
  if (cart.length === 0) {
    Swal.fire({
      html: '<div style="text-align:center; font-size:24px; font-weight:600;">Cart is empty!</div>',
      icon: 'warning',
      confirmButtonColor: '#74512D'
    });
    return;
  }

  const customerNameEl = document.getElementById("customerName");
  const paymentMethodEl = document.getElementById("paymentMethod");
  const diningTypeEl = document.getElementById("diningType");

  const customerName = customerNameEl.value.trim();
  if (!customerName) {
    Swal.fire({
      html: '<div style="text-align:center; font-size:24px; font-weight:600;">Please enter the customer\'s name.</div>',
      icon: 'warning',
      confirmButtonColor: '#74512D'
    });
    customerNameEl.focus();
    return;
  }

  const paymentMethod = paymentMethodEl.value;
  let orderType = diningTypeEl.value;
  orderType = orderType.replace("_", "-").toUpperCase();

  // compute total
  let total = 0;
  cart.forEach(item => {
    const priceNum = parseFloat(item.price.replace(/[₱,]/g, "")) || 0;
    total += priceNum * item.quantity;
  });

  const payload = {
    cart: cart,
    payment: paymentMethod,
    orderType: orderType,
    customerName: customerName,
    total: total.toFixed(2)
  };

  // Build HTML for preview
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
          Swal.fire("Error", data.message || "Something went wrong.", "error");
        }
      } catch (err) {
        console.error("Error:", err);
        Swal.fire("Error", "Could not save order.", "error");
      }
    } else {
      // User chose to “Edit Order” – stay in modal for further edits
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

// --- Navbar Slide Down Toggle ---
document.addEventListener("DOMContentLoaded", () => {
  const iconButtons = document.querySelectorAll(".center-icons .icon-btn");
  const navLinks = document.querySelector(".nav-links");

  if (!iconButtons.length || !navLinks) return;

  iconButtons.forEach(btn => {
    btn.addEventListener("click", e => {
      e.preventDefault();

      // Toggle the dropdown
      navLinks.classList.toggle("show");

      // Optional: remove dropdown when clicking anywhere else
      document.addEventListener("click", event => {
        if (!navLinks.contains(event.target) && !btn.contains(event.target)) {
          navLinks.classList.remove("show");
        }
      }, { once: true });
    });
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