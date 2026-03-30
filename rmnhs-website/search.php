<?php
// Front‑end search page; searches database tables (does not include static page contents)

require_once 'connection/db_connection.php';

$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$groupedResults = [];

function linkForType($type) {
    switch ($type) {
        case 'Announcements':
        case 'News':
        case 'Videos':
            return 'index.php';
        case 'Research':
            return 'research.php';
        case 'Transparency':
            return 'transparency.php';
        case 'School Memorandum':
            return 'usermemo.php?type=School';
        case 'Division Memorandum':
            return 'usermemo.php?type=Division';
        case 'DepEd Orders':
            return 'usermemo.php?type=depedorder';
        case 'Learning Materials':
            return 'learning-materials.php';
        default:
            return 'index.php';
    }
}

function addResult(&$grouped, $type, $row) {
    if (!isset($grouped[$type])) {
        $grouped[$type] = [];
    }
    $grouped[$type][] = $row;
}

if ($query !== '') {
    $term = '%' . $conn->real_escape_string($query) . '%';

    // announcements
    $sql = "SELECT id, announcement_posts AS text FROM announcement WHERE announcement_posts LIKE '$term'";
    if ($res = $conn->query($sql)) {
        while ($r = $res->fetch_assoc()) {
            addResult($groupedResults, 'Announcements', $r);
        }
    }

    // news
    $sql = "SELECT id, news_posts AS text FROM news WHERE news_posts LIKE '$term'";
    if ($res = $conn->query($sql)) {
        while ($r = $res->fetch_assoc()) {
            addResult($groupedResults, 'News', $r);
        }
    }

    // research (search title)
    $sql = "SELECT id, title AS text FROM research WHERE title LIKE '$term'";
    if ($res = $conn->query($sql)) {
        while ($r = $res->fetch_assoc()) {
            addResult($groupedResults, 'Research', $r);
        }
    }

    // transparency
    $sql = "SELECT id, title AS text FROM transparency WHERE title LIKE '$term'";
    if ($res = $conn->query($sql)) {
        while ($r = $res->fetch_assoc()) {
            addResult($groupedResults, 'Transparency', $r);
        }
    }

    // school memorandum
    $sql = "SELECT id, title AS text FROM school_memorandum WHERE title LIKE '$term'";
    if ($res = $conn->query($sql)) {
        while ($r = $res->fetch_assoc()) {
            addResult($groupedResults, 'School Memorandum', $r);
        }
    }

    // division memorandum
    $sql = "SELECT id, title AS text FROM division_memorandum WHERE title LIKE '$term'";
    if ($res = $conn->query($sql)) {
        while ($r = $res->fetch_assoc()) {
            addResult($groupedResults, 'Division Memorandum', $r);
        }
    }

    // deped order
    $sql = "SELECT id, title AS text FROM deped_order WHERE title LIKE '$term'";
    if ($res = $conn->query($sql)) {
        while ($r = $res->fetch_assoc()) {
            addResult($groupedResults, 'DepEd Orders', $r);
        }
    }

    // learning materials (search subject)
    $sql = "SELECT id, subject AS text FROM learning_materials WHERE subject LIKE '$term'";
    if ($res = $conn->query($sql)) {
        while ($r = $res->fetch_assoc()) {
            addResult($groupedResults, 'Learning Materials', $r);
        }
    }

    // featured videos
    $sql = "SELECT id, title AS text FROM featured_videos WHERE title LIKE '$term'";
    if ($res = $conn->query($sql)) {
        while ($r = $res->fetch_assoc()) {
            addResult($groupedResults, 'Videos', $r);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Search Results</title>
  <link rel="stylesheet" href="assets/css/unified.css">
  <link rel="stylesheet" href="index.css">
  <link rel="stylesheet" href="assets/css/home.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    :root {
      --primary: #5d0000;
      --secondary: #ffc107;
      --dark: #2c2c2c;
      --light: #f8f9fa;
      --gray: #6c757d;
      --border: #e9ecef;
      --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.06);
      --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.1);
      --shadow-lg: 0 12px 24px rgba(93, 0, 0, 0.12);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', sans-serif;
      color: var(--dark);
      line-height: 1.65;
    }

    .search-results {
      max-width: 1200px;
      margin: 2.5rem auto;
      padding: 0 1rem;
    }

    .search-results h1 {
      font-size: 2.25rem;
      font-weight: 700;
      color: var(--primary);
      margin-bottom: 2rem;
      position: relative;
      padding-bottom: 1rem;
    }

    .search-results h1::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 80px;
      height: 3px;
      background: linear-gradient(90deg, var(--secondary), var(--primary));
      border-radius: 2px;
    }

    .group {
      margin-bottom: 3rem;
      animation: fadeIn 0.4s ease;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .group h2 {
      font-size: 1.3rem;
      font-weight: 600;
      color: var(--primary);
      margin-bottom: 1.25rem;
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .group h2::before {
      content: '';
      width: 4px;
      height: 24px;
      background: var(--secondary);
      border-radius: 2px;
    }

    .group ul {
      list-style: none;
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
      gap: 1.25rem;
    }

    .group li {
      background: #fff;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: var(--shadow-md);
      transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
      border: 1px solid var(--border);
    }

    .group li::before {
      content: '';
      display: block;
      width: 100%;
      height: 3px;
      background: linear-gradient(90deg, var(--primary), var(--secondary));
      transform: scaleX(0);
      transform-origin: left;
      transition: transform 0.3s ease;
    }

    .group li:hover {
      transform: translateY(-6px);
      box-shadow: var(--shadow-lg);
    }

    .group li:hover::before {
      transform: scaleX(1);
    }

    .group li a {
      display: block;
      padding: 1.5rem;
      color: var(--dark);
      text-decoration: none;
      font-weight: 500;
      font-size: 1.05rem;
      transition: color 0.2s ease;
      line-height: 1.6;
    }

    .group li a:hover {
      color: var(--primary);
    }

    .no-results {
      background: #fff;
      padding: 3rem 2rem;
      border-radius: 12px;
      text-align: center;
      color: var(--gray);
      box-shadow: var(--shadow-md);
      border: 1px solid var(--border);
      font-size: 1.1rem;
      font-weight: 500;
    }

    .no-results::before {
      content: '🔍';
      display: block;
      font-size: 3.5rem;
      margin-bottom: 1rem;
      opacity: 0.4;
    }

    @media (max-width: 768px) {
      .search-results {
        margin: 2rem auto;
      }

      .search-results h1 {
        font-size: 1.75rem;
      }

      .group h2 {
        font-size: 1.15rem;
      }

      .group ul {
        grid-template-columns: 1fr;
      }

      .group li a {
        padding: 1.25rem;
      }
    }

    @media (max-width: 480px) {
      .search-results h1 {
        font-size: 1.5rem;
      }

      .group h2 {
        font-size: 1.05rem;
      }

      .group li a {
        padding: 1rem;
        font-size: 1rem;
      }
    }
  </style>
</head>
<body>

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

<main class="search-results">
  <h1>Search Results for "<?php echo htmlspecialchars($query); ?>"</h1>

  <?php if ($query === ''): ?>
    <p class="no-results">Enter a search term above to begin.</p>
  <?php else: ?>
    <?php if (empty($groupedResults)): ?>
      <p class="no-results">No results found.</p>
    <?php else: ?>
      <?php foreach ($groupedResults as $type => $items): ?>
        <div class="group">
          <h2><?php echo htmlspecialchars($type); ?></h2>
          <ul>
            <?php foreach ($items as $row): ?>
              <?php $link = isset($row['url']) ? $row['url'] : linkForType($type); ?>
              <li><a href="<?php echo htmlspecialchars($link); ?>"><?php echo nl2br(htmlspecialchars(mb_strimwidth($row['text'], 0, 200, '...'))); ?></a></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  <?php endif; ?>
</main>

<?php include 'footer/footer.php'; ?>

</body>
</html>
