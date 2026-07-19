<?php
session_start();
require_once 'config.php';

// 🔒 Clear any existing session (Logout logic)
session_unset();
session_destroy();
session_start(); // restart a clean session after destroying the old one

$loginError = "";

// Clear session if redirected from reset password
if (isset($_GET['reset']) && $_GET['reset'] === "success") {
    $_SESSION = [];
}

// Login function
function loginUser($conn, $username, $password) {
    $stmt = $conn->prepare("SELECT * FROM accounts WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }
    return false;
}

// Handle login submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $user = loginUser($conn, $username, $password);

    if ($user) {
        // ✅ Regenerate session ID for security
        session_regenerate_id(true);

        // ✅ Store user session
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role'] ?? 'user'
        ];

        // ✅ Redirect ALL users to dtr.php
        header("Location: ./dtr/dtr.php");
        exit();
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
                <div class="error-message"><?= htmlspecialchars($loginError) ?></div>
              <?php endif; ?>

              <button type="submit" name="login" class="submit-btn">Login</button>
              
              <div class="toggle-text">
                <a href="forgot_password.php">Forgot Password?</a>
              </div>
              <div class="toggle-text">
                Don’t have an account? <a href="sign_up.php">Sign Up</a>
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
