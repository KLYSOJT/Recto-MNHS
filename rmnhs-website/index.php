 <!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>RMNHS Home</title>
  <link rel="stylesheet" href="assets/css/unified.css">
  <link rel="stylesheet" href="index.css">
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


<!-- Carousel Section -->
<section class="carousel-container">
  <div class="carousel">
    <div class="carousel-inner">
      <div class="carousel-item active">
        <img src="userimages/welcome.png" alt="Welcome to RMNHS">
      </div>
      <div class="carousel-item">
        <img src="userimages/making.png" alt="Making Excellence as a Habit">
      </div>
      <div class="carousel-item">
        <img src="userimages/tatakrecto.png" alt="Making Excellence as a Habit">
      </div>
    </div>

    <!-- Carousel Indicators -->
    <div class="carousel-indicators">
      <span class="indicator active" onclick="currentSlide(1)"></span>
      <span class="indicator" onclick="currentSlide(2)"></span>
      <span class="indicator" onclick="currentSlide(3)"></span>
    </div>
  </div>
</section>

<!-- Announcements Section -->
<section class="announcements">
  <div class="announcements-container">
    <h2 class="announcements-title">Announcements</h2>
    
    <div class="announcements-grid" id="announcementsGrid">
      <!-- Announcements will be dynamically populated here -->
    </div>

    <!-- Pagination -->
    <div class="pagination" id="paginationContainer">
      <!-- Pagination buttons will be dynamically generated here -->
    </div>
  </div>
</section>

<!-- Modal for Announcement Details -->
<div id="announcementModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <img id="modalImage" src="" alt="Announcement Image" class="modal-image">
    </div>
    <div class="modal-body">
      <p id="modalCategory" class="modal-category">ANNOUNCEMENT</p>
      <p id="modalDate" class="modal-date"></p>
      <h2 id="modalTitle" class="modal-article-title"></h2>
      <p id="modalAuthor" class="modal-author">RMNHS Office</p>
      <p id="modalDescription" class="modal-description"></p>
    </div>
  </div>
</div>

<!-- Latest News Section -->
<section class="latest-news">
  <div class="latest-news-container">
    <h2 class="latest-news-title">Latest News</h2>
    
    <div class="news-grid" id="newsGrid">
      <!-- News items will be dynamically populated here -->
    </div>

    <!-- Pagination -->
    <div class="news-pagination" id="newsPaginationContainer">
      <!-- Pagination buttons will be dynamically generated here -->
    </div>
  </div>
</section>

<?php
// grab   videos list
require_once 'connection/db_connection.php';
$videos = [];
$videoSql = "SELECT id, title, description, filename, url, created_at FROM featured_videos ORDER BY created_at DESC";
if ($result = $conn->query($videoSql)) {
    while ($row = $result->fetch_assoc()) {
        $videos[] = $row;
    }
}
?>

<!-- Featured Video Section -->
<section class="featured-video">
  <div class="featured-video-container">
    <h2 class="featured-video-title">Featured Video</h2>
    <?php if (!empty($videos)) : ?>
    <div class="featured-video-grid">
      <div class="featured-main">
        <div class="video-wrapper">
          <?php if (!empty($videos[0]['url'])): ?>
            <iframe id="mainVideo" width="100%" height="500" style="border: none; border-radius: 8px;" src="<?php echo htmlspecialchars($videos[0]['url']); ?>" title="Featured video" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" sandbox="allow-scripts allow-same-origin allow-popups allow-popups-to-escape-sandbox" allowfullscreen referrerpolicy="no-referrer-when-downgrade"></iframe>
          <?php else: ?>
            <video id="mainVideo" controls poster="userimages/video-poster.jpg" style="width: 100%; height: 500px; background: #000;">
              <source src="uploads/featured_videos/<?php echo htmlspecialchars($videos[0]['filename']); ?>" type="video/mp4">
            </video>
          <?php endif; ?>
        </div>
        <div class="video-info">
          <h3 id="mainTitle"><?php echo htmlspecialchars($videos[0]['title']); ?></h3>
          <?php if (!empty($videos[0]['description'])): ?>
          <p id="mainDesc"><?php echo htmlspecialchars($videos[0]['description']); ?></p>
          <?php endif; ?>
          <p id="mainDate"><?php echo date('F j, Y', strtotime($videos[0]['created_at'])); ?></p>
        </div>
      </div>
      <div class="video-list" id="videoList">
        <!-- Video items will be rendered here -->
      </div>
    </div>
    <?php else: ?>
    <p>No featured video has been uploaded yet.</p>
    <?php endif; ?>
  </div>
</section>

<!-- Modal for News Details -->
<div id="newsModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <img id="newsModalImage" src="" alt="News Image" class="modal-image">
    </div>
    <div class="modal-body">
      <p id="newsModalCategory" class="modal-category">NEWS</p>
      <p id="newsModalDate" class="modal-date"></p>
      <h2 id="newsModalTitle" class="modal-article-title"></h2>
      <p id="newsModalAuthor" class="modal-author">THE RECTORIAN</p>
      <p id="newsModalDescription" class="modal-description"></p>
    </div>
  </div>
</div>

<script>
  // Announcement Data
  let announcements = [];

  // Pagination settings
  const announcementsPerPage = 3;
  let currentPage = 1;
  let totalPages = 0;

  // Get modal elements
  const modal = document.getElementById("announcementModal");
  const announcementsGrid = document.getElementById("announcementsGrid");
  const paginationContainer = document.getElementById("paginationContainer");

  // Function to render announcements for current page
  function renderAnnouncements() {
    announcementsGrid.innerHTML = "";
    
    if (announcements.length === 0) {
      announcementsGrid.innerHTML = '<p style="text-align: center; grid-column: 1/-1; padding: 20px;">No announcements yet.</p>';
      return;
    }
    
    const startIndex = (currentPage - 1) * announcementsPerPage;
    const endIndex = startIndex + announcementsPerPage;
    const paginatedAnnouncements = announcements.slice(startIndex, endIndex);
    
    paginatedAnnouncements.forEach((announcement, index) => {
      const actualIndex = startIndex + index;
      const card = document.createElement("div");
      card.className = "announcement-card";
      card.innerHTML = `
        <div class="announcement-image">
          <img src="${announcement.image}" alt="${announcement.title}">
        </div>
        <div class="announcement-content">
          <p class="announcement-date">${announcement.date}</p>
          <p class="announcement-description">${announcement.description}</p>
          <button class="read-more-btn" data-index="${actualIndex}">Read more</button>
        </div>
      `;
      announcementsGrid.appendChild(card);
    });

    // Attach read more button listeners
    const readMoreButtons = document.querySelectorAll(".read-more-btn");
    readMoreButtons.forEach((button) => {
      button.addEventListener("click", function(e) {
        e.preventDefault();
        const index = parseInt(this.getAttribute("data-index"));
        const announcement = announcements[index];
        
        // Clear modal content first
        document.getElementById("modalCategory").textContent = "";
        document.getElementById("modalDate").textContent = "";
        document.getElementById("modalTitle").textContent = "";
        document.getElementById("modalDescription").innerHTML = "";
        
        // Set modal content
        document.getElementById("modalImage").src = announcement.image;
        document.getElementById("modalCategory").textContent = "ANNOUNCEMENT";
        document.getElementById("modalDate").textContent = announcement.date;
        document.getElementById("modalTitle").textContent = announcement.fullDescription.split('\n')[0] || announcement.description;
        
        // Parse description into paragraphs
        const fullText = announcement.fullDescription || announcement.description;
        const paragraphs = fullText.split(/\n\n+/).filter(p => p.trim());
        const htmlContent = paragraphs.map(para => `<p>${para.trim()}</p>`).join('');
        document.getElementById("modalDescription").innerHTML = htmlContent;
        
        // Show modal
        modal.style.display = "block";
        document.body.style.overflow = "hidden";
      });
    });
  }

  // Function to render pagination buttons
  function renderPagination() {
    paginationContainer.innerHTML = "";
    
    // First button
    const firstBtn = document.createElement("a");
    firstBtn.href = "#";
    firstBtn.className = "pagination-btn pagination-prev";
    firstBtn.textContent = "<<";
    
    if (currentPage === 1) {
      firstBtn.classList.add("disabled");
      firstBtn.style.pointerEvents = "none";
    } else {
      firstBtn.addEventListener("click", function(e) {
        e.preventDefault();
        if (currentPage > 1) {
          currentPage = 1;
          renderAnnouncements();
          renderPagination();
        }
      });
    }
    paginationContainer.appendChild(firstBtn);

    // Page number buttons
    for (let i = 1; i <= totalPages; i++) {
      const btn = document.createElement("a");
      btn.href = "#";
      btn.className = "pagination-btn";
      btn.textContent = i;
      
      if (i === currentPage) {
        btn.classList.add("pagination-active");
      }
      
      btn.addEventListener("click", function(e) {
        e.preventDefault();
        currentPage = i;
        renderAnnouncements();
        renderPagination();
      });
      
      paginationContainer.appendChild(btn);
    }

    // Last button
    const lastBtn = document.createElement("a");
    lastBtn.href = "#";
    lastBtn.className = "pagination-btn pagination-next";
    lastBtn.textContent = ">>";
    
    if (currentPage === totalPages) {
      lastBtn.classList.add("disabled");
      lastBtn.style.pointerEvents = "none";
    } else {
      lastBtn.addEventListener("click", function(e) {
        e.preventDefault();
        if (currentPage < totalPages) {
          currentPage = totalPages;
          renderAnnouncements();
          renderPagination();
        }
      });
    }
    paginationContainer.appendChild(lastBtn);
  }

  // Close modal when clicking outside of it
  window.addEventListener("click", function(event) {
    if (event.target === modal) {
      modal.style.display = "none";
      document.body.style.overflow = "auto";
    }
  });

  // Fetch announcements from database
  async function fetchAnnouncements() {
    try {
      const response = await fetch('includes/fetch_announcements_news.php?type=announcement');
      const result = await response.json();
      
      if (result.success) {
        announcements = result.data;
        totalPages = Math.ceil(announcements.length / announcementsPerPage);
        renderAnnouncements();
        renderPagination();
      } else {
        console.error('Fetch error:', result.message);
        announcementsGrid.innerHTML = '<p style="text-align: center; color: red;">Error loading announcements: ' + result.message + '</p>';
      }
    } catch (error) {
      console.error('Error fetching announcements:', error);
      announcementsGrid.innerHTML = '<p style="text-align: center; color: red;">Error loading announcements</p>';
    }
  }

  // Initial fetch and render
  fetchAnnouncements();

  // ============= Latest News Section =============
  
  // Latest News Data
  let latestNews = [];

  // News Pagination settings
  const newsPerPage = 3;
  let newsCurrentPage = 1;
  let newsTotalPages = 0;

  // Get news elements
  const newsModal = document.getElementById("newsModal");
  const newsGrid = document.getElementById("newsGrid");
  const newsPaginationContainer = document.getElementById("newsPaginationContainer");

  // Function to render news for current page
  function renderNews() {
    newsGrid.innerHTML = "";
    
    if (latestNews.length === 0) {
      newsGrid.innerHTML = '<p style="text-align: center; grid-column: 1/-1; padding: 20px;">No news yet.</p>';
      return;
    }
    
    const startIndex = (newsCurrentPage - 1) * newsPerPage;
    const endIndex = startIndex + newsPerPage;
    const paginatedNews = latestNews.slice(startIndex, endIndex);
    
    paginatedNews.forEach((news, index) => {
      const actualIndex = startIndex + index;
      const newsCard = document.createElement("div");
      newsCard.className = "news-card";
      newsCard.innerHTML = `
        <div class="news-image">
          <img src="${news.image}" alt="${news.title}">
        </div>
        <div class="news-content">
          <h3 class="news-title">${news.title}</h3>
          <p class="news-description">${news.description}</p>
          <button class="read-more-btn news-read-more" data-index="${actualIndex}">Read more</button>
        </div>
      `;
      newsGrid.appendChild(newsCard);
    });

    // Attach read more button listeners for news
    const newsReadMoreButtons = document.querySelectorAll(".news-read-more");
    newsReadMoreButtons.forEach((button) => {
      button.addEventListener("click", function(e) {
        e.preventDefault();
        const index = parseInt(this.getAttribute("data-index"));
        const news = latestNews[index];
        
        // Clear modal content first
        document.getElementById("newsModalTitle").textContent = "";
        document.getElementById("newsModalDescription").textContent = "";
        
        // Set modal content
        document.getElementById("newsModalImage").src = news.image;
        
        // Only show fullDescription if available, otherwise show title + description
        if (news.fullDescription) {
          document.getElementById("newsModalDescription").textContent = news.fullDescription;
        } else {
          if (news.title) {
            document.getElementById("newsModalTitle").textContent = news.title;
          }
          document.getElementById("newsModalDescription").textContent = news.description;
        }
        
        // Show modal
        newsModal.style.display = "block";
        document.body.style.overflow = "hidden";
      });
    });
  }

  // Function to render news pagination buttons
  function renderNewsPagination() {
    newsPaginationContainer.innerHTML = "";
    
    // First button
    const newsFirstBtn = document.createElement("a");
    newsFirstBtn.href = "#";
    newsFirstBtn.className = "pagination-btn pagination-prev";
    newsFirstBtn.textContent = "<<";
    
    if (newsCurrentPage === 1) {
      newsFirstBtn.classList.add("disabled");
      newsFirstBtn.style.pointerEvents = "none";
    } else {
      newsFirstBtn.addEventListener("click", function(e) {
        e.preventDefault();
        if (newsCurrentPage > 1) {
          newsCurrentPage = 1;
          renderNews();
          renderNewsPagination();
          window.scrollTo(0, document.querySelector(".latest-news").offsetTop);
        }
      });
    }
    newsPaginationContainer.appendChild(newsFirstBtn);

    // Page number buttons
    for (let i = 1; i <= newsTotalPages; i++) {
      const newsBtn = document.createElement("a");
      newsBtn.href = "#";
      newsBtn.className = "pagination-btn";
      newsBtn.textContent = i;
      
      if (i === newsCurrentPage) {
        newsBtn.classList.add("pagination-active");
      }
      
      newsBtn.addEventListener("click", function(e) {
        e.preventDefault();
        newsCurrentPage = i;
        renderNews();
        renderNewsPagination();
        window.scrollTo(0, document.querySelector(".latest-news").offsetTop);
      });
      
      newsPaginationContainer.appendChild(newsBtn);
    }

    // Last button
    const newsLastBtn = document.createElement("a");
    newsLastBtn.href = "#";
    newsLastBtn.className = "pagination-btn pagination-next";
    newsLastBtn.textContent = ">>";
    
    if (newsCurrentPage === newsTotalPages) {
      newsLastBtn.classList.add("disabled");
      newsLastBtn.style.pointerEvents = "none";
    } else {
      newsLastBtn.addEventListener("click", function(e) {
        e.preventDefault();
        if (newsCurrentPage < newsTotalPages) {
          newsCurrentPage = newsTotalPages;
          renderNews();
          renderNewsPagination();
          window.scrollTo(0, document.querySelector(".latest-news").offsetTop);
        }
      });
    }
    newsPaginationContainer.appendChild(newsLastBtn);
  }

  // Close news modal when clicking outside of it
  window.addEventListener("click", function(event) {
    if (event.target === newsModal) {
      newsModal.style.display = "none";
      document.body.style.overflow = "auto";
    }
  });

  // Fetch news from database
  async function fetchNews() {
    try {
      const response = await fetch('includes/fetch_announcements_news.php?type=news');
      const result = await response.json();
      
      if (result.success) {
        latestNews = result.data;
        newsTotalPages = Math.ceil(latestNews.length / newsPerPage);
        renderNews();
        renderNewsPagination();
      } else {
        console.error('Fetch error:', result.message);
        newsGrid.innerHTML = '<p style="text-align: center; color: red;">Error loading news: ' + result.message + '</p>';
      }
    } catch (error) {
      console.error('Error fetching news:', error);
      newsGrid.innerHTML = '<p style="text-align: center; color: red;">Error loading news</p>';
    }
  }

  // Initial fetch and render for news
  fetchNews();

  // Carousel Functions
  let currentCarouselIndex = 1;
  
  function changeSlide(n) {
    showSlide(currentCarouselIndex += n);
  }
  
  function currentSlide(n) {
    showSlide(currentCarouselIndex = n);
  }
  
  function showSlide(n) {
    const slides = document.querySelectorAll(".carousel-item");
    const indicators = document.querySelectorAll(".indicator");
    
    // Add safety checks - return if no slides or indicators exist
    if (slides.length === 0 || indicators.length === 0) {
      return;
    }
    
    if (n > slides.length) {
      currentCarouselIndex = 1;
    }
    if (n < 1) {
      currentCarouselIndex = slides.length;
    }
    
    // Safety check - ensure index is within bounds
    if (currentCarouselIndex < 1 || currentCarouselIndex > slides.length) {
      return;
    }
    
    slides.forEach(slide => slide.classList.remove("active"));
    indicators.forEach(indicator => indicator.classList.remove("active"));
    
    slides[currentCarouselIndex - 1].classList.add("active");
    indicators[currentCarouselIndex - 1].classList.add("active");
  }
  
  // Auto-advance carousel every 5 seconds  
  setInterval(() => {
    changeSlide(1);
  }, 5000);

  // ===================== Featured Video Behavior (scrollable, no pagination) =====================
  const videosData = <?php echo json_encode($videos, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP); ?>;
  const videosPerPageClient = videosData.length; // Show all videos
  let videoCurrentPage = 1;
  let videoTotalPages = 1; // Only one "page" since we show all

  const videoListContainer = document.getElementById('videoList');
  let mainVideo = document.getElementById('mainVideo');
  const mainTitle = document.getElementById('mainTitle');
  const mainDesc = document.getElementById('mainDesc');
  const mainDate = document.getElementById('mainDate');

  // Function to adjust video height for phone screens
  function adjustVideoHeight() {
    const isPhone = window.innerWidth <= 480;
    const isTablet = window.innerWidth <= 768;
    
    if (mainVideo) {
      if (mainVideo.tagName === 'IFRAME') {
        // For iframes, we rely on the CSS aspect ratio, but adjust min-height
        mainVideo.style.height = isPhone ? '200px' : (isTablet ? '300px' : '500px');
      } else if (mainVideo.tagName === 'VIDEO' || mainVideo.tagName === 'DIV') {
        // For video elements or div placeholders
        mainVideo.style.height = isPhone ? '200px' : (isTablet ? '300px' : '500px');
      }
    }
  }

  // Adjust video height on load and resize
  adjustVideoHeight();
  window.addEventListener('resize', adjustVideoHeight);

  function formatDate(dateStr) {
    if (!dateStr) return '';
    const d = new Date(dateStr);
    return d.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
  }

  function setMainVideo(videoObj) {
    if (!videoObj) return;
    const url = videoObj.url || '';
    const filename = videoObj.filename || '';

    // Check if URL is a Facebook URL
    const isFacebookUrl = url && (url.includes('facebook.com') || url.includes('fb.watch'));
    
    const newNode = (url && !isFacebookUrl) ? document.createElement('iframe') : document.createElement('div');

    if (url && !isFacebookUrl) {
      newNode.id = 'mainVideo';
      newNode.width = '100%';
      newNode.height = '500';
      newNode.style.border = 'none';
      newNode.style.borderRadius = '8px';
      newNode.src = url;
      newNode.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; xr-spatial-tracking';
      newNode.sandbox.add('allow-scripts');
      newNode.sandbox.add('allow-same-origin');
      newNode.sandbox.add('allow-popups');
      newNode.sandbox.add('allow-popups-to-escape-sandbox');
      newNode.allowFullscreen = true;
    } else if (isFacebookUrl) {
      // Handle Facebook URLs
      newNode.id = 'mainVideo';
      newNode.style.cssText = 'width: 100%; height: 500px; border-radius: 8px; display: flex; flex-direction: column; align-items: center; justify-content: center; background: linear-gradient(135deg, #f0f2f5 0%, #e4e6eb 100%); border: 1px solid #ddd;';
      newNode.innerHTML = `
        <div style="text-align: center; padding: 40px;">
          <i class="fab fa-facebook" style="font-size: 64px; color: #1877f2; margin-bottom: 20px;"></i>
          <h3 style="color: #333; margin: 10px 0;">Available on Facebook</h3>
          <p style="color: #666; margin: 10px 0; max-width: 400px;">This video is hosted on Facebook. Click the button below to watch it.</p>
          <a href="${url}" target="_blank" rel="noopener noreferrer" style="display: inline-block; margin-top: 20px; padding: 12px 24px; background: #1877f2; color: white; text-decoration: none; border-radius: 6px; font-weight: bold; transition: background 0.3s;">
            <i class="fab fa-facebook" style="margin-right: 8px;"></i>Watch on Facebook
          </a>
        </div>
      `;
    } else {
      newNode.id = 'mainVideo';
      newNode.controls = true;
      newNode.poster = 'userimages/video-poster.jpg';
      newNode.style.width = '100%';
      newNode.style.height = '500px';
      newNode.style.background = '#000';
      const source = document.createElement('source');
      source.src = filename ? ('uploads/featured_videos/' + filename) : '';
      source.type = 'video/mp4';
      newNode.appendChild(source);
    }

    if (mainVideo && mainVideo.parentNode) {
      mainVideo.parentNode.replaceChild(newNode, mainVideo);
    }
    mainVideo = newNode;

    // Adjust video height for phone screens after video switch
    adjustVideoHeight();

    if (mainTitle) mainTitle.textContent = videoObj.title || '';
    if (mainDesc) mainDesc.textContent = videoObj.description || '';
    if (mainDate) mainDate.textContent = formatDate(videoObj.created_at || '');
  }

  function renderVideoList() {
    videoListContainer.innerHTML = '';
    if (!videosData || videosData.length === 0) {
      videoListContainer.innerHTML = '<p style="text-align:center; grid-column:1/-1; padding:20px;">No featured videos.</p>';
      return;
    }

    const start = (videoCurrentPage - 1) * videosPerPageClient;
    const end = start + videosPerPageClient;
    const pageVideos = videosData.slice(start, end);

    pageVideos.forEach((video, idx) => {
      const actualIndex = start + idx;
      const item = document.createElement('div');
      item.className = 'video-list-item';
      item.dataset.index = actualIndex;
      item.dataset.filename = video.filename || '';
      item.dataset.url = video.url || '';
      item.dataset.title = video.title || '';
      item.dataset.description = video.description || '';
      item.dataset.date = formatDate(video.created_at || '');

      const thumbWrapper = document.createElement('div');
      thumbWrapper.className = 'thumb-wrapper';

      const durationSpan = document.createElement('span');
      durationSpan.className = 'duration';
      durationSpan.textContent = '0:00';

      if (video.url) {
        const placeholder = document.createElement('div');
        placeholder.className = 'thumb-placeholder';
        placeholder.style.cssText = 'width:100%; height:70px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius:4px; display:flex; align-items:center; justify-content:center;';
        placeholder.innerHTML = '<i class="fas fa-play" style="color:white; font-size:24px;"></i>';
        thumbWrapper.appendChild(placeholder);
      } else {
        const thumbVideo = document.createElement('video');
        thumbVideo.className = 'thumb-video';
        thumbVideo.muted = true;
        thumbVideo.preload = 'metadata';
        const src = document.createElement('source');
        src.src = video.filename ? ('uploads/featured_videos/' + video.filename) : '';
        src.type = 'video/mp4';
        thumbVideo.appendChild(src);
        thumbWrapper.appendChild(thumbVideo);

        thumbVideo.addEventListener('loadedmetadata', () => {
          const sec = Math.floor(thumbVideo.duration || 0);
          const h = Math.floor(sec / 3600);
          const m = Math.floor((sec % 3600) / 60);
          const s = sec % 60;
          const formatted = (h>0? h+':':'') + String(m).padStart(2,'0') + ':' + String(s).padStart(2,'0');
          durationSpan.textContent = formatted;
        });
      }

      thumbWrapper.appendChild(durationSpan);

      const info = document.createElement('div');
      info.className = 'info';
      info.innerHTML = `<h4>${video.title ? video.title : ''}</h4><p class="list-date">${formatDate(video.created_at || '')}</p>`;

      item.appendChild(thumbWrapper);
      item.appendChild(info);

      item.addEventListener('click', () => {
        setMainVideo(video);
      });

      videoListContainer.appendChild(item);
    });
  }

  function renderVideoPagination() {
    // Pagination removed - all videos are now displayed in a scrollable list
  }

  // Initial render for videos
  renderVideoList();

  // Ensure main video reflects the first video record (keeps server-rendered main in sync)
  if (videosData && videosData.length > 0) {
    setMainVideo(videosData[0]);
  }
</script>
 <?php include '<facilities>.php'; ?>
<?php include 'footer/footer.php'; ?>