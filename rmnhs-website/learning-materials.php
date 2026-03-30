<?php
// determine grade/subject early to build page title
$grade = isset($_GET['grade']) ? preg_replace('/[^0-9]/', '', $_GET['grade']) : '7';
$subject = isset($_GET['subject']) ? $_GET['subject'] : '';
$titleText = 'RMNHS Learning Materials - Grade ' . htmlspecialchars($grade);
if ($subject) {
  $titleText .= ' - ' . htmlspecialchars($subject);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $titleText; ?></title>
  <link rel="stylesheet" href="assets/css/unified.css">
  <link rel="stylesheet" href="index.css">
  <link rel="stylesheet" href="learning-materials.css">
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
    <?php
    // Use DB to fetch learning materials (fallback to filesystem if empty)
    require_once __DIR__ . '/connection/db_connection.php';

    $grade = isset($_GET['grade']) ? preg_replace('/[^0-9]/', '', $_GET['grade']) : '7';
    $subject = isset($_GET['subject']) ? $_GET['subject'] : '';

    // base uploads directory for learning materials (used for filesystem fallback)
    $baseDir = __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'learning-materials' . DIRECTORY_SEPARATOR . $grade . DIRECTORY_SEPARATOR;

    $folders = [];
    $files = [];

    // get distinct subjects for the grade from DB
    $stmt = $conn->prepare('SELECT DISTINCT subject FROM learning_materials WHERE grade = ? ORDER BY subject');
    if ($stmt) {
      $stmt->bind_param('s', $grade);
      $stmt->execute();
      $res = $stmt->get_result();
      while ($row = $res->fetch_assoc()) {
        $folders[] = $row['subject'];
      }
      $stmt->close();
    }

    // if no subject selected, pick first from DB (or keep empty)
    if (!$subject && count($folders)) {
      $subject = $folders[0];
    }

    // if a subject was requested but it's not available in DB, fall back to first available
    if ($subject && !in_array($subject, $folders, true) && count($folders)) {
      $subject = $folders[0];
    }

    // fetch files from DB for selected grade and subject
    if ($subject) {
      $stmt2 = $conn->prepare('SELECT id, grade, subject, file, path, filesize, created_at FROM learning_materials WHERE grade = ? AND subject = ? ORDER BY created_at DESC');
      if ($stmt2) {
        $stmt2->bind_param('ss', $grade, $subject);
        $stmt2->execute();
        $res2 = $stmt2->get_result();
        while ($r = $res2->fetch_assoc()) {
          $fileName = $r['file'];
          $filePath = $r['path'];
          // build URL: prefer stored path if it looks like a web path, otherwise construct
          if ($filePath && (strpos($filePath, 'uploads') !== false || strpos($filePath, '/') === 0)) {
            $url = $filePath;
          } else {
            $url = 'uploads/learning-materials/' . rawurlencode($grade) . '/' . rawurlencode($subject) . '/' . rawurlencode($fileName);
          }
          $mtime = strtotime($r['created_at']);
          $files[] = [
            'name' => $fileName,
            'path' => $filePath,
            'url'  => $url,
            'mtime'=> $mtime,
            'filesize' => $r['filesize'] ? (int)$r['filesize'] : null,
          ];
        }
        $stmt2->close();
      }
    }

    // fallback: if DB has no subjects/files, scan filesystem as before
    if (empty($folders)) {
      $baseDir = __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'learning-materials' . DIRECTORY_SEPARATOR . $grade . DIRECTORY_SEPARATOR;
      if (is_dir($baseDir)) {
        $items = scandir($baseDir);
        foreach ($items as $it) {
          if ($it === '.' || $it === '..') continue;
          if (is_dir($baseDir . $it)) $folders[] = $it;
        }
      }
    }

    if (empty($files) && $subject) {
      $subjectDir = $subject ? $baseDir . $subject . DIRECTORY_SEPARATOR : '';
      if ($subjectDir && is_dir($subjectDir)) {
        $fitems = scandir($subjectDir);
        foreach ($fitems as $f) {
          if ($f === '.' || $f === '..') continue;
          $full = $subjectDir . $f;
          if (is_file($full)) {
            $files[] = [
              'name' => $f,
              'path' => $full,
              'url'  => 'uploads/learning-materials/' . rawurlencode($grade) . '/' . rawurlencode($subject) . '/' . rawurlencode($f),
              'mtime'=> filemtime($full),
            ];
          }
        }
      }
    }

    // sort files by name or date
    $sort = isset($_GET['sort']) ? $_GET['sort'] : 'name';
    if ($sort === 'date') {
      usort($files, function($a,$b){ return ($b['mtime'] ?? 0) - ($a['mtime'] ?? 0); });
    } else {
      usort($files, function($a,$b){ return strcasecmp($a['name'],$b['name']); });
    }
    ?>

    <main class="lm-wrapper">
    <h1 class="lm-title">GRADE - <?php echo htmlspecialchars($grade); ?></h1>
    <div class="lm-content">
      <aside class="lm-left">
        <h3>FOLDERS</h3>
        <ul class="folder-list">
          <?php if (count($folders) === 0): ?>
            <li class="folder">No subjects found for Grade <?php echo htmlspecialchars($grade); ?></li>
          <?php else: ?>
            <?php foreach ($folders as $f):
              $isActive = ($f === $subject) ? ' active' : '';
              $link = 'learning-materials.php?grade=' . rawurlencode($grade) . '&subject=' . rawurlencode($f);
            ?>
              <li class="folder<?php echo $isActive; ?>">
                <a href="<?php echo $link; ?>"><span class="folder-ic"></span><?php echo htmlspecialchars($f); ?></a>
              </li>
            <?php endforeach; ?>
          <?php endif; ?>
        </ul>
      </aside>

      <section class="lm-right">

        <div class="files-grid">
          <?php if (empty($files)): ?>
            <div class="empty-grid">No files in this subject.</div>
          <?php else: ?>
            <?php foreach ($files as $file):
              $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
              $isPdf = ($ext === 'pdf');
              $fileUrl = $file['url'];
            ?>
              <div class="file-card">
                <a class="file-link" href="<?php echo $fileUrl; ?>" onclick="window.open('<?php echo $fileUrl; ?>','_blank'); return false;" rel="noopener noreferrer">
                  <div class="file-doc">
                    <?php if ($isPdf): ?>
                      <i class="fas fa-file-pdf pdf-file-icon"></i>
                    <?php else: ?>
                      <div class="pdf-label small"><?php echo htmlspecialchars(strtoupper($ext)); ?></div>
                    <?php endif; ?>
                  </div>
                  <div class="file-name"><?php echo htmlspecialchars($file['name']); ?></div>
                </a>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </section>
    </div>
  </main>


   <?php include 'footer/footer.php'; ?>