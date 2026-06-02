<?php
// Connect to the DB
$conn = new mysqli('127.0.0.1', 'u425263752_SQbpZ', 'o!a4&X(ePe', 'u425263752_txx1M');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT ID, user_login, user_email, user_registered FROM wp_users");
$users = [];
while($row = $result->fetch_assoc()) {
    $users[] = $row;
}

header('Content-Type: application/json');
echo json_encode($users, JSON_PRETTY_PRINT);
