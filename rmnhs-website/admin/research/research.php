
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>RMNHS Admin Dashboard</title>
  <link rel="stylesheet" href="../research/research.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>
<body>
<?php include '../admin-navbar/navbar.php'; ?>
  

  <!-- Main Content -->
  <main class="main-content">
    <div class="research-container">
      <h1 class="section-title">Research Bulletin</h1>
      
      <div class="search-section">
        <h2 class="search-title">Create New Research</h2>
        
        <form class="search-form" id="researchForm">
          <div class="form-group">
            <label for="research-title">Title of the Research</label>
            <input type="text" id="research-title" name="title" placeholder="Enter research title" required>
          </div>

          <div class="form-group">
            <label for="grade-level">Grade Level</label>
            <select id="grade-level" name="grade_level" required>
              <option value="">Select Grade Level</option>
              <option value="grade-7">Grade 7</option>
              <option value="grade-8">Grade 8</option>
              <option value="grade-9">Grade 9</option>
              <option value="grade-10">Grade 10</option>
              <option value="grade-11">Grade 11</option>
              <option value="grade-12">Grade 12</option>
            </select>
          </div>

          <div class="form-group">
            <label for="department">Department</label>
            <select id="department" name="department" required>
              <option value="">Select Department</option>
              <option value="science">Science</option>
              <option value="mathematics">Mathematics</option>
              <option value="english">English</option>
              <option value="social-studies">Social Studies</option>
              <option value="technology">Technology</option>
              <option value="arts">Arts</option>
            </select>
          </div>

          <div class="form-group">
            <label for="year-publication">Year of Publication</label>
            <select id="year-publication" name="year_publication" required>
              <option value="">Select Year</option>
              <option value="2026">2026</option>
              <option value="2025">2025</option>
              <option value="2024">2024</option>
              <option value="2023">2023</option>
              <option value="2022">2022</option>
              <option value="2021">2021</option>
            </select>
          </div>

          <div class="form-group">
            <label for="research-category">Research Category / Field</label>
            <input type="text" id="research-category" name="research_category" placeholder="Enter category (e.g., Game, STEM, etc.)" required>
          </div>

          <div class="form-group">
            <label for="research-image">Upload Picture</label>
            <div class="file-upload-container">
              <div class="drag-drop-area" id="dragDropImageArea">
                <button type="button" class="browse-btn" onclick="document.getElementById('research-image').click()"></button>
                <span id="imageNameDisplay">| No file chosen</span>
              </div>
              <input type="file" id="research-image" name="image" accept=".jpg,.jpeg,.png,.gif" style="display: none;">
            </div>
          </div>

          <div class="form-group">
            <label for="research-file">Upload File</label>
            <div class="file-upload-container">
              <div class="drag-drop-area" id="dragDropArea">
                <button type="button" class="browse-btn" onclick="document.getElementById('research-file').click()"></button>
                <span id="fileNameDisplay">| No file chosen</span>
              </div>
              <input type="file" id="research-file" name="file" style="display: none;" accept=".pdf">
            </div>
          </div>

          <div class="form-actions">
            <button type="submit" class="btn-search" id="submitBtn">Create New</button>
            <button type="reset" class="btn-reset" id="resetBtn">Reset</button>
            <button type="button" class="btn-reset" id="cancelEditBtn" style="display: none;" onclick="cancelEdit()">Cancel Edit</button>
          </div>
        </form>
      </div>

      <!-- Preview Modal -->
      <div id="previewModal" class="modal" style="display: none;">
        <div class="modal-content">
          <span class="modal-close" onclick="closePreviewModal()">&times;</span>
          <h2 class="modal-title">Preview Research</h2>
          <div id="previewContent" class="preview-info">
            <div class="preview-item">
              <label>Title:</label>
              <p id="previewTitle"></p>
            </div>
            <div class="preview-item">
              <label>Grade Level:</label>
              <p id="previewGrade"></p>
            </div>
            <div class="preview-item">
              <label>Department:</label>
              <p id="previewDept"></p>
            </div>
            <div class="preview-item">
              <label>Year:</label>
              <p id="previewYear"></p>
            </div>
            <div class="preview-item">
              <label>Category:</label>
              <p id="previewCategory"></p>
            </div>
            <div class="preview-item">
              <label>Picture:</label>
              <img id="previewImage" src="" alt="Research" style="max-width: 200px; margin-top: 10px;">
            </div>
            <div class="preview-item" id="previewFileItem" style="display: none;">
              <label>PDF File:</label>
              <iframe id="previewPDF" src="" style="width: 100%; height: 400px; border: 1px solid #ddd; border-radius: 4px; margin-top: 10px;"></iframe>
            </div>
          </div>
          <div class="modal-actions">
            <button type="button" class="btn-save" onclick="submitResearch()">Save</button>
            <button type="button" class="btn-cancel" onclick="closePreviewModal()">Cancel</button>
          </div>
        </div>
      </div>

      <!-- Research Items List -->
      <div class="research-items" id="researchList" style="margin-top: 40px;">
        <!-- Populated by JavaScript -->
      </div>
    </div>
  </main>


  <script>
    let currentFormData = null;
    let imagePreviewUrl = null;
    let isEditMode = false;
    let editingId = null;

    // Handle image drag and drop
    const dragDropImageArea = document.getElementById('dragDropImageArea');
    const imageInput = document.getElementById('research-image');
    const imageNameDisplay = document.getElementById('imageNameDisplay');

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
      dragDropImageArea.addEventListener(eventName, preventDefaults, false);
    });

    ['dragenter', 'dragover'].forEach(eventName => {
      dragDropImageArea.addEventListener(eventName, () => {
        dragDropImageArea.style.backgroundColor = '#e0e0e0';
      });
    });

    ['dragleave', 'drop'].forEach(eventName => {
      dragDropImageArea.addEventListener(eventName, () => {
        dragDropImageArea.style.backgroundColor = 'transparent';
      });
    });

    dragDropImageArea.addEventListener('drop', (e) => {
      const dt = e.dataTransfer;
      const files = dt.files;
      imageInput.files = files;
      updateImageDisplay(files);
    });

    imageInput.addEventListener('change', (e) => {
      updateImageDisplay(e.target.files);
    });

    function updateImageDisplay(files) {
      if (files.length > 0) {
        imageNameDisplay.textContent = '| ' + files[0].name;
        
        // Create preview URL
        const reader = new FileReader();
        reader.onload = (e) => {
          imagePreviewUrl = e.target.result;
        };
        reader.readAsDataURL(files[0]);
      } else {
        imageNameDisplay.textContent = '| No file chosen';
        imagePreviewUrl = null;
      }
    }

    // Handle drag and drop
    const dragDropArea = document.getElementById('dragDropArea');
    const fileInput = document.getElementById('research-file');
    const fileNameDisplay = document.getElementById('fileNameDisplay');

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
      dragDropArea.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
      e.preventDefault();
      e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
      dragDropArea.addEventListener(eventName, () => {
        dragDropArea.style.backgroundColor = '#e0e0e0';
      });
    });

    ['dragleave', 'drop'].forEach(eventName => {
      dragDropArea.addEventListener(eventName, () => {
        dragDropArea.style.backgroundColor = 'transparent';
      });
    });

    dragDropArea.addEventListener('drop', (e) => {
      const dt = e.dataTransfer;
      const files = dt.files;
      fileInput.files = files;
      updateFileDisplay(files);
    });

    fileInput.addEventListener('change', (e) => {
      updateFileDisplay(e.target.files);
    });

    function updateFileDisplay(files) {
      if (files.length > 0) {
        fileNameDisplay.textContent = '| ' + files[0].name;
      } else {
        fileNameDisplay.textContent = '| No file chosen';
      }
    }

    // Handle form submission
    document.getElementById('researchForm').addEventListener('submit', (e) => {
      e.preventDefault();
      
      const formData = {
        title: document.getElementById('research-title').value,
        grade: document.getElementById('grade-level').value,
        department: document.getElementById('department').value,
        year: document.getElementById('year-publication').value,
        category: document.getElementById('research-category').value,
        image: imageInput.files[0] || null,
        file: fileInput.files[0] || null
      };

      currentFormData = formData;
      
      // Show preview modal
      const previewModal = document.getElementById('previewModal');
      document.getElementById('previewTitle').textContent = formData.title;
      document.getElementById('previewGrade').textContent = formData.grade.replace('grade-', 'Grade ') + ' Grade';
      document.getElementById('previewDept').textContent = formData.department.charAt(0).toUpperCase() + formData.department.slice(1) + ' Department';
      document.getElementById('previewYear').textContent = formData.year;
      document.getElementById('previewCategory').textContent = formData.category;
      
      // Update preview image
      if (imagePreviewUrl) {
        document.getElementById('previewImage').src = imagePreviewUrl;
      } else {
        document.getElementById('previewImage').style.display = 'none';
      }
      
      // Update preview PDF
      if (formData.file) {
        const pdfUrl = URL.createObjectURL(formData.file);
        document.getElementById('previewPDF').src = pdfUrl;
        document.getElementById('previewFileItem').style.display = 'block';
      } else {
        document.getElementById('previewFileItem').style.display = 'none';
      }
      
      previewModal.style.display = 'block';
    });

    function submitResearch() {
      if (!currentFormData) return;

      const formDataToSend = new FormData();
      formDataToSend.append('title', currentFormData.title);
      formDataToSend.append('grade', currentFormData.grade);
      formDataToSend.append('department', currentFormData.department);
      formDataToSend.append('year', currentFormData.year);
      formDataToSend.append('category', currentFormData.category);
      if (isEditMode && editingId) {
        formDataToSend.append('id', editingId);
        formDataToSend.append('is_update', '1');
      }
      if (currentFormData.image) {
        formDataToSend.append('image', currentFormData.image);
      }
      if (currentFormData.file) {
        formDataToSend.append('file', currentFormData.file);
      }

      fetch('save_research.php', {
        method: 'POST',
        body: formDataToSend
      })
      .then(response => {
        if (!response.ok) {
          throw new Error('HTTP error, status = ' + response.status);
        }
        return response.text().then(text => {
          try {
            return JSON.parse(text);
          } catch (e) {
            console.error('Invalid JSON response:', text);
            throw new Error('Invalid JSON response from server: ' + text);
          }
        });
      })
      .then(data => {
        if (data.success) {
          const message = isEditMode ? 'Research updated successfully!' : 'Research created successfully!';
          alert(message);
          document.getElementById('researchForm').reset();
          document.getElementById('previewModal').style.display = 'none';
          fileNameDisplay.textContent = '| No file chosen';
          imageNameDisplay.textContent = '| No file chosen';
          imagePreviewUrl = null;
          cancelEdit();
          loadResearchList();
        } else {
          alert('Error: ' + (data.message || 'Unknown error'));
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Error saving research: ' + error.message);
      });
    }

    function editResearch(id) {
      fetch('load_research.php')
        .then(response => {
          if (!response.ok) {
            throw new Error('HTTP error, status = ' + response.status);
          }
          return response.text().then(text => {
            try {
              return JSON.parse(text);
            } catch (e) {
              console.error('Invalid JSON response:', text);
              throw new Error('Invalid JSON response from server');
            }
          });
        })
        .then(data => {
          const item = data.find(r => r.id === id);
          if (item) {
            // Populate form with existing data
            document.getElementById('research-title').value = item.title;
            document.getElementById('grade-level').value = item.grade;
            document.getElementById('department').value = item.department;
            document.getElementById('year-publication').value = item.year;
            document.getElementById('research-category').value = item.category;
            
            // Set edit mode
            isEditMode = true;
            editingId = item.id;
            
            // Update UI
            document.getElementById('submitBtn').textContent = 'Update';
            document.getElementById('submitBtn').className = 'btn-search';
            document.getElementById('cancelEditBtn').style.display = 'inline-block';
            document.querySelector('.search-title').textContent = 'Edit Research';
            
            // Scroll to form
            document.querySelector('.search-section').scrollIntoView({ behavior: 'smooth' });
          }
        })
        .catch(error => console.error('Error loading research:', error));
    }

    function closePreviewModal() {
      document.getElementById('previewModal').style.display = 'none';
      // Clean up blob URL if exists
      const pdfSrc = document.getElementById('previewPDF').src;
      if (pdfSrc && pdfSrc.startsWith('blob:')) {
        URL.revokeObjectURL(pdfSrc);
      }
      document.getElementById('previewPDF').src = '';
      document.getElementById('previewFileItem').style.display = 'none';
    }

    function cancelEdit() {
      isEditMode = false;
      editingId = null;
      document.getElementById('researchForm').reset();
      document.getElementById('previewModal').style.display = 'none';
      fileNameDisplay.textContent = '| No file chosen';
      imageNameDisplay.textContent = '| No file chosen';
      imagePreviewUrl = null;
      // Clean up blob URL if exists
      const pdfSrc = document.getElementById('previewPDF').src;
      if (pdfSrc && pdfSrc.startsWith('blob:')) {
        URL.revokeObjectURL(pdfSrc);
      }
      document.getElementById('previewPDF').src = '';
      document.getElementById('previewFileItem').style.display = 'none';
      document.getElementById('submitBtn').textContent = 'Create New';
      document.getElementById('submitBtn').className = 'btn-search';
      document.getElementById('cancelEditBtn').style.display = 'none';
      document.querySelector('.search-title').textContent = 'Create New Research';
    }

    // Close modal when clicking outside of it
    window.addEventListener('click', (event) => {
      const modal = document.getElementById('previewModal');
      if (event.target == modal) {
        modal.style.display = 'none';
      }
    });

    // Load research list
    function loadResearchList() {
      fetch('load_research.php')
        .then(response => response.json())
        .then(data => {
          const researchList = document.getElementById('researchList');
          researchList.innerHTML = '';

          if (data && data.length > 0) {
            const title = document.createElement('h2');
            title.className = 'search-title';
            title.textContent = 'Existing Research';
            researchList.appendChild(title);

            const itemsContainer = document.createElement('div');
            itemsContainer.className = 'research-items';

            data.forEach((item, index) => {
              const bg_class = index % 2 === 0 ? 'light-bg' : 'dark-bg';
              const itemDiv = document.createElement('div');
              itemDiv.className = 'research-item ' + bg_class;
              const imageSrc = item.image ? '../../uploads/research_images/' + item.image : '../../assets/images/book-cover.png';
              itemDiv.innerHTML = `
                <div class="item-image">
                  <img src="${imageSrc}" alt="${item.title}">
                </div>
                <div class="item-content">
                  <h3 class="item-title">${item.title}</h3>
                  <p class="item-meta">
                    <span class="meta-item">Grade ${item.grade.replace('grade-', '')} ${item.department.charAt(0).toUpperCase() + item.department.slice(1)}</span>
                  </p>
                  <p class="item-meta">
                    <span class="meta-item">${item.year}</span>
                    <span class="meta-item">${item.category}</span>
                  </p>
                  <div style="display: flex; gap: 10px; margin-top: 15px;">
                    <button class="btn-view" onclick="viewResearch(${item.id})">View</button>
                  </div>
                </div>
              `;
              itemsContainer.appendChild(itemDiv);
            });

            researchList.appendChild(itemsContainer);
          }
        })
        .catch(error => console.error('Error loading research:', error));
    }

    function deleteResearch(id) {
      if (confirm('Are you sure you want to delete this research?')) {
        fetch('delete_research.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: 'id=' + id
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert('Research deleted successfully!');
            loadResearchList();
          } else {
            alert('Error: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Error deleting research');
        });
      }
    }

    function viewResearch(id) {
      fetch('load_research.php')
        .then(response => response.json())
        .then(data => {
          const item = data.find(r => r.id === id);
          if (item && item.file) {
            // Open PDF in new tab
            const pdfUrl = '../../uploads/research_pdfs/' + item.file;
            const newTab = window.open(pdfUrl, '_blank');
            if (!newTab || newTab.closed || typeof newTab.closed === 'undefined') {
              // If popup blocked, show alert
              alert('Please enable pop-ups to view the PDF file.');
            }
          } else {
            alert('No PDF file available for this research.');
          }
        })
        .catch(error => {
          console.error('Error loading research:', error);
          alert('Error loading research file.');
        });
    }

    // Load research on page load
    document.addEventListener('DOMContentLoaded', loadResearchList);
  </script>

</body>
</html>