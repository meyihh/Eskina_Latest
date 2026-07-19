<?php
require_once 'config.php';
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
        $mail->Password = 'kelf yifr huuh wavw'; // App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('cambriblessmae.bsit@gmail.com', 'Eskina Coffee');
        $mail->addAddress($toEmail);

        $mail->isHTML(true);
        $mail->Subject = '☕ Eskina Coffee - Verify Email';
        $mail->Body = "<p>Your OTP is: <b>$otp</b></p>";
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
    $otp = generateOTP();
    $_SESSION['register'][$email] = [
        'full_name' => $full_name,
        'email'     => $email,
        'contact'   => $contact,
        'address'   => $address,
        'username'  => $username,
        'password'  => $password,
        'otp'       => $otp
    ];

    if (sendOTPEmail($email, $otp)) {
        echo json_encode(["success" => true, "message" => "OTP sent"]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to send OTP"]);
    }
}
