<?php
header('Content-Type: application/json');

require_once '../connection/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$type = isset($_POST['type']) ? trim($_POST['type']) : '';
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$content = isset($_POST['content']) ? trim($_POST['content']) : '';

if (!$type || !$id || !$content) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

// capture description if provided
$description = isset($_POST['description']) ? trim($_POST['description']) : null;

try {
    if ($type === 'announcement') {
        $table = 'announcement';
        $contentColumn = 'announcement_posts';
        $uploadDir = '../uploads/announcements/';
    } elseif ($type === 'news') {
        $table = 'news';
        $contentColumn = 'news_posts';
        $uploadDir = '../uploads/news/';
    } elseif ($type === 'video') {
        // featured video entries
        $table = 'featured_videos';
        $contentColumn = 'title';
        $uploadDir = '../uploads/featured_videos/';
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid type']);
        exit;
    }

    // Get current file reference depending on type
    if ($type === 'video') {
        // featured_videos stores uploaded file path in `filename` (or url separately)
        $getCurrentSql = "SELECT filename FROM $table WHERE id = " . intval($id);
    } else {
        $getCurrentSql = "SELECT image FROM $table WHERE id = " . intval($id);
    }

    $result = $conn->query($getCurrentSql);
    if (!$result) {
        echo json_encode(['success' => false, 'message' => 'Query error: ' . $conn->error]);
        exit;
    }

    $currentRow = $result->fetch_assoc();
    if (!$currentRow) {
        echo json_encode(['success' => false, 'message' => 'Item not found']);
        exit;
    }

    // determine column name for current file depending on type
    if ($type === 'video') {
        // if the video was added via URL instead of upload, the filename column may be empty
        $newImage = $currentRow['filename'];
    } else {
        $newImage = $currentRow['image'];
    }

    // Handle new file upload (image or video)
    if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        $file = $_FILES['image'];
        
        // Validate file based on type
        if ($type === 'video') {
            $validTypes = ['video/mp4', 'video/webm', 'video/ogg', 'video/avi', 'video/mkv'];
        } else {
            $validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        }
        if (!in_array($file['type'], $validTypes)) {
            echo json_encode(['success' => false, 'message' => 'Invalid file type']);
            exit;
        }

        // Delete old file if exists
        if (!empty($newImage) && file_exists($uploadDir . $newImage)) {
            unlink($uploadDir . $newImage);
        }

        // Generate unique filename
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $prefix = ($type === 'video') ? 'vid_' : 'img_';
        $newImage = $prefix . uniqid() . '.' . $ext;
        
        // Create upload directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $uploadDir . $newImage)) {
            echo json_encode(['success' => false, 'message' => 'Failed to upload file']);
            exit;
        }
    }

    // Update database
    $escapedContent = $conn->real_escape_string($content);
    $escapedImage = $conn->real_escape_string($newImage);
    if ($type === 'video') {
        $escapedDescription = $conn->real_escape_string($description);
        $updateSql = "UPDATE $table SET $contentColumn = '$escapedContent', description = '$escapedDescription', filename = '$escapedImage' WHERE id = " . intval($id);
    } else {
        $updateSql = "UPDATE $table SET $contentColumn = '$escapedContent', image = '$escapedImage' WHERE id = " . intval($id);
    }
    
    if ($conn->query($updateSql) === TRUE) {
        $responseData = [
            'id' => $id,
            'content' => $content,
            'image' => $newImage
        ];
        if ($type === 'video') {
            $responseData['description'] = $description;
        }
        echo json_encode([
            'success' => true,
            'message' => ucfirst($type) . ' updated successfully',
            'data' => $responseData
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
?>

