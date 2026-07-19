<?php
// contact_us.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eskina Coffee | Contact Us</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=Noto+Serif:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="contact_us.css">
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
            <li><a href="index.php#home">Home</a></li>
            <li><a href="menu.php">Menu</a></li>
            <li><a href="about_us.php">About Us</a></li>
            <li><a href="contact_us.php" class="active">Contact Us</a></li>
        </ul>
    </nav>

    <div class="contact-container">
        <!-- Left Column -->
        <div class="contact-info">
            <h2>CONTACT US</h2>
            <p>
                <img src="images/map-pin.svg" alt="Map Pin" class="icon">
                <a href="https://www.google.com/maps?q=203+11th+Ave+Cor+4th+St,+East+Grace+Park,+Caloocan,+Philippines,+1403" 
                target="_blank" 
                class="location-link">
                203 11th Ave Cor 4th St, East Grace Park, <br>
                &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;Caloocan, Philippines, 1403
            </a>
            </p>

            <p>
                <img src="images/mail.svg" alt="Mail" class="icon">
                <a href="https://mail.google.com/mail/?view=cm&fs=1&to=eskinacoffee@gmail.com" 
                target="_blank"  class="email-link"> eskinacoffee@gmail.com</a> 
            </p>

            <h3>Follow us on</h3>
            <p>
                <strong>Facebook:</strong> 
                <a href="https://www.facebook.com/eskinacoffee" target="_blank" class="social-link">
                    Eskina Coffee
                </a>
                </p>
                <p>
                <strong>Instagram:</strong> 
                <a href="https://www.instagram.com/eskinacoffeeph/" target="_blank" class="social-link">
                    eskinacoffeeph
                </a>
            </p>
        </div>

        <div class="opening-hours">
            <h2>Opening Hours</h2>
            <ul>
                <li><span>Monday</span><span>1:00PM – 11:00PM</span></li>
                <li><span>Tuesday</span><span>1:00PM – 11:00PM</span></li>
                <li><span>Wednesday</span><span>1:00PM – 11:00PM</span></li>
                <li><span>Thursday</span><span>1:00PM – 11:00PM</span></li>
                <li><span>Friday</span><span>1:00PM – 12:00AM</span></li>
                <li><span>Saturday</span><span>1:00PM – 12:00AM</span></li>
                <li><span>Sunday</span><span>1:00PM – 11:00PM</span></li>
            </ul>
            <div class="services">
                <p> Takeout · Dine-in · Outdoor seating · Online order</p>
            </div>
        </div>
    </div>

    <footer>
    <div class="footer-container">
      <!-- Left side -->
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

    <!-- Bottom copyright -->
    <div class="footer-bottom">
      Copyright &copy; <?php echo date("Y"); ?> Eskina Coffee
    </div>
  </footer>

</body>
</html>