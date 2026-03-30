<?php
header('Content-Type: application/json');

require_once '../connection/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$type = isset($_POST['type']) ? trim($_POST['type']) : '';
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

if (!$type || !$id) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

if ($type === 'announcement') {
    $table = 'announcement';
    $getImageSql = "SELECT image FROM announcement WHERE id = $id";
} elseif ($type === 'news') {
    $table = 'news';
    $getImageSql = "SELECT image FROM news WHERE id = $id";
} elseif ($type === 'video') {
    $table = 'featured_videos';
    $getImageSql = "SELECT filename FROM featured_videos WHERE id = $id";
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid type']);
    exit;
}

try {
    // Get image filename to delete from server
    $imageResult = $conn->query($getImageSql);
    if ($imageResult && $row = $imageResult->fetch_assoc()) {
        if (!empty($row['image']) || !empty($row['filename'])) {
            $filename = $row['image'] ?? $row['filename'];
            $folder = ($type === 'video') ? 'featured_videos' : $type . 's';
            $imagePath = '../uploads/' . $folder . '/' . $filename;
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
    }

    // Delete from database
    $deleteSql = "DELETE FROM $table WHERE id = $id";
    
    if ($conn->query($deleteSql) === TRUE) {
        echo json_encode(['success' => true, 'message' => ucfirst($type) . ' deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
?>
