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
  <title>Eskina Coffee | Barista Management</title>
  <link rel="stylesheet" href="./css/barista_create.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<div class="main-layout">
  <aside class="sidebar">
    <button class="sidebar-close">×</button>
    <ul>
      <li><a href="admin_dashboard.php">Dashboard</a></li>
      <li><a href="menu_items.php">Menu Items</a></li>
      <li><a href="orders.php">Order Transactions</a></li>
      <li><a href="customers.php">Customers</a></li>
      <li><a href="employees.php">Employees</a></li>
      <li class="active"><a href="create_barista.php">Barista</a></li>
      <li><a href="admin_order.php">Order Taking</a></li>
      <li><a href="admin_serve.php">Order Status</a></li>
    </ul>

    <div class="sidebar-logo">
      <img src="./images/eslogo.jpg" alt="Eskina Coffee" class="logo-img" id="logoutLogo">
      <span class="admin-name"><?= $adminName ?></span>
    </div>

    <div class="logout-popup" id="logoutPopup">Logout</div>
    <form id="logoutForm" action="logout.php" method="POST" style="display:none;"></form>
  </aside>

  <button class="menu-toggle">☰</button>

  <main class="content">
    <!-- Tabs -->
    <div class="tab-container">
      <button class="tab-btn active" data-tab="tab-create">Account Creation for Barista</button>
      <button class="tab-btn" data-tab="tab-manage">Manage Barista Accounts</button>
    </div>

    <!-- Create Barista -->
    <div id="tab-create" class="tab-panel" style="display: block;">
      <h2>Account Creation for Barista</h2>
      <form class="barista-form" id="baristaForm">
        <div class="form-group">
          <label>Email</label>
          <input type="email" name="email" placeholder="Enter barista email" required>
        </div>
        <div class="form-group">
          <label>Username</label>
          <input type="text" name="username" placeholder="Enter username" required>
        </div>
        <div class="form-group password-wrapper">
          <label>Password</label>
          <input type="password" name="password" placeholder="Enter password" required>
          <span class="toggle-password">👁</span>
        </div>
        <div class="form-group password-wrapper">
          <label>Confirm Password</label>
          <input type="password" name="confirm_password" placeholder="Confirm password" required>
          <span class="toggle-password">👁</span>
        </div>
        <div class="form-actions">
          <button type="submit" class="btn create-btn">Create</button>
        </div>
      </form>
    </div>

    <!-- Manage Baristas -->
    <div id="tab-manage" class="tab-panel" style="display: none;">
      <h2 class="heading-manage-baristas">Manage Barista Accounts</h2>
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
              <button class="btn edit-barista-btn" data-id="<?= htmlspecialchars($row['id']) ?>" data-email="<?= htmlspecialchars($row['email']) ?>" data-username="<?= htmlspecialchars($row['username']) ?>">Edit</button>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
      <?php $stmt->close(); ?>

      <!-- Edit Modal -->
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
            <label>Username:</label>
            <input type="text" class="form-control" name="username" id="editBaristaUsername" required>
          </div>

          <div class="form-group">
            <label>New Password (leave blank if no change):</label>
            <input type="password" class="form-control" name="password" id="editBaristaPassword">
          </div>

          <div class="form-group">
            <label>Confirm New Password:</label>
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

    </div>
  </main>
</div>

<script>
// Logout popup
const logo = document.getElementById("logoutLogo");
const popup = document.getElementById("logoutPopup");
logo.addEventListener("click", () => popup.style.display = popup.style.display === "block" ? "none" : "block");
document.addEventListener("click", (e) => { if(!logo.contains(e.target) && !popup.contains(e.target)) popup.style.display='none'; });
popup.addEventListener("click", () => {
  Swal.fire({
    html: '<div style="text-align:center; font-size:30px; font-weight:bold;">Are you sure you want to log out?</div>',
    icon:"warning", showCancelButton:true, confirmButtonColor:"#3D2419", cancelButtonColor:"#3D2419", confirmButtonText:"Yes", cancelButtonText:"No"
  }).then(result => { if(result.isConfirmed) document.getElementById("logoutForm").submit(); });
});

// Sidebar toggle
const sidebar = document.querySelector('.sidebar');
document.querySelector('.menu-toggle').addEventListener('click', ()=>sidebar.classList.add('active'));
document.querySelector('.sidebar-close').addEventListener('click', ()=>sidebar.classList.remove('active'));

// Tabs
document.querySelectorAll('.tab-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active'));
    document.querySelectorAll('.tab-panel').forEach(p=>p.style.display='none');
    btn.classList.add('active');
    document.getElementById(btn.dataset.tab).style.display='block';
  });
});

// Show edit modal only when Edit button clicked
document.querySelectorAll('.edit-barista-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    const id = btn.getAttribute('data-id');
    const email = btn.getAttribute('data-email');
    const username = btn.getAttribute('data-username');

    // Fill modal fields
    document.getElementById('editBaristaId').value = id;
    document.getElementById('editBaristaEmail').value = email;
    document.getElementById('editBaristaUsername').value = username;
    document.getElementById('editBaristaPassword').value = '';
    document.getElementById('editBaristaConfirm').value = '';

    // Show Bootstrap 5 modal
    const editModal = new bootstrap.Modal(document.getElementById('editBaristaModal'));
    editModal.show();
  });
});

// Handle edit form submission
document.getElementById('editBaristaForm').addEventListener('submit', function(e) {
  e.preventDefault();

  const formData = new FormData(this);
  formData.append('action', 'update');

  fetch('update_barista.php', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(data => {
      if(data.status === 'success') {
        Swal.fire({
          icon: 'success',
          title: 'Barista account updated!',
          confirmButtonColor: '#3b2a28'
        }).then(() => window.location.reload());
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Error: ' + data.message,
          confirmButtonColor: '#3b2a28'
        });
      }
    })
    .catch(err => {
      console.error(err);
      Swal.fire({
        icon: 'error',
        title: 'An unexpected error occurred.',
        confirmButtonColor: '#3b2a28'
      });
    });
});



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

            fetch("barista_create.php", {
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
document.getElementById("baristaForm").addEventListener("submit", function(e) {
    e.preventDefault();
    let formData = new FormData(this);
    formData.append("action", "register"); // <-- ADD THIS

    fetch("barista_create.php", { 
        method: "POST", 
        body: formData 
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === "error") {
            Swal.fire({ icon: 'error', title: data.message, confirmButtonColor: '#3b2a28' });
        } else if(data.status === "otp") {
            showOTPModal();
        }
    })
    .catch(err => {
        console.error(err);
        Swal.fire({ icon: 'error', title: 'Request failed', confirmButtonColor: '#3b2a28' });
    });
});

</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
