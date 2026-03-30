<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Resources - RMNHS Admin</title>
  <link rel="stylesheet" href="../memorandum/memo.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>


<?php include '../admin-navbar/navbar.php'; ?>



  <!-- Main Content -->
  <main class="main-content">
    <div class="resources-container">
      <div class="page-header">
        <h1>School Memorandum</h1>
      </div>

      <!-- Upload Form Section -->
      <div class="upload-form-container">
        <h2>Add New Document</h2>
        <form id="uploadForm" class="upload-form">
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
              <label for="docFile">Upload File :</label>
              <div class="file-input-wrapper">
                <input type="file" id="docFile" name="file" accept=".pdf" class="file-input-hidden">
                <label for="docFile" class="file-label" id="docFileLabel">Choose file</label>
                <span class="file-name" id="docFileName">No file chosen</span>
              </div>
            </div>

            <button type="submit" class="upload-btn">Upload</button>
          </div>
        </form>
      </div>

      <!-- Search Bar Section -->
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
            <!-- Data will be loaded here from database -->
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
      <form id="editForm" class="modal-form">
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
            <label for="editFile">Upload File (Optional) :</label>
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
        <p>Are you sure you want to delete this memorandum? This action cannot be undone.</p>
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

  <!-- Footer -->
  <footer class="footer">
    <div class="footer-content">
      <div class="footer-section footer-logos">
        <div class="logos-grid">
          <img src="../../footer/rectologo.png" alt="Recto Logo" class="footer-logo">
          <img src="../../footer/depedquezon.png" alt="DepED Quezon" class="footer-logo">
          <img src="../../footer/bagongpilipinas.png" alt="Bagong Pilipinas" class="footer-logo">
          <img src="../../footer/schoolseal.png" alt="School Seal" class="footer-logo">
        </div>
      </div>

      <div class="footer-section footer-facilities">
        <h3>Our facilities</h3>
        <ul>
          <li><a href="#">Libraries</a></li>
          <li><a href="#">Conferences</a></li>
          <li><a href="#">Research</a></li>
          <li><a href="#">IT support</a></li>
          <li><a href="#">Sport</a></li>
        </ul>
      </div>

      <div class="footer-section footer-contact">
        <h3>Contact Us</h3>
        <h4>RECTO MEMORIAL NATIONAL HIGH SCHOOL</h4>
        <p class="address">X85C+R5C, Tiaong, Quezon Province</p>
        <p class="phone">0949 995 1769</p>
        <div class="social-icons">
          <a href="https://www.facebook.com/TheRectorianPress" target="_blank" title="Facebook">
            <i class="fab fa-facebook-f"></i>
          </a>
          <a href="https://mail.google.com" target="_blank" title="Gmail">
            <i class="fas fa-envelope"></i>
          </a>
          <a href="https://maps.google.com/?q=X85C+R5C,+Tiaong,+Quezon+Province" target="_blank" title="Google Maps">
            <i class="fas fa-location-dot"></i>
          </a>
        </div>
      </div>
    </div>

    <div class="footer-bottom">
      <p>&copy;2026 All Rights Reserved</p>
    </div>
  </footer>

  <script>
    let editingId = null;
    let deletingId = null;
    
    // Load memorandums on page load
    document.addEventListener('DOMContentLoaded', function() {
      loadMemorandums();
      setupFormListener();
      setupEditFormListener();
      setupModalBackdropClose();
      setupFileInputListeners();
    });

    // Setup file input listeners
    function setupFileInputListeners() {
      const docFileInput = document.getElementById('docFile');
      const editFileInput = document.getElementById('editFile');
      
      if (docFileInput) {
        docFileInput.addEventListener('change', function() {
          const fileName = this.files.length > 0 ? this.files[0].name : 'No file chosen';
          document.getElementById('docFileName').textContent = fileName;
        });
      }
      
      if (editFileInput) {
        editFileInput.addEventListener('change', function() {
          const fileName = this.files.length > 0 ? this.files[0].name : 'No file chosen';
          document.getElementById('editFileName').textContent = fileName;
        });
      }
    }

    // Setup modal backdrop close
    function setupModalBackdropClose() {
      const editModal = document.getElementById('editModal');
      const deleteModal = document.getElementById('deleteModal');
      
      editModal.addEventListener('click', function(e) {
        if (e.target === editModal) {
          closeEditModal();
        }
      });
      
      deleteModal.addEventListener('click', function(e) {
        if (e.target === deleteModal) {
          closeDeleteModal();
        }
      });
    }

    // Modal functions
    function openEditModal() {
      document.getElementById('editModal').classList.add('show');
    }

    function closeEditModal() {
      document.getElementById('editModal').classList.remove('show');
      document.getElementById('editForm').reset();
      editingId = null;
    }

    function openDeleteModal() {
      document.getElementById('deleteModal').classList.add('show');
    }

    function closeDeleteModal() {
      document.getElementById('deleteModal').classList.remove('show');
      deletingId = null;
    }

    let successTimer;

    function openSuccessModal(message) {
      const modal = document.getElementById('successModal');
      document.getElementById('successMessage').textContent = message;
      modal.classList.add('show');
      clearTimeout(successTimer);
      successTimer = setTimeout(closeSuccessModal, 2000);
    }

    function closeSuccessModal() {
      const modal = document.getElementById('successModal');
      modal.classList.remove('show');
      clearTimeout(successTimer);
    }

    // Load all memorandums from database
    function loadMemorandums() {
      fetch('load_memo.php')
        .then(response => {
          if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.status);
          }
          return response.text();
        })
        .then(text => {
          try {
            const data = JSON.parse(text);
            if (data.success) {
              displayMemorandums(data.data);
            } else {
              console.error('Data error:', data.message);
              document.getElementById('tableBody').innerHTML = '<tr><td colspan="5" style="text-align: center; color: red;">Error: ' + data.message + '</td></tr>';
            }
          } catch (error) {
            console.error('JSON Parse error:', error);
            console.error('Response was:', text);
            document.getElementById('tableBody').innerHTML = '<tr><td colspan="5" style="text-align: center; color: red;">Error parsing server response</td></tr>';
          }
        })
        .catch(error => {
          console.error('Error loading memorandums:', error);
          document.getElementById('tableBody').innerHTML = '<tr><td colspan="5" style="text-align: center; color: red;">Error loading data</td></tr>';
        });
    }

    // Display memorandums in table
    function displayMemorandums(memorandums) {
      const tableBody = document.getElementById('tableBody');
      tableBody.innerHTML = '';

      if (memorandums.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="5" style="text-align: center;">No memorandums found</td></tr>';
        return;
      }

      memorandums.forEach(memo => {
        const row = document.createElement('tr');
        const dateObj = new Date(memo.date);
        const formattedDate = (dateObj.getMonth() + 1) + '-' + dateObj.getDate() + '-' + dateObj.getFullYear();
        
        let fileCell = '<td>-</td>';
        if (memo.file) {
          fileCell = `<td><a href="../../uploads/memorandum/${memo.file}" class="file-link" target="_blank">View</a></td>`;
        }

        row.innerHTML = `
          <td>${formattedDate}</td>
          <td>${memo.title}</td>
          <td>${memo.description || '-'}</td>
          ${fileCell}
          <td>
            <button class="btn-edit" onclick="editMemo(${memo.id})">Edit</button>
            <button class="btn-delete" onclick="deleteMemo(${memo.id})">Delete</button>
          </td>
        `;
        tableBody.appendChild(row);
      });

      // Re-setup edit buttons with updated listeners
      setupEditButtons();
    }

    // Setup form submission for add new
    function setupFormListener() {
      const form = document.getElementById('uploadForm');
      form.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(form);
        formData.append('action', 'add');

        // Show loading state
        const submitBtn = form.querySelector('.upload-btn');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Uploading...';

        fetch('save_memo.php', {
          method: 'POST',
          body: formData
        })
        .then(response => {
          if (!response.ok) {
            throw new Error('Network response was not ok');
          }
          return response.json();
        })
        .then(data => {
          submitBtn.disabled = false;
          submitBtn.textContent = originalText;

          if (data.success) {
            openSuccessModal(data.message);
            form.reset();
            loadMemorandums();
            setupSearch();
          } else {
            alert('Error: ' + data.message);
          }
        })
        .catch(error => {
          submitBtn.disabled = false;
          submitBtn.textContent = originalText;
          console.error('Error:', error);
          alert('Error uploading memorandum');
        });
      });
    }

    // Setup form submission for edit (in modal)
    function setupEditFormListener() {
      const form = document.getElementById('editForm');
      if (!form) return;
      
      form.addEventListener('submit', function(e) {
        e.preventDefault();

        if (!editingId) {
          alert('No memorandum selected for editing');
          return;
        }

        const formData = new FormData(form);
        formData.append('action', 'update');
        formData.append('id', editingId);

        // Show loading state
        const submitBtn = form.querySelector('.btn-save');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Saving...';

        fetch('save_memo.php', {
          method: 'POST',
          body: formData
        })
        .then(response => {
          if (!response.ok) {
            throw new Error('Network response was not ok');
          }
          return response.text();
        })
        .then(text => {
          submitBtn.disabled = false;
          submitBtn.textContent = originalText;

          try {
            const data = JSON.parse(text);
            if (data.success) {
              openSuccessModal(data.message);
              closeEditModal();
              loadMemorandums();
              setupSearch();
            } else {
              alert('Error: ' + data.message);
            }
          } catch (error) {
            console.error('JSON Parse error:', error);
            console.error('Response was:', text);
            alert('Error parsing server response');
          }
        })
        .catch(error => {
          submitBtn.disabled = false;
          submitBtn.textContent = originalText;
          console.error('Error:', error);
          alert('Error updating memorandum');
        });
      });
    }

    // Edit memorandum
    function editMemo(id) {
      // Ensure id is a number
      id = parseInt(id);
      
      fetch('load_memo.php')
        .then(response => {
          if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.status);
          }
          return response.text();
        })
        .then(text => {
          try {
            const data = JSON.parse(text);
            console.log('Loaded data:', data);
            console.log('Looking for ID:', id);
            
            if (data.success) {
              const memo = data.data.find(m => parseInt(m.id) === id);
              console.log('Found memo:', memo);
              
              if (memo) {
                document.getElementById('editTitle').value = memo.title;
                document.getElementById('editDate').value = memo.date;
                document.getElementById('editDescription').value = memo.description || '';
                
                editingId = id;
                openEditModal();
              } else {
                console.error('Memo not found. Available IDs:', data.data.map(m => m.id));
                alert('Memorandum not found');
              }
            } else {
              alert('Error: ' + data.message);
            }
          } catch (error) {
            console.error('JSON Parse error:', error);
            console.error('Response was:', text);
            alert('Error parsing server response');
          }
        })
        .catch(error => {
          console.error('Error loading memo:', error);
          alert('Error loading memorandum details');
        });
    }

    // Delete memorandum
    function deleteMemo(id) {
      deletingId = id;
      openDeleteModal();
    }

    // Confirm delete
    function confirmDelete() {
      if (!deletingId) return;

      const formData = new FormData();
      formData.append('action', 'delete');
      formData.append('id', deletingId);

      fetch('save_memo.php', {
        method: 'POST',
        body: formData
      })
      .then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.json();
      })
      .then(data => {
        if (data.success) {
          openSuccessModal(data.message);
          closeDeleteModal();
          loadMemorandums();
          setupSearch();
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Error deleting memorandum');
      });
    }

    // Setup edit buttons
    function setupEditButtons() {
      // No longer needed with modals
    }

    // Search functionality
    function setupSearch() {
      const searchInput = document.getElementById('searchInput');
      const table = document.querySelector('.resources-table tbody');

      if (searchInput) {
        searchInput.addEventListener('keyup', function() {
          const query = this.value.toLowerCase();
          const rows = table.querySelectorAll('tr');

          rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(query) ? '' : 'none';
          });
        });
      }
    }

    document.addEventListener('DOMContentLoaded', setupSearch);
  </script>

</body>
</html>