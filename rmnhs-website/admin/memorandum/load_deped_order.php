<?php
header('Content-Type: application/json');

require_once '../../connection/db_connection.php';

// Check database connection
if (!$conn) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

// Create table if it doesn't exist
$create_table = "CREATE TABLE IF NOT EXISTS deped_order (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    date DATE NOT NULL,
    description LONGTEXT,
    file VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (!$conn->query($create_table)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error creating table: ' . $conn->error]);
    $conn->close();
    exit();
}

// Get all orders from database
$query = "SELECT id, title, date, description, file, created_at FROM deped_order ORDER BY date DESC";
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