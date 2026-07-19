
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eskina Coffee</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=Noto+Serif:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Navbar -->
    <nav>
        <div class="logo">
        <img src="images/logo_img.jpg" alt="Eskina Coffee Logo"> Eskina Coffee </div>

        <input type="checkbox" id="menu-toggle">
        <label for="menu-toggle" class="hamburger">☰</label>

        <!-- ✅ Wrap links inside ul for better styling -->
        <ul class="nav-links">
            <a href="index.php#home" class="active">Home</a></li>
            <li><a href="menu.php">Menu</a></li>
            <li><a href="about_us.php">About Us</a></li>
            <li><a href="contact_us.php">Contact Us</a></li>
        </ul>
    </nav>

    <!-- Landing Page -->
    <div class="image-container" id="home">
        <img src="images/image5.png" alt="Eskina">
        <h1>Experience the comfort of home in every sip!</h1>
    </div>

    <!-- Our Story -->
    <section class="story-container">
        <div class="story-image">
            <img src="images/image4.jpg" alt="Eskina" id="about">
        </div>
        <div class="story-text">
            <h2>OUR STORY</h2>
            <p>
                We at Eskina Coffee believe that a satisfying cup of coffee is about more than just the coffee itself.
                It is the welcoming ambiance, friendly service, and delicious treats that come with every sip.
                Our café is your sanctuary for authentic Filipino brews and heartfelt conversations until late-night.
            </p>
            <a href="about_us.php" class="btn1">ABOUT US</a>
        </div>
    </section>

    <!-- What We Have -->
    <section class="menu-categories" id="menu">
        <h2>Eskina Menu</h2>
        <div class="menu-carousel">
            <div class="menu-track">
                <div class="menu-item">
                    <img src="images/coffee_drinks.png" alt="Coffee and Drinks">
                    <a href="menu.php?category=drinks-classics" class="btn2">COFFEE AND DRINKS</a>
                </div>
                <div class="menu-item">
                    <img src="images/ricebowls.png" alt="Rice Bowls">
                    <a href="menu.php?category=foods-rice-bowls" class="btn2">RICE BOWLS</a>
                </div>
                <div class="menu-item">
                    <img src="images/pasta.png" alt="Pasta">
                    <a href="menu.php?category=foods-pasta" class="btn2">PASTA</a>
                </div>
                <div class="menu-item">
                    <img src="images/wraps_sandwiches.png" alt="Wraps and Sandwiches">
                    <a href="menu.php?category=foods-wraps-and-sandwiches" class="btn2">WRAPS & SANDWICHES</a>
                </div>
                <div class="menu-item">
                    <img src="images/munchies.png" alt="Munchies">
                    <a href="menu.php?category=foods-munchies" class="btn2">MUNCHIES</a>
                </div>
                <!-- Duplicate for seamless scrolling -->
                <div class="menu-item">
                    <img src="images/coffee_drinks.png" alt="Coffee and Drinks">
                    <a href="menu.php?category=drinks-classics" class="btn2">COFFEE AND DRINKS</a>
                </div>
                <div class="menu-item">
                    <img src="images/ricebowls.png" alt="Rice Bowls">
                    <a href="menu.php?category=foods-rice-bowls" class="btn2">RICE BOWLS</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact -->
    <section class="contact-container">
        <div class="contact-image">
            <img src="images/image3.jpg" alt="Eskina Coffee" id="contact">
        </div>
        <div class="contact-text">
            <h2>Eskina Coffee</h2>
            <h3>Our Location</h3>
            <p>203 11th Ave Cor 4th St, East Grace Park, Caloocan, Philippines, 1403</p>
            <h3>Opening Hours</h3>
            <p>
                Sunday - Thursday | 1:00 PM - 11:00 PM <br>
                Friday - Saturday | 1:00 PM - 12:00 AM
            </p>
            <a href="contact_us.php" class="btn3">CONTACT US</a>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-container">
            <div class="footer-left">
                <img src="images/logo_img.jpg" alt="Eskina Coffee Logo">
                <h2>Eskina Coffee</h2>
            </div>
            <div class="footer-right">
                <a href="https://www.facebook.com/eskinacoffee" target="_blank">
                    <img src="images/facebook.svg" alt="Facebook" class="icon"> Eskina Coffee
                </a>
                <a href="https://www.instagram.com/eskinacoffeeph/" target="_blank">
                    <img src="images/instagram.svg" alt="Instagram" class="icon"> eskinacoffeeph
                </a>
                <a href="mailto:eskinacoffee@gmail.com" target="_blank">
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
