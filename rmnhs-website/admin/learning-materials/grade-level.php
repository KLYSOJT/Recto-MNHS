
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>RMNHS Admin Dashboard<?php if (isset($_GET['grade'])) { $g = preg_replace('/[^0-9]/', '', $_GET['grade']); if ($g !== '') echo ' - Grade ' . $g; } ?></title>
  <link rel="stylesheet" href="grade-level.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<?php include '../admin-navbar/navbar.php'; ?>

  <?php
  $message = '';
  $message_type = '';
  // include DB connection
  require_once __DIR__ . '/../../connection/db_connection.php';
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // handle edit/delete actions first
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
      $oldGrade = isset($_POST['old_grade']) ? preg_replace('/[^0-9]/', '', $_POST['old_grade']) : '';
      $oldSubject = isset($_POST['old_subject']) ? str_replace(array('..','/','\\'), '', $_POST['old_subject']) : '';
      $oldFile = isset($_POST['old_file']) ? basename($_POST['old_file']) : '';
      $filePath = __DIR__ . '/../../uploads/learning-materials/' . $oldGrade . '/' . $oldSubject . '/' . $oldFile;
      if ($oldGrade === '' || $oldSubject === '' || $oldFile === '') {
        $message = 'Invalid delete request.';
        $message_type = 'error';
      } elseif (is_file($filePath) && unlink($filePath)) {
        // remove subject dir if empty
        $subjectDir = dirname($filePath);
        if (is_dir($subjectDir) && count(array_diff(scandir($subjectDir), array('.', '..'))) === 0) {
          rmdir($subjectDir);
        }
        // remove grade dir if empty
        $gradeDir = dirname($subjectDir);
        if (is_dir($gradeDir) && count(array_diff(scandir($gradeDir), array('.', '..'))) === 0) {
          rmdir($gradeDir);
        }
        // remove DB record
        if (isset($conn)) {
          if ($delstmt = $conn->prepare("DELETE FROM learning_materials WHERE grade = ? AND subject = ? AND file = ? LIMIT 1")) {
            $delstmt->bind_param('sss', $oldGrade, $oldSubject, $oldFile);
            $delstmt->execute();
            $delstmt->close();
          }
        }
        $message = 'File deleted.';
        $message_type = 'success';
      } else {
        $message = 'Failed to delete file.';
        $message_type = 'error';
      }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'edit') {
      $oldGrade = isset($_POST['old_grade']) ? preg_replace('/[^0-9]/', '', $_POST['old_grade']) : '';
      $oldSubject = isset($_POST['old_subject']) ? str_replace(array('..','/','\\'), '', $_POST['old_subject']) : '';
      $oldFile = isset($_POST['old_file']) ? basename($_POST['old_file']) : '';
      $newGrade = isset($_POST['new_grade']) ? preg_replace('/[^0-9]/', '', $_POST['new_grade']) : '';
      $newSubject = isset($_POST['new_subject']) ? str_replace(array('..','/','\\'), '', $_POST['new_subject']) : '';

      if ($oldGrade === '' || $oldSubject === '' || $oldFile === '' || $newGrade === '' || $newSubject === '') {
        $message = 'Invalid edit request.';
        $message_type = 'error';
      } else {
        $oldPath = __DIR__ . '/../../uploads/learning-materials/' . $oldGrade . '/' . $oldSubject . '/' . $oldFile;
        $targetDir = __DIR__ . '/../../uploads/learning-materials/' . $newGrade . '/' . $newSubject . '/';
        if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);

        // if a replacement file was uploaded, use it
        if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
          $file = $_FILES['pdf_file'];
          $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
          $finfo = finfo_open(FILEINFO_MIME_TYPE);
          $mime = finfo_file($finfo, $file['tmp_name']);
          finfo_close($finfo);
          if ($ext !== 'pdf' || $mime !== 'application/pdf') {
            $message = 'Replacement must be a PDF.';
            $message_type = 'error';
          } else {
            $baseName = pathinfo($file['name'], PATHINFO_FILENAME);
            $timestamp = time();
            $targetFile = $targetDir . $baseName . '_' . $timestamp . '.pdf';
            if (move_uploaded_file($file['tmp_name'], $targetFile)) {
              // delete old file
              if (is_file($oldPath)) unlink($oldPath);
              // update DB record
              $storedFileName = $baseName . '_' . $timestamp . '.pdf';
              $relPath = 'uploads/learning-materials/' . $newGrade . '/' . $newSubject . '/' . $storedFileName;
              if (isset($conn) && $upstmt = $conn->prepare("UPDATE learning_materials SET grade = ?, subject = ?, file = ?, path = ?, filesize = ?, created_at = NOW() WHERE grade = ? AND subject = ? AND file = ? LIMIT 1")) {
                $filesize = (int)$file['size'];
                $upstmt->bind_param('ssssisss', $newGrade, $newSubject, $storedFileName, $relPath, $filesize, $oldGrade, $oldSubject, $oldFile);
                $upstmt->execute();
                $upstmt->close();
              }
              $message = 'File updated.';
              $message_type = 'success';
            } else {
              $message = 'Failed to move replacement file.';
              $message_type = 'error';
            }
          }
        } else {
          // move existing file to new location
            if (!is_file($oldPath)) {
            $message = 'Original file not found.';
            $message_type = 'error';
          } else {
            $targetFile = $targetDir . $oldFile;
            if (is_file($targetFile)) {
              // avoid overwrite
              $targetFile = $targetDir . pathinfo($oldFile, PATHINFO_FILENAME) . '_' . time() . '.' . pathinfo($oldFile, PATHINFO_EXTENSION);
            }
            if (rename($oldPath, $targetFile)) {
              // remove empty old dirs
              $oldSubjectDir = dirname($oldPath);
              if (is_dir($oldSubjectDir) && count(array_diff(scandir($oldSubjectDir), array('.', '..'))) === 0) rmdir($oldSubjectDir);
              $oldGradeDir = dirname($oldSubjectDir);
              if (is_dir($oldGradeDir) && count(array_diff(scandir($oldGradeDir), array('.', '..'))) === 0) rmdir($oldGradeDir);
              // update DB record for moved file
              $newFileName = basename($targetFile);
              $relPath = 'uploads/learning-materials/' . $newGrade . '/' . $newSubject . '/' . $newFileName;
              if (isset($conn) && $upstmt = $conn->prepare("UPDATE learning_materials SET grade = ?, subject = ?, file = ?, path = ?, created_at = NOW() WHERE grade = ? AND subject = ? AND file = ? LIMIT 1")) {
                $upstmt->bind_param('sssssss', $newGrade, $newSubject, $newFileName, $relPath, $oldGrade, $oldSubject, $oldFile);
                $upstmt->execute();
                $upstmt->close();
              }
              $message = 'File moved successfully.';
              $message_type = 'success';
            } else {
              $message = 'Failed to move file.';
              $message_type = 'error';
            }
          }
        }
      }
    } else {
      // regular upload handling
      $subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
      $grade = isset($_POST['grade']) ? trim($_POST['grade']) : '';

      if ($subject === '' || $grade === '') {
        $message = 'Please provide both Subject and Grade level.';
        $message_type = 'error';
      } elseif (!isset($_FILES['pdf_file']) || $_FILES['pdf_file']['error'] !== UPLOAD_ERR_OK) {
        $message = 'Please choose a PDF file to upload.';
        $message_type = 'error';
      } else {
        $file = $_FILES['pdf_file'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if ($ext !== 'pdf' || $mime !== 'application/pdf') {
          $message = 'Only PDF files are allowed.';
          $message_type = 'error';
        } elseif ($file['size'] > 10 * 1024 * 1024) {
          $message = 'File is too large. Max 10MB allowed.';
          $message_type = 'error';
        } else {
          // build target directory: ../uploads/learning-materials/<grade>/<subject>/
          // sanitize grade and subject to avoid traversal
          $safeGrade = preg_replace('/[^0-9]/', '', $grade);
          $safeSubject = str_replace(array('..','/','\\'), '', $subject);
          $targetDir = __DIR__ . '/../../uploads/learning-materials/' . $safeGrade . '/' . $safeSubject . '/';
          if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
          }

          $baseName = pathinfo($file['name'], PATHINFO_FILENAME);
          $timestamp = time();
          $targetFile = $targetDir . $baseName . '_' . $timestamp . '.pdf';

          if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            // insert into DB
            $storedFileName = $baseName . '_' . $timestamp . '.pdf';
            $relPath = 'uploads/learning-materials/' . $safeGrade . '/' . $safeSubject . '/' . $storedFileName;
            if (isset($conn) && $istmt = $conn->prepare("INSERT INTO learning_materials (grade, subject, file, path, filesize) VALUES (?, ?, ?, ?, ?)")) {
              $filesize = (int)$file['size'];
              $istmt->bind_param('ssssi', $safeGrade, $safeSubject, $storedFileName, $relPath, $filesize);
              $istmt->execute();
              $istmt->close();
            }
            $message = 'Upload successful.';
            $message_type = 'success';
          } else {
            $message = 'Failed to move uploaded file.';
            $message_type = 'error';
          }
        }
      }
    }
  }
  
  // define available subjects for dropdown
  $subjects = array('ENGLISH','ESP','FILIPINO','MATH','MUSIC & ARTS','SCIENCE','PE & HEALTH','SPJ','TLE','SPSTEM','SPA');

  // fetch uploaded files from DB
  $uploadsList = array();
  $dbReturnedRows = 0;
  if (isset($conn)) {
    $sql = "SELECT * FROM learning_materials ORDER BY created_at DESC";
    if ($res = $conn->query($sql)) {
      while ($r = $res->fetch_assoc()) {
        $uploadsList[] = array(
          'grade' => $r['grade'],
          'subject' => $r['subject'],
          'file' => $r['file'],
          'path' => $r['path'],
          'mtime' => strtotime($r['created_at']),
          'filesize' => isset($r['filesize']) ? (int)$r['filesize'] : null,
        );
        $dbReturnedRows++;
      }
      $res->free();
    }
  }

  // If DB empty or missing (e.g. table not created), fall back to scanning filesystem so existing uploads appear
  if ($dbReturnedRows === 0) {
    $uploadsBaseDir = __DIR__ . '/../../uploads/learning-materials';
    if (is_dir($uploadsBaseDir)) {
      $gradeDirs = scandir($uploadsBaseDir);
        foreach ($gradeDirs as $g) {
          if ($g === '.' || $g === '..') continue;
          $gradePath = $uploadsBaseDir . '/' . $g;
          if (!is_dir($gradePath)) continue;
          $subjectDirs = scandir($gradePath);
          foreach ($subjectDirs as $s) {
            if ($s === '.' || $s === '..') continue;
            $subjectPath = $gradePath . '/' . $s;
            if (!is_dir($subjectPath)) continue;
            $fileList = scandir($subjectPath);
            foreach ($fileList as $f) {
              if ($f === '.' || $f === '..') continue;
              $filePath = $subjectPath . '/' . $f;
              if (!is_file($filePath)) continue;
              $uploadsList[] = array(
                'grade' => $g,
                'subject' => $s,
                'file' => $f,
                'path' => 'uploads/learning-materials/' . $g . '/' . rawurlencode($s) . '/' . rawurlencode($f),
                'mtime' => filemtime($filePath),
                'filesize' => filesize($filePath)
              );
            }
          }
        }

        
    }
  }

  ?>

  <?php
  // Filtering and pagination (apply regardless of DB or filesystem source)
  $perPage = 10;
  $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
  $filterGrade = isset($_GET['filter_grade']) ? preg_replace('/[^0-9]/', '', $_GET['filter_grade']) : '';

  // apply grade filter if set
  if ($filterGrade !== '') {
    $uploadsList = array_values(array_filter($uploadsList, function($r) use ($filterGrade) { return (string)$r['grade'] === (string)$filterGrade; }));
  }

  $totalItems = count($uploadsList);
  $totalPages = $totalItems > 0 ? (int)ceil($totalItems / $perPage) : 1;
  if ($page > $totalPages) $page = $totalPages;
  $start = ($page - 1) * $perPage;
  $pageItems = array_slice($uploadsList, $start, $perPage);
  ?>

  <main class="content">
    <div class="container">
      <?php $selGrade = isset($_GET['grade']) ? preg_replace('/[^0-9]/', '', $_GET['grade']) : ''; ?>
      <div class="page-header">
      <?php if ($selGrade !== ''): ?>
        <h1>Grade <?php echo htmlspecialchars($selGrade); ?></h1>
      <?php else: ?>
        <h1>Upload Learning Materials</h1>
      <?php endif; ?>
      </div>

      <div class="card">
        <h2>Add New Document</h2>
        <?php /* server messages are shown in modal (notifyModal) - inline alert removed */ ?>

        <form method="post" enctype="multipart/form-data" class="upload-form">
          <div class="form-row">
            <label>Subject Name :</label>
            <select name="subject" required>
              <option value="">Select subject</option>
              <?php foreach ($subjects as $sub): ?>
                <option value="<?php echo htmlspecialchars($sub); ?>" <?php if (isset($_POST['subject']) && $_POST['subject'] === $sub) echo 'selected'; ?>><?php echo htmlspecialchars($sub); ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-row">
            <label>Grade Level :</label>
            <select name="grade" required>
              <option value="">Select grade</option>
              <option value="7">Grade 7</option>
              <option value="8">Grade 8</option>
              <option value="9">Grade 9</option>
              <option value="10">Grade 10</option>
              <option value="11">Grade 11</option>
              <option value="12">Grade 12</option>
            </select>
          </div>

          <div class="form-row file-row">
            <label>Upload File :</label>
            <input type="file" name="pdf_file" accept="application/pdf" required>
            <button type="submit" class="btn-upload">Upload</button>
          </div>
        </form>
      </div>

      <div class="card">
        <form method="get" class="filter-form" style="display:flex;gap:12px;align-items:center;margin-bottom:12px">
          <label style="min-width:120px">Filter Grade :</label>
          <select name="filter_grade">
            <option value="">All grades</option>
            <option value="7" <?php if ($filterGrade === '7') echo 'selected'; ?>>Grade 7</option>
            <option value="8" <?php if ($filterGrade === '8') echo 'selected'; ?>>Grade 8</option>
            <option value="9" <?php if ($filterGrade === '9') echo 'selected'; ?>>Grade 9</option>
            <option value="10" <?php if ($filterGrade === '10') echo 'selected'; ?>>Grade 10</option>
            <option value="11" <?php if ($filterGrade === '11') echo 'selected'; ?>>Grade 11</option>
            <option value="12" <?php if ($filterGrade === '12') echo 'selected'; ?>>Grade 12</option>
          </select>
          <button type="submit" class="action-btn">Apply</button>
          <a href="grade-level.php" class="action-btn" style="margin-left:8px">Clear</a>
        </form>
      <?php if (!empty($uploadsList)): ?>
        <h2>Uploaded Documents</h2>
        <table class="uploads-table">
          <thead>
            <tr>
              <th>Grade</th>
              <th>Subject</th>
              <th>File</th>
              <th>Uploaded</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($pageItems as $row):
              $url = '../../uploads/learning-materials/' . rawurlencode($row['grade']) . '/' . rawurlencode($row['subject']) . '/' . rawurlencode($row['file']);
            ?>
            <tr>
              <td><?php echo htmlspecialchars($row['grade']); ?></td>
              <td><?php echo htmlspecialchars($row['subject']); ?></td>
              <td><a href="<?php echo $url; ?>" target="_blank"><?php echo htmlspecialchars($row['file']); ?></a></td>
              <td><?php echo date('Y-m-d H:i', $row['mtime']); ?></td>
              <td>
                <div class="uploads-actions">
                  <button type="button" class="action-btn edit" data-grade="<?php echo htmlspecialchars($row['grade']); ?>" data-subject="<?php echo htmlspecialchars($row['subject']); ?>" data-file="<?php echo htmlspecialchars($row['file']); ?>">Edit</button>
                  <button type="button" class="action-btn delete" data-grade="<?php echo htmlspecialchars($row['grade']); ?>" data-subject="<?php echo htmlspecialchars($row['subject']); ?>" data-file="<?php echo htmlspecialchars($row['file']); ?>">Delete</button>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php else: ?>
      <div class="card"><p class="no-uploads">No uploaded documents yet.</p></div>
      <?php endif; ?>

      <!-- Pagination -->
      <?php if ($totalItems > $perPage): ?>
      <div style="display:flex;gap:8px;align-items:center;justify-content:center;margin-top:12px">
        <?php if ($page > 1): ?>
          <a class="action-btn" href="?page=<?php echo $page-1; ?><?php echo $filterGrade!=='' ? '&filter_grade='.urlencode($filterGrade):''; ?>">&laquo; Prev</a>
        <?php endif; ?>

        <?php for ($p=1;$p<=$totalPages;$p++): ?>
          <?php if ($p == $page): ?>
            <span class="action-btn" style="background:#5d0000;color:#fff"><?php echo $p; ?></span>
          <?php else: ?>
            <a class="action-btn" href="?page=<?php echo $p; ?><?php echo $filterGrade!=='' ? '&filter_grade='.urlencode($filterGrade):''; ?>"><?php echo $p; ?></a>
          <?php endif; ?>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
          <a class="action-btn" href="?page=<?php echo $page+1; ?><?php echo $filterGrade!=='' ? '&filter_grade='.urlencode($filterGrade):''; ?>">Next &raquo;</a>
        <?php endif; ?>
      </div>
      <?php endif; ?>

    </div>
  </main>

  <!-- Edit Modal -->
  <div id="editModal" class="modal" role="dialog" aria-hidden="true">
    <div class="modal-content">
      <div class="modal-header">
        <div class="modal-title">Edit Document</div>
        <button class="modal-close" data-target="editModal">×</button>
      </div>
      <div class="modal-body">
        <form id="editForm" method="post" enctype="multipart/form-data">
          <input type="hidden" name="action" value="edit">
          <input type="hidden" name="old_grade" id="edit_old_grade">
          <input type="hidden" name="old_subject" id="edit_old_subject">
          <input type="hidden" name="old_file" id="edit_old_file">

          <div class="form-row">
            <label>New Grade :</label>
            <select name="new_grade" id="edit_new_grade" required>
              <option value="">Select grade</option>
              <option value="7">Grade 7</option>
              <option value="8">Grade 8</option>
              <option value="9">Grade 9</option>
              <option value="10">Grade 10</option>
              <option value="11">Grade 11</option>
              <option value="12">Grade 12</option>
            </select>
          </div>

          <div class="form-row">
            <label>New Subject :</label>
            <select name="new_subject" id="edit_new_subject" required>
              <option value="">Select subject</option>
              <?php foreach ($subjects as $s): ?>
                <option value="<?php echo htmlspecialchars($s); ?>"><?php echo htmlspecialchars($s); ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-row">
            <label>Replace File (optional) :</label>
            <input type="file" name="pdf_file" accept="application/pdf">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button class="btn-cancel modal-close" data-target="editModal">Cancel</button>
        <button class="btn-confirm" id="editSubmit">Save</button>
      </div>
    </div>
  </div>

  <!-- Delete Modal -->
  <div id="deleteModal" class="modal" role="dialog" aria-hidden="true">
    <div class="modal-content">
      <div class="modal-header">
        <div class="modal-title">Delete Document</div>
        <button class="modal-close" data-target="deleteModal">×</button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete <strong id="deleteFileName"></strong>?</p>
        <form id="deleteForm" method="post">
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="old_grade" id="delete_old_grade">
          <input type="hidden" name="old_subject" id="delete_old_subject">
          <input type="hidden" name="old_file" id="delete_old_file">
        </form>
      </div>
      <div class="modal-footer">
        <button class="btn-cancel modal-close" data-target="deleteModal">Cancel</button>
        <button class="btn-confirm" id="deleteConfirm">Delete</button>
      </div>
    </div>
  </div>

  <!-- Notification Modal -->
  <div id="notifyModal" class="modal" role="dialog" aria-hidden="true">
    <div class="modal-content">
      <div class="modal-header">
        <div class="modal-title" id="notifyTitle">Notification</div>
        <button class="modal-close" data-target="notifyModal">×</button>
      </div>
      <div class="modal-body">
        <p id="notifyBody"></p>
      </div>
      <div class="modal-footer">
        <button class="btn-confirm modal-close" data-target="notifyModal">OK</button>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // keep existing submenu behavior
      const gradeLinks = document.querySelectorAll('.dropdown-submenu-list a');
      gradeLinks.forEach(link => {
        link.addEventListener('click', function(e) {
          document.querySelectorAll('.dropdown-submenu').forEach(el => el.classList.remove('active'));
          const submenu = this.closest('.dropdown-submenu');
          if (submenu) submenu.classList.add('active');
        });
      });

      // modal helpers
      function showModal(id) { document.getElementById(id).classList.add('show'); document.getElementById(id).setAttribute('aria-hidden','false'); }
      function hideModal(id) { document.getElementById(id).classList.remove('show'); document.getElementById(id).setAttribute('aria-hidden','true'); }

      document.querySelectorAll('.modal-close').forEach(btn => {
        btn.addEventListener('click', function() { hideModal(this.getAttribute('data-target')); });
      });

      // close modal when clicking outside content
      document.querySelectorAll('.modal').forEach(mod => {
        mod.addEventListener('click', function(e) { if (e.target === mod) hideModal(mod.id); });
      });

      // Edit button handler
      document.querySelectorAll('.action-btn.edit').forEach(btn => {
        btn.addEventListener('click', function() {
          const g = this.dataset.grade || '';
          const s = this.dataset.subject || '';
          const f = this.dataset.file || '';
          document.getElementById('edit_old_grade').value = g;
          document.getElementById('edit_old_subject').value = s;
          document.getElementById('edit_old_file').value = f;
          // set selects
          const gradeSel = document.getElementById('edit_new_grade');
          for (let i=0;i<gradeSel.options.length;i++) if (gradeSel.options[i].value === g) gradeSel.selectedIndex = i;
          const subjSel = document.getElementById('edit_new_subject');
          for (let i=0;i<subjSel.options.length;i++) if (subjSel.options[i].value === s) subjSel.selectedIndex = i;
          showModal('editModal');
        });
      });

      // submit edit form when save clicked
      document.getElementById('editSubmit').addEventListener('click', function(e){
        e.preventDefault();
        document.getElementById('editForm').submit();
      });

      // Delete button handler
      document.querySelectorAll('.action-btn.delete').forEach(btn => {
        btn.addEventListener('click', function() {
          const g = this.dataset.grade || '';
          const s = this.dataset.subject || '';
          const f = this.dataset.file || '';
          document.getElementById('delete_old_grade').value = g;
          document.getElementById('delete_old_subject').value = s;
          document.getElementById('delete_old_file').value = f;
          document.getElementById('deleteFileName').textContent = f;
          showModal('deleteModal');
        });
      });

      document.getElementById('deleteConfirm').addEventListener('click', function(e){
        e.preventDefault();
        document.getElementById('deleteForm').submit();
      });
    });
  </script>

  <script>
    // show server-side notification modal if message present
    document.addEventListener('DOMContentLoaded', function() {
      var serverMessage = <?php echo json_encode($message); ?>;
      var serverMessageType = <?php echo json_encode($message_type); ?>;
      if (serverMessage) {
        var notifyTitle = document.getElementById('notifyTitle');
        var notifyBody = document.getElementById('notifyBody');
        if (notifyBody) notifyBody.textContent = serverMessage;
        if (notifyTitle) {
          if (serverMessageType === 'success') notifyTitle.textContent = 'Success';
          else if (serverMessageType === 'error') notifyTitle.textContent = 'Error';
          else notifyTitle.textContent = 'Notice';
        }
        var nm = document.getElementById('notifyModal');
        if (nm) { nm.classList.add('show'); nm.setAttribute('aria-hidden','false'); }
      }
    });
  </script>

</body>
</html>
