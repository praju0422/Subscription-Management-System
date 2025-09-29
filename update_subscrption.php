<?php
session_start();
header('Content-Type: application/json');
require 'dbconn.php';

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    echo json_encode(['success' => false, 'message' => 'No data received']);
    exit;
}

if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['id'];

$subscription_id   = $data['id'];
$subscription_name = $data['subscription_name'];
$start_date        = $data['start_date'];
$payment_due_date  = $data['payment_due_date'];
$price             = $data['price'];
$status            = $data['status'];

$stmt = $conn->prepare("UPDATE subs_table 
                        SET subscription_name = ?, start_date = ?, payment_due_date = ?, price = ?, status = ? 
                        WHERE id = ? AND user_id = ?");
$stmt->bind_param("sssdsii", $subscription_name, $start_date, $payment_due_date, $price, $status, $subscription_id, $user_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Subscription updated']);
} else {
    echo json_encode(['success' => false, 'message' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
