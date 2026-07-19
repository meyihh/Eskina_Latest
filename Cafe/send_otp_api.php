<?php
require_once 'config.php';
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

$full_name = $data["full_name"];
$email     = $data["email"];
$contact   = $data["contact"];
$address   = $data["address"];
$username  = $data["username"];
$password  = password_hash($data["password"], PASSWORD_BCRYPT);

// check if user exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
$stmt->bind_param("ss", $email, $username);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Email or username already in use"]);
    exit;
}

// generate OTP
$otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
$expires_at = date("Y-m-d H:i:s", strtotime("+5 minutes"));

// store in otp_verification table
$stmt = $conn->prepare("REPLACE INTO otp_verification (email, otp, full_name, contact, address, username, password, expires_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssss", $email, $otp, $full_name, $contact, $address, $username, $password, $expires_at);

if ($stmt->execute()) {
    // send OTP email
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'cambriblessmae.bsit@gmail.com';
        $mail->Password   = 'kelf yifr huuh wavw'; // app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('cambriblessmae.bsit@gmail.com', 'Eskina Coffee');
        $mail->addAddress($email, $full_name);

        $mail->isHTML(true);
        $mail->Subject = 'Your OTP Code - Eskina Coffee';
        $mail->Body    = "Hello $full_name,<br><br>Your OTP code is: <b>$otp</b><br>This OTP will expire in 5 minutes.<br><br>Thank you!";

        $mail->send();
        echo json_encode(["success" => true, "message" => "OTP sent to your email"]);
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "Failed to send OTP: {$mail->ErrorInfo}"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Database error"]);
}
