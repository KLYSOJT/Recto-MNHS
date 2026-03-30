<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>RMNHS Location</title>
  <link rel="stylesheet" href="assets/css/unified.css">
  <link rel="stylesheet" href="index.css">
  <link rel="stylesheet" href="location.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">


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


<div class="title">
  Location
</div>

<div class="schoolmap">
      <img src="userimages/schoolmap.png"  alt="Map of Recto Memorial National High School" class="schoolmap">
    </div>
    <div class="text-container">
        <div class="title-top">HOME OF</div>
        <div class="title-bottom">
            <span class="letter-btn" data-letter="R">R</span>
            <span class="letter-btn" data-letter="E">E</span>
            <span class="letter-btn" data-letter="C">C</span>
            <span class="letter-btn" data-letter="T">T</span>
            <span class="letter-btn" data-letter="O">O</span>
            <span class="letter-btn" data-letter="R">R</span>
            <span class="letter-btn" data-letter="I">I</span>
            <span class="letter-btn" data-letter="A">A</span>
            <span class="letter-btn" data-letter="N">N</span>
            <span class="letter-btn" data-letter="S">S</span>
        </div>
    </div>

    <!-- Modal -->
    <div id="facilityModal" class="modal">
      <div class="modal-content">
        <span class="close">&times;</span>
        <img id="modalImage" src="" alt="" style="max-width:100%;max-height:80vh;width:auto;height:auto;object-fit:contain;display:none;">
        
      </div>
    </div>
    
<?php include 'footer/footer.php'; ?>

<script>
    // Modal functionality
    const modal = document.getElementById('facilityModal');
    const closeBtn = document.querySelector('.close');
    const letterBtns = document.querySelectorAll('.letter-btn');

  // Modal image element
  const modalImage = document.getElementById('modalImage');
  const firstR = document.querySelector('.letter-btn[data-letter="R"]');

  letterBtns.forEach((btn) => {
    btn.addEventListener('click', function() {
      const letter = this.dataset.letter;

      if (letter === 'R' && this === firstR) {
        modalImage.src = 'userimages/1.png';
        modalImage.alt = 'Rise';
        modalImage.style.display = 'block';
      } else if (letter === 'E') {
        modalImage.src = 'userimages/2.png';
        modalImage.alt = 'Excellence';
        modalImage.style.display = 'block';
      } else if (letter === 'C') {
        modalImage.src = 'userimages/3.png';
        modalImage.alt = 'Creativity';
        modalImage.style.display = 'block';
      } else if (letter === 'T') {
        modalImage.src = 'userimages/4.png';
        modalImage.alt = 'Talent';
        modalImage.style.display = 'block';
      } else if (letter === 'O') {
        modalImage.src = 'userimages/5.png';
        modalImage.alt = 'Optimism';
        modalImage.style.display = 'block';
      } else if (letter === 'R') {
        modalImage.src = 'userimages/6.png';
        modalImage.alt = 'Resilience';
        modalImage.style.display = 'block';
      } else if (letter === 'I') {
        modalImage.src = 'userimages/7.png';
        modalImage.alt = 'Innovation';
        modalImage.style.display = 'block';
      } else if (letter === 'A') {
        modalImage.src = 'userimages/8.png';
        modalImage.alt = 'Aspiration';
        modalImage.style.display = 'block';
      } else if (letter === 'N') {
        modalImage.src = 'userimages/9.png';
        modalImage.alt = 'Nobility';
        modalImage.style.display = 'block';
      } else if (letter === 'S') {
        modalImage.src = 'userimages/10.png';
        modalImage.alt = 'Service';
        modalImage.style.display = 'block';
      } else {
        modalImage.style.display = 'none';
        modalImage.src = '';
        modalImage.alt = '';
      }

      modal.style.display = 'flex';
    });
  });

    closeBtn.addEventListener('click', function() {
      modal.style.display = 'none';
      modalImage.style.display = 'none';
      modalImage.src = '';
      modalImage.alt = '';
    });

    window.addEventListener('click', function(event) {
      if (event.target == modal) {
        modal.style.display = 'none';
        modalImage.style.display = 'none';
        modalImage.src = '';
        modalImage.alt = '';
      }
    });
</script>
</body>
</html>