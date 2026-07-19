<?php
require_once 'config.php';
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
}
