<?php
session_start();
header('Content-Type: application/json');
require 'dbconn.php';

$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !isset($data['id'])) {
    echo json_encode(['success' => false, 'message' => 'No ID received']);
    exit;
}

if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['id'];
$subscription_id = $data['id'];

$stmt = $conn->prepare("DELETE FROM subs_table WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $subscription_id, $user_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Subscription deleted']);
} else {
    echo json_encode(['success' => false, 'message' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
