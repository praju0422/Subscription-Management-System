<?php
session_start();
require 'dbconn.php';

$user_id = $_SESSION['id'] ?? 1;

$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? '';

$sql = "SELECT * FROM subs_table WHERE user_id=? ";
$params = [$user_id];
$types = "i";

if ($search !== '') {
    $sql .= " AND subscription_name LIKE ? ";
    $params[] = "%$search%";
    $types .= "s";
}
if ($status !== '') {
    $sql .= " AND status=? ";
    $params[] = $status;
    $types .= "s";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
$stmt->close();
$conn->close();
?>