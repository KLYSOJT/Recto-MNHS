<?php
header('Content-Type: application/json');
require_once '../connection/db_connection.php';

// Function to convert video URLs to embed URLs
function convertToEmbedUrl($url) {
    // YouTube - youtu.be format
    if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]{11})/', $url, $matches)) {
        return 'https://www.youtube.com/embed/' . $matches[1];
    }
    
    // YouTube - youtube.com format
    if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]{11})/', $url, $matches)) {
        return 'https://www.youtube.com/embed/' . $matches[1];
    }
    
    // YouTube - youtube.com/embed format (already embed)
    if (strpos($url, 'youtube.com/embed/') !== false) {
        return $url;
    }
    
    // Vimeo
    if (preg_match('/vimeo\.com\/(\d+)/', $url, $matches)) {
        return 'https://player.vimeo.com/video/' . $matches[1];
    }
    
    // Vimeo - already embed format
    if (strpos($url, 'player.vimeo.com/video/') !== false) {
        return $url;
    }
    
    // Google Drive - drive.google.com/file/d/FILE_ID/view format
    if (preg_match('/drive\.google\.com\/file\/d\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
        return 'https://drive.google.com/file/d/' . $matches[1] . '/preview';
    }
    
    // Google Drive - already embed format (preview)
    if (strpos($url, 'drive.google.com/file/d/') !== false && strpos($url, '/preview') !== false) {
        return $url;
    }
    
    // Return original URL if it's already embed-friendly or not recognized
    return $url;
}

// ensure table exists (supports either uploaded filename or external URL)
$conn->query("CREATE TABLE IF NOT EXISTS `featured_videos` (
    `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `title` varchar(255) NOT NULL,
    `description` text,
    `filename` varchar(255) DEFAULT NULL,
    `url` varchar(500) DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $video_url = isset($_POST['video_url']) ? trim($_POST['video_url']) : '';
    
    if (empty($title)) {
        echo json_encode(['success' => false, 'message' => 'Video title is required']);
        exit;
    }

    // If a URL is provided, accept it instead of a file upload
    if (!empty($video_url)) {
        if (!filter_var($video_url, FILTER_VALIDATE_URL)) {
            echo json_encode(['success' => false, 'message' => 'Invalid video URL']);
            exit;
        }

        // Convert URL to embed format if needed
        $embed_url = convertToEmbedUrl($video_url);

        $sql = "INSERT INTO featured_videos (title, description, url) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
            exit;
        }
        $stmt->bind_param("sss", $title, $description, $embed_url);
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Video URL saved successfully',
                'data' => [
                    'title' => $title,
                    'description' => $description,
                    'url' => $embed_url
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
        }
        $stmt->close();
        $conn->close();
        exit;
    }

    // Otherwise, expect a file upload (backwards compatibility)
    if (!isset($_FILES['video']) || $_FILES['video']['size'] === 0) {
        echo json_encode(['success' => false, 'message' => 'No video file uploaded and no URL provided']);
        exit;
    }

    $file = $_FILES['video'];
    $target_dir = "../uploads/featured_videos/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    $file_name = basename($file['name']);
    $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
    $unique_name = time() . '_' . uniqid() . '.' . $file_ext;
    $target_file = $target_dir . $unique_name;

    // Validate file type
    $allowed_types = array('mp4', 'webm', 'ogg', 'avi', 'mkv');
    if (!in_array(strtolower($file_ext), $allowed_types)) {
        echo json_encode(['success' => false, 'message' => 'Invalid file type. Only video files are allowed']);
        exit;
    }

    // Validate file size (max 100MB)
    if ($file['size'] > 100 * 1024 * 1024) {
        echo json_encode(['success' => false, 'message' => 'File is too large. Maximum size is 100MB']);
        exit;
    }

    if (!move_uploaded_file($file['tmp_name'], $target_file)) {
        echo json_encode(['success' => false, 'message' => 'Failed to upload video']);
        exit;
    }

    // insert record for uploaded file
    $sql = "INSERT INTO featured_videos (title, description, filename) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
        exit;
    }
    $stmt->bind_param("sss", $title, $description, $unique_name);
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Video uploaded successfully',
            'data' => [
                'title' => $title,
                'filename' => $unique_name
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>