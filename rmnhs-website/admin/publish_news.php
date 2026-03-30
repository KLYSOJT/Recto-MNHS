<?php
header('Content-Type: application/json');
require_once '../connection/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $news_content = isset($_POST['news']) ? trim($_POST['news']) : '';
    $image_name = '';

    // Validate content
    if (empty($news_content)) {
        echo json_encode(['success' => false, 'message' => 'News content is required']);
        exit;
    }

    // Handle image upload if provided
    if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        $target_dir = "../uploads/news/";
        
        // Create directory if it doesn't exist
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        $file_name = basename($_FILES['image']['name']);
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $unique_name = time() . '_' . uniqid() . '.' . $file_ext;
        $target_file = $target_dir . $unique_name;

        // Validate file type
        $allowed_types = array('jpg', 'jpeg', 'png', 'gif', 'webp');
        if (!in_array(strtolower($file_ext), $allowed_types)) {
            echo json_encode(['success' => false, 'message' => 'Invalid file type. Only image files are allowed']);
            exit;
        }

        // Validate file size (max 5MB)
        if ($_FILES['image']['size'] > 5000000) {
            echo json_encode(['success' => false, 'message' => 'File is too large. Maximum size is 5MB']);
            exit;
        }

        // Move uploaded file
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
            exit;
        }

        $image_name = $unique_name;
    }

    // Insert into database
    $sql = "INSERT INTO news (image, news_posts) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
        exit;
    }

    $stmt->bind_param("ss", $image_name, $news_content);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'News published successfully',
            'data' => [
                'image' => $image_name,
                'content' => $news_content
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
