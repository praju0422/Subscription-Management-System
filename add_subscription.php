<?php
session_start();
header('Content-Type: application/json');
require 'dbconn.php';

$user_id = $_SESSION['id'] ?? 1; // fallback

$subscription_name = $_POST['subscription_name'] ?? null;
$start_date = $_POST['start_date'] ?? null;
$payment_due_date = $_POST['payment_due_date'] ?? null;
$price = $_POST['price'] ?? null;
$status = $_POST['status'] ?? null;

if (!$subscription_name || !$start_date || !$payment_due_date || !$price || !$status) {
    echo json_encode(['success'=>false,'message'=>'All fields are required']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO subs_table (user_id, subscription_name, start_date, payment_due_date, price, status) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isssds", $user_id, $subscription_name, $start_date, $payment_due_date, $price, $status);

if ($stmt->execute()) {
    echo json_encode(['success'=>true,'message'=>'Subscription added']);
} else {
    echo json_encode(['success'=>false,'message'=>$stmt->error]);
}

$stmt->close();
$conn->close();
?>
