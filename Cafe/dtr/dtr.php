<?php
session_start();
require_once '../config.php';
date_default_timezone_set('Asia/Manila');
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];
$username = trim($_SESSION['user']['username']);
$message = "";

// Fetch user details
$stmt = $conn->prepare("SELECT full_name, email, contact, address FROM accounts WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Ensure uploads folder exists
$uploadDir = "uploads/dtr/";
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

// Handle Time In / Time Out
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action'])) {
    $photoPath = null;
    $timestamp = date("Y-m-d H:i:s");

    // Handle selfie upload
    if (!empty($_POST['selfie'])) {
        $data = $_POST['selfie'];
        if (strpos($data, ',') !== false) {
            list($meta, $data) = explode(',', $data);
            $data = base64_decode($data);
            if ($data !== false) {
                $fileName = $user_id . "_" . time() . "_selfie.png";
                $photoPath = $uploadDir . $fileName;
                file_put_contents($photoPath, $data);
            } else {
                $_SESSION['message'] = "⚠️ Invalid photo data!";
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }
        }
    } else {
        $_SESSION['message'] = "⚠️ Please capture a photo before recording!";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // Get today's log using BETWEEN instead of DATE()
    $startOfDay = date("Y-m-d 00:00:00");
    $endOfDay = date("Y-m-d 23:59:59");

    $stmt = $conn->prepare("SELECT * FROM dtr_logs WHERE user_id=? AND time_in BETWEEN ? AND ? ORDER BY id DESC LIMIT 1");
    $stmt->bind_param("iss", $user_id, $startOfDay, $endOfDay);
    $stmt->execute();
    $todayLog = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($_POST['action'] === "time_in") {
        if ($todayLog && $todayLog['time_in']) {
            $_SESSION['message'] = "⚠️ You already timed in today!";
        } else {
            $stmt = $conn->prepare("INSERT INTO dtr_logs (user_id, time_in, photo_in) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $user_id, $timestamp, $photoPath);
            $stmt->execute();
            $stmt->close();
            $_SESSION['message'] = "✅ Time In recorded successfully!";
        }
    } elseif ($_POST['action'] === "time_out") {
        $stmt = $conn->prepare("UPDATE dtr_logs SET time_out=?, photo_out=? 
                                WHERE user_id=? AND time_out IS NULL AND time_in BETWEEN ? AND ?");
        $stmt->bind_param("ssiss", $timestamp, $photoPath, $user_id, $startOfDay, $endOfDay);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $_SESSION['message'] = "✅ Time Out recorded successfully!";
        } else {
            $_SESSION['message'] = "⚠️ You already timed out today or no active log found!";
        }
        $stmt->close();
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Display session message if any
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// Fetch attendance logs
$stmt = $conn->prepare("SELECT * FROM dtr_logs WHERE user_id=? ORDER BY id DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$logs = $stmt->get_result();
$stmt->close();

// Determine today’s button state
$startOfDay = date("Y-m-d 00:00:00");
$endOfDay = date("Y-m-d 23:59:59");

$stmt = $conn->prepare("SELECT * FROM dtr_logs WHERE user_id=? AND time_in BETWEEN ? AND ? ORDER BY id DESC LIMIT 1");
$stmt->bind_param("iss", $user_id, $startOfDay, $endOfDay);
$stmt->execute();
$todayLog = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$todayLog) {
    $buttonAction = "time_in";
    $buttonLabel = "🕒 Time In";
    $buttonDisabled = "";
} elseif ($todayLog && !$todayLog['time_out']) {
    $buttonAction = "time_out";
    $buttonLabel = "⏰ Time Out";
    $buttonDisabled = "";
} else {
    $buttonAction = "disabled";
    $buttonLabel = "✔️ Completed";
    $buttonDisabled = "disabled";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>DTR Dashboard</title>
<link rel="stylesheet" href="./css/dtr.css" />
<style>
button[disabled] {
  opacity: 0.6;
  cursor: not-allowed;
  background: #aaa !important;
}
</style>
</head>
<body>

<header>
  <a href="index.php"><img src="../images/eslogo.jpg" alt="Logo" class="logo"></a>
  <a href="../logout.php" class="logout-btn">Logout</a>
</header>


<main class="main-content">
  <section class="profile-section">
    <div class="profile-container">

      <div class="camera-section">
        <video id="video" autoplay playsinline width="320" height="240" class="video-preview"></video>
        <canvas id="canvas" style="display:none;"></canvas>
        <img id="previewImg" class="preview-image" style="display:none;" />

        <form method="POST" id="dtrForm" enctype="multipart/form-data" class="capture-form">
          <input type="hidden" name="selfie" id="selfie" />
          <button type="button" id="captureBtn" class="btn capture-btn">📸 Capture</button>
          <button type="submit" name="action" id="timeActionBtn" class="btn time-btn"
                  value="<?= $buttonAction ?>" <?= $buttonDisabled ?>>
            <?= $buttonLabel ?>
          </button>
        </form>
      </div>

      <div class="staff-details-container">
        <h2>Profile</h2>
        <?php if ($message): ?>
          <div class="alert-box"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <form method="POST" action="update_profile.php" class="staff-details-form">
          <div><label>Full Name:</label><input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']); ?>" required /></div>
          <div><label>Email:</label><input type="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required /></div>
          <div><label>Contact:</label><input type="text" name="contact" value="<?= htmlspecialchars($user['contact']); ?>" required /></div>
          <div><label>Address:</label><input type="text" name="address" value="<?= htmlspecialchars($user['address']); ?>" required /></div>
          <div><label>New Password:</label><input type="password" name="password" /><small>(Leave blank if you don’t want to change it)</small></div>
          <div><label>Confirm Password:</label><input type="password" name="confirm_password" /></div>
          <button type="submit" class="btn save-details-btn">Save Details</button>
        </form>
      </div>
    </div>
  </section>

  <section class="attendance-section">
    <h2 class="attendance-title">Attendance Log</h2>
    <div class="attendance-container">
      <table class="attendance-table">
        <thead>
          <tr>
            <th>DATE</th>
            <th>TIME IN</th>
            <th>PHOTO IN</th>
            <th>TIME OUT</th>
            <th>PHOTO OUT</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($log = $logs->fetch_assoc()) : ?>
            <tr>
              <td><?= date("Y-m-d", strtotime($log['time_in'])) ?></td>
              <td><?= date("h:i:s A", strtotime($log['time_in'])) ?></td>
              <td><?= $log['photo_in'] ? "<img src='{$log['photo_in']}' class='thumb-img'/>" : "-" ?></td>
              <td><?= $log['time_out'] ? date("h:i:s A", strtotime($log['time_out'])) : "-" ?></td>
              <td><?= $log['photo_out'] ? "<img src='{$log['photo_out']}' class='thumb-img'/>" : "-" ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </section>
</main>

<script>
const video = document.getElementById('video');
const canvas = document.getElementById('canvas');
const selfieInput = document.getElementById('selfie');
const captureBtn = document.getElementById('captureBtn');
const previewImg = document.getElementById('previewImg');

navigator.mediaDevices.getUserMedia({ video: { facingMode: "user" } })
  .then(stream => video.srcObject = stream)
  .catch(err => console.error("Camera access denied:", err));

captureBtn.addEventListener('click', () => {
  canvas.width = video.videoWidth;
  canvas.height = video.videoHeight;
  const ctx = canvas.getContext('2d');
  ctx.translate(canvas.width, 0);
  ctx.scale(-1, 1);
  ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
  const dataURL = canvas.toDataURL('image/png');
  selfieInput.value = dataURL;
  previewImg.src = dataURL;
  previewImg.style.display = 'block';
  video.style.display = 'none';
});
</script>

</body>
</html>
