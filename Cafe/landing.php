<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

include 'config.php'; // connect to your database

$loginError = "";
$redirect = false;
$redirectUrl = "";

// Clear session if redirected from reset password (only once)
if (isset($_GET['reset']) && $_GET['reset'] === "success") {
    if (session_status() === PHP_SESSION_ACTIVE) {
        $_SESSION = [];
    }
}

// Login function
function loginUser($conn, $username, $password) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;
        return $user; // return full user record including role
    }
    return false;
}

// Handle login submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $user = loginUser($conn, $username, $password);
    if ($user) {
        $redirect = true;

        // Redirect based on role
        if ($user['role'] === 'admin') {
            $redirectUrl = "./admin/admin_dashboard.php";
        } else {
            $redirectUrl = "./barista/main.php"; // barista default dashboard
        }
    } else {
        $loginError = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Eskina Coffee | Login</title>
  <link rel="stylesheet" href="./css/landing.css" />
</head>
<body>
  <div class="fade-container" id="pageContainer">
    <div class="container">
      <div class="left">
        <img src="./images/eslogo.jpg" alt="Eskina Coffee Logo" />
      </div>
      <div class="right">
        <div class="form-wrapper">
          <div class="form-box">
            <h2>Login to Eskina</h2>
            
           <form method="POST" id="loginForm">
           <input type="text" name="username" placeholder="Username" required />

           <div class="password-wrapper">
            <input type="password" id="password" name="password" placeholder="Password" required />
            <img src="./images/eye.svg" class="toggle-password" onclick="togglePassword('password', this)" alt="Toggle Password">
           </div>

              <?php if (!empty($loginError)): ?>
                <div class="error-message"><?= $loginError ?></div>
              <?php endif; ?>
              <button type="submit" name="login" class="submit-btn">Login</button>
              <div class="toggle-text">
                <a href="forgot_password.php">Forgot Password?</a>
              </div>             
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Loader -->
  <div class="loader-wrapper" id="loader">
    <div class="loader"></div>
    <span class="loader-letter">L</span>
    <span class="loader-letter">o</span>
    <span class="loader-letter">a</span>
    <span class="loader-letter">d</span>
    <span class="loader-letter">i</span>
    <span class="loader-letter">n</span>
    <span class="loader-letter">g</span>
    <span class="loader-letter">.</span>
    <span class="loader-letter">.</span>
    <span class="loader-letter">.</span>
  </div>

<script>
  const form = document.getElementById("loginForm");
  const loader = document.getElementById("loader");
  const page = document.getElementById("pageContainer");

  // Show loader on form submit
  form.addEventListener("submit", () => {
    loader.style.display = "flex";
    page.classList.add("fade-out");
  });

  <?php if ($redirect): ?>
    // Show loader during redirect
    loader.style.display = "flex";
    setTimeout(() => {
      window.location.href = "<?= $redirectUrl ?>";
    }, 800);
  <?php endif; ?>
</script>

<script>
  function togglePassword(inputId, icon) {
  const input = document.getElementById(inputId);
  if (input.type === "password") {
    input.type = "text";
    icon.src = "./images/eye-off.svg"; 
  } else {
    input.type = "password";
    icon.src = "./images/eye.svg"; 
  }
}
</script>

</body>
</html>
