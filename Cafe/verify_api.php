<?php
require_once 'config.php';
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);
$email = $data['email'] ?? '';
$otp   = $data['otp'] ?? '';

if (empty($email) || empty($otp)) {
    echo json_encode(["success" => false, "message" => "Missing email or OTP"]);
    exit;
}

// 1️⃣ Check OTP exists
$stmt = $conn->prepare("SELECT * FROM otp_verification WHERE email = ? AND otp = ?");
$stmt->bind_param("ss", $email, $otp);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Invalid OTP"]);
    exit;
}

$user = $result->fetch_assoc();

// 2️⃣ Check expiry manually (fix timezone issues)
if (strtotime($user['expires_at']) < time()) {
    echo json_encode(["success" => false, "message" => "OTP expired"]);
    exit;
}

// 3️⃣ Insert into users table
$stmt2 = $conn->prepare("INSERT INTO users (full_name, email, contact, address, username, password) 
                         VALUES (?, ?, ?, ?, ?, ?)");
$stmt2->bind_param("ssssss",
    $user['full_name'],
    $user['email'],
    $user['contact'],
    $user['address'],
    $user['username'],
    $user['password']
);

if ($stmt2->execute()) {
    // delete OTP record
    $stmt3 = $conn->prepare("DELETE FROM otp_verification WHERE email = ?");
    $stmt3->bind_param("s", $email);
    $stmt3->execute();

    echo json_encode([
        "success" => true,
        "message" => "Registration complete",
        "redirect" => "dashboard"
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Database error: " . $conn->error]);
}
?>