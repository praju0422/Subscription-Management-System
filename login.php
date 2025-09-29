<?php
echo "Request method: " . $_SERVER["REQUEST_METHOD"] . "<br>";
echo "<pre>";
print_r($_POST);
echo "</pre>";

session_start();

// Database connection
require 'dbconn.php';  // Make sure this file connects to your server1 DB properly

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['username']) && isset($_POST['password'])) {

        $username = trim($_POST['username']);
        $password = $_POST['password'];

        // Optional input length validation
        if (strlen($username) > 50 || strlen($password) > 255) {
            echo "Invalid input length.";
            exit;
        }

        // Prepare statement to select user by username
        $stmt = $conn->prepare("SELECT id, password FROM users_table WHERE username = ?");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        // Debug: Check how many users found
        // echo "Number of users found: " . $stmt->num_rows . "<br>";

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $hashed_password);
            $stmt->fetch();

            // Verify password
            if (password_verify($password, $hashed_password)) {
                // Password correct! Regenerate session ID for security
                session_regenerate_id(true);

                $_SESSION['id'] = $id;
                $_SESSION['username'] = $username;
                $_SESSION['password'] = $password;

                // Redirect to dashboard
                header("Location: dashboard.php");
                exit;
            } else {
                // Password incorrect - debug info
                echo "❌ Password is incorrect.<br>";
                // Uncomment for debugging ONLY (do NOT show hashes on live)
                // echo "Entered password: " . htmlspecialchars($password) . "<br>";
                // echo "Stored hash: " . htmlspecialchars($hashed_password) . "<br>";
            }
        } else {
            echo "❌ User not found.";
        }

        $stmt->close();

    } else {
        echo "Please fill in both username and password.";
    }
} else {
    echo "Invalid request method.";
}
?>
