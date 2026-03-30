<?php
require_once __DIR__ . '/../../connection/db_connection.php';

// Create table if it doesn't exist
$createTableSQL = "CREATE TABLE IF NOT EXISTS `recognized_organization` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `org_name` varchar(255) NOT NULL,
  `date_established` date DEFAULT NULL,
  `adviser_name` varchar(255) DEFAULT NULL,
  `mime` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `pdf` varchar(255) DEFAULT NULL,
  `pdf_mime` varchar(100) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$conn->query($createTableSQL);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save') {
  $orgName = trim($_POST['org_name'] ?? '');
  $dateEstablished = trim($_POST['date_established'] ?? '');
  $adviserName = trim($_POST['adviser_name'] ?? '');

  if ($orgName === '') {
    header('Location: recognized.php?error=' . urlencode('Organization name is required'));
    exit;
  }

  $uploadDir = __DIR__ . '/../../uploads/recognized-organization/';
  if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
  }

  $newImage = null;
  $newImageMime = null;
  $newPdf = null;
  $newPdfMime = null;

  // Get existing record
  $existingRow = null;
  $existingSql = "SELECT image, pdf FROM recognized_organization WHERE org_name = ? LIMIT 1";
  $existingStmt = $conn->prepare($existingSql);
  if ($existingStmt) {
    $existingStmt->bind_param('s', $orgName);
    $existingStmt->execute();
    $existingResult = $existingStmt->get_result();
    $existingRow = $existingResult->fetch_assoc();
    $existingStmt->close();
  }

  // Handle image upload
  if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $tmp = $_FILES['image']['tmp_name'];
    $filename = $_FILES['image']['name'];
    $newImageMime = mime_content_type($tmp) ?: ($_FILES['image']['type'] ?? 'application/octet-stream');
    
    $fileExt = pathinfo($filename, PATHINFO_EXTENSION);
    $newImage = preg_replace('/[^a-zA-Z0-9_-]/', '_', $orgName) . '_img_' . time() . '.' . $fileExt;
    $uploadPath = $uploadDir . $newImage;
    
    // Delete old image if exists
    if ($existingRow && !empty($existingRow['image'])) {
      $oldPath = $uploadDir . $existingRow['image'];
      if (file_exists($oldPath)) {
        unlink($oldPath);
      }
    }
    
    if (!move_uploaded_file($tmp, $uploadPath)) {
      header('Location: recognized.php?error=' . urlencode('Failed to upload image'));
      exit;
    }
  }

  // Handle PDF upload
  if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] === UPLOAD_ERR_OK) {
    $tmp = $_FILES['pdf']['tmp_name'];
    $filename = $_FILES['pdf']['name'];
    $newPdfMime = mime_content_type($tmp) ?: ($_FILES['pdf']['type'] ?? 'application/pdf');
    
    // Validate PDF file
    $fileExt = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    if ($fileExt !== 'pdf' && $newPdfMime !== 'application/pdf') {
      header('Location: recognized.php?error=' . urlencode('Only PDF files are allowed'));
      exit;
    }
    
    $newPdf = preg_replace('/[^a-zA-Z0-9_-]/', '_', $orgName) . '_pdf_' . time() . '.pdf';
    $pdfUploadPath = $uploadDir . $newPdf;
    
    // Delete old PDF if exists
    if ($existingRow && !empty($existingRow['pdf'])) {
      $oldPdfPath = $uploadDir . $existingRow['pdf'];
      if (file_exists($oldPdfPath)) {
        unlink($oldPdfPath);
      }
    }
    
    if (!move_uploaded_file($tmp, $pdfUploadPath)) {
      header('Location: recognized.php?error=' . urlencode('Failed to upload PDF'));
      exit;
    }
  }

  // Check if record exists
  $checkSql = "SELECT id FROM recognized_organization WHERE org_name = ? LIMIT 1";
  $checkStmt = $conn->prepare($checkSql);
  $checkStmt->bind_param('s', $orgName);
  $checkStmt->execute();
  $checkResult = $checkStmt->get_result();
  $exists = $checkResult->num_rows > 0;
  $checkStmt->close();

  if ($exists) {
    // Update existing record
    $updateColumns = ["org_name = VALUES(org_name)", "date_established = VALUES(date_established)", "adviser_name = VALUES(adviser_name)", "updated_at = NOW()"];
    $params = [];
    $types = '';

    $params[] = $orgName;
    $types .= 's';
    $params[] = $dateEstablished !== '' ? $dateEstablished : null;
    $types .= 's';
    $params[] = $adviserName;
    $types .= 's';

    if ($newImage !== null) {
      $updateColumns[] = "mime = VALUES(mime)";
      $updateColumns[] = "image = VALUES(image)";
      $params[] = $newImageMime;
      $params[] = $newImage;
      $types .= 'ss';
    }

    if ($newPdf !== null) {
      $updateColumns[] = "pdf_mime = VALUES(pdf_mime)";
      $updateColumns[] = "pdf = VALUES(pdf)";
      $params[] = $newPdfMime;
      $params[] = $newPdf;
      $types .= 'ss';
    }

    $sql = "INSERT INTO recognized_organization (org_name, date_established, adviser_name, mime, image, pdf_mime, pdf) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE " . implode(", ", $updateColumns);
    
    $stmt = $conn->prepare($sql);
    if ($stmt) {
      // Build the full params array for the ON DUPLICATE KEY UPDATE
      $fullParams = [$orgName, $dateEstablished ?: null, $adviserName];
      $fullTypes = 'sss';
      
      if ($newImage !== null) {
        $fullParams[] = $newImageMime;
        $fullParams[] = $newImage;
        $fullTypes .= 'ss';
      } else {
        $fullParams[] = null;
        $fullParams[] = null;
        $fullTypes .= 'ss';
      }
      
      if ($newPdf !== null) {
        $fullParams[] = $newPdfMime;
        $fullParams[] = $newPdf;
        $fullTypes .= 'ss';
      } else {
        $fullParams[] = null;
        $fullParams[] = null;
        $fullTypes .= 'ss';
      }
      
      $stmt->bind_param($fullTypes, ...$fullParams);
      $stmt->execute();
      $stmt->close();
    }
  } else {
    // Insert new record (at least image or pdf required)
    if ($newImage === null && $newPdf === null) {
      header('Location: recognized.php?error=' . urlencode('Please upload an image or PDF file'));
      exit;
    }

    $params = [];
    $types = '';
    
    $params[] = $orgName;
    $types .= 's';
    $params[] = $dateEstablished !== '' ? $dateEstablished : null;
    $types .= 's';
    $params[] = $adviserName;
    $types .= 's';
    $params[] = $newImageMime;
    $types .= 's';
    $params[] = $newImage;
    $types .= 's';
    $params[] = $newPdfMime;
    $types .= 's';
    $params[] = $newPdf;
    $types .= 's';

    $sql = "INSERT INTO recognized_organization (org_name, date_established, adviser_name, mime, image, pdf_mime, pdf) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    if ($stmt) {
      $stmt->bind_param($types, ...$params);
      $stmt->execute();
      $stmt->close();
    }
  }

  header('Location: recognized.php?success=1');
  exit;
}

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
  $id = intval($_POST['id'] ?? 0);
  
  if ($id > 0) {
    // Get file paths before deleting
    $getSql = "SELECT image, pdf FROM recognized_organization WHERE id = ? LIMIT 1";
    $getStmt = $conn->prepare($getSql);
    $getStmt->bind_param('i', $id);
    $getStmt->execute();
    $getResult = $getStmt->get_result();
    $row = $getResult->fetch_assoc();
    $getStmt->close();
    
    // Delete files
    if ($row) {
      $uploadDir = __DIR__ . '/../../uploads/recognized-organization/';
      if (!empty($row['image'])) {
        $imgPath = $uploadDir . $row['image'];
        if (file_exists($imgPath)) unlink($imgPath);
      }
      if (!empty($row['pdf'])) {
        $pdfPath = $uploadDir . $row['pdf'];
        if (file_exists($pdfPath)) unlink($pdfPath);
      }
    }
    
    // Delete database record
    $deleteSql = "DELETE FROM recognized_organization WHERE id = ?";
    $deleteStmt = $conn->prepare($deleteSql);
    $deleteStmt->bind_param('i', $id);
    $deleteStmt->execute();
    $deleteStmt->close();
    
    header('Location: recognized.php?success=2');
    exit;
  }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - Recognized Organization</title>
  <link rel="stylesheet" href="recognized.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>
<body>

<?php include '../admin-navbar/navbar.php'; ?>

<!-- Main Content -->
<main class="main-content">
  <div class="dashboard-header">
      <h1>Recognized Organization</h1>
      <p>Manage recognized organization details here.</p>
    </div>



  <div class="content-card">
    <?php if (!empty($_GET['success'])): ?>
      <div class="notice success">
        <?php echo $_GET['success'] == '1' ? 'Organization saved successfully.' : 'Organization deleted successfully.'; ?>
      </div>
    <?php elseif (!empty($_GET['error'])): ?>
      <div class="notice error"><?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>

    <form id="recognizedForm" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="action" value="save">
      
      <div class="form-group">
        <label for="org_name">Organization Name *</label>
        <input type="text" id="org_name" name="org_name" placeholder="Enter organization name" required>
      </div>

      <div class="form-group">
        <label for="date_established">Date Established</label>
        <input type="date" id="date_established" name="date_established">
      </div>

      <div class="form-group">
        <label for="adviser_name">Adviser Name</label>
        <input type="text" id="adviser_name" name="adviser_name" placeholder="Enter adviser name">
      </div>

      <div class="upload-zone">
        <label>Upload Image</label>
        <div class="drag-drop-area" id="imageDragDrop">
          <i class="fas fa-cloud-upload-alt"></i>
          <p class="primary-text">Upload Image</p>
          <p class="secondary-text">Drag & drop or click to browse</p>
        </div>

        <input type="file" id="orgImage" name="image" accept="image/*">
        
      </div>

      <div class="upload-zone">
        <label>Upload PDF (Optional)</label>
        <div class="drag-drop-area" id="pdfDragDrop">
          <i class="fas fa-file-pdf"></i>
          <p class="primary-text">Upload PDF (Optional)</p>
          <p class="secondary-text">Drag & drop or click to browse</p>
        </div>

        <input type="file" id="orgPdf" name="pdf" accept=".pdf,application/pdf">
        
      </div>

      <button type="submit" class="btn btn-primary">
        Save Organization
      </button> 

    </form>

    <!-- Uploaded Organizations Section -->
    <div class="section-title">Uploaded Organizations</div>
    <div class="uploaded-grid">
      <?php
        $query = "SELECT id, org_name, date_established, adviser_name, mime, image, pdf, updated_at FROM recognized_organization ORDER BY org_name ASC";
        $result = $conn->query($query);
        
        if ($result && $result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            $imagePath = !empty($row['image']) ? '../../uploads/recognized-organization/' . htmlspecialchars($row['image']) : null;
            $pdfPath = !empty($row['pdf']) ? '../../uploads/recognized-organization/' . htmlspecialchars($row['pdf']) : null;
            $dateEstablished = !empty($row['date_established']) ? date('M d, Y', strtotime($row['date_established'])) : 'Not specified';
            ?>
            <div class="uploaded-item">
              <?php if ($imagePath): ?>
                <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($row['org_name']); ?>" class="item-image">
              <?php else: ?>
                <div class="item-image no-image"><i class="fas fa-image"></i><p>No Image</p></div>
              <?php endif; ?>
              <div class="meta"><?php echo htmlspecialchars($row['org_name']); ?></div>
              <div class="established">Established: <?php echo htmlspecialchars($dateEstablished); ?></div>
              <div class="adviser">Adviser: <?php echo htmlspecialchars($row['adviser_name'] ?: 'Not specified'); ?></div>
              <div class="date">Updated: <?php echo date('M d, Y', strtotime($row['updated_at'])); ?></div>
              <?php if ($pdfPath): ?>
                <div class="pdf-link">
                  <a href="<?php echo $pdfPath; ?>" target="_blank" class="btn-pdf">
                    <i class="fas fa-file-pdf"></i> View PDF
                  </a>
                </div>
              <?php endif; ?>
              <form method="POST" enctype="multipart/form-data" class="replace-form" onsubmit="return confirm('Are you sure you want to delete this organization?');">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
                <button type="submit" class="btn-delete">Delete</button>
              </form>
            </div>
            <?php
          }
        } else {
          echo '<p style="grid-column: 1/-1; text-align: center; color: var(--text-muted);">No organizations uploaded yet.</p>';
        }
      ?>
    </div>
  </div>

  </div>

</main>

    <script>
    (function(){
      const imageDragArea = document.getElementById('imageDragDrop');
      const imageInput = document.getElementById('orgImage');
      const pdfDragArea = document.getElementById('pdfDragDrop');
      const pdfInput = document.getElementById('orgPdf');
      const form = document.getElementById('recognizedForm');

      if (!imageDragArea || !imageInput || !form) return;

      // Image upload handlers
      imageDragArea.addEventListener('click', () => imageInput.click());

      ['dragenter','dragover'].forEach(ev => {
        imageDragArea.addEventListener(ev, e => { e.preventDefault(); imageDragArea.classList.add('dragover'); });
      });
      ['dragleave','drop'].forEach(ev => {
        imageDragArea.addEventListener(ev, e => { e.preventDefault(); imageDragArea.classList.remove('dragover'); });
      });

      imageDragArea.addEventListener('drop', e => {
        const f = e.dataTransfer.files && e.dataTransfer.files[0];
        if (f && f.type.startsWith('image/')) {
          imageInput.files = e.dataTransfer.files;
          showImagePreview(f);
        }
      });

      imageInput.addEventListener('change', () => {
        const f = imageInput.files && imageInput.files[0];
        if (f) showImagePreview(f);
      });

      function showImagePreview(file) {
        if (!file.type || !file.type.startsWith('image/')) return;
        const reader = new FileReader();
        reader.onload = function(ev) {
          imageDragArea.innerHTML = '<img class="preview" src="'+ev.target.result+'" alt="preview" />';
        };
        reader.readAsDataURL(file);
      }

      // PDF upload handlers
      if (pdfDragArea && pdfInput) {
        pdfDragArea.addEventListener('click', () => pdfInput.click());

        ['dragenter','dragover'].forEach(ev => {
          pdfDragArea.addEventListener(ev, e => { e.preventDefault(); pdfDragArea.classList.add('dragover'); });
        });
        ['dragleave','drop'].forEach(ev => {
          pdfDragArea.addEventListener(ev, e => { e.preventDefault(); pdfDragArea.classList.remove('dragover'); });
        });

        pdfDragArea.addEventListener('drop', e => {
          const f = e.dataTransfer.files && e.dataTransfer.files[0];
          if (f && (f.type === 'application/pdf' || f.name.toLowerCase().endsWith('.pdf'))) {
            pdfInput.files = e.dataTransfer.files;
            showPdfPreview(f);
          }
        });

        pdfInput.addEventListener('change', () => {
          const f = pdfInput.files && pdfInput.files[0];
          if (f) showPdfPreview(f);
        });
      }

      function showPdfPreview(file) {
        if (file.type !== 'application/pdf' && !file.name.toLowerCase().endsWith('.pdf')) return;
        pdfDragArea.innerHTML = '<i class="fas fa-file-pdf"></i><p class="primary-text">'+file.name+'</p><p class="secondary-text">Ready to upload</p>';
      }

      form.addEventListener('submit', function(e){
        if (!imageInput.files.length && !pdfInput.files.length) { 
          e.preventDefault(); 
          alert('Please upload an image or PDF file.'); 
          return; 
        }
        if (!document.getElementById('org_name').value) { 
          e.preventDefault(); 
          alert('Please enter an organization name.'); 
          return; 
        }
      });
    })();
    </script>

    </body>
    </html>

