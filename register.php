<?php
require 'dbconn.php';

$username = $_POST['username'];
$email = $_POST['email'];
$pw1 = $_POST['password_1'];
$pw2 = $_POST['password_2'];

if($pw1 !== $pw2) {
    die('Passwords do not match.');
}
// Securely hash password
$hash = password_hash($pw1, PASSWORD_BCRYPT);

// Use prepared statements to prevent SQL injection
$stmt = $conn->prepare("INSERT INTO information (username, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $username, $email, $hash);
if($stmt->execute()) {
    header("Location: login.html");
} else {
    echo "Error: " . $stmt->error;
}
?>
