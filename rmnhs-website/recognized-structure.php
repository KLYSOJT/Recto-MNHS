<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>RMNHS Recognized Organizations</title>
  <link rel="stylesheet" href="assets/css/unified.css">
  <link rel="stylesheet" href="recognized.css">
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



  <main class="organization-page">
    <h2 class="section-title">Recognized Organizations</h2>
    <div class="organization-container">
      <?php
        // Fetch recognized organizations from database
        $query = "SELECT id, org_name, date_established, adviser_name, image, pdf FROM recognized_organization ORDER BY org_name ASC";
        $result = mysqli_query($conn, $query);
        
        if ($result && mysqli_num_rows($result) > 0) {
          while ($row = mysqli_fetch_assoc($result)) {
            $org_name = htmlspecialchars($row['org_name']);
            $date_established = htmlspecialchars($row['date_established'] ?? '');
            $adviser_name = htmlspecialchars($row['adviser_name'] ?? '');
            $image = !empty($row['image']) ? 'uploads/recognized-organization/' . htmlspecialchars($row['image']) : 'userimages/organization.png';
            $pdf = !empty($row['pdf']) ? 'uploads/recognized-organization/' . htmlspecialchars($row['pdf']) : '';
            $image_json = htmlspecialchars(json_encode($row['image'] ? 'uploads/recognized-organization/' . $row['image'] : ''), ENT_QUOTES, 'UTF-8');
            $pdf_json = htmlspecialchars(json_encode($row['pdf'] ? 'uploads/recognized-organization/' . $row['pdf'] : ''), ENT_QUOTES, 'UTF-8');
            $date_json = htmlspecialchars(json_encode($row['date_established'] ?? ''), ENT_QUOTES, 'UTF-8');
            $adviser_json = htmlspecialchars(json_encode($row['adviser_name'] ?? ''), ENT_QUOTES, 'UTF-8');
            echo '
      <div class="organization-card" onclick="openModal(\'' . str_replace("'", "\\'", $org_name) . '\', ' . $image_json . ', ' . $pdf_json . ', ' . $date_json . ', ' . $adviser_json . ')">
        <img src="' . $image . '" alt="' . $org_name . '">
        <div class="org-name">' . $org_name . '</div>
      </div>';
          }
        } else {
          echo '<p class="no-data">No organizations found.</p>';
        }
      ?>
    </div>
  </main>

  <!-- Organization Modals -->
  <div id="organizationModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModal()">&times;</span>
      <div id="modalBody">
        <!-- Modal content will be loaded here -->
      </div>
    </div>
  </div>

  <?php include 'footer/footer.php'; ?>

  <script>
    const modal = document.getElementById('organizationModal');
    const modalBody = document.getElementById('modalBody');

    function openModal(organization, imagePath, pdfPath, dateEstablished, adviserName) {
      let html = `<h2>${organization.toUpperCase()}</h2>`;

      // Show organization info
      html += `<div class="modal-info-section">`;
      
      if (dateEstablished && dateEstablished.trim() !== '') {
        html += `
          <div class="info-item">
            <span class="info-label">Date Established:</span>
            <span class="info-value">${dateEstablished}</span>
          </div>
        `;
      }

      if (adviserName && adviserName.trim() !== '') {
        html += `
          <div class="info-item"> 
            <span class="info-label">Adviser:</span>
            <span class="info-value">${adviserName}</span>
          </div>
        `;
      }

      html += `</div>`;

      // show pdf link if available
      if (pdfPath && pdfPath.trim() !== '') {
        html += `
          <div class="modal-pdf-section">
            <a href="${pdfPath}" target="_blank" class="pdf-link">
              <i class="fa fa-file-pdf"></i> View Accomplishments 
            </a>
          </div>
        `;
      } else {
        // if no pdf exists, show placeholder message
        html += `<p class="no-data"><i class="fa fa-info-circle"></i> No accomplishments file has been uploaded for this organization yet.</p>`;
      }

      modalBody.innerHTML = html;
      modal.style.display = 'flex';
    }

    function closeModal() {
      modal.style.display = 'none';
    }

    window.onclick = function(event) {
      if (event.target === modal) {
        closeModal();
      }
    }
  </script>

</body>
</html>

