<?php
session_start();
require 'dbconn.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.html");
    exit;
}
$user_id = $_SESSION['id'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Subscription Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
        .navbar { background: #004466; color: white; padding: 15px; display: flex; justify-content: space-between; align-items: center; }
        .navbar h2 { margin: 0; font-size: 20px; }
        .nav-links a { color: white; margin-left: 15px; text-decoration: none; font-weight: bold; padding: 5px 10px; border-radius: 5px; }
        .nav-links a:hover { background: #0077aa; }
        h2, h3 { text-align: center; margin-top: 20px; }
        table { width: 90%; margin: 20px auto; border-collapse: collapse; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: center; }
        th { background-color: #004466; color: white; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        form { width: 90%; margin: 20px auto; }
        input, select { padding: 8px; margin: 6px 0; width: 100%; box-sizing: border-box; }
        button { padding: 10px 20px; margin-top: 10px; cursor: pointer; }
        .search-filter { display: flex; gap: 10px; justify-content: center; margin: 20px auto; width: 90%; }
        .search-filter input, .search-filter select { width: auto; flex: 1; }
    </style>
</head>
<body>

<!-- Navigation Bar -->
<div class="navbar">
    <h2>📊 Subscription Dashboard</h2>
    <div class="nav-links">
        <a href="settings.php">⚙ Settings</a>
        <a href="logout.php">🚪 Logout</a>
    </div>
</div>

<h2>Your Subscriptions</h2>

<!-- Search + Filter -->
<div class="search-filter">
    <input type="text" id="searchInput" placeholder="Search by name">
    <select id="statusFilter">
        <option value="">-- All Status --</option>
        <option value="active">Active</option>
        <option value="expired">Expired</option>
    </select>
    <button onclick="applyFilters()">Search</button>
    <button onclick="resetFilters()">Reset</button>
</div>

<!-- Add Subscription Form -->
<h3>Add New Subscription</h3>
<form id="addSubscriptionForm" method="POST" action="add_subscription.php">
    <input type="text" name="subscription_name" placeholder="Service Name (e.g., Netflix)" required />
    <input type="date" name="start_date" required />
    <input type="date" name="payment_due_date" required />
    <input type="number" step="0.01" name="price" placeholder="Subscription Amount" required />
    <select name="status" required>
        <option value="active">Active</option>
        <option value="expired">Expired</option>
    </select>
    <button type="submit">Add Subscription</button>
</form>

<!-- Subscriptions Table -->
<table id="subsTable">
    <thead>
        <tr>
            <th>Subscription Name</th>
            <th>Start Date</th>
            <th>Next Payment Due</th>
            <th>Price ($)</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

<script>
// Escape HTML
function escapeHtml(text) {
    var div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Fetch subscriptions
function fetchSubscriptions(search = "", status = "") {
    fetch(`fetch_subscriptions.php?search=${encodeURIComponent(search)}&status=${encodeURIComponent(status)}`)
    .then(res => res.json())
    .then(data => {
        const tbody = document.querySelector('#subsTable tbody');
        tbody.innerHTML = '';
        if (data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6">No subscriptions found.</td></tr>';
            return;
        }
        data.forEach(sub => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${escapeHtml(sub.subscription_name)}</td>
                <td>${escapeHtml(sub.start_date)}</td>
                <td>${escapeHtml(sub.payment_due_date)}</td>
                <td>${parseFloat(sub.price).toFixed(2)}</td>
                <td>${escapeHtml(sub.status.charAt(0).toUpperCase() + sub.status.slice(1))}</td>
                <td>
                    <button onclick="editSubscription(${sub.id})">Edit</button>
                    <button onclick="deleteSubscription(${sub.id})">Delete</button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }).catch(err => console.error("Fetch error:", err));
}

// Add subscription
document.getElementById('addSubscriptionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch('add_subscription.php', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(response => {
        alert(response.message);
        if (response.success) {
            this.reset();
            fetchSubscriptions();
        }
    }).catch(err => console.error("Add subscription error:", err));
});

// Delete subscription
function deleteSubscription(id) {
    if (!confirm('Are you sure you want to delete this subscription?')) return;
    fetch('delete_subscription.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'id=' + encodeURIComponent(id)
    })
    .then(res => res.json())
    .then(response => {
        alert(response.message);
        if (response.success) fetchSubscriptions();
    });
}

// Edit subscription
function editSubscription(id) {
    fetch('get_subscription.php?id=' + id)
    .then(res => res.json())
    .then(sub => {
        if (!sub.success) { alert(sub.message); return; }

        const newName = prompt('Subscription Name:', sub.data.subscription_name);
        if (newName === null) return;
        const newStartDate = prompt('Start Date (YYYY-MM-DD):', sub.data.start_date);
        if (newStartDate === null) return;
        const newPaymentDate = prompt('Payment Due Date (YYYY-MM-DD):', sub.data.payment_due_date);
        if (newPaymentDate === null) return;
        const newPrice = prompt('Price:', sub.data.price);
        if (newPrice === null) return;
        const newStatus = prompt('Status (active/expired):', sub.data.status);
        if (newStatus === null) return;

        const formData = new FormData();
        formData.append('id', id);
        formData.append('subscription_name', newName);
        formData.append('start_date', newStartDate);
        formData.append('payment_due_date', newPaymentDate);
        formData.append('price', newPrice);
        formData.append('status', newStatus);

        fetch('edit_subscription.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(response => {
            alert(response.message);
            if (response.success) fetchSubscriptions();
        });
    });
}

// Filters
function applyFilters() {
    const search = document.getElementById('searchInput').value;
    const status = document.getElementById('statusFilter').value;
    fetchSubscriptions(search, status);
}
function resetFilters() {
    document.getElementById('searchInput').value = "";
    document.getElementById('statusFilter').value = "";
    fetchSubscriptions();
}

// Initial load
fetchSubscriptions();
</script>
</body>
</html>
