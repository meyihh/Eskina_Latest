<?php
require_once 'config.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
    header("Location: landing.php");
    exit();
}

$user_id = $_SESSION['user']['id'];

// Get data from POST request
$full_name        = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
$email            = isset($_POST['email']) ? trim($_POST['email']) : '';
$contact          = isset($_POST['contact']) ? trim($_POST['contact']) : '';
$address          = isset($_POST['address']) ? trim($_POST['address']) : '';
$password         = isset($_POST['password']) ? trim($_POST['password']) : '';
$confirm_password = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';

// Validate inputs
if (empty($full_name) || empty($email) || empty($contact) || empty($address)) {
    $_SESSION['message'] = "⚠️ All fields except password are required.";
    header("Location: dtr.php");
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['message'] = "⚠️ Invalid email format.";
    header("Location: dtr.php");
    exit();
}

if (!empty($password)) {
    if (strlen($password) < 6) {
        $_SESSION['message'] = "⚠️ Password must be at least 6 characters long.";
        header("Location: dtr.php");
        exit();
    }
    if ($password !== $confirm_password) {
        $_SESSION['message'] = "⚠️ Passwords do not match.";
        header("Location: dtr.php");
        exit();
    }
}

// Build query depending on whether password is updated
if (!empty($password)) {
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("UPDATE accounts
        SET full_name = ?, email = ?, contact = ?, address = ?, password = ?
        WHERE id = ?");
    $stmt->bind_param("sssssi", $full_name, $email, $contact, $address, $hashed_password, $user_id);
} else {
    $stmt = $conn->prepare("UPDATE accounts 
        SET full_name = ?, email = ?, contact = ?, address = ?
        WHERE id = ?");
    $stmt->bind_param("ssssi", $full_name, $email, $contact, $address, $user_id);
}

if ($stmt && $stmt->execute()) {
    $_SESSION['message'] = "✅ Profile updated successfully!";
} else {
    $_SESSION['message'] = "❌ Update failed: " . ($stmt ? $stmt->error : $conn->error);
}

$stmt->close();
$conn->close();

// Redirect back to DTR
header("Location: dtr.php");
exit();
?>
