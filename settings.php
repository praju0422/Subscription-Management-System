<?php
session_start();
require 'dbconn.php';

// User login check
if (!isset($_SESSION['id'])) {
    header("Location: login.html");
    exit;
}

$user_id = $_SESSION['id'];

// Fetch user details from DB
$sql = "SELECT name, email FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$user = $result->fetch_assoc(); 
?>
<!DOCTYPE html>
<html>
<head>
    <title>Settings</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f9; margin: 0; padding: 0; }
        .container { width: 400px; margin: 50px auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0px 4px 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #004466; }
        label { font-weight: bold; display: block; margin-top: 10px; }
        input { width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 5px; }
        button { background: #004466; color: white; padding: 10px; border: none; border-radius: 5px; margin-top: 15px; width: 100%; cursor: pointer; }
        button:hover { background: #0077aa; }
    </style>
</head>
<body>
    <div class="container">
        <h2>⚙ Settings</h2>
        <form action="update_settings.php" method="POST">
            <label>Name</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required>

            <label>Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>

            <label>New Password (leave blank to keep old)</label>
            <input type="password" name="password">

            <button type="submit">Update</button>
        </form>
        <form action="logout.php" method="POST">
            <button type="submit" style="background: red;">🚪 Logout</button>
        </form>
    </div>
</body>
</html>
