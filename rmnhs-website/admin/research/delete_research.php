<?php
require_once '../../connection/db_connection.php';

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(400);
    die(json_encode(['success' => false, 'message' => 'Invalid request method']));
}

// Get research ID
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

if ($id <= 0) {
    die(json_encode(['success' => false, 'message' => 'Invalid research ID']));
}

// Get research data to delete files
$query = "SELECT image, file FROM research WHERE id = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die(json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]));
}

$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    die(json_encode(['success' => false, 'message' => 'Research not found']));
}

$row = $result->fetch_assoc();
$stmt->close();

// Delete files from server
if ($row['image']) {
    $image_path = '../../uploads/research_images/' . $row['image'];
    if (file_exists($image_path)) {
        unlink($image_path);
    }
}

if ($row['file']) {
    $file_path = '../../uploads/research_pdfs/' . $row['file'];
    if (file_exists($file_path)) {
        unlink($file_path);
    }
}

// Delete from database
$delete_query = "DELETE FROM research WHERE id = ?";
$delete_stmt = $conn->prepare($delete_query);

if (!$delete_stmt) {
    die(json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]));
}

$delete_stmt->bind_param("i", $id);

if (!$delete_stmt->execute()) {
    die(json_encode(['success' => false, 'message' => 'Database error: ' . $delete_stmt->error]));
}

$delete_stmt->close();
$conn->close();

die(json_encode(['success' => true, 'message' => 'Research deleted successfully']));
?>
