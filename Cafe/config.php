<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'eskina';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<?php
// $host = 'localhost';
// $user = 'u465107535_eskina_cafe';
// $pass = 'Coffe_alltheway123';
// $dbname = 'u465107535_eskina';

// $conn = new mysqli($host, $user, $pass, $dbname);

// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }
?>