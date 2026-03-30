<?php
// Admin-facing search page; similar logic to root search.php but keeps admin navbar
// (page content scanning has been removed)
require_once '../connection/db_connection.php';

$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$groupedResults = [];

function linkForType($type) {
    switch ($type) {
        case 'Announcements':
        case 'News':
        case 'Videos':
            return '/rmnhs-website/index.php';
        case 'Research':
            return '/rmnhs-website/research.php'; 
        case 'Transparency':
            return '/rmnhs-website/transparency.php';
        case 'School Memorandum':
            return '/rmnhs-website/usermemo.php?type=School';
        case 'Division Memorandum':
            return '/rmnhs-website/usermemo.php?type=Division';
        case 'DepEd Orders':
            return '/rmnhs-website/usermemo.php?type=depedorder';
        case 'Learning Materials':
            return '/rmnhs-website/learning-materials.php';
        default:
            return '/rmnhs-website/';
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
  <title>Search Results &ndash; Admin</title>
  <link rel="stylesheet" href="../assets/css/unified.css">
  <link rel="stylesheet" href="../assets/css/home.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    :root{--maroon:#7a0710;--muted:#666}
    body{background:#f4f6f8;font-family: 'Roboto', system-ui, -apple-system, 'Segoe UI', Arial}
    .search-results{max-width:1000px;margin:36px auto;padding:20px}
    .search-results h1{color:var(--maroon);font-size:1.5rem;margin-bottom:16px}
    .results-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:14px}
    .result-card{background:#fff;padding:14px;border-radius:10px;box-shadow:0 8px 20px rgba(19,38,63,0.05);border-left:5px solid var(--maroon);transition:transform .12s ease}
    .result-card a{color:#111;font-weight:700;text-decoration:none}
    .result-card p{margin:8px 0 0;color:var(--muted)}
    .no-results{background:#fff;padding:24px;border-radius:8px;text-align:center;color:var(--muted);box-shadow:0 6px 18px rgba(19,38,63,0.04)}
  </style>
</head>
<body>

<?php include 'admin-navbar/navbar.php'; ?>

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

</body>
</html>
