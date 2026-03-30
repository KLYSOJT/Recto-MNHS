<?php
require_once '../../connection/db_connection.php';

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(400);
    die(json_encode(['success' => false, 'message' => 'Invalid request method']));
}

// Get form data
$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$grade = isset($_POST['grade']) ? trim($_POST['grade']) : '';
$department = isset($_POST['department']) ? trim($_POST['department']) : '';
$year = isset($_POST['year']) ? trim($_POST['year']) : '';
$category = isset($_POST['category']) ? trim($_POST['category']) : '';
$is_update = isset($_POST['is_update']) ? $_POST['is_update'] : '0';
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

// Validate required fields
if (empty($title) || empty($grade) || empty($department) || empty($year) || empty($category)) {
    die(json_encode(['success' => false, 'message' => 'All fields are required']));
}

// Initialize file names
$image_file = null;
$pdf_file = null;

// Handle image upload
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $image_tmp = $_FILES['image']['tmp_name'];
    $image_name = $_FILES['image']['name'];
    $image_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
    
    // Validate image extension
    $allowed_img_ext = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($image_ext, $allowed_img_ext)) {
        die(json_encode(['success' => false, 'message' => 'Invalid image format. Allowed: JPG, PNG, GIF']));
    }
    
    // Create uploads directory if it doesn't exist
    $upload_dir = '../../uploads/research_images/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Generate unique filename
    $image_file = 'research_' . time() . '_' . uniqid() . '.' . $image_ext;
    $image_path = $upload_dir . $image_file;
    
    if (!move_uploaded_file($image_tmp, $image_path)) {
        die(json_encode(['success' => false, 'message' => 'Failed to upload image']));
    }
}

// Handle PDF upload
if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    $pdf_tmp = $_FILES['file']['tmp_name'];
    $pdf_name = $_FILES['file']['name'];
    $pdf_ext = strtolower(pathinfo($pdf_name, PATHINFO_EXTENSION));
    
    // Validate PDF extension
    if ($pdf_ext !== 'pdf') {
        // Clean up uploaded image if it exists
        if ($image_file) {
            unlink('../../uploads/research_images/' . $image_file);
        }
        die(json_encode(['success' => false, 'message' => 'Only PDF files are allowed']));
    }
    
    // Create uploads directory if it doesn't exist
    $upload_dir = '../../uploads/research_pdfs/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Generate unique filename
    $pdf_file = 'research_' . time() . '_' . uniqid() . '.pdf';
    $pdf_path = $upload_dir . $pdf_file;
    
    if (!move_uploaded_file($pdf_tmp, $pdf_path)) {
        // Clean up uploaded image if it exists
        if ($image_file) {
            unlink('../../uploads/research_images/' . $image_file);
        }
        die(json_encode(['success' => false, 'message' => 'Failed to upload PDF file']));
    }
}

// Insert or Update database
if ($is_update && $id > 0) {
    // Update existing research
    $query = "UPDATE research SET title = ?, grade = ?, department = ?, year = ?, category = ?";
    $params = [$title, $grade, $department, $year, $category];
    $types = "sssss";
    
    if ($image_file) {
        $query .= ", image = ?";
        $params[] = $image_file;
        $types .= "s";
    }
    
    if ($pdf_file) {
        $query .= ", file = ?";
        $params[] = $pdf_file;
        $types .= "s";
    }
    
    $query .= " WHERE id = ?";
    $params[] = $id;
    $types .= "i";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die(json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]));
    }
    
    $stmt->bind_param($types, ...$params);
    
    if (!$stmt->execute()) {
        die(json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]));
    }
    
    $stmt->close();
    die(json_encode(['success' => true, 'message' => 'Research updated successfully']));
} else {
    // Insert new research
    $query = "INSERT INTO research (title, grade, department, year, category, image, file) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        die(json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]));
    }
    
    $stmt->bind_param("sssssss", $title, $grade, $department, $year, $category, $image_file, $pdf_file);
    
    if (!$stmt->execute()) {
        die(json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]));
    }
    
    $stmt->close();
    die(json_encode(['success' => true, 'message' => 'Research created successfully']));
}

$conn->close();
?>
