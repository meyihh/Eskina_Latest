<?php
// about_us.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eskina Coffee | About Us</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=Noto+Serif:wght@400;700&display=swap" rel="stylesheet">

    <!-- External CSS -->
    <link rel="stylesheet" href="about_us.css">
</head>
<body>

    <!-- Navbar -->
    <nav>
        <div class="logo">
            <img src="images/logo_img.jpg" alt="Eskina Coffee Logo">
            <span>Eskina Coffee</span>
        </div>

        <input type="checkbox" id="menu-toggle" aria-label="Toggle navigation menu">
        <label for="menu-toggle" class="hamburger">☰</label>

        <ul class="nav-links">
            <li><a href="index.php#home">Home</a></li>
            <li><a href="menu.php">Menu</a></li>
            <li><a href="about_us.php" class="active">About Us</a></li>
            <li><a href="contact_us.php">Contact Us</a></li>
        </ul>
    </nav>

    <!-- About Us Section -->
    <section class="about-us" id="about">
  <h2>ABOUT US</h2>
  <div class="about-container">

    <!-- Flip Card 1 -->
    <div class="flip-card">
      <div class="flip-card-inner">
        <div class="flip-card-front">
          <p>
            Welcome to Eskina Coffee! We believe that coffee is more than just a drink, 
            it’s an experience that brings people together. Founded with the vision of creating 
            a warm and welcoming space, our cafe is a place where friends, families, and coffee lovers 
            can connect, relax, and enjoy handcrafted beverages made with passion.
          </p>
        </div>
        <div class="flip-card-back">
          <p>
            Quality beans that are expertly brewed to bring out their distinct flavors 
            are what we take delight in sourcing. We provide a variety of pastries and comfort food 
            to complete your cafe experience in addition to our specialty drinks. 
            Eskina Coffee has a comfortable spot for everyone.
          </p>
        </div>
      </div>
    </div>

    <!-- Flip Card 2 -->
    <div class="flip-card">
      <div class="flip-card-inner">
        <div class="flip-card-front">
          <p>
            The word "Eskina," commonly used in the Philippines, 
            comes from the Spanish word “esquina,” which also means “corner,” 
            especially a street corner or an alleyway.
          </p>
        </div>
        <div class="flip-card-back">
          <p>
            Eskina Coffee was established in June 2023. 
            Owned by two dedicated partners and managed by an experienced cafe manager, 
            our team works hard to bring a unique coffee experience to every guest. 
            Despite being new to the coffee business, our team embraced every challenge 
            with determination, learning and growing along the way.
          </p>
        </div>
      </div>
    </div>

  </div>
</section>


    <!-- Footer -->
    <footer>
        <div class="footer-container">
            <!-- Left side -->
            <div class="footer-left">
                <img src="images/logo_img.jpg" alt="Eskina Coffee Logo">
                <h2>Eskina Coffee</h2>
            </div>

            <!-- Right side -->
            <div class="footer-right">
                <a href="https://www.facebook.com/eskinacoffee" target="_blank" class="social-link" aria-label="Facebook Page">
                    <img src="images/facebook.svg" alt="Facebook Icon" class="icon"> Eskina Coffee
                </a>
            
                <a href="https://www.instagram.com/eskinacoffeeph/" target="_blank" class="social-link" aria-label="Instagram Profile">
                    <img src="images/instagram.svg" alt="Instagram Icon" class="icon"> eskinacoffeeph
                </a>

                <a href="mailto:eskinacoffee@gmail.com" target="_blank" class="email-link" aria-label="Send Email">
                    <img src="images/email.svg" alt="Email Icon" class="icon"> eskinacoffee@gmail.com
                </a>
            </div>
        </div>

        <!-- Bottom copyright -->
        <div class="footer-bottom">
            Copyright &copy; <?php echo date("Y"); ?> Eskina Coffee
        </div>
    </footer>

</body>
</html>
