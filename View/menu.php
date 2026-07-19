<?php
$menu = [
    "Drinks" => [
        "Classics" => [
            "Americano" => 120.00,
            "Café Latte" => 130.00,
            "Café Ilustrado" => 140.00,
            "French Vanilla" => 160.00,
            "Hazelnut" => 160.00,
            "Roasted Almond" => 160.00,
            "Mocha" => 170.00,
            "White Choco Mocha" => 170.00,
            "Salted Caramel" => 170.00,
            "Caramel Macchiato" => 170.00
        ],
        "Ice Blended Coffee Based" => [
            "Coffee Jelly" => 180.00,
            "Sea Salt Caramel" => 180.00,
            "Java Chip" => 180.00
        ],
        "Ice Blended Cream Based" => [
            "Cookies & Cream" => 160.00,
            "Pure Matcha" => 160.00,
            "Chocnut" => 160.00,
            "Strawberries & Cream" => 170.00,
            "Blueberries & Cream" => 170.00,
            "Biscoff Cream" => 200.00
        ],
        "Specials" => [
            "Calamansi Espresso" => 140.00,
            "Einspanner" => 150.00,
            "Matcha-Presso Fusion" => 160.00,
            "Café con Miel (Hot/Iced)" => 160.00,
            "Iced Shaken Oatmilk Latte" => 170.00,
            "Cinderella Latte" => 180.00
        ],
        "Tea" => [
            "Earl Grey" => 140.00,
            "English Breakfast" => 140.00,
            "Chamomile" => 140.00,
            "Pure Peppermint" => 140.00
        ],
        "Anti-Coffee" => [
            "Matcha Latte (Hot/Iced)" => 150.00,
            "Strawberry Milk" => 145.00,
            "Blueberry Milk" => 145.00,
            "Matcha-Berry Fusion" => 160.00,
            "Sikwate (Hot/Iced)" => 165.00
        ],
        "Refreshers" => [
            "Blueberry Ade" => 120.00,
            "Lychee Ade" => 120.00,
            "Peach Mango Ade" => 120.00,
            "Raspberry Ade" => 120.00,
            "Honey Lemon Soda Tea" => 130.00,
            "Blue Lemonade" => 150.00,
            "Mango-Biscuit Bliss" => 160.00,
            "Sola Lemon/Raspberry" => 95.00
        ],
        "Extras" => [
            "Expresso Shot" => 40.00,
            "Syrup" => 30.00,
            "Sauce" => 30.00,
            "Whipped Cream" => 30.00,
            "Oatmilk" => 40.00,
            "Soya" => 25.00,
            "Sinkers" => 25.00,
            "Mineral Water" => 30.00
        ]
    ],
    "Foods" => [
        "Rice Bowls" => [
            "Spam" => 130.00,
            "Corned Beef" => 130.00,
            "Hungarian" => 150.00,
            "Tocino" => 150.00,
            "Tapa" => 150.00,
            "Bacon" => 150.00,
            "Garlic Chicken Parmesan" => 170.00,
            "Honey Chicken Sriracha" => 170.00,
            "Fish Fillet" => 160.00,
            "Lechon Kawali" => 190.00
        ],
        "Pasta" => [
            "Pesto Basilico" => 195.00,
            "Creamy Chicken Alfredo" => 200.00,
            "Umami Truffle Delight" => 220.00,
            "Baked Lasagna" => 250.00
        ],
        "Wraps and Sandwiches" => [
            "Grilled Cheese" => 120.00,
            "Double Cheese Ham" => 130.00,
            "Cheesy Quesadilla" => 135.00,
            "Cheesy Beef Quesadilla" => 180.00
        ],
        "Munchies" => [
            "Fries Solo" => 80.00,
            "Fries Duo" => 140.00,
            "Chick and Chips" => 210.00,
            "Fish and Chips" => 210.00,
            "Cheesy Potato Croquettes" => 220.00
        ]
    ]
];

$bestSellers = [
    "Drinks" => ["Café Ilustrado", "Roasted Almond", "Caramel Macchiato", "Coffee Jelly", "Java Chip", "Cookies & Cream", "Strawberries & Cream", 
    "Iced Shaken Oatmilk Latte",  "Matcha Latte (Hot/Iced)", "Strawberry Milk", "Blueberry Ade", "Lychee Ade",  "Raspberry Ade" ],
    "Foods" => ["Tapa", "Garlic Chicken Parmesan",  "Honey Chicken Sriracha", "Lechon Kawali",  "Creamy Chicken Alfredo",  
    "Grilled Cheese", "Cheesy Beef Quesadilla", "Fries Duo", "Chick and Chips", "Cheesy Potato Croquettes"]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eskina Coffee | Menu</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=Noto+Serif:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="menu.css">
</head>
<body>

    <nav>
        <div class="logo">
        <img src="images/logo_img.jpg" alt="Eskina Coffee Logo"> Eskina Coffee </div>

        <input type="checkbox" id="menu-toggle">
        <label for="menu-toggle" class="hamburger">☰</label>

        <ul class="nav-links">
            <li><a href="index.php#home">Home</a></li>
            <li><a href="menu.php" class="active">Menu</a></li>
            <li><a href="about_us.php">About Us</a></li>
            <li><a href="contact_us.php">Contact Us</a></li>
        </ul>
    </nav>

    <div class="container">
        <aside class="sidebar">
           <div class="sidebar-menu">
    <div class="dropdown">
        <button class="dropdown-btn">Drinks</button>
        <div class="dropdown-content">
            <?php foreach (array_keys($menu["Drinks"]) as $category): ?>
                <a href="#" class="category-link" data-category="drinks-<?php echo strtolower(str_replace(" ", "-", $category)); ?>">
                    <?php echo $category; ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="dropdown">
        <button class="dropdown-btn">Foods</button>
        <div class="dropdown-content">
            <?php foreach (array_keys($menu["Foods"]) as $category): ?>
                <a href="#" class="category-link" data-category="foods-<?php echo strtolower(str_replace(" ", "-", $category)); ?>">
                    <?php echo $category; ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>
        </aside>

        <main class="main-content">
            <h1 id="category-title">Classics</h1>
            <p id="hot-iced-label" class="subheading">Hot / Iced</p>

            <div class="best-sellers-note">
            <span class="best-seller-icon">&#x2728;&#xfe0e;</span> Best Sellers
            </div>

            <div class="item-grid" id="drinks-classics">
                <?php foreach ($menu["Drinks"]["Classics"] as $item => $price): ?>
                    <div class="item-card <?php echo in_array($item, $bestSellers["Drinks"]) ? 'best-seller' : ''; ?>">
                        <div class="item-image"></div>
                        <div class="item-details">
                        <h3>
                            <?php 
                                echo $item; 
                                if (in_array($item, $bestSellers["Drinks"]) || in_array($item, $bestSellers["Foods"])) {
                                    echo "<span class='best-seller-icon'>&#x2728;&#xfe0e;</span>";
                                }
                            ?>
                        </h3>
                        <p>₱<?php echo number_format($price, 2); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php foreach (array_keys($menu["Drinks"]) as $category): if ($category !== "Classics"): ?>
                <div class="item-grid" id="drinks-<?php echo strtolower(str_replace(" ", "-", $category)); ?>" style="display:none;">
                    <?php foreach ($menu["Drinks"][$category] as $item => $price): ?>
                        <div class="item-card <?php echo in_array($item, $bestSellers["Drinks"]) ? 'best-seller' : ''; ?>">
                            <div class="item-image"></div>
                            <div class="item-details">
                            <h3>
                                <?php 
                                    echo $item; 
                                    if (in_array($item, $bestSellers["Drinks"]) || in_array($item, $bestSellers["Foods"])) {
                                        echo "<span class='best-seller-icon'>&#x2728;&#xfe0e;</span>";
                                    }
                                ?>
                            </h3>
                            <p>₱<?php echo number_format($price, 2); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; endforeach; ?>

            <?php foreach (array_keys($menu["Foods"]) as $category): ?>
                <div class="item-grid" id="foods-<?php echo strtolower(str_replace(" ", "-", $category)); ?>" style="display:none;">
                    <?php foreach ($menu["Foods"][$category] as $item => $price): ?>
                        <div class="item-card <?php echo in_array($item, $bestSellers["Foods"]) ? 'best-seller' : ''; ?>">
                            <div class="item-image"></div>
                            <div class="item-details">
                            <h3>
                                <?php 
                                    echo $item; 
                                    if (in_array($item, $bestSellers["Drinks"]) || in_array($item, $bestSellers["Foods"])) {
                                        echo "<span class='best-seller-icon'>&#x2728;&#xfe0e;</span>";
                                    }
                                ?>
                            </h3>
                            <p>₱<?php echo number_format($price, 2); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </main>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {

        document.querySelectorAll(".dropdown-btn").forEach(btn => {
            btn.addEventListener("click", function () {
                this.classList.toggle("active");
                let dropdownContent = this.nextElementSibling;
                dropdownContent.style.display =
                    dropdownContent.style.display === "block" ? "none" : "block";
            });
        });

        function showCategory(target, text) {
            document.querySelectorAll(".item-grid").forEach(grid => {
                grid.style.display = "none";
            });

            const grid = document.getElementById(target);
            if (grid) grid.style.display = "grid";

            const mainHeading = document.getElementById("category-title");
            const hotIcedLabel = document.getElementById("hot-iced-label");

            if (mainHeading) mainHeading.textContent = text;

            if (hotIcedLabel) {
                if (text === "Classics" || text === "Tea") {
                    hotIcedLabel.style.display = "block";
                } else {
                    hotIcedLabel.style.display = "none";
                }
            }

            document.querySelectorAll(".category-link").forEach(link => link.classList.remove("active"));
            const activeLink = document.querySelector(`[data-category="${target}"]`);
            if (activeLink) activeLink.classList.add("active");

            try {
                localStorage.setItem("lastCategory", target);
                localStorage.setItem("lastText", text);
            } catch (e) {
            }
        }

        document.querySelectorAll(".category-link").forEach(link => {
            link.addEventListener("click", function(e) {
                e.preventDefault();
                showCategory(this.dataset.category, this.textContent.trim());
            });
        });

        const lastCategory = localStorage.getItem("lastCategory") || "drinks-classics";
        const lastText = localStorage.getItem("lastText") || "Classics";

        const initialHotIced = document.getElementById("hot-iced-label");
        if (initialHotIced) initialHotIced.style.display = "none";

        showCategory(lastCategory, lastText);

        const urlParams = new URLSearchParams(window.location.search);
        const selectedCategory = urlParams.get("category");

        if (selectedCategory) {
            const categoryLink = document.querySelector(`.category-link[data-category="${selectedCategory}"]`);
            if (categoryLink) {
                showCategory(selectedCategory, categoryLink.textContent.trim());
            }
        }
    });
    </script>

    <footer>
    <div class="footer-container">
      <div class="footer-left">
        <img src="images/logo_img.jpg" alt="Eskina Coffee Logo">
        <h2>Eskina Coffee</h2>
      </div>

      <div class="footer-right">
        <a href="https://www.facebook.com/eskinacoffee" target="_blank" class="social-link">
        <img src="images/facebook.svg" alt="Facebook" class="icon"> Eskina Coffee
        </a>
    
        <a href="https://www.instagram.com/eskinacoffeeph/" target="_blank" class="social-link">
         <img src="images/instagram.svg" alt="Instagram" class="icon"> eskinacoffeeph
        </a>

        <a href="https://mail.google.com/mail/?view=cm&fs=1&to=eskinacoffee@gmail.com" 
                 target="_blank"  class="email-link">
         <img src="images/email.svg" alt="Email" class="icon"> eskinacoffee@gmail.com
        </a>
      </div>
    </div>

    <div class="footer-bottom">
      Copyright &copy; <?php echo date("Y"); ?> Eskina Coffee
    </div>
  </footer>

</body>
</html>
