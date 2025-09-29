<?php
session_start();
require 'dbconn.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.html");
    exit;
}

$user_id = $_SESSION['id'];
$name = trim($_POST['name']);
$email = trim($_POST['email']);
$password = trim($_POST['password']);

// Update query
if (!empty($password)) {
    // If password is updated
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?");
    $stmt->bind_param("sssi", $name, $email, $hashed, $user_id);
} else {
    // Without changing password
    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
    $stmt->bind_param("ssi", $name, $email, $user_id);
}

if ($stmt->execute()) {
    echo "<script>alert('Profile updated successfully!'); window.location='settings.php';</script>";
} else {
    echo "<script>alert('Error updating profile.'); window.location='settings.php';</script>";
}
