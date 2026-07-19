<?php
require_once 'config.php';
session_start();
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function generateOTP($length = 6) {
    return str_pad(random_int(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
}

function sendOTPEmail($toEmail, $otp) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'cambriblessmae.bsit@gmail.com';
        $mail->Password = 'kelf yifr huuh wavw'; // Gmail App Password
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

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

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
}
