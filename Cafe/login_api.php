<?php
header("Content-Type: application/json");
require_once "config.php";
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$response = ["status" => "error", "message" => "Invalid request."];

$username = trim($_POST['username'] ?? $_GET['username'] ?? "");
$password = $_POST['password'] ?? $_GET['password'] ?? "";

if (!empty($username) && !empty($password)) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $dbPassword = $user['password'];

        // ✅ Allow both hashed and plain text passwords
        if (password_verify($password, $dbPassword) || $password === $dbPassword) {
            $_SESSION['user'] = $user;
            $response = [
                "status" => "success",
                "message" => "Login successful",
                "user" => [
                    "id" => $user['id'],
                    "username" => $user['username'],
                    "email" => $user['email'] ?? null
                ]
            ];
        } else {
            $response = ["status" => "error", "message" => "Invalid username or password"];
        }
    } else {
        $response = ["status" => "error", "message" => "Invalid username or password"];
    }
}

echo json_encode($response);
