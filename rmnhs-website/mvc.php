<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>RMNHS VMC</title>
  <link rel="stylesheet" href="assets/css/unified.css">
  <link rel="stylesheet" href="index.css">
  <link rel="stylesheet" href="mvc.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

  <style>
    /* Integrated Dropdown & Grid Styles */
    .core-values-grid {
      display: flex;
      flex-wrap: nowrap;
      gap: 16px;
      margin-top: 20px;
      box-sizing: border-box;
      align-items: stretch;
    }

    .custom-dropdown {
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(93, 0, 0, 0.1);
        height: fit-content;
        transition: transform 0.3s ease;
    }

    .custom-dropdown {
      flex: 1 1 220px;
      min-width: 180px;
    }

    .custom-dropdown:hover {
        transform: translateY(-5px);
    }

    .dropdown-header {
      background: linear-gradient(180deg, var(--primary-color) 0%, var(--secondary-color) 100%);
      color: white;
      padding: 18px 14px;
      font-size: 1.05rem;
      font-weight: 700;
      text-align: center;
      cursor: pointer;
      position: relative;
      user-select: none;
      box-sizing: border-box;
    }

    .dropdown-header .dropdown-icon {
      position: absolute;
      right: 15px;
      bottom: 10px;
      width: 16px;
      height: 16px;
      transition: transform 0.35s ease;
      color: #fff;
      display: inline-block;
    }

    .reports-list {
        list-style: none;
        padding: 0;
        margin: 0;
        max-height: 0;
        opacity: 0;
        overflow: hidden;
        transition: max-height 0.4s ease, opacity 0.3s ease;
        background: #fff;
    }

    .custom-dropdown.active .reports-list {
        max-height: 500px;
        opacity: 1;
    }

    .custom-dropdown.active .dropdown-icon {
      transform: rotate(180deg);
    }

    .reports-list li {
        padding: 15px 25px;
        border-top: 1px solid #eee;
        color: var(--text-dark);
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .reports-list li:hover {
        background-color: #f9f9f9;
        color: var(--primary-color);
        padding-left: 30px;
    }
  </style>

  <?php include 'navbar/navbar.php'; ?>

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

<main class="mvc-main">
  <div class="container">
    <section>
        <h2>Mission</h2>
        <p>To protect and promote the right of every Filipino to quality, equitable, culture-based, and complete basic education where:</p>
        <ul class="mission-points">
            <li><strong>Students</strong> learn in a child-friendly, gender-sensitive, safe, and motivating environment.</li>
            <li><strong>Teachers</strong> facilitate learning and constantly nurture every learner.</li>
            <li><strong>Administrators and staff</strong>, as stewards of the institution, ensure an enabling and supportive environment for effective learning to happen.</li>
            <li><strong>Family, community, and other stakeholders</strong> are actively engaged and share responsibility for developing life-long learners.</li>
        </ul>
    </section>

    <hr>

    <section>
        <h2>Vision</h2>
        <p>We dream of Filipinos who passionately love their country and whose values and competencies enable them to realize their full potential and contribute meaningfully to building the nation.</p>
        <p>As a learner-centered public institution, the Department of Education continuously improves itself to better serve its stakeholders.</p>
    </section>

    <hr>

    <section>
        <h2>Core Values</h2>
        <div class="core-values-grid">
            
            <div class="custom-dropdown" id="dropdown-diyos">
                <div class="dropdown-header">
                  <span onclick="openValueModal('userimages/makadiyos.png', 'Maka-diyos: pagkakaroon ng matibay na pananalig, pagmamahal, at pagsunod sa mga utos ng Panginoong Maykapal')">Maka-Diyos</span>
                  <svg class="dropdown-icon" viewBox="0 0 24 24" aria-hidden="true" onclick="event.stopPropagation(); toggleDropdown('dropdown-diyos')"><path d="M6 9l6 6 6-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <ul class="reports-list">
                    <li>Accomplishment Report 1</li>
                    <li>Accomplishment Report 2</li>
                </ul>
            </div>

            <div class="custom-dropdown" id="dropdown-tao">
                <div class="dropdown-header">
                  <span onclick="openValueModal('userimages/makatao.png', 'Maka-tao: pagpapakita ng pagmamahal, paggalang, at malasakit sa kapwa tao')">Maka-Tao</span>
                  <svg class="dropdown-icon" viewBox="0 0 24 24" aria-hidden="true" onclick="event.stopPropagation(); toggleDropdown('dropdown-tao')"><path d="M6 9l6 6 6-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <ul class="reports-list">
                    <li>Accomplishment Report 1</li>
                </ul>
            </div>

            <div class="custom-dropdown" id="dropdown-kalikasan">
                <div class="dropdown-header">
                  <span onclick="openValueModal('userimages/makakalikasan.png', 'Maka-kalikasan: pagmamahal, pagpapahalaga, at pangangalaga sa kapaligiran')">Makakalikasan</span>
                  <svg class="dropdown-icon" viewBox="0 0 24 24" aria-hidden="true" onclick="event.stopPropagation(); toggleDropdown('dropdown-kalikasan')"><path d="M6 9l6 6 6-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <ul class="reports-list">
                    <li>Accomplishment Report 1</li>
                </ul>
            </div>

            <div class="custom-dropdown" id="dropdown-bansa">
                <div class="dropdown-header">
                  <span onclick="openValueModal('userimages/makabansa.png', 'Maka-bansa: pagmamahal, katapatan, at paggalang ng isang indibidwal sa kanyang sariling bayan')">Makabansa</span>
                  <svg class="dropdown-icon" viewBox="0 0 24 24" aria-hidden="true" onclick="event.stopPropagation(); toggleDropdown('dropdown-bansa')"><path d="M6 9l6 6 6-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <ul class="reports-list">
                    <li>Accomplishment Report 1</li>
                </ul>
            </div>

        </div>
    </section>

    <div id="coreModal" class="core-modal" aria-hidden="true">
      <div class="core-modal-overlay" id="coreModalOverlay"></div>
      <div class="core-modal-content" role="dialog" aria-modal="true">
        <button class="core-modal-close" id="coreModalClose" aria-label="Close modal">&times;</button>
        <div class="core-modal-body">
          <img id="coreModalImage" src="" alt="Core value image">
          <p id="coreModalCaption" class="core-modal-caption"></p>
        </div>
      </div>
    </div>

    <script>
      /* Dropdown Logic */
      function toggleDropdown(id) {
          const el = document.getElementById(id);
          const isActive = el.classList.contains('active');
          
          // Close others
          document.querySelectorAll('.custom-dropdown').forEach(d => d.classList.remove('active'));
          
          // Toggle current
          if (!isActive) el.classList.add('active');
      }

      /* Modal Logic */
      const modal = document.getElementById('coreModal');
      const overlay = document.getElementById('coreModalOverlay');
      const modalImg = document.getElementById('coreModalImage');
      const modalCaption = document.getElementById('coreModalCaption');
      const closeBtn = document.getElementById('coreModalClose');

      function openValueModal(src, text) {
          modalImg.src = src;
          modalCaption.textContent = text;
          modal.classList.add('open');
          modal.setAttribute('aria-hidden', 'false');
      }

      function closeModal() {
          modal.classList.remove('open');
          modal.setAttribute('aria-hidden', 'true');
      }

      overlay.addEventListener('click', closeModal);
      closeBtn.addEventListener('click', closeModal);
      document.addEventListener('keydown', (e) => { if(e.key === 'Escape') closeModal(); });

      // Close dropdowns on outside click
      window.onclick = function(event) {
          if (!event.target.closest('.custom-dropdown')) {
              document.querySelectorAll('.custom-dropdown').forEach(d => d.classList.remove('active'));
          }
      }
    </script>
  </div>
</main>

<?php include 'footer/footer.php'; ?>
</body>
</html>