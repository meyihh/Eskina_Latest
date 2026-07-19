<?php
include '../config.php';
require_once '../auth.php';

// Check admin role
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $id               = intval($_POST['id'] ?? 0);
    $newUsername      = trim($_POST['username'] ?? '');
    $newPassword      = trim($_POST['password'] ?? '');
    $confirmPassword  = trim($_POST['confirm_password'] ?? '');

    if ($id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid user ID.']);
        exit;
    }
    if ($newUsername === '') {
        echo json_encode(['status' => 'error', 'message' => 'Username cannot be empty.']);
        exit;
    }

    // If password provided, check confirmation & pattern
    if ($newPassword !== '') {
        if ($newPassword !== $confirmPassword) {
            echo json_encode(['status' => 'error', 'message' => 'Passwords do not match.']);
            exit;
        }
        // Example password strength check (optional)
        $passwordPattern = "/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/";
        if (!preg_match($passwordPattern, $newPassword)) {
            echo json_encode(['status' => 'error', 'message' => 'Password must be at least 8 characters, include upper & lower case letters, number and special character.']);
            exit;
        }
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
    }

    // Check username uniqueness (excluding this current user)
    $checkStmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND id <> ? AND role = ?");
    $role       = 'barista';
    $checkStmt->bind_param("sis", $newUsername, $id, $role);
    $checkStmt->execute();
    $checkStmt->store_result();
    if ($checkStmt->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Username already taken.']);
        exit;
    }
    $checkStmt->close();

    // Build update SQL
    if ($newPassword !== '') {
        $sql = "UPDATE users SET username = ?, password = ? WHERE id = ? AND role = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssis", $newUsername, $hashedPassword, $id, $role);
    } else {
        $sql = "UPDATE users SET username = ? WHERE id = ? AND role = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sis", $newUsername, $id, $role);
    }

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Barista account updated successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database update failed.']);
    }
    $stmt->close();
    exit;
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
    exit;
}
?>
