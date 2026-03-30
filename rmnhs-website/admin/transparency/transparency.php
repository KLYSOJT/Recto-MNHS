<?php
session_start();
include '../../connection/db_connection.php';

$type = isset($_GET['type']) ? strtoupper($_GET['type']) : 'SSLG';
$typeNames = [
    'SPTA' => 'School Parents and Teachers Association',
  'SSLG' => 'Supreme Secondary Learnes Government',
  'BSP' => 'Boy Scout of the Philippines',
  'GSP' => 'Girl Scouts of the Philippines',
  'TR' => 'The Rectorian',
  'MOOE' => 'Maintenance and Other Operating Expenses',
  'REDCROSS' => 'Red Cross',
  // procurement bulletin types
  'PROCUREMENT_BULLETIN' => 'Procurement Bulletin',
  'APP' => 'Annual Procurement Plan',
  'AWARD_OF_CONTRACTS' => 'Award of Contracts',
  'BAC' => 'Bid and Awards Committee',
  'BID_BULLETIN' => 'Bid Bulletin',
  'INVITATION_TO_BID' => 'Invitation to Bid',
  'PHILGEPS' => 'PhilGEPS',
  'PROCUREMENT_REPORTS' => 'Procurement Reports',
];
$displayName = $typeNames[$type] ?? 'Transparency Report';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = isset($_POST['action']) ? $_POST['action'] : '';
  
  if ($action === 'upload') {
    $title = $_POST['title'] ?? '';
    $date = $_POST['date'] ?? '';
    $description = $_POST['description'] ?? '';
    $fileName = '';

    // Handle file upload
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
      // Validate file type - only PDF allowed
      $finfo = finfo_open(FILEINFO_MIME_TYPE);
      $mimeType = finfo_file($finfo, $_FILES['file']['tmp_name']);
      finfo_close($finfo);
      
      // Check both MIME type and file extension
      $fileExtension = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
      
      if ($mimeType !== 'application/pdf' || $fileExtension !== 'pdf') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Only PDF files are allowed']);
        exit;
      }
      
      $uploadDir = '../../uploads/transparency/';
      if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
      }
      
      $fileName = time() . '_' . basename($_FILES['file']['name']);
      $uploadPath = $uploadDir . $fileName;
      
      if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadPath)) {
        // Insert into database
        $query = "INSERT INTO transparency (type, title, date, description, file, created_at) 
                  VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssss", $type, $title, $date, $description, $fileName);
        
        if ($stmt->execute()) {
          header('Content-Type: application/json');
          echo json_encode(['success' => true, 'message' => 'Document uploaded successfully']);
          exit;
        } else {
          header('Content-Type: application/json');
          echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
          exit;
        }
      } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'File upload failed']);
        exit;
      }
    } else {
      header('Content-Type: application/json');
      echo json_encode(['success' => false, 'message' => 'No file uploaded']);
      exit;
    }
  } elseif ($action === 'edit') {
    $id = $_POST['id'] ?? '';
    $title = $_POST['title'] ?? '';
    $date = $_POST['date'] ?? '';
    $description = $_POST['description'] ?? '';
    $fileName = '';

    // Handle new file if uploaded
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
      // Validate file type - only PDF allowed
      $finfo = finfo_open(FILEINFO_MIME_TYPE);
      $mimeType = finfo_file($finfo, $_FILES['file']['tmp_name']);
      finfo_close($finfo);
      
      // Check both MIME type and file extension
      $fileExtension = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
      
      if ($mimeType !== 'application/pdf' || $fileExtension !== 'pdf') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Only PDF files are allowed']);
        exit;
      }
      
      $uploadDir = '../../uploads/transparency/';
      $fileName = time() . '_' . basename($_FILES['file']['name']);
      $uploadPath = $uploadDir . $fileName;
      
      if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadPath)) {
        $query = "UPDATE transparency SET title=?, date=?, description=?, file=? WHERE id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssi", $title, $date, $description, $fileName, $id);
      } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'File upload failed']);
        exit;
      }
    } else {
      $query = "UPDATE transparency SET title=?, date=?, description=? WHERE id=?";
      $stmt = $conn->prepare($query);
      $stmt->bind_param("sssi", $title, $date, $description, $id);
    }
    
    if ($stmt->execute()) {
      header('Content-Type: application/json');
      echo json_encode(['success' => true, 'message' => 'Document updated successfully']);
      exit;
    } else {
      header('Content-Type: application/json');
      echo json_encode(['success' => false, 'message' => 'Update failed: ' . $stmt->error]);
      exit;
    }
  } elseif ($action === 'delete') {
    $id = $_POST['id'] ?? '';
    
    $query = "DELETE FROM transparency WHERE id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
      header('Content-Type: application/json');
      echo json_encode(['success' => true, 'message' => 'Document deleted successfully']);
      exit;
    } else {
      header('Content-Type: application/json');
      echo json_encode(['success' => false, 'message' => 'Delete failed']);
      exit;
    }
  }
}

// Fetch data
$query = "SELECT * FROM transparency WHERE type=? ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $type);
$stmt->execute();
$result = $stmt->get_result();
$documents = [];
while ($row = $result->fetch_assoc()) {
  $documents[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Transparency - RMNHS Admin</title>
  <link rel="stylesheet" href="../assets/css/home.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="transparency.css">
</head>
<body>


 <?php include '../admin-navbar/navbar.php'; ?>

   <!-- Main Content -->
  <main class="main-content">
    <div class="resources-container">
      <div class="page-header">
        <h1><?php echo $displayName; ?></h1>
      </div>

      <?php if ($type === 'PROCUREMENT_BULLETIN'): ?>
        <div class="links-grid">
          <a class="procurement-link" href="transparency.php?type=APP">APP</a>
          <a class="procurement-link" href="transparency.php?type=AWARD_OF_CONTRACTS">Award of Contracts</a>
          <a class="procurement-link" href="transparency.php?type=BAC">Bid and Awards Committee</a>
          <a class="procurement-link" href="transparency.php?type=BID_BULLETIN">Bid Bulletin</a>
          <a class="procurement-link" href="transparency.php?type=INVITATION_TO_BID">Invitation to Bid</a>
          <a class="procurement-link" href="transparency.php?type=PHILGEPS">PhilGEPS</a>
          <a class="procurement-link" href="transparency.php?type=PROCUREMENT_REPORTS">Procurement Reports</a>
        </div>
      <?php else: ?>
        <!-- Upload Form Section -->
        <div class="upload-form-container">
          <h2>Add New Document</h2>
          <form id="uploadForm" class="upload-form" enctype="multipart/form-data">
            <input type="hidden" name="action" value="upload">
            <div class="form-row">
              <div class="form-group">
                <label for="docTitle">Title :</label>
                <input type="text" id="docTitle" name="title" placeholder="Enter title" required>
              </div>

              <div class="form-group">
                <label for="docDate">Date :</label>
                <input type="date" id="docDate" name="date" required>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group full-width">
                <label for="docDescription">Description :</label>
                <textarea id="docDescription" name="description" placeholder="Enter description" rows="4"></textarea>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="docFile">Upload File (PDF only):</label>
                <div class="file-input-wrapper">
                  <input type="file" id="docFile" name="file" accept=".pdf" required class="file-input-hidden">
                  <label for="docFile" class="file-label" id="docFileLabel">Choose file</label>
                  <span class="file-name" id="docFileName">No file chosen</span>
                </div>
              </div>

              <button type="submit" class="upload-btn">Upload</button>
            </div>
          </form>
        </div>

        <!-- Search Bar Section -->
      <?php endif; ?>
      <div class="search-section">
        <input type="text" id="searchInput" class="search-bar" placeholder="Search">
      </div>

      <!-- Resources Table -->
      <div class="table-wrapper">
        <table class="resources-table">
          <thead>
            <tr>
              <th>Date Added <span class="sort-arrow">↓↑</span></th>
              <th>Title</th>
              <th>Description</th>
              <th>File</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="tableBody">
            <?php if (!empty($documents)): ?>
              <?php foreach ($documents as $doc): ?>
                <tr data-id="<?php echo $doc['id']; ?>">
                  <td><?php echo date('m-d-Y', strtotime($doc['created_at'])); ?></td>
                  <td><?php echo htmlspecialchars($doc['title']); ?></td>
                  <td><?php echo htmlspecialchars($doc['description'] ?? ''); ?></td>
                  <td>
                    <?php if ($doc['file']): ?>
                      <a href="../../uploads/transparency/<?php echo htmlspecialchars($doc['file']); ?>" class="file-link" target="_blank">
                        View File
                      </a>
                    <?php else: ?>
                      <span>-</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <button class="btn-edit" onclick="openEditModal(<?php echo $doc['id']; ?>, '<?php echo htmlspecialchars(addslashes($doc['title'])); ?>', '<?php echo $doc['date']; ?>', '<?php echo htmlspecialchars(addslashes($doc['description'] ?? '')); ?>')">Edit</button>
                    <button class="btn-delete" onclick="openDeleteModal(<?php echo $doc['id']; ?>)">Delete</button>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="5" style="text-align: center; padding: 20px;">No documents found</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <!-- Edit Modal -->
  <div id="editModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Edit</h2>
        <span class="close" onclick="closeEditModal()">&times;</span>
      </div>
      <form id="editForm" class="modal-form" enctype="multipart/form-data">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" id="editId" name="id">
        <div class="form-row">
          <div class="form-group">
            <label for="editTitle">Title :</label>
            <input type="text" id="editTitle" name="title" placeholder="Enter title" required>
          </div>
          <div class="form-group">
            <label for="editDate">Date :</label>
            <input type="date" id="editDate" name="date" required>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group full-width">
            <label for="editDescription">Description :</label>
            <textarea id="editDescription" name="description" placeholder="Enter description" rows="4"></textarea>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="editFile">Upload File (PDF only, Optional) :</label>
            <div class="file-input-wrapper">
              <input type="file" id="editFile" name="file" accept=".pdf" class="file-input-hidden">
              <label for="editFile" class="file-label" id="editFileLabel">Choose file</label>
              <span class="file-name" id="editFileName">No file chosen</span>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn-save">Save Changes</button>
          <button type="button" class="btn-cancel" onclick="closeEditModal()">Cancel</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Delete Confirmation Modal -->
  <div id="deleteModal" class="modal">
    <div class="modal-content modal-confirm">
      <div class="modal-header">
        <h2>Confirm Delete</h2>
        <span class="close" onclick="closeDeleteModal()">&times;</span>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete this document? This action cannot be undone.</p>
      </div>
      <div class="modal-footer">
        <button class="btn-delete-confirm" onclick="confirmDelete()">Delete</button>
        <button class="btn-cancel" onclick="closeDeleteModal()">Cancel</button>
      </div>
    </div>
  </div>

  <!-- Success Modal -->
  <div id="successModal" class="modal">
    <div class="modal-content modal-success">
      <div class="modal-header">
        <h2>Success</h2>
        <span class="close" onclick="closeSuccessModal()">&times;</span>
      </div>
      <div class="modal-body">
        <p id="successMessage"></p>
      </div>
      <div class="modal-footer">
        <button class="btn-save" onclick="closeSuccessModal()">OK</button>
      </div>
    </div>
  </div>

  

  <script>
    let currentDeleteId = null;

    // Setup file input listeners
    function setupFileInputListeners() {
      const docFileInput = document.getElementById('docFile');
      const editFileInput = document.getElementById('editFile');
      
      if (docFileInput) {
        docFileInput.addEventListener('change', function() {
          const fileName = this.files.length > 0 ? this.files[0].name : 'No file chosen';
          // Validate file type
          if (this.files.length > 0 && !this.files[0].name.toLowerCase().endsWith('.pdf')) {
            alert('Only PDF files are allowed');
            this.value = '';
            document.getElementById('docFileName').textContent = 'No file chosen';
            return;
          }
          document.getElementById('docFileName').textContent = fileName;
        });
      }
      
      if (editFileInput) {
        editFileInput.addEventListener('change', function() {
          const fileName = this.files.length > 0 ? this.files[0].name : 'No file chosen';
          // Validate file type
          if (this.files.length > 0 && !this.files[0].name.toLowerCase().endsWith('.pdf')) {
            alert('Only PDF files are allowed');
            this.value = '';
            document.getElementById('editFileName').textContent = 'No file chosen';
            return;
          }
          document.getElementById('editFileName').textContent = fileName;
        });
      }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
      setupFileInputListeners();
    });

    // Upload Form Submission
    document.getElementById('uploadForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const formData = new FormData(this);
      
      fetch('', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          document.getElementById('successMessage').textContent = data.message;
          document.getElementById('successModal').classList.add('show');
          
          setTimeout(() => {
            location.reload();
          }, 1500);
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
      });
    });

    // Edit Form Submission
    document.getElementById('editForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const formData = new FormData(this);
      
      fetch('', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          closeEditModal();
          document.getElementById('successMessage').textContent = data.message;
          document.getElementById('successModal').classList.add('show');
          
          setTimeout(() => {
            location.reload();
          }, 1500);
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
      });
    });

    // Modal Functions
    function openEditModal(id, title, date, description) {
      document.getElementById('editId').value = id;
      document.getElementById('editTitle').value = title;
      document.getElementById('editDate').value = date;
      document.getElementById('editDescription').value = description;
      document.getElementById('editModal').classList.add('show');
    }

    function closeEditModal() {
      document.getElementById('editModal').classList.remove('show');
      document.getElementById('editForm').reset();
    }

    function openDeleteModal(id) {
      currentDeleteId = id;
      document.getElementById('deleteModal').classList.add('show');
    }

    function closeDeleteModal() {
      document.getElementById('deleteModal').classList.remove('show');
      currentDeleteId = null;
    }

    function confirmDelete() {
      if (currentDeleteId === null) return;
      
      const formData = new FormData();
      formData.append('action', 'delete');
      formData.append('id', currentDeleteId);
      
      fetch('', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          closeDeleteModal();
          document.getElementById('successMessage').textContent = data.message;
          document.getElementById('successModal').classList.add('show');
          
          setTimeout(() => {
            location.reload();
          }, 1500);
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
      });
    }

    function closeSuccessModal() {
      document.getElementById('successModal').classList.remove('show');
    }

    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const table = document.querySelector('.resources-table tbody');

    if (searchInput && table) {
      searchInput.addEventListener('keyup', function() {
        const query = this.value.toLowerCase();
        const rows = table.querySelectorAll('tr');

        rows.forEach(row => {
          const text = row.textContent.toLowerCase();
          row.style.display = text.includes(query) ? '' : 'none';
        });
      });
    }

    // Close modals when clicking outside
    window.onclick = function(event) {
      const editModal = document.getElementById('editModal');
      const deleteModal = document.getElementById('deleteModal');
      const successModal = document.getElementById('successModal');
      
      if (event.target == editModal) {
        closeEditModal();
      }
      if (event.target == deleteModal) {
        closeDeleteModal();
      }
      if (event.target == successModal) {
        closeSuccessModal();
      }
    }
  </script>

</body>
</html>