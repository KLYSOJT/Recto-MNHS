<?php
require_once '../../connection/db_connection.php';

// Fetch all research records
$query = "SELECT id, title, grade, department, year, category, image, file FROM research ORDER BY created_at DESC";
$result = $conn->query($query);

if (!$result) {
    http_response_code(500);
    die(json_encode(['error' => 'Database error: ' . $conn->error]));
}

$research = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $research[] = [
            'id' => intval($row['id']),
            'title' => $row['title'],
            'grade' => $row['grade'],
            'department' => $row['department'],
            'year' => $row['year'],
            'category' => $row['category'],
            'image' => $row['image'] ?: null,
            'file' => $row['file'] ?: null
        ];
    }
}

$result->free();
$conn->close();

header('Content-Type: application/json');
echo json_encode($research);
?>
