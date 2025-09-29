<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'server1');
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

$username = 'admin2';
$password_plain = 'demo1234';

// Hash the password
$password_hashed = password_hash($password_plain, PASSWORD_DEFAULT);

// Insert user
$stmt = $conn->prepare("INSERT INTO users_table (username, password) VALUES (?, ?)");
$stmt->bind_param('ss', $username, $password_hashed);
if ($stmt->execute()) {
    echo "User created successfully with username: $username and password: $password_plain";
} else {
    echo "Error creating user: " . $stmt->error;
}
$stmt->close();
$conn->close();
