<?php
require_once __DIR__ . '/../../connection/db_connection.php';

header('Content-Type: application/json');

// Get specific organization or all organizations
$orgName = isset($_GET['org_name']) ? trim($_GET['org_name']) : '';

// Check if recognized_organization table exists
$tableCheckRes = $conn->query("SHOW TABLES LIKE 'recognized_organization'");
if (!$tableCheckRes || $tableCheckRes->num_rows === 0) {
  http_response_code(500);
  echo json_encode(['success' => false, 'message' => 'Database table not found']);
  exit;
}

if (!empty($orgName)) {
  // Fetch specific organization
  $sql = "SELECT id, org_name, date_established, adviser_name, image, pdf FROM recognized_organization WHERE org_name = ? LIMIT 1";
  $stmt = $conn->prepare($sql);
  
  if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database query error']);
    exit;
  }
  
  $stmt->bind_param('s', $orgName);
  $stmt->execute();
  $result = $stmt->get_result();
  
  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    
    $imagePath = null;
    if (!empty($row['image'])) {
      $imagePath = 'uploads/recognized-organization/' . htmlspecialchars($row['image']);
    }
    
    $pdfPath = null;
    if (!empty($row['pdf'])) {
      $pdfPath = 'uploads/recognized-organization/' . htmlspecialchars($row['pdf']);
    }
    
    $response = [
      'success' => true,
      'id' => $row['id'],
      'org_name' => htmlspecialchars($row['org_name']),
      'date_established' => $row['date_established'],
      'adviser_name' => $row['adviser_name'] ? htmlspecialchars($row['adviser_name']) : null,
      'imagePath' => $imagePath,
      'pdfPath' => $pdfPath
    ];
    
    echo json_encode($response);
  } else {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Organization not found']);
  }
  
  $stmt->close();
} else {
  // Fetch all recognized organizations
  $sql = "SELECT id, org_name, date_established, adviser_name, image, pdf FROM recognized_organization ORDER BY org_name ASC";
  $result = $conn->query($sql);
  
  if (!$result) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database query error']);
    exit;
  }
  
  $organizations = [];
  while ($row = $result->fetch_assoc()) {
    $imagePath = null;
    if (!empty($row['image'])) {
      $imagePath = 'uploads/recognized-organization/' . htmlspecialchars($row['image']);
    }
    
    $pdfPath = null;
    if (!empty($row['pdf'])) {
      $pdfPath = 'uploads/recognized-organization/' . htmlspecialchars($row['pdf']);
    }
    
    $organizations[] = [
      'id' => $row['id'],
      'org_name' => htmlspecialchars($row['org_name']),
      'date_established' => $row['date_established'],
      'adviser_name' => $row['adviser_name'] ? htmlspecialchars($row['adviser_name']) : null,
      'imagePath' => $imagePath,
      'pdfPath' => $pdfPath
    ];
  }
  
  echo json_encode([
    'success' => true,
    'count' => count($organizations),
    'data' => $organizations
  ]);
}

$conn->close();
?>
