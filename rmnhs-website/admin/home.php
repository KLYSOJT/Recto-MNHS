<?php
require_once '../connection/db_connection.php';

// make sure featured_videos table exists
// make sure featured_videos table exists (support URL or uploaded filename)
$conn->query("CREATE TABLE IF NOT EXISTS `featured_videos` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `title` varchar(255) NOT NULL,
  `description` text,
  `filename` varchar(255) DEFAULT NULL,
  `url` varchar(500) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
// ensure description column exists for older installations (no-op if present)
$conn->query("ALTER TABLE `featured_videos` ADD COLUMN IF NOT EXISTS `description` text AFTER `title`");


// Fetch announcements
$announcementsSql = "SELECT id, image, announcement_posts, created_at FROM announcement ORDER BY created_at DESC";
$announcementsResult = $conn->query($announcementsSql);
$announcements = [];
if ($announcementsResult) {
    while ($row = $announcementsResult->fetch_assoc()) {
        $announcements[] = $row;
    }
}

// Fetch news
$newsSql = "SELECT id, image, news_posts, created_at FROM news ORDER BY created_at DESC";
$newsResult = $conn->query($newsSql);
$news = [];
if ($newsResult) {
    while ($row = $newsResult->fetch_assoc()) {
        $news[] = $row;
    }
}

// Fetch featured videos for list and upload page
$videosSql = "SELECT id, title, description, filename, url, created_at FROM featured_videos ORDER BY created_at DESC";
$videosResult = $conn->query($videosSql);
$videos = [];
if ($videosResult) {
    while ($row = $videosResult->fetch_assoc()) {
        $videos[] = $row;
    }
}

    // --- video upload size helpers ------------------------------------------------
    // these mirror the functions used in publish_video.php so the client can
    // know the current server limits without having to guess.
    function convertToBytes($val) {
      // normalize and bail out on empty values (ini_get may return false or an empty string)
      $val = trim($val);
      if ($val === '') {
        return 0;
      }

      // cast the numeric portion first so subsequent math won't trigger warnings
      $num = (int) $val;
      $last = strtolower($val[strlen($val) - 1]);
      switch ($last) {
        case 'g': $num *= 1024; // fall through
        case 'm': $num *= 1024; // fall through
        case 'k': $num *= 1024; // fall through
      }
      return $num;
    }

    function formatBytes($bytes) {
      if ($bytes >= 1073741824) {
        return round($bytes / 1073741824, 2) . ' GB';
      } elseif ($bytes >= 1048576) {
        return round($bytes / 1048576, 2) . ' MB';
      } elseif ($bytes >= 1024) {
        return round($bytes / 1024, 2) . ' KB';
      }
      return $bytes . ' bytes';
    }

    // determine the effective maximum upload size based on PHP settings
    $videoMaxBytes = min(
      convertToBytes(ini_get('upload_max_filesize')),
      convertToBytes(ini_get('post_max_size'))
    );
    $videoMaxHuman = formatBytes($videoMaxBytes);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>RMNHS Admin Dashboard</title>
  <link rel="stylesheet" href="../assets/css/unified.css">
  <link rel="stylesheet" href="../assets/css/home.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>


<?php include 'admin-navbar/navbar.php'; ?>

  <!-- Main Content -->
  <main class="admin-dashboard">
    <div class="dashboard-header">
      <h1>Dashboard</h1>
      <p>Welcome to the RMNHS Admin Panel</p>
    </div>

    <div class="dashboard-grid">
      <!-- Announcement Card -->
      <div class="card announcement-card">
        <div class="card-header">
          <h2 class="card-title">
            <i class="fas fa-bullhorn"></i>
            Announcement
          </h2>
        </div>
        <div class="card-body">
          <form id="announcementForm" enctype="multipart/form-data">
            <div class="upload-zone">
              <div class="drag-drop-area" id="announcementDragDrop">
                <i class="fas fa-cloud-upload-alt"></i>
                <p class="primary-text">Drag and drop your image here</p>
                <p class="secondary-text">or click to select</p>
                <div class="loading-spinner" id="announcementLoading" style="display: none;">
                  <div class="spinner"></div>
                  <p style="margin-top: 10px; color: #666;">Processing...</p>
                </div>
              </div>
              <input type="file" id="announcementImage" name="image" accept="image/*" style="display: none;">
              <div id="announcementImagePreview" style="display: none; margin-top: 10px;">
                <img id="announcementImagePreviewImg" style="max-width: 100%; max-height: 200px; border-radius: 5px;">
                <button type="button" onclick="clearAnnouncementImage()" style="margin-top: 5px; padding: 5px 10px; background: #ff6b6b; color: white; border: none; border-radius: 3px; cursor: pointer;">Remove Image</button>
              </div>
            </div>
            <textarea class="form-input" name="announcement" placeholder="Write your announcement here..."></textarea>
            <button type="submit" class="btn btn-primary btn-block">
              <i class="fas fa-upload"></i>
              Publish Announcement
            </button>
          </form>
        </div>
      </div>

      <!-- News Card -->
      <div class="card news-card">
        <div class="card-header">
          <h2 class="card-title">
            <i class="fas fa-newspaper"></i>
            News
          </h2>
        </div>
        <div class="card-body">
          <form id="newsForm" enctype="multipart/form-data">
            <div class="upload-zone">
              <div class="drag-drop-area" id="newsDragDrop">
                <i class="fas fa-cloud-upload-alt"></i>
                <p class="primary-text">Drag and drop your image here</p>
                <p class="secondary-text">or click to select</p>
                <div class="loading-spinner" id="newsLoading" style="display: none;">
                  <div class="spinner"></div>
                  <p style="margin-top: 10px; color: #666;">Processing...</p>
                </div>
              </div>
              <input type="file" id="newsImage" name="image" accept="image/*" style="display: none;">
              <div id="newsImagePreview" style="display: none; margin-top: 10px;">
                <img id="newsImagePreviewImg" style="max-width: 100%; max-height: 200px; border-radius: 5px;">
                <button type="button" onclick="clearNewsImage()" style="margin-top: 5px; padding: 5px 10px; background: #ff6b6b; color: white; border: none; border-radius: 3px; cursor: pointer;">Remove Image</button>
              </div>
            </div>
            <textarea class="form-input" name="news" placeholder="Write your news here..."></textarea>
            <button type="submit" class="btn btn-primary btn-block">
              <i class="fas fa-upload"></i>
              Publish News
            </button>
          </form>
        </div>
      </div>
    </div> <!-- end dashboard-grid -->

    <!-- Published Items Section -->
    <div class="published-items-section">
      <!-- Announcements List -->
      <?php if (!empty($announcements)) { ?>
      <div class="published-card announcements-list-card">
        <div class="card-header">
          <h2 class="card-title">
            <i class="fas fa-list"></i>
            Published Announcements
          </h2>
        </div>
        <div class="card-body">
          <div class="items-container">
            <?php foreach ($announcements as $announcement) { ?>
            <div class="published-item" data-id="<?php echo $announcement['id']; ?>" data-type="announcement" data-content="<?php echo htmlspecialchars($announcement['announcement_posts'], ENT_QUOTES); ?>" data-image="<?php echo !empty($announcement['image']) ? htmlspecialchars($announcement['image'], ENT_QUOTES) : ''; ?>">
              <div class="item-content">
                <?php if (!empty($announcement['image'])) { ?>
                <div class="item-image">
                  <img src="../uploads/announcements/<?php echo htmlspecialchars($announcement['image']); ?>" alt="Announcement Image">
                </div>
                <?php } ?>
                <div class="item-text">
                  <p class="item-content-text"><?php echo htmlspecialchars(substr($announcement['announcement_posts'], 0, 150)) . '...'; ?></p>
                  <p class="item-date"><?php echo date('M d, Y - h:i A', strtotime($announcement['created_at'])); ?></p>
                </div>
              </div>
              <div class="item-actions">
                <button class="btn-action btn-edit" type="button">
                  <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn-action btn-delete" type="button">
                  <i class="fas fa-trash"></i> Delete
                </button>
              </div>
            </div>
            <?php } ?>
          </div>
        </div>
      </div>
      <?php } ?>

      <!-- News List -->
      <?php if (!empty($news)) { ?>
      <div class="published-card news-list-card">
        <div class="card-header">
          <h2 class="card-title">
            <i class="fas fa-list"></i>
            Published News
          </h2>
        </div>
        <div class="card-body">
          <div class="items-container">
            <?php foreach ($news as $newsItem) { ?>
            <div class="published-item" data-id="<?php echo $newsItem['id']; ?>" data-type="news" data-content="<?php echo htmlspecialchars($newsItem['news_posts'], ENT_QUOTES); ?>" data-image="<?php echo !empty($newsItem['image']) ? htmlspecialchars($newsItem['image'], ENT_QUOTES) : ''; ?>">
              <div class="item-content">
                <?php if (!empty($newsItem['image'])) { ?>
                <div class="item-image">
                  <img src="../uploads/news/<?php echo htmlspecialchars($newsItem['image']); ?>" alt="News Image">
                </div>
                <?php } ?>
                <div class="item-text">
                  <p class="item-content-text"><?php echo htmlspecialchars(substr($newsItem['news_posts'], 0, 150)) . '...'; ?></p>
                  <p class="item-date"><?php echo date('M d, Y - h:i A', strtotime($newsItem['created_at'])); ?></p>
                </div>
              </div>
              <div class="item-actions">
                <button class="btn-action btn-edit" type="button">
                  <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn-action btn-delete" type="button">
                  <i class="fas fa-trash"></i> Delete
                </button>
              </div>
            </div>
            <?php } ?>
          </div>
        </div>
      </div>
      <?php } ?>

      <!-- Featured Videos removed from here and relocated below the Upload Video section -->
    </div>

    <!-- Upload Video Section -->
    <div class="video-upload-section" style="margin-top: 2rem;">
      <div class="card video-card">
        <div class="card-header">
          <h2 class="card-title">
            <i class="fas fa-video"></i>
            Upload Video
          </h2>
        </div>
        <div class="card-body">
          <form id="videoForm" enctype="multipart/form-data">
            <input type="text" class="form-input" name="title" placeholder="Video title">
            <textarea class="form-input" name="description" placeholder="Video description (optional)" style="min-height:80px;"></textarea>
            
            <!-- Video Upload Method Selection -->
            <div style="margin: 15px 0; padding: 10px; background: #f9f9f9; border-radius: 5px;">
              <p style="font-size: 13px; font-weight: 600; margin: 0 0 10px 0; color: #333;">Choose upload method:</p>
              <div style="display: flex; gap: 20px;">
                <label style="display: flex; align-items: center; cursor: pointer;">
                  <input type="radio" name="video_method" value="upload" checked style="margin-right: 8px; cursor: pointer;">
                  <span style="font-size: 13px;">Upload Video File</span>
                </label>
                <label style="display: flex; align-items: center; cursor: pointer;">
                  <input type="radio" name="video_method" value="url" style="margin-right: 8px; cursor: pointer;">
                  <span style="font-size: 13px;">Paste Video URL</span>
                </label>
              </div>
            </div>

            <div id="videoProgress" style="display:none; margin-bottom:10px;">
              <div id="videoProgressBar" style="width:0; height:8px; background:var(--primary-color); border-radius:4px;"></div>
              <p id="videoProgressText" style="font-size:12px; margin:4px 0 0;">0%</p>
            </div>
            
            <!-- File Upload Section -->
            <div id="fileUploadSection" class="upload-zone">
              <div class="drag-drop-area" id="videoDragDrop">
                <i class="fas fa-cloud-upload-alt"></i>
                <p class="primary-text">Drag and drop your video here</p>
                <p class="secondary-text">or click to select</p>
                <div class="loading-spinner" id="videoLoading" style="display: none;">
                  <div class="spinner"></div>
                  <p style="margin-top: 10px; color: #666;">Processing...</p>
                </div>
              </div>
              <input type="file" id="videoFile" name="video" accept="video/*" style="display: none;">
              <div id="videoPreview" style="display: none; margin-top: 10px;">
                <video id="videoPreviewElem" controls style="max-width: 100%; max-height: 200px; border-radius: 5px;"></video>
                <button type="button" onclick="clearVideo()" style="margin-top: 5px; padding: 5px 10px; background: #ff6b6b; color: white; border: none; border-radius: 3px; cursor: pointer;">Remove Video</button>
              </div>
            </div>

            <!-- URL Input Section -->
            <div id="urlInputSection" style="display: none;">
              <input type="url" class="form-input" id="videoUrl" name="video_url" placeholder="Enter video URL (YouTube, Vimeo, Google Drive, Facebook, etc.)" style="margin-bottom: 10px;">
              <div id="urlPreview" style="display: none; margin-top: 10px; text-align: center;">
                <p style="font-size: 12px; color: #666; margin-bottom: 8px;">URL Preview</p>
                <iframe id="urlPreviewFrame" width="100%" height="300" style="border-radius: 5px; border: 1px solid #ddd;" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; xr-spatial-tracking" sandbox="allow-scripts allow-same-origin allow-popups allow-popups-to-escape-sandbox" allowfullscreen></iframe>
                <button type="button" onclick="clearVideoUrl()" style="margin-top: 5px; padding: 5px 10px; background: #ff6b6b; color: white; border: none; border-radius: 3px; cursor: pointer;">Remove URL</button>
              </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block">
              <i class="fas fa-upload"></i>
              Upload 
            </button>
          </form>
        </div>
      </div>
    </div>

  <?php if (!empty($videos)) { ?>
  <div class="published-card videos-list-card" style="margin-top:2rem;">
    <div class="card-header">
      <h2 class="card-title">
        <i class="fas fa-video"></i>
        Featured Videos
      </h2>
    </div>
    <div class="card-body">
      <div class="items-container">
        <?php foreach ($videos as $video) { ?>
        <div class="published-item" data-id="<?php echo $video['id']; ?>" data-type="video" data-content="<?php echo htmlspecialchars($video['title'], ENT_QUOTES); ?>" data-description="<?php echo htmlspecialchars($video['description'], ENT_QUOTES); ?>" data-filename="<?php echo htmlspecialchars($video['filename'], ENT_QUOTES); ?>">
          <div class="item-content">
            <div class="item-text">
              <p class="item-content-text"><?php echo htmlspecialchars($video['title']); ?></p>
              <?php if (!empty($video['description'])) { ?>
              <p class="item-desc"><?php echo htmlspecialchars(substr($video['description'],0,100)); ?><?php if(strlen($video['description'])>100) echo '...'; ?></p>
              <?php } ?>
              <p class="item-date"><?php echo date('M d, Y - h:i A', strtotime($video['created_at'])); ?></p>
            </div>
          </div>
          <div class="item-actions">
            <button class="btn-action btn-edit" type="button">
              <i class="fas fa-edit"></i> Edit
            </button>
            <button class="btn-action btn-delete" type="button">
              <i class="fas fa-trash"></i> Delete
            </button>
          </div>
        </div>
        <?php } ?>
      </div>
    </div>
  </div>
  <?php } ?>

  <!-- Publication Modal -->
  <div id="publishModal" class="modal">
    <div class="modal-content">
      <span class="close-modal" onclick="closeModal()">&times;</span>
      <div class="modal-icon">
        <i class="fas fa-check-circle"></i>
      </div>
      <h2 class="modal-title" id="modalTitle">Published Successfully!</h2>
      
      <!-- Modal Image Preview -->
      <div id="modalImageContainer" style="display: none; margin: 20px 0; text-align: center;">
        <p style="font-size: 14px; color: #999; margin-bottom: 10px;">Image Preview</p>
        <img id="modalImage" style="max-width: 100%; max-height: 300px; border-radius: 5px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
      </div>

      <!-- Modal Video Preview -->
      <div id="modalVideoContainer" style="display: none; margin: 20px 0; text-align: center;">
        <p style="font-size: 14px; color: #999; margin-bottom: 10px;">Video Preview</p>
        <video id="modalVideo" controls style="max-width: 100%; max-height: 300px; border-radius: 5px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);"></video>
      </div>

      <!-- Modal Content Preview -->
      <div id="modalContentContainer" style="display: none; margin: 20px 0; padding: 15px; background: #f5f5f5; border-radius: 5px; text-align: left; max-height: 200px; overflow-y: auto;">
        <p style="font-size: 12px; color: #999; margin-bottom: 10px; margin-top: 0;">Content Preview</p>
        <p id="modalContent" style="color: #333; margin: 0; white-space: pre-wrap; word-wrap: break-word; line-height: 1.5;"></p>
      </div>

      <p class="modal-message" id="modalMessage">Your announcement has been published successfully.</p>
      <div class="modal-buttons">
        <button class="modal-button modal-button-primary" onclick="closeModal()">
          <i class="fas fa-check"></i> OK
        </button>
      </div>
    </div>
  </div>

  <!-- Edit Modal -->
  <div id="editModal" class="modal">
    <div class="modal-content">
      <span class="close-modal" onclick="closeEditModal()">&times;</span>
      <h2 class="modal-title">Edit Item</h2>
      
      <form id="editForm" enctype="multipart/form-data">
        <input type="hidden" id="editId" name="id">
        <input type="hidden" id="editType" name="type">
        
        <div class="form-group">
          <label>Current Image:</label>
          <div id="currentImageContainer" style="margin-bottom: 10px;"></div>
        </div>

        <div class="form-group">
          <label>Upload New Image (Optional):</label>
          <div class="upload-zone">
            <div class="drag-drop-area" id="editDragDrop" style="border: 2px dashed #ddd; padding: 20px; text-align: center; border-radius: 5px; cursor: pointer;">
              <i class="fas fa-cloud-upload-alt"></i>
              <p style="margin: 10px 0;">Drag and drop or click to select</p>
            </div>
            <input type="file" id="editImage" name="image" accept="image/*" style="display: none;">
            <div id="editImagePreview" style="display: none; margin-top: 10px;">
              <img id="editImagePreviewImg" style="max-width: 100%; max-height: 200px; border-radius: 5px;">
              <button type="button" onclick="clearEditImage()" style="margin-top: 5px; padding: 5px 10px; background: #ff6b6b; color: white; border: none; border-radius: 3px; cursor: pointer;">Remove Image</button>
            </div>
          </div>
        </div>

        <div class="form-group">
          <label>Content:</label>
          <textarea id="editContent" class="form-input" name="content" placeholder="Edit content here..." style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: Arial, sans-serif; resize: vertical;"></textarea>
        </div>
        <div class="form-group" id="editDescriptionGroup" style="display:none;">
          <label>Description:</label>
          <textarea id="editDescription" class="form-input" name="description" placeholder="Edit description here..." style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: Arial, sans-serif; resize: vertical;"></textarea>
        </div>

        <div class="modal-buttons">
          <button type="button" class="modal-button modal-button-secondary" onclick="closeEditModal()">Cancel</button>
          <button type="submit" class="modal-button modal-button-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Delete Confirmation Modal -->
  <div id="deleteConfirmModal" class="modal">
    <div class="modal-content">
      <h2 class="modal-title">Confirm Delete</h2>
      <div class="modal-icon">
        <i class="fas fa-exclamation-triangle" style="color: #f44336; font-size: 50px;"></i>
      </div>
      <p class="modal-message">Are you sure you want to delete this item? This action cannot be undone.</p>
      <div class="modal-buttons">
        <button class="modal-button modal-button-secondary" onclick="closeDeleteModal()">Cancel</button>
        <button class="modal-button modal-button-delete" onclick="confirmDelete()" style="background: #f44336;">
          <i class="fas fa-trash"></i> Delete
        </button>
      </div>
    </div>
  </div>

  <!-- Edit Success Modal -->
  <div id="editSuccessModal" class="modal">
    <div class="modal-content">
      <span class="close-modal" onclick="closeEditSuccessModal()">&times;</span>
      <div class="modal-icon">
        <i class="fas fa-check-circle" style="color: #4CAF50; font-size: 50px;"></i>
      </div>
      <h2 class="modal-title">Success!</h2>
      <p class="modal-message">Your item has been updated successfully.</p>
      <div class="modal-buttons">
        <button class="modal-button modal-button-primary" onclick="closeEditSuccessModalAndReload()" style="background: #4CAF50;">
          <i class="fas fa-check"></i> OK
        </button>
      </div>
    </div>
  </div>

  <script>
    // ==================== Event Delegation for Edit and Delete ====================
    document.addEventListener('DOMContentLoaded', function() {
      // Edit button handlers
      document.querySelectorAll('.btn-edit').forEach(button => {
        button.addEventListener('click', function(e) {
          e.preventDefault();
          const item = this.closest('.published-item');
          const id = item.dataset.id;
          const type = item.dataset.type;
          const content = item.dataset.content;
          const description = item.dataset.description || '';
          // for videos the filename is stored in data-filename; fall back to data-image for compatibility
          const image = item.dataset.filename || item.dataset.image || '';
          
          console.log('Edit clicked:', {id, type, content, description, image});
          openEditModal(id, type, content, image, description);
        });
      });

      // Delete button handlers
      document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function(e) {
          e.preventDefault();
          const item = this.closest('.published-item');
          const id = item.dataset.id;
          const type = item.dataset.type;
          
          console.log('Delete clicked:', {id, type});
          deleteItem(id, type, this);
        });
      });
    });

    // ==================== Edit Modal Functions ====================
    function openEditModal(id, type, content, image, description = '') {
      console.log('Opening edit modal with:', {id, type, content, description, image});
      
      document.getElementById('editId').value = id;
      document.getElementById('editType').value = type;
      const editContentElem = document.getElementById('editContent');
      editContentElem.value = content;
      const descGroup = document.getElementById('editDescriptionGroup');
      const editDescElem = document.getElementById('editDescription');
      if (type === 'video') {
        editContentElem.placeholder = 'Video title';
        descGroup.style.display = 'block';
        editDescElem.value = description || '';
      } else {
        descGroup.style.display = 'none';
        editDescElem.value = '';
        if (type === 'announcement') {
          editContentElem.placeholder = 'Write your announcement here...';
        } else if (type === 'news') {
          editContentElem.placeholder = 'Write your news here...';
        }
      }

      // Display current image if exists
      const currentImageContainer = document.getElementById('currentImageContainer');
      if (image) {
        let filePath = '';
        if (type === 'announcement') {
          filePath = '../uploads/announcements/' + image;
          currentImageContainer.innerHTML = `<img src="${filePath}" style="max-width: 100%; max-height: 200px; border-radius: 5px;">`;
        } else if (type === 'news') {
          filePath = '../uploads/news/' + image;
          currentImageContainer.innerHTML = `<img src="${filePath}" style="max-width: 100%; max-height: 200px; border-radius: 5px;">`;
        } else if (type === 'video') {
          filePath = '../uploads/featured_videos/' + image;
          currentImageContainer.innerHTML = `<video controls style="max-width: 100%; max-height: 200px; border-radius: 5px;"><source src="${filePath}"></video>`;
        }
      } else {
        currentImageContainer.innerHTML = '<p style="color: #999;">No image</p>';
      }

      // adjust drag/drop text and file accept type if video
      const editDragDropArea = document.getElementById('editDragDrop');
      const editFileInput = document.getElementById('editImage');
      if (type === 'video') {
        editDragDropArea.querySelector('p').textContent = 'Drag and drop your video here or click to select';
        editFileInput.accept = 'video/*';
      } else {
        editDragDropArea.querySelector('p').textContent = 'Drag and drop or click to select';
        editFileInput.accept = 'image/*';
      }

      // Show modal
      const modal = document.getElementById('editModal');
      console.log('Modal element:', modal);
      modal.classList.add('show');
      console.log('Modal class after add:', modal.className);
    }

    function closeEditModal() {
      document.getElementById('editModal').classList.remove('show');
      document.getElementById('editForm').reset();
      document.getElementById('currentImageContainer').innerHTML = '';
      document.getElementById('editImagePreview').style.display = 'none';
    }

    function closeEditSuccessModal() {
      document.getElementById('editSuccessModal').classList.remove('show');
    }

    function closeEditSuccessModalAndReload() {
      closeEditSuccessModal();
      location.reload();
    }

    // ==================== Delete Function ====================
    let pendingDeleteItem = null;
    let pendingDeleteButton = null;

    function deleteItem(id, type, button) {
      pendingDeleteItem = {id, type};
      pendingDeleteButton = button;
      document.getElementById('deleteConfirmModal').classList.add('show');
    }

    function closeDeleteModal() {
      document.getElementById('deleteConfirmModal').classList.remove('show');
      pendingDeleteItem = null;
      pendingDeleteButton = null;
    }

    function confirmDelete() {
      if (!pendingDeleteItem || !pendingDeleteButton) return;

      const {id, type} = pendingDeleteItem;
      const formData = new FormData();
      formData.append('id', id);
      formData.append('type', type);

      fetch('delete_item.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Remove item from DOM with animation
          const item = pendingDeleteButton.closest('.published-item');
          item.style.opacity = '0';
          item.style.transform = 'slideOut';
          setTimeout(() => {
            item.remove();
            closeDeleteModal();
            // If no more items, reload page
            const container = pendingDeleteButton.closest('.items-container');
            if (container && container.children.length === 0) {
              location.reload();
            }
          }, 300);
        } else {
          alert('Error: ' + data.message);
          closeDeleteModal();
        }
      })
      .catch(error => {
        console.error('Delete error:', error);
        alert('An error occurred: ' + error.message);
        closeDeleteModal();
      });
    }

    function clearEditImage() {
      document.getElementById('editImage').value = '';
      document.getElementById('editImagePreview').style.display = 'none';
    }

    // Edit Modal Setup
    const editDragDrop = document.getElementById('editDragDrop');
    const editImage = document.getElementById('editImage');
    const editImagePreview = document.getElementById('editImagePreview');

    if (editDragDrop) {
      editDragDrop.addEventListener('dragover', (e) => {
        e.preventDefault();
        editDragDrop.style.backgroundColor = '#f0f0f0';
      });

      editDragDrop.addEventListener('dragleave', () => {
        editDragDrop.style.backgroundColor = 'transparent';
      });

      editDragDrop.addEventListener('drop', (e) => {
        e.preventDefault();
        editDragDrop.style.backgroundColor = 'transparent';
        const files = e.dataTransfer.files;
        if (files.length > 0) {
          editImage.files = files;
          previewEditImage(files[0]);
        }
      });

      editDragDrop.addEventListener('click', () => editImage.click());
    }

    editImage.addEventListener('change', (e) => {
      if (e.target.files.length > 0) {
        previewEditImage(e.target.files[0]);
      }
    });

    function previewEditImage(file) {
      const reader = new FileReader();
      reader.onload = (e) => {
        // if the selected file is a video, display a video element instead
        if (file.type.startsWith('video')) {
          editImagePreview.innerHTML = `<video controls style="max-width:100%; max-height:200px; border-radius:5px;"><source src="${e.target.result}"></video>
            <button type="button" onclick="clearEditImage()" style="margin-top: 5px; padding: 5px 10px; background: #ff6b6b; color: white; border: none; border-radius: 3px; cursor: pointer;">Remove Video</button>`;
        } else {
          const img = document.getElementById('editImagePreviewImg');
          if (img) {
            img.src = e.target.result;
          }
        }
        editImagePreview.style.display = 'block';
      };
      reader.readAsDataURL(file);
    }

    // Edit Form Submission
    document.getElementById('editForm').addEventListener('submit', async (e) => {
      e.preventDefault();

      const content = document.getElementById('editContent').value.trim();
      if (!content) {
        alert('Please enter content');
        return;
      }

      const formData = new FormData(document.getElementById('editForm'));
      console.log('Submitting edit form...');

      try {
        const response = await fetch('edit_item.php', {
          method: 'POST',
          body: formData
        });

        console.log('Response Status:', response.status);
        const result = await response.json();
        console.log('Response Data:', result);

        if (result.success) {
          closeEditModal();
          document.getElementById('editSuccessModal').classList.add('show');
        } else {
          alert('Error: ' + result.message);
        }
      } catch (error) {
        console.error('Fetch Error:', error);
        alert('An error occurred: ' + error.message);
      }
    });

    // ==================== Announcement Functions ====================
    const announcementDragDrop = document.getElementById('announcementDragDrop');
    const announcementImage = document.getElementById('announcementImage');
    const announcementImagePreview = document.getElementById('announcementImagePreview');
    const announcementForm = document.getElementById('announcementForm');

    // Announcement Drag and Drop
    announcementDragDrop.addEventListener('dragover', (e) => {
      e.preventDefault();
      announcementDragDrop.style.backgroundColor = '#f0f0f0';
    });

    announcementDragDrop.addEventListener('dragleave', () => {
      announcementDragDrop.style.backgroundColor = 'transparent';
    });

    announcementDragDrop.addEventListener('drop', (e) => {
      e.preventDefault();
      announcementDragDrop.style.backgroundColor = 'transparent';
      const files = e.dataTransfer.files;
      if (files.length > 0) {
        document.getElementById('announcementLoading').style.display = 'block';
        announcementImage.files = files;
        previewAnnouncementImage(files[0]);
      }
    });

    announcementDragDrop.addEventListener('click', () => announcementImage.click());

    announcementImage.addEventListener('change', (e) => {
      if (e.target.files.length > 0) {
        previewAnnouncementImage(e.target.files[0]);
      }
    });

    function previewAnnouncementImage(file) {
      const reader = new FileReader();
      reader.onload = (e) => {
        document.getElementById('announcementLoading').style.display = 'none';
        document.getElementById('announcementImagePreviewImg').src = e.target.result;
        announcementImagePreview.style.display = 'block';
        announcementDragDrop.style.display = 'none';
      };
      reader.readAsDataURL(file);
    }

    function clearAnnouncementImage() {
      announcementImage.value = '';
      announcementImagePreview.style.display = 'none';
      document.getElementById('announcementLoading').style.display = 'none';
      announcementDragDrop.style.display = 'block';
    }

    // Announcement Form Submission
    announcementForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      
      const textarea = announcementForm.querySelector('textarea[name="announcement"]');
      if (textarea.value.trim() === '') {
        alert('Please write an announcement before publishing.');
        return;
      }

      const formData = new FormData(announcementForm);
      
      try {
        const response = await fetch('publish_announcement.php', {
          method: 'POST',
          body: formData
        });

        const text = await response.text();
        let result;
        try {
          result = JSON.parse(text);
        } catch (parseErr) {
          console.error('Publish announcement returned non-JSON', text);
          throw new Error('Server returned unexpected response');
        }

        if (result.success) {
          showModal('Announcement Published!', 'Your announcement has been published successfully.', result.data);
          announcementForm.reset();
          clearAnnouncementImage();
        } else {
          alert('Error: ' + result.message);
        }
      } catch (error) {
        alert('An error occurred: ' + error.message);
      }
    });

    // ==================== News Functions ====================
    const newsDragDrop = document.getElementById('newsDragDrop');
    const newsImage = document.getElementById('newsImage');
    const newsImagePreview = document.getElementById('newsImagePreview');
    const newsForm = document.getElementById('newsForm');

    // News Drag and Drop
    newsDragDrop.addEventListener('dragover', (e) => {
      e.preventDefault();
      newsDragDrop.style.backgroundColor = '#f0f0f0';
    });

    newsDragDrop.addEventListener('dragleave', () => {
      newsDragDrop.style.backgroundColor = 'transparent';
    });

    newsDragDrop.addEventListener('drop', (e) => {
      e.preventDefault();
      newsDragDrop.style.backgroundColor = 'transparent';
      const files = e.dataTransfer.files;
      if (files.length > 0) {
        document.getElementById('newsLoading').style.display = 'block';
        newsImage.files = files;
        previewNewsImage(files[0]);
      }
    });

    newsDragDrop.addEventListener('click', () => newsImage.click());

    newsImage.addEventListener('change', (e) => {
      if (e.target.files.length > 0) {
        previewNewsImage(e.target.files[0]);
      }
    });

    function previewNewsImage(file) {
      const reader = new FileReader();
      reader.onload = (e) => {
        document.getElementById('newsLoading').style.display = 'none';
        if (file.type.startsWith('video')) {
          // replace img tag with video element for preview
          editImagePreview.innerHTML = `<video controls style="max-width:100%; max-height:200px; border-radius:5px;"><source src="${e.target.result}"></video>
            <button type="button" onclick="clearEditImage()" style="margin-top: 5px; padding: 5px 10px; background: #ff6b6b; color: white; border: none; border-radius: 3px; cursor: pointer;">Remove Video</button>`;
        } else {
          document.getElementById('editImagePreviewImg').src = e.target.result;
          editImagePreview.innerHTML += `<button type="button" onclick="clearEditImage()" style="margin-top: 5px; padding: 5px 10px; background: #ff6b6b; color: white; border: none; border-radius: 3px; cursor: pointer;">Remove Image</button>`;
        }
        newsImagePreview.style.display = 'block';
        newsDragDrop.style.display = 'none';
      };
      reader.readAsDataURL(file);
    }

    function clearNewsImage() {
      newsImage.value = '';
      newsImagePreview.style.display = 'none';
      document.getElementById('newsLoading').style.display = 'none';
      newsDragDrop.style.display = 'block';
    }

    // News Form Submission
    newsForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      
      const textarea = newsForm.querySelector('textarea[name="news"]');
      if (textarea.value.trim() === '') {
        alert('Please write news before publishing.');
        return;
      }

      const formData = new FormData(newsForm);
      
      try {
        const response = await fetch('publish_news.php', {
          method: 'POST',
          body: formData
        });

        const text = await response.text();
        let result;
        try {
          result = JSON.parse(text);
        } catch (parseErr) {
          console.error('Publish news returned non-JSON', text);
          throw new Error('Server returned unexpected response');
        }

        if (result.success) {
          showModal('News Published!', 'Your news has been published successfully.', result.data);
          newsForm.reset();
          clearNewsImage();
        } else {
          alert('Error: ' + result.message);
        }
      } catch (error) {
        alert('An error occurred: ' + error.message);
      }
    });

    // ==================== Video Functions ====================
    const videoDragDrop = document.getElementById('videoDragDrop');
    const videoFile = document.getElementById('videoFile');
    const videoPreview = document.getElementById('videoPreview');
    const videoForm = document.getElementById('videoForm');
    const fileUploadSection = document.getElementById('fileUploadSection');
    const urlInputSection = document.getElementById('urlInputSection');
    const videoMethodRadios = document.querySelectorAll('input[name="video_method"]');

    // Handle video method selection (upload vs URL)
    videoMethodRadios.forEach(radio => {
      radio.addEventListener('change', (e) => {
        if (e.target.value === 'upload') {
          fileUploadSection.style.display = 'block';
          urlInputSection.style.display = 'none';
          clearVideoUrl();
          // Ensure file upload requirements are set
        } else {
          fileUploadSection.style.display = 'none';
          urlInputSection.style.display = 'block';
          clearVideo();
        }
      });
    });

    // Video Drag and Drop
    videoDragDrop.addEventListener('dragover', (e) => {
      e.preventDefault();
      videoDragDrop.style.backgroundColor = '#f0f0f0';
    });

    videoDragDrop.addEventListener('dragleave', () => {
      videoDragDrop.style.backgroundColor = 'transparent';
    });

    videoDragDrop.addEventListener('drop', (e) => {
      e.preventDefault();
      videoDragDrop.style.backgroundColor = 'transparent';
      const files = e.dataTransfer.files;
      if (files.length > 0) {
        document.getElementById('videoLoading').style.display = 'block';
        videoFile.files = files;
        previewVideo(files[0]);
      }
    });

    videoDragDrop.addEventListener('click', () => videoFile.click());

    videoFile.addEventListener('change', (e) => {
      if (e.target.files.length > 0) {
        previewVideo(e.target.files[0]);
      }
    });

    // Handle video URL input and preview
    const videoUrlInput = document.getElementById('videoUrl');
    const urlPreview = document.getElementById('urlPreview');
    const urlPreviewFrame = document.getElementById('urlPreviewFrame');

    // Function to convert video URLs to embed URLs
    function convertToEmbedUrl(url) {
      // YouTube - youtu.be format
      if (/youtu\.be\/([a-zA-Z0-9_-]{11})/.test(url)) {
        const match = url.match(/youtu\.be\/([a-zA-Z0-9_-]{11})/);
        return 'https://www.youtube.com/embed/' + match[1];
      }
      
      // YouTube - youtube.com format
      if (/youtube\.com\/watch\?v=([a-zA-Z0-9_-]{11})/.test(url)) {
        const match = url.match(/youtube\.com\/watch\?v=([a-zA-Z0-9_-]{11})/);
        return 'https://www.youtube.com/embed/' + match[1];
      }
      
      // YouTube - youtube.com/embed format (already embed)
      if (url.includes('youtube.com/embed/')) {
        return url;
      }
      
      // Vimeo
      if (/vimeo\.com\/(\d+)/.test(url)) {
        const match = url.match(/vimeo\.com\/(\d+)/);
        return 'https://player.vimeo.com/video/' + match[1];
      }
      
      // Vimeo - already embed format
      if (url.includes('player.vimeo.com/video/')) {
        return url;
      }
      
      // Google Drive - drive.google.com/file/d/FILE_ID/view format
      if (/drive\.google\.com\/file\/d\/([a-zA-Z0-9_-]+)/.test(url)) {
        const match = url.match(/drive\.google\.com\/file\/d\/([a-zA-Z0-9_-]+)/);
        return 'https://drive.google.com/file/d/' + match[1] + '/preview';
      }
      
      // Google Drive - already embed format (preview)
      if (url.includes('drive.google.com/file/d/') && url.includes('/preview')) {
        return url;
      }
      
      // Facebook - video URL
      if (url.includes('facebook.com') || url.includes('fb.watch')) {
        // Use Facebook's embed format
        return 'https://www.facebook.com/plugins/video.php?href=' + encodeURIComponent(url);
      }
      
      // Return original URL if not recognized
      return url;
    }

    videoUrlInput.addEventListener('input', (e) => {
      const url = e.target.value.trim();
      if (url) {
        // Convert URL to embed format and display
        const embedUrl = convertToEmbedUrl(url);
        urlPreviewFrame.src = embedUrl;
        urlPreview.style.display = 'block';
      } else {
        urlPreview.style.display = 'none';
      }
    });

    function clearVideoUrl() {
      videoUrlInput.value = '';
      urlPreview.style.display = 'none';
      urlPreviewFrame.src = '';
    }

    function previewVideo(file) {
      const reader = new FileReader();
      reader.onload = (e) => {
        document.getElementById('videoLoading').style.display = 'none';
        document.getElementById('videoPreviewElem').src = e.target.result;
        videoPreview.style.display = 'block';
        videoDragDrop.style.display = 'none';
      };
      reader.readAsDataURL(file);
    }

    function clearVideo() {
      videoFile.value = '';
      videoPreview.style.display = 'none';
      document.getElementById('videoLoading').style.display = 'none';
      videoDragDrop.style.display = 'block';
    }

    videoForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const titleInput = videoForm.querySelector('input[name="title"]');
      if (titleInput.value.trim() === '') {
        alert('Please enter a title for the video.');
        return;
      }

      const selectedMethod = document.querySelector('input[name="video_method"]:checked').value;

      if (selectedMethod === 'upload') {
        // Handle file upload
        const fileInput = videoForm.querySelector('input[name="video"]');
        if (fileInput.files.length === 0) {
          alert('Please select a video file to upload.');
          return;
        }
        // client-side size check to avoid 413s when server limits are low
        const selectedSize = fileInput.files[0].size;
        const serverMax = <?php echo $videoMaxBytes; ?>;
        if (selectedSize > serverMax) {
          alert('Selected file is larger than the server limit of <?php echo $videoMaxHuman; ?>.\n' +
                'Increase upload_max_filesize/post_max_size in php.ini or choose a smaller file.');
          return;
        }

        const formData = new FormData(videoForm);
        formData.append('video_method', 'upload');

        // show progress bar
        const progressWrapper = document.getElementById('videoProgress');
        const progressBar = document.getElementById('videoProgressBar');
        const progressText = document.getElementById('videoProgressText');
        progressWrapper.style.display = 'block';
        progressBar.style.width = '0%';
        progressText.textContent = '0%';

        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'publish_video.php');
        xhr.upload.addEventListener('progress', (evt) => {
          if (evt.lengthComputable) {
            const percent = Math.round((evt.loaded / evt.total) * 100);
            progressBar.style.width = percent + '%';
            progressText.textContent = percent + '%';
          }
        });
        xhr.onreadystatechange = () => {
          if (xhr.readyState === XMLHttpRequest.DONE) {
            progressWrapper.style.display = 'none';
            if (xhr.status === 200) {
              const text = xhr.responseText;
              let result;
              try {
                result = JSON.parse(text);
              } catch (e) {
                console.error('Publish video returned non-JSON', text);
                alert('Server returned unexpected response');
                return;
              }
              if (result.success) {
                showModal('Video Uploaded!', 'Your featured video has been uploaded successfully.', result.data);
                videoForm.reset();
                clearVideo();
                // Reset radio button to upload
                document.querySelector('input[name="video_method"][value="upload"]').checked = true;
                fileUploadSection.style.display = 'block';
                urlInputSection.style.display = 'none';
              } else {
                alert('Error: ' + result.message);
              }
            } else {
              if (xhr.status === 413) {
                alert('Upload failed: the file is too large for the server configuration.\n' +
                      'Increase upload_max_filesize/post_max_size in php.ini or use smaller file.\n' +
                      'Server limit is <?php echo $videoMaxHuman; ?>.');
              } else {
                alert('Upload failed with status ' + xhr.status);
              }
            }
          }
        };
        xhr.send(formData);
      } else {
        // Handle URL submission
        const urlInput = document.getElementById('videoUrl');
        if (!urlInput.value.trim()) {
          alert('Please enter a video URL.');
          return;
        }

        const formData = new FormData(videoForm);
        formData.append('video_method', 'url');

        fetch('publish_video.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(result => {
          if (result.success) {
            showModal('Video Added!', 'Your featured video URL has been added successfully.', result.data);
            videoForm.reset();
            clearVideoUrl();
            // Reset radio button to upload
            document.querySelector('input[name="video_method"][value="upload"]').checked = true;
            fileUploadSection.style.display = 'block';
            urlInputSection.style.display = 'none';
          } else {
            alert('Error: ' + result.message);
          }
        })
        .catch(error => {
          alert('An error occurred: ' + error.message);
        });
      }
    });

    // ==================== Modal Functions ====================
    function showModal(title, message, data) {
      document.getElementById('modalTitle').textContent = title;
      document.getElementById('modalMessage').textContent = message;
      
      // Show image preview if image exists
      const imageContainer = document.getElementById('modalImageContainer');
      if (data.image) {
        const imagePath = title.includes('Announcement') ? '../uploads/announcements/' + data.image : '../uploads/news/' + data.image;
        document.getElementById('modalImage').src = imagePath;
        imageContainer.style.display = 'block';
      } else {
        imageContainer.style.display = 'none';
      }

      // Show video preview if filename exists
      const videoContainer = document.getElementById('modalVideoContainer');
      if (data.filename) {
        const videoPath = '../uploads/featured_videos/' + data.filename;
        document.getElementById('modalVideo').src = videoPath;
        videoContainer.style.display = 'block';
      } else {
        videoContainer.style.display = 'none';
      }

      // Show content preview
      const contentContainer = document.getElementById('modalContentContainer');
      document.getElementById('modalContent').textContent = data.content || data.title || '';
      contentContainer.style.display = 'block';

      document.getElementById('publishModal').classList.add('show');
    }

    function closeModal() {
      document.getElementById('publishModal').classList.remove('show');
      // Clear modal content
      document.getElementById('modalImageContainer').style.display = 'none';
      document.getElementById('modalContentContainer').style.display = 'none';
    }

    // Close modal when clicking outside of it
    window.onclick = function(event) {
      const publishModal = document.getElementById('publishModal');
      const editModal = document.getElementById('editModal');
      const deleteModal = document.getElementById('deleteConfirmModal');
      const successModal = document.getElementById('editSuccessModal');

      if (event.target === publishModal) {
        closeModal();
      }
      if (event.target === editModal) {
        closeEditModal();
      }
      if (event.target === deleteModal) {
        closeDeleteModal();
      }
      if (event.target === successModal) {
        closeEditSuccessModal();
      }
    }
  </script>

</body>
</html>
