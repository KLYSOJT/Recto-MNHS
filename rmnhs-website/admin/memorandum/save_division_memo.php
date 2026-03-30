<?php
header('Content-Type: application/json');
error_reporting(0);
ini_set('display_errors', 0);

require_once '../../connection/db_connection.php';

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(400);
    die(json_encode(['success' => false, 'message' => 'Invalid request method']));
}

// Get form data
$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$date = isset($_POST['date']) ? trim($_POST['date']) : '';
$description = isset($_POST['description']) ? trim($_POST['description']) : '';
$action = isset($_POST['action']) ? trim($_POST['action']) : 'add';
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

// Handle delete action first (before validation)
if ($action === 'delete') {
    if ($id <= 0) {
        die(json_encode(['success' => false, 'message' => 'Invalid item ID']));
    }

    // Get file name before deleting
    $select_query = "SELECT file FROM division_memorandum WHERE id = ?";
    $stmt = $conn->prepare($select_query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row && $row['file']) {
        $file_path = '../../uploads/memorandum/' . $row['file'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
    $stmt->close();

    // Delete from database
    $delete_query = "DELETE FROM division_memorandum WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Memorandum deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting memorandum']);
    }
    $stmt->close();
    exit();
}

// Validate required fields for add/update actions
if (empty($title) || empty($date)) {
    die(json_encode(['success' => false, 'message' => 'Title and date are required']));
}

// Initialize file name
$file_name = null;

// Handle file upload
if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    $file_tmp = $_FILES['file']['tmp_name'];
    $file_original = $_FILES['file']['name'];
    $file_ext = strtolower(pathinfo($file_original, PATHINFO_EXTENSION));
    
    // Validate file extension
    $allowed_ext = ['pdf'];
    if (!in_array($file_ext, $allowed_ext)) {
        die(json_encode(['success' => false, 'message' => 'Only PDF files are allowed']));
    }
    
    // Create uploads directory if it doesn't exist
    $upload_dir = '../../uploads/memorandum/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Generate unique filename
    $file_name = 'memo_' . time() . '_' . uniqid() . '.' . $file_ext;
    $file_path = $upload_dir . $file_name;
    
    if (!move_uploaded_file($file_tmp, $file_path)) {
        die(json_encode(['success' => false, 'message' => 'Failed to upload file']));
    }
}

// Insert or Update database
if ($action === 'update' && $id > 0) {
    // Update existing memorandum
    $query = "UPDATE division_memorandum SET title = ?, date = ?, description = ?";
    $params = [$title, $date, $description];
    $types = "sss";
    
    if ($file_name) {
        // Delete old file
        $select_query = "SELECT file FROM division_memorandum WHERE id = ?";
        $stmt = $conn->prepare($select_query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        if ($row && $row['file']) {
            $old_file_path = '../../uploads/memorandum/' . $row['file'];
            if (file_exists($old_file_path)) {
                unlink($old_file_path);
            }
        }
        $stmt->close();
        
        $query .= ", file = ?";
        $params[] = $file_name;
        $types .= "s";
    }
    
    $query .= " WHERE id = ?";
    $params[] = $id;
    $types .= "i";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Memorandum updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating memorandum']);
    }
    $stmt->close();
} else {
    // Insert new memorandum
    $query = "INSERT INTO division_memorandum (title, date, description, file) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $title, $date, $description, $file_name);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Memorandum added successfully', 'id' => $stmt->insert_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error adding memorandum']);
    }
    $stmt->close();
}

$conn->close();
?>
