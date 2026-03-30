<?php
header('Content-Type: application/json');

// Include database connection
$basePath = dirname(__DIR__);
$dbConnectionPath = $basePath . '/connection/db_connection.php';

if (!file_exists($dbConnectionPath)) {
    echo json_encode(['success' => false, 'message' => 'Connection file not found']);
    exit;
}

require_once $dbConnectionPath;

// Check if connection exists
if (!isset($conn) || !$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$type = isset($_GET['type']) ? $_GET['type'] : 'announcement';

if ($type === 'announcement') {
    $sql = "SELECT id, image, announcement_posts, created_at FROM announcement ORDER BY created_at DESC";
} else {
    $sql = "SELECT id, image, news_posts, created_at FROM news ORDER BY created_at DESC";
}

$result = $conn->query($sql);

if (!$result) {
    echo json_encode(['success' => false, 'message' => 'Query error: ' . $conn->error]);
    exit;
}

$items = [];
while ($row = $result->fetch_assoc()) {
    // Build image path
    if ($row['image'] && $row['image'] !== '') {
        if ($type === 'announcement') {
            $imagePath = 'uploads/announcements/' . htmlspecialchars($row['image']);
        } else {
            $imagePath = 'uploads/news/' . htmlspecialchars($row['image']);
        }
    } else {
        $imagePath = 'https://via.placeholder.com/400';
    }
    
    if ($type === 'announcement') {
        $items[] = [
            'id' => $row['id'],
            'date' => date('F j, Y', strtotime($row['created_at'])),
            'description' => substr($row['announcement_posts'], 0, 100) . (strlen($row['announcement_posts']) > 100 ? '...' : ''),
            'fullDescription' => $row['announcement_posts'],
            'image' => $imagePath
        ];
    } else {
        $items[] = [
            'id' => $row['id'],
            'title' => substr($row['news_posts'], 0, 50) . (strlen($row['news_posts']) > 50 ? '...' : ''),
            'fullTitle' => $row['news_posts'],
            'description' => substr($row['news_posts'], 0, 80) . (strlen($row['news_posts']) > 80 ? '...' : ''),
            'fullDescription' => $row['news_posts'],
            'image' => $imagePath
        ];
    }
}

echo json_encode(['success' => true, 'data' => $items]);
$conn->close();
?>
