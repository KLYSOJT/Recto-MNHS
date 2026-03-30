<?php
header('Content-Type: application/json');
error_reporting(0);
ini_set('display_errors', 0);

require_once '../../connection/db_connection.php';

// Get all memorandums from database
$query = "SELECT id, title, date, description, file, created_at FROM division_memorandum ORDER BY date DESC";
$result = $conn->query($query);

if (!$result) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    $conn->close();
    exit();
}

$memorandums = [];
while ($row = $result->fetch_assoc()) {
    $memorandums[] = $row;
}

$conn->close();
echo json_encode(['success' => true, 'data' => $memorandums]);
?>
