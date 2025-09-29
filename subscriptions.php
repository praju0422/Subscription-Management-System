<?php
session_start();
require 'dbconn.php';

// Default values
$search = "";
$status = "";
$whereClauses = [];

// If search is applied
if (!empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $whereClauses[] = "name LIKE '%$search%'";
}

// If filter is applied
if (!empty($_GET['status'])) {
    $status = mysqli_real_escape_string($conn, $_GET['status']);
    if ($status === "Active" || $status === "Expired") {
        $whereClauses[] = "status = '$status'";
    }
}

// Build final WHERE clause
$whereSQL = "";
if (count($whereClauses) > 0) {
    $whereSQL = "WHERE " . implode(" AND ", $whereClauses);
}

// Fetch subscriptions
$sql = "SELECT * FROM subscriptions $whereSQL ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Subscriptions</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .search-filter { margin-bottom: 20px; }
        input[type=text], select { padding: 6px; margin-right: 10px; }
        button { padding: 6px 12px; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table, th, td { border: 1px solid #ccc; }
        th, td { padding: 10px; text-align: left; }
        h2 { margin-bottom: 15px; }
    </style>
</head>
<body>
    <h2>Dashboard - Subscription List</h2>

    <!-- Search & Filter Form -->
    <form method="GET" class="search-filter">
        <input type="text" name="search" placeholder="Search by name" value="<?php echo htmlspecialchars($search); ?>">
        <select name="status">
            <option value="">-- All Status --</option>
            <option value="Active" <?php if ($status == "Active") echo "selected"; ?>>Active</option>
            <option value="Expired" <?php if ($status == "Expired") echo "selected"; ?>>Expired</option>
        </select>
        <button type="submit">Search</button>
        <a href="dashboard.php"><button type="button">Reset</button></a>
    </form>

    <!-- Subscriptions Table -->
    <table>
        <tr>
            <th>ID</th>
            <th>Subscription Name</th>
            <th>Status</th>
            <th>Start Date</th>
            <th>End Date</th>
        </tr>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo $row['status']; ?></td>
                    <td><?php echo $row['start_date']; ?></td>
                    <td><?php echo $row['end_date']; ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="5">No subscriptions found</td></tr>
        <?php endif; ?>
    </table>
</body>
</html>
