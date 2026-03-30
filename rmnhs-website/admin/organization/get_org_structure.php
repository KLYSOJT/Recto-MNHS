<?php
require_once __DIR__ . '/../../connection/db_connection.php';

header('Content-Type: application/json');

$department = isset($_GET['dept_name']) ? trim($_GET['dept_name']) : (isset($_GET['department']) ? trim($_GET['department']) : '');

if (empty($department)) {
  http_response_code(400);
  echo json_encode(['success' => false, 'message' => 'Department not specified']);
  exit;
}

// Check if organizational_structure table exists
$tableCheckRes = $conn->query("SHOW TABLES LIKE 'organizational_structure'");
if (!$tableCheckRes || $tableCheckRes->num_rows === 0) {
  http_response_code(500);
  echo json_encode(['success' => false, 'message' => 'Database table not found']);
  exit;
}

// Determine if pdf column exists
$hasPdfColumn = false;
$colRes = $conn->query("SHOW COLUMNS FROM organizational_structure LIKE 'pdf'");
if ($colRes && $colRes->num_rows > 0) {
  $hasPdfColumn = true;
}

// Build select query to get image data
$sql = "SELECT id, department, image, mime";
if ($hasPdfColumn) {
  $sql .= ", pdf, pdf_mime";
}
$sql .= " FROM organizational_structure WHERE department = ? LIMIT 1";

$stmt = $conn->prepare($sql);
if (!$stmt) {
  http_response_code(500);
  echo json_encode(['success' => false, 'message' => 'Database query error: ' . $conn->error]);
  exit;
}

$stmt->bind_param('s', $department);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
  $row = $result->fetch_assoc();
  
  // Build image path
  $imagePath = null;
  if (!empty($row['image'])) {
    $imagePath = 'uploads/organizational-structure/' . htmlspecialchars($row['image']);
  }
  
  // Fetch all PDFs from organizational_structure_pdfs table
  $pdfs = [];
  $org_id = $row['id'] ?? null; // Need to get the ID from the query
  
  if ($org_id) {
    $pdfQuery = "SELECT id, pdf_filename FROM organizational_structure_pdfs WHERE org_structure_id = ? ORDER BY created_at DESC";
    $pdfStmt = $conn->prepare($pdfQuery);
    if ($pdfStmt) {
      $pdfStmt->bind_param('i', $org_id);
      $pdfStmt->execute();
      $pdfResult = $pdfStmt->get_result();
      
      while ($pdfRow = $pdfResult->fetch_assoc()) {
        $pdfs[] = [
          'id' => $pdfRow['id'],
          'filename' => htmlspecialchars($pdfRow['pdf_filename']),
          'path' => 'uploads/organizational-structure/' . htmlspecialchars($pdfRow['pdf_filename'])
        ];
      }
      $pdfStmt->close();
    }
  }
  
  $response = [
    'success' => true,
    'department' => htmlspecialchars($row['department']),
    'image' => $imagePath,
    'pdfs' => $pdfs
  ];

  echo json_encode($response);
} else {
  echo json_encode([
    'success' => false, 
    'message' => 'No data found for this department',
    'image' => null,
    'pdfs' => []
  ]);
}

$stmt->close();
$conn->close();
?>
