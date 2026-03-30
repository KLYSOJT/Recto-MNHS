<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>RMNHS Organizational Structure</title>
  <link rel="stylesheet" href="assets/css/unified.css">
  <link rel="stylesheet" href="index.css">
  <link rel="stylesheet" href="department.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>
<body>
  <?php 
    require_once 'connection/db_connection.php';
  ?>
  <?php include 'navbar/navbar.php'; ?>

<!-- Main Header Section -->

<header class="main-header">
  <div class="header-content">
    <div class="brand-section">
      <div class="vertical-divider"></div>
      <h1 class="school-name">
        Recto Memorial National High School
      </h1>
    </div>

    <div class="contact-info">
      <div class="contact-item">
        <div class="icon-circle"><i class="fa fa-phone"></i></div>
        <div class="contact-text">
          <span class="label">Phone Number :</span>
          <span class="value">0949-995-1769</span>
        </div>
      </div>
      <div class="contact-item">
        <div class="icon-circle"><i class="fa fa-envelope"></i></div>
        <div class="contact-text">
          <span class="label">Email Address :</span>
          <span class="value">rectomns301380@gmail.com</span>
        </div>
      </div>
    </div>
  </div>
</header>


  <main class="department-page">
    <h2 class="section-title">Organizational Structure</h2>
    <div class="departments-container">
      <!-- static list of departments -->
      <div class="department-card" onclick="openModal('TLE')">
        <img src="userimages/tle.png" alt="TLE DEPARTMENT">
        <div class="dept-name">TLE DEPARTMENT</div>
      </div>
      <div class="department-card" onclick="openModal('Math')">
        <img src="userimages/math.png" alt="MATH DEPARTMENT">
        <div class="dept-name">MATH DEPARTMENT</div>
      </div>
      <div class="department-card" onclick="openModal('English')">
        <img src="userimages/ENGLISH.png" alt="ENGLISH DEPARTMENT">
        <div class="dept-name">ENGLISH DEPARTMENT</div>
      </div>
      <div class="department-card" onclick="openModal('Science')">
        <img src="userimages/science.png" alt="SCIENCE DEPARTMENT">
        <div class="dept-name">SCIENCE DEPARTMENT</div>
      </div>
      <div class="department-card" onclick="openModal('Filipino')">
        <img src="userimages/filipino.jpg" alt="FILIPINO DEPARTMENT">
        <div class="dept-name">FILIPINO DEPARTMENT</div>
      </div>
      <div class="department-card" onclick="openModal('AP')">
        <img src="userimages/ap.jpg" alt="AP DEPARTMENT">
        <div class="dept-name">AP DEPARTMENT</div>
      </div>
      <div class="department-card" onclick="openModal('MAPEH')">
        <img src="userimages/mapeh.png" alt="MAPEH DEPARTMENT">
        <div class="dept-name">MAPEH DEPARTMENT</div>
      </div>
      <div class="department-card" onclick="openModal('Values Education')">
        <img src="userimages/esp.png" alt="VALUES EDUCATION DEPARTMENT">
        <div class="dept-name">VALUES EDUCATION DEPARTMENT</div>
      </div>
    </div>
  </main>

  <!-- Department Modal -->
  <div id="departmentModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModal()">&times;</span>
      <div id="modalBody">
        <!-- Modal content will be loaded here -->
      </div>
    </div>
  </div>

  <?php include 'footer/footer.php'; ?>

  <script>
    const modal = document.getElementById('departmentModal');
    const modalBody = document.getElementById('modalBody');

    function openModal(department) {
      // Fetch data from database based on department name
      fetch(`admin/organization/get_org_structure.php?dept_name=${encodeURIComponent(department)}`)
        .then(response => response.json())
        .then(data => {
          let html = `<h2>${department.toUpperCase()}</h2>`;
          
          if (data.success) {
            const imagePath = data.image ? data.image : '';
            const pdfs = data.pdfs ? data.pdfs : [];
            
            if (imagePath) {
              html += `<div class="modal-image-section"><img src="${imagePath}" alt="${department}" class="modal-image"></div>`;
            }
            
            if (pdfs.length > 0) {
              html += `<div class="modal-pdf-section">`;
              html += `<h3>Accomplishment Reports</h3>`;
              html += `<div class="pdf-items-container">`;
              pdfs.forEach(pdf => {
                html += `<a href="${pdf.path}" target="_blank" class="pdf-item" title="${pdf.filename}">
                  <i class="fas fa-file-pdf"></i>
                  <span class="pdf-filename">${pdf.filename}</span>
                </a>`;
              });
              html += `</div>`;
              html += `</div>`;
            }
            
            if (!imagePath && pdfs.length === 0) {
              html += `<p class="no-data">No files available for this department.</p>`;
            }
          } else {
            html += `<p class="no-data">No information available for this department.</p>`;
          }
          
          modalBody.innerHTML = html;
          modal.style.display = 'block';
        })
        .catch(error => {
          console.error('Error fetching department data:', error);
          modalBody.innerHTML = `<h2>${department.toUpperCase()}</h2><p class="no-data">Error loading department information.</p>`;
          modal.style.display = 'block';
        });
    }
  
    function closeModal() {
      modal.style.display = 'none';
    }

    window.onclick = function(event) {
      if (event.target == modal) {
        modal.style.display = 'none';
      }
    }
  </script>
</body>
</html>
