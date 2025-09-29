<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';
require 'dbconn.php'; // Database connection

// 1️⃣ Get today's date and next 3 days
$today = date('Y-m-d');
$next3days = date('Y-m-d', strtotime('+3 days'));

// 2️⃣ Fetch subscriptions with upcoming payment
$sql = "SELECT s.id, s.subscription_name, s.payment_due_date, s.price, u.email AS user_email
        FROM subscriptions s
        JOIN users u ON s.user_id = u.id
        WHERE s.status='active' AND s.payment_due_date BETWEEN ? AND ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $today, $next3days);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {

    $mail = new PHPMailer(true);

    try {
        // 3️⃣ SMTP settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'your-email@gmail.com';       // Replace with your Gmail
        $mail->Password = 'your-app-password';         // Replace with Gmail App password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // 4️⃣ Recipient & Sender
        $mail->setFrom('your-email@gmail.com', 'Subscription Reminder');
        $mail->addAddress($row['user_email']);

        // 5️⃣ Email content
        $mail->isHTML(true);
        $mail->Subject = 'Payment Reminder: ' . $row['subscription_name'];
        $mail->Body = "
            <h3>Upcoming Payment Reminder</h3>
            <p>Dear user,</p>
            <p>Your subscription <b>{$row['subscription_name']}</b> payment of <b>\${$row['price']}</b> is due on <b>{$row['payment_due_date']}</b>.</p>
            <p>Please make sure to pay on time to avoid interruption.</p>
            <p>Thank you!</p>
        ";

        // 6️⃣ Send email
        $mail->send();
        echo "Reminder sent to: {$row['user_email']}<br>";

    } catch (Exception $e) {
        echo "Mailer Error for {$row['user_email']}: {$mail->ErrorInfo}<br>";
    }
}
