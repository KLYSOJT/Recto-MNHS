<?php
require_once __DIR__ . '/../../connection/db_connection.php';

$uploadDir = __DIR__ . '/../../uploads/organizational-structure/';
if (!is_dir($uploadDir)) {
  mkdir($uploadDir, 0755, true);
}

// Create organizational_structure_pdfs table if it doesn't exist
$createTableSQL = "CREATE TABLE IF NOT EXISTS `organizational_structure_pdfs` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `org_structure_id` int(11) NOT NULL,
  `pdf_filename` varchar(255) NOT NULL,
  `pdf_mime` varchar(100) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`org_structure_id`) REFERENCES `organizational_structure`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
$conn->query($createTableSQL);

// Handle delete PDF
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_pdf') {
  $pdf_id = intval($_POST['pdf_id']);
  
  // Get PDF filename
  $deleteSQL = "SELECT pdf_filename FROM organizational_structure_pdfs WHERE id = ?";
  $deleteStmt = $conn->prepare($deleteSQL);
  $deleteStmt->bind_param('i', $pdf_id);
  $deleteStmt->execute();
  $result = $deleteStmt->get_result();
  $pdfRow = $result->fetch_assoc();
  $deleteStmt->close();
  
  if ($pdfRow) {
    $pdfPath = $uploadDir . $pdfRow['pdf_filename'];
    if (file_exists($pdfPath)) {
      unlink($pdfPath);
    }
    
    // Delete from database
    $removeSQL = "DELETE FROM organizational_structure_pdfs WHERE id = ?";
    $removeStmt = $conn->prepare($removeSQL);
    $removeStmt->bind_param('i', $pdf_id);
    if ($removeStmt->execute()) {
      $removeStmt->close();
      header('Location: structure.php?success=1');
      exit;
    }
    $removeStmt->close();
  }
  
  header('Location: structure.php?error=' . urlencode('Failed to delete PDF'));
  exit;
}

// Handle file upload (image or pdf)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload_file') {
  $id = intval($_POST['id']);
  $fileType = $_POST['file_type'] ?? ''; // 'image' or 'pdf'
  
  if ($fileType === 'image' && isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    // Get current department name
    $getSQL = "SELECT department, image FROM organizational_structure WHERE id = ?";
    $getStmt = $conn->prepare($getSQL);
    $getStmt->bind_param('i', $id);
    $getStmt->execute();
    $result = $getStmt->get_result();
    $row = $result->fetch_assoc();
    $getStmt->close();
    
    if ($row) {
      $department = $row['department'];
      $tmp = $_FILES['image']['tmp_name'];
      $filename = $_FILES['image']['name'];
      $newImageMime = mime_content_type($tmp) ?: ($_FILES['image']['type'] ?? 'application/octet-stream');
      
      $fileExt = pathinfo($filename, PATHINFO_EXTENSION);
      $newImage = $department . '_img_' . time() . '.' . $fileExt;
      $uploadPath = $uploadDir . $newImage;
      
      // Delete old image if exists
      if (!empty($row['image'])) {
        $oldPath = $uploadDir . $row['image'];
        if (file_exists($oldPath)) {
          unlink($oldPath);
        }
      }
      
      if (move_uploaded_file($tmp, $uploadPath)) {
        $updateSQL = "UPDATE organizational_structure SET mime = ?, image = ? WHERE id = ?";
        $updateStmt = $conn->prepare($updateSQL);
        $updateStmt->bind_param('ssi', $newImageMime, $newImage, $id);
        if ($updateStmt->execute()) {
          $updateStmt->close();
          header('Location: structure.php?success=1');
          exit;
        }
        $updateStmt->close();
      }
    }
  } elseif ($fileType === 'pdf' && isset($_FILES['pdf']) && $_FILES['pdf']['error'] === UPLOAD_ERR_OK) {
    // Get current department name
    $getSQL = "SELECT department FROM organizational_structure WHERE id = ?";
    $getStmt = $conn->prepare($getSQL);
    $getStmt->bind_param('i', $id);
    $getStmt->execute();
    $result = $getStmt->get_result();
    $row = $result->fetch_assoc();
    $getStmt->close();
    
    if ($row) {
      $department = $row['department'];
      $tmp = $_FILES['pdf']['tmp_name'];
      $filename = $_FILES['pdf']['name'];
      $newPdfMime = mime_content_type($tmp) ?: 'application/pdf';
      
      // Validate PDF file
      $fileExt = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
      if ($fileExt !== 'pdf' && $newPdfMime !== 'application/pdf') {
        header('Location: structure.php?error=' . urlencode('Only PDF files are allowed'));
        exit;
      }
      
      $newPdf = $department . '_pdf_' . time() . '.pdf';
      $pdfUploadPath = $uploadDir . $newPdf;
      
      if (move_uploaded_file($tmp, $pdfUploadPath)) {
        // Insert into organizational_structure_pdfs table (multiple PDFs)
        $insertSQL = "INSERT INTO organizational_structure_pdfs (org_structure_id, pdf_filename, pdf_mime) VALUES (?, ?, ?)";
        $insertStmt = $conn->prepare($insertSQL);
        $insertStmt->bind_param('iss', $id, $newPdf, $newPdfMime);
        if ($insertStmt->execute()) {
          $insertStmt->close();
          header('Location: structure.php?success=1');
          exit;
        }
        $insertStmt->close();
      }
    }
  }
  
  header('Location: structure.php?error=' . urlencode('Failed to upload file'));
  exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Department Files - RMNHS Admin</title>
  <link rel="stylesheet" href="structure.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<?php include '../admin-navbar/navbar.php'; ?>

<!-- Main Content -->
<main class="main-content">
  <div class="dashboard-header">
    <h1>School Department</h1>
    <p>Manage department information, images, and PDFs</p>
  </div>

  <div class="content-card">
    <!-- Success Modal -->
    <?php if (!empty($_GET['success'])): ?>
    <div id="successModal" class="modal active">
      <div class="modal-content">
        <div class="modal-header success-header">
          <i class="fas fa-check-circle"></i>
          <h2>Success</h2>
        </div>
        <div class="modal-body">
          <p>Update saved successfully.</p>
        </div>
        <div class="modal-footer">
          <button onclick="closeSuccessModal()" class="btn-modal-close">Close</button>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <!-- Error Modal -->
    <?php if (!empty($_GET['error'])): ?>
    <div id="errorModal" class="modal active">
      <div class="modal-content">
        <div class="modal-header error-header">
          <i class="fas fa-exclamation-circle"></i>
          <h2>Error</h2>
        </div>
        <div class="modal-body">
          <p><?php echo htmlspecialchars($_GET['error']); ?></p>
        </div>
        <div class="modal-footer">
          <button onclick="closeErrorModal()" class="btn-modal-close">Close</button>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
      <div class="modal-content">
        <div class="modal-header delete-header">
          <i class="fas fa-trash"></i>
          <h2>Remove PDF</h2>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to delete this PDF? This action cannot be undone.</p>
        </div>
        <div class="modal-footer">
          <button onclick="closeDeleteModal()" class="btn-modal-cancel">Cancel</button>
          <form id="deleteForm" method="POST" style="display: inline;">
            <input type="hidden" name="action" value="delete_pdf">
            <input type="hidden" id="pdfIdToDelete" name="pdf_id" value="">
            <button type="submit" class="btn-modal-delete">Remove PDF</button>
          </form>
        </div>
      </div>
    </div>

    <!-- Uploaded Department Files Section -->
    <div class="section-title">Department Files</div>
    <div class="uploaded-grid">
      <?php
        $query = "SELECT id, department, mime, image, updated_at FROM organizational_structure ORDER BY department ASC";
        $result = $conn->query($query);
        
        if ($result && $result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            $imagePath = !empty($row['image']) ? '../../uploads/organizational-structure/' . htmlspecialchars($row['image']) : null;
            
            // Get all PDFs for this department
            $pdfQuery = "SELECT id, pdf_filename FROM organizational_structure_pdfs WHERE org_structure_id = ? ORDER BY created_at DESC";
            $pdfStmt = $conn->prepare($pdfQuery);
            $pdfStmt->bind_param('i', $row['id']);
            $pdfStmt->execute();
            $pdfResult = $pdfStmt->get_result();
            $pdfs = [];
            while ($pdfRow = $pdfResult->fetch_assoc()) {
              $pdfs[] = $pdfRow;
            }
            $pdfStmt->close();
            ?>
            <div class="uploaded-item">
              <!-- Image Display -->
              <?php if ($imagePath): ?>
                <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($row['department']); ?>" class="item-image">
              <?php else: ?>
                <div class="item-image no-image"><i class="fas fa-image"></i><p>No Image</p></div>
              <?php endif; ?>
              
              <!-- Department Name and Date -->
              <div class="meta"><?php echo htmlspecialchars($row['department']); ?></div>
              <div style="font-size: 12px; color: #666; margin-bottom: 8px;">Updated: <?php echo date('M d, Y', strtotime($row['updated_at'])); ?></div>

              <!-- PDF Links -->
              <?php if (!empty($pdfs)): ?>
                <div class="pdf-list">
                  <?php foreach ($pdfs as $pdf): ?>
                    <div class="pdf-item">
                      <a href="../../uploads/organizational-structure/<?php echo htmlspecialchars($pdf['pdf_filename']); ?>" target="_blank" class="btn-pdf">
                        <i class="fas fa-file-pdf"></i> View PDF
                      </a>
                      <button type="button" class="btn-remove-pdf" onclick="openDeleteModal(<?php echo $pdf['id']; ?>)">
                        <i class="fas fa-trash"></i> Remove
                      </button>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>

              <!-- File Upload Controls -->
              <div class="file-controls">
                <!-- Image Upload -->
                <form method="POST" enctype="multipart/form-data" style="display: inline;">
                  <input type="hidden" name="action" value="upload_file">
                  <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                  <input type="hidden" name="file_type" value="image">
                  <div class="file-input-wrapper">
                    <label class="file-input-label">
                      <i class="fas fa-image"></i> Change Image
                      <input type="file" name="image" accept="image/*" onchange="this.form.submit()">
                    </label>
                  </div>
                </form>

                <!-- PDF Upload -->
                <form method="POST" enctype="multipart/form-data" style="display: inline;">
                  <input type="hidden" name="action" value="upload_file">
                  <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                  <input type="hidden" name="file_type" value="pdf">
                  <div class="file-input-wrapper">
                    <label class="file-input-label">
                      <i class="fas fa-file-pdf"></i> Add PDF
                      <input type="file" name="pdf" accept=".pdf,application/pdf" onchange="this.form.submit()">
                    </label>
                  </div>
                </form>
              </div>
            </div>
            <?php
          }
        } else {
          echo '<p style="grid-column: 1/-1; text-align: center; color: var(--text-muted);">No department files uploaded yet.</p>';
        }
      ?>
    </div>
  </div>
</main>

</body>
</html>