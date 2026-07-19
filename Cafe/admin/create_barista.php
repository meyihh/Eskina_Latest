<?php
include '../config.php';
require_once '../auth.php';
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function generateOTP($length = 6) {
    return str_pad(random_int(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
}

$adminName = htmlspecialchars($_SESSION['user']['username'] ?? 'Admin');

function sendOTPEmail($toEmail, $otp) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'cambriblessmae.bsit@gmail.com';
        $mail->Password = 'kelf yifr huuh wavw';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('cambriblessmae.bsit@gmail.com', 'Eskina Coffee');
        $mail->addAddress($toEmail);

        $mail->isHTML(true);
        $mail->Subject = '☕ Verify Your Barista Account';
        $mail->Body = "<h2>Your OTP: <b>$otp</b></h2><p>Enter this to complete registration.</p>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');

    if ($_POST['action'] === 'register') {
        $email = trim($_POST['email']);
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        $passwordPattern = "/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/";
    if (!preg_match($passwordPattern, $password)) {
        echo json_encode([
            "status" => "error",
            "message" => "Password must be at least 8 characters long and include an uppercase letter, lowercase letter, number, and special character."
        ]);
        exit;
    }

        if ($password !== $confirm_password) {
            echo json_encode(["status" => "error", "message" => "Passwords do not match!"]);
            exit;
        }

        $check = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $check->bind_param("ss", $email, $username);
        $check->execute();
        $check->store_result();
        if ($check->num_rows > 0) {
            echo json_encode(["status" => "error", "message" => "Email or Username already exists!"]);
            exit;
        }

        $otp = generateOTP();
        $_SESSION['pending_user'] = [
            'email' => $email,
            'username' => $username,
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'role' => 'barista'
        ];
        $_SESSION['otp'] = $otp;

        if (sendOTPEmail($email, $otp)) {
            echo json_encode(["status" => "otp", "message" => "OTP sent to your email."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to send OTP. Try again later."]);
        }
        exit;
    }

    if ($_POST['action'] === 'verify') {
        $enteredOtp = $_POST['otp'];

        if (isset($_SESSION['otp'], $_SESSION['pending_user'])) {
            if ($enteredOtp === $_SESSION['otp']) {
                $user = $_SESSION['pending_user'];
                $stmt = $conn->prepare("INSERT INTO users (email, username, password, role) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $user['email'], $user['username'], $user['password'], $user['role']);

                if ($stmt->execute()) {
                    unset($_SESSION['otp'], $_SESSION['pending_user']);
                    echo json_encode(["status" => "success", "message" => "Account created successfully!"]);
                } else {
                    echo json_encode(["status" => "error", "message" => "Database error!"]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "Invalid OTP. Please try again."]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Session expired. Please register again."]);
        }
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Eskina Coffee | Barista</title>
<link rel="stylesheet" href="./css/create_barista.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


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
      <span class="admin-name"><?= $adminName ?></span>
    </div>

    <div class="logout-popup" id="logoutPopup">Logout</div>
    <form id="logoutForm" action="logout.php" method="POST" style="display:none;"></form>
  </aside>

  <button class="menu-toggle">☰</button>

<!-- MAIN CONTENT -->
<div class="main-layout">
  <main class="content">
    <div class="tab-container">
        <button class="tab-btn active" data-tab="tab-create">Account Creation for Barista</button>
        <button class="tab-btn" data-tab="tab-manage">Manage Barista Accounts</button>
      </div>

      <div id="tab-create" class="tab-panel" style="display: block;">
    <h2>Account Creation for Barista</h2>

    <form class="barista-form" id="baristaForm">
      <input type="hidden" name="action" value="register">

      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>
      </div>

      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <div class="password-wrapper">
          <input type="password" id="password" name="password" required>
          <img src="./images/eye.svg" class="toggle-password" onclick="togglePassword('password', this)" alt="Show/Hide Password">
        </div>
      </div>

      <div class="form-group">
        <label for="confirm_password">Confirm Password</label>
        <div class="password-wrapper">
          <input type="password" id="confirm_password" name="confirm_password" required>
          <img src="./images/eye.svg" class="toggle-password" onclick="togglePassword('confirm_password', this)" alt="Show/Hide Password">
        </div>
      </div>

       <div class="form-actions">
            <button type="submit" class="btn create-btn">Create</button>
          </div>
        </form>
      </div>

  <div id="tab-manage" class="tab-panel" style="display: none;">

  <div class="tab-panel‑wrapper">
  <h2 class="heading-manage‑baristas">Manage Barista Accounts</h2>

  <?php
    $stmt = $conn->prepare("SELECT id, email, username FROM users WHERE role = ?");
    $role = 'barista';
    $stmt->bind_param("s", $role);
    $stmt->execute();
    $result = $stmt->get_result();
  ?>

  <table border="1" cellpadding="8" cellspacing="0" style="width:100%; max-width:800px; margin-top:20px;">
    <thead>
      <tr>
        <th>ID</th>
        <th>Email</th>
        <th>Username</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($row['id']) ?></td>
        <td><?= htmlspecialchars($row['email']) ?></td>
        <td><?= htmlspecialchars($row['username']) ?></td>
        <td>
          <button class="btn edit-barista-btn" data-id="<?= htmlspecialchars($row['id']) ?>" data-email="<?= htmlspecialchars($row['email']) ?>" data-username="<?= htmlspecialchars($row['username']) ?>">
            Edit
          </button>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <?php $stmt->close(); ?>

  <div class="modal fade" id="editBaristaModal" tabindex="-1" aria-labelledby="editBaristaLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="editBaristaForm">
        <div class="modal-header">
          <h5 class="modal-title" id="editBaristaLabel">Edit Barista Account</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="editBaristaId">

          <div class="form-group">
            <label>Email (cannot change):</label>
            <input type="email" class="form-control" id="editBaristaEmail" disabled>
          </div>

          <div class="form-group">
            <label for="editBaristaUsername">Username:</label>
            <input type="text" class="form-control" name="username" id="editBaristaUsername" required>
          </div>

          <div class="form-group">
            <label for="editBaristaPassword">New Password (leave blank if no change):</label>
            <input type="password" class="form-control" name="password" id="editBaristaPassword">
          </div>

          <div class="form-group">
            <label for="editBaristaConfirm">Confirm New Password:</label>
            <input type="password" class="form-control" name="confirm_password" id="editBaristaConfirm">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>
    </main>
  </div>
</div>

 <script>

  popup.addEventListener("click", () => {
    Swal.fire({
      title: "Are you sure you want to log out?",
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

    document.querySelectorAll('.edit-barista-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.getAttribute('data-id');
        const email = btn.getAttribute('data-email');
        const username = btn.getAttribute('data-username');

        document.getElementById('editBaristaId').value = id;
        document.getElementById('editBaristaEmail').value = email;
        document.getElementById('editBaristaUsername').value = username;
        document.getElementById('editBaristaPassword').value = '';
        document.getElementById('editBaristaConfirm').value = '';

        // Show modal (Bootstrap 5 syntax)
        var editModal = new bootstrap.Modal(document.getElementById('editBaristaModal'));
        editModal.show();
      });
    });

    document.getElementById('editBaristaForm').addEventListener('submit', function(e) {
      e.preventDefault();

      const form = this;
      const formData = new FormData(form);
      formData.append('action', 'update');

      fetch('update_barista.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success') {
          alert('Barista account updated successfully.');
          window.location.reload();  // reload to reflect changes
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(err => {
        console.error(err);
        alert('An error occurred while updating.');
      });
    });
  </script>

<!-- ✅ LOGOUT SCRIPT -->
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

// Toggle password visibility
function togglePassword(inputId, icon) {
    const input = document.getElementById(inputId);
    if(input.type === "password") {
        input.type = "text";
        icon.src = "./images/eye-off.svg";
    } else {
        input.type = "password";
        icon.src = "./images/eye.svg";
    }
}

// Clear form fields
function clearBaristaForm() {
    document.getElementById("baristaForm").reset();
}

// OTP modal
function showOTPModal() {
    Swal.fire({
        title: 'Enter OTP',
        html: `
            <div class="otp-container">
                <input type="text" maxlength="1" class="otp-input" inputmode="numeric"/>
                <input type="text" maxlength="1" class="otp-input" inputmode="numeric"/>
                <input type="text" maxlength="1" class="otp-input" inputmode="numeric"/>
                <input type="text" maxlength="1" class="otp-input" inputmode="numeric"/>
                <input type="text" maxlength="1" class="otp-input" inputmode="numeric"/>
                <input type="text" maxlength="1" class="otp-input" inputmode="numeric"/>
            </div>
        `,
        confirmButtonText: 'Verify',
        confirmButtonColor: '#3b2a28',
        showCancelButton: true,
        cancelButtonColor: '#aaa',
        preConfirm: () => {
            let otp = '';
            document.querySelectorAll('.otp-input').forEach(input => otp += input.value);
            if(otp.length !== 6) {
                Swal.showValidationMessage('Please enter all 6 digits');
                return false;
            }
            return otp;
        },
        didOpen: () => {
            const inputs = document.querySelectorAll('.otp-input');
            inputs.forEach((input, i) => {
                input.addEventListener('input', () => {
                    if(input.value.length === 1 && i < inputs.length - 1) inputs[i+1].focus();
                });
                input.addEventListener('keydown', e => {
                    if(e.key === 'Backspace' && input.value === '' && i > 0) inputs[i-1].focus();
                });
            });
            inputs[0].focus();
        }
    }).then(result => {
        if(result.isConfirmed) {
            const otp = result.value;
            let verifyData = new URLSearchParams();
            verifyData.append("action","verify");
            verifyData.append("otp", otp);

            fetch("create_barista.php", {
                method: "POST",
                headers: {"Content-Type":"application/x-www-form-urlencoded"},
                body: verifyData
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === "success") {
                    Swal.fire({ icon:'success', title: 'Account created successfully!', confirmButtonColor:'#3b2a28'})
                    .then(() => clearBaristaForm());
                } else {
                    Swal.fire({ icon:'error', title: data.message, confirmButtonColor:'#3b2a28'})
                    .then(() => showOTPModal());
                }
            });
        }
    });
}

// Form submission
document.getElementById("baristaForm").addEventListener("submit", function(e) {
    e.preventDefault();
    let formData = new FormData(this);

    fetch("create_barista.php", { method: "POST", body: formData })
    .then(res => res.json())
    .then(data => {
        if(data.status === "error") {
            Swal.fire({ icon: 'error', title: data.message, confirmButtonColor: '#3b2a28' });
        } else if(data.status === "otp") {
            showOTPModal();
        }
    });
});

// Tab switching logic
document.querySelectorAll('.tab-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-panel').forEach(panel => panel.style.display = 'none');

    btn.classList.add('active');
    const tabId = btn.getAttribute('data-tab');
    document.getElementById(tabId).style.display = 'block';
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
