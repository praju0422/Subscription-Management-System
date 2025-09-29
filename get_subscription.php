<?php
session_start();
header('Content-Type: application/json');
require 'dbconn.php';

if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['id'];

$stmt = $conn->prepare("SELECT * FROM subs_table WHERE user_id = ? ORDER BY id DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$subscriptions = [];
while ($row = $result->fetch_assoc()) {
    $subscriptions[] = $row;
}

echo json_encode(['success' => true, 'data' => $subscriptions]);

$stmt->close();
$conn->close();
?>
