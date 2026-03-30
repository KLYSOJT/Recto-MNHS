<?php
// determine type & display name for page header/title
$type = isset($_GET['type']) ? strtoupper($_GET['type']) : '';
$displayName = '';
switch ($type) {
  case 'SPTA': $displayName = 'SPTA Transparency'; break;
  case 'SSLG': $displayName = 'SSLG Transparency'; break;
  case 'BSP': $displayName = 'BSP Transparency'; break;
  case 'GSP': $displayName = 'GSP Transparency'; break;
  case 'TR': $displayName = 'TR Transparency'; break;
  case 'MOOE': $displayName = 'MOOE Transparency'; break;
  case 'REDCROSS': $displayName = 'Red Cross Transparency'; break;
  case 'PROCUREMENT_BULLETIN': $displayName = 'Procurement Bulletin'; break;
  case 'APP': $displayName = 'APP'; break;
  case 'AWARD_OF_CONTRACTS': $displayName = 'Award of Contracts'; break;
  case 'BAC': $displayName = 'Bid and Awards Committee'; break;
  case 'BID_BULLETIN': $displayName = 'Bid Bulletin'; break;
  case 'INVITATION_TO_BID': $displayName = 'Invitation to Bid'; break;
  case 'PHILGEPS': $displayName = 'PhilGEPS'; break;
  case 'PROCUREMENT_REPORTS': $displayName = 'Procurements Report'; break;
  default: $displayName = 'Transparency'; break;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $displayName; ?> - RMNHS</title>
  <link rel="stylesheet" href="transparency.css">
  <link rel="stylesheet" href="index.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">


  <?php
    $type = isset($_GET['type']) ? strtoupper($_GET['type']) : '';
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;
    $year = isset($_GET['year']) ? $_GET['year'] : '';
    $month = isset($_GET['month']) ? $_GET['month'] : '';
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    include_once 'connection/db_connection.php';
    $memos = [];
    $where = [];
    $params = [];
    $types = '';
    if ($type) {
      $where[] = 'type = ?';
      $params[] = $type;
      $types .= 's';
    }
    if ($year) {
      $where[] = 'YEAR(date) = ?';
      $params[] = $year;
      $types .= 'i';
    }
    if ($month) {
      $where[] = 'MONTH(date) = ?';
      $params[] = $month;
      $types .= 'i';
    }
    if ($search) {
      $where[] = '(title LIKE ? OR description LIKE ?)';
      $params[] = "%$search%";
      $params[] = "%$search%";
      $types .= 'ss';
    }
    $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
    // Count total memos for pagination
    $sqlCount = "SELECT COUNT(*) FROM transparency $whereSql";
    $stmt = $conn->prepare($sqlCount);
    if ($types) {
      $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $stmt->bind_result($totalRows);
    $stmt->fetch();
    $stmt->close();
    // Fetch memos
    $sql = "SELECT * FROM transparency $whereSql ORDER BY date DESC LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $bindTypes = $types . 'ii';
    $bindParams = array_merge($params, [$limit, $offset]);
    $stmt->bind_param($bindTypes, ...$bindParams);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
      $memos[] = $row;
    }
    $stmt->close();
    $totalPages = ceil($totalRows / $limit);
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

<!-- Main Content Section -->
<main class="main-content">
  <div class="container">
    <!-- Section Title Header -->
    <div class="section-title-container">
      <h1 class="section-title"><?php echo $displayName; ?></h1>
    </div>

    <!-- Filter Section -->
    <form class="filter-section" method="get" id="filterForm">
      <div class="filter-controls">
        <select class="filter-dropdown" name="year" id="yearFilter">
          <option value="">Year</option>
          <option value="2026"<?php if(isset($_GET['year']) && $_GET['year']=='2026') echo ' selected'; ?>>2026</option>
          <option value="2025"<?php if(isset($_GET['year']) && $_GET['year']=='2025') echo ' selected'; ?>>2025</option>
          <option value="2024"<?php if(isset($_GET['year']) && $_GET['year']=='2024') echo ' selected'; ?>>2024</option>
        </select>

        <select class="filter-dropdown" name="month" id="monthFilter">
          <option value="">Month</option>
          <option value="01"<?php if(isset($_GET['month']) && $_GET['month']=='01') echo ' selected'; ?>>January</option>
          <option value="02"<?php if(isset($_GET['month']) && $_GET['month']=='02') echo ' selected'; ?>>February</option>
          <option value="03"<?php if(isset($_GET['month']) && $_GET['month']=='03') echo ' selected'; ?>>March</option>
          <option value="04"<?php if(isset($_GET['month']) && $_GET['month']=='04') echo ' selected'; ?>>April</option>
          <option value="05"<?php if(isset($_GET['month']) && $_GET['month']=='05') echo ' selected'; ?>>May</option>
          <option value="06"<?php if(isset($_GET['month']) && $_GET['month']=='06') echo ' selected'; ?>>June</option>
          <option value="07"<?php if(isset($_GET['month']) && $_GET['month']=='07') echo ' selected'; ?>>July</option>
          <option value="08"<?php if(isset($_GET['month']) && $_GET['month']=='08') echo ' selected'; ?>>August</option>
          <option value="09"<?php if(isset($_GET['month']) && $_GET['month']=='09') echo ' selected'; ?>>September</option>
          <option value="10"<?php if(isset($_GET['month']) && $_GET['month']=='10') echo ' selected'; ?>>October</option>
          <option value="11"<?php if(isset($_GET['month']) && $_GET['month']=='11') echo ' selected'; ?>>November</option>
          <option value="12"<?php if(isset($_GET['month']) && $_GET['month']=='12') echo ' selected'; ?>>December</option>
        </select>
      </div>
      <script>
        // Auto-submit the form when year or month is changed
        document.addEventListener('DOMContentLoaded', function() {
          var yearFilter = document.getElementById('yearFilter');
          var monthFilter = document.getElementById('monthFilter');
          var filterForm = document.getElementById('filterForm');
          if (yearFilter && monthFilter && filterForm) {
            yearFilter.addEventListener('change', function() {
              filterForm.submit();
            });
            monthFilter.addEventListener('change', function() {
              filterForm.submit();
            });
          }
        });
      </script>

      <div class="search-box">
        <input type="text" class="search-input" placeholder="Search" name="search" id="searchInput" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
        <button class="search-btn" type="submit"><i class="fas fa-search"></i></button>
      </div>
      <?php if ($type): ?>
        <input type="hidden" name="type" value="<?php echo htmlspecialchars($type); ?>">
      <?php endif; ?>
    </form>

    <!-- Table Section -->
    <div class="table-container">
      <table class="memo-table">
        <thead>
          <tr>
            <th class="sortable">
              Date Added <span class="sort-icon">↓↑</span>
            </th>
            <th>Title</th>
            <th>Description</th>
            <th>File</th>
          </tr>
        </thead>
        <tbody id="memoTableBody">
          <?php if (empty($memos)): ?>
          <tr>
            <td colspan="4" style="text-align:center; padding:20px;">No memorandums found</td>
          </tr>
          <?php else: ?>
            <?php foreach ($memos as $memo): ?>
            <tr>
              <td><?php echo date('m-d-Y', strtotime($memo['date'])); ?></td>
              <td><?php echo htmlspecialchars($memo['title']); ?></td>
              <td><?php echo htmlspecialchars(substr($memo['description'], 0, 100)); ?></td>
              <td>
                <?php if ($memo['file']): ?>
                  <a href="uploads/transparency/<?php echo htmlspecialchars($memo['file']); ?>" class="download-link" target="_blank">View</a>
                <?php else: ?>
                  <span style="color:#999;">No file</span>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php endif; ?> 
        </tbody>
      </table>
      <?php if ($totalPages > 1): ?>
      <div class="pagination-container">
        <div class="pagination">
          <?php
            $baseUrl = $_SERVER['PHP_SELF'] . '?';
            $queryParams = $_GET;
            unset($queryParams['page']);
            $baseUrl .= http_build_query($queryParams);
            if ($baseUrl && substr($baseUrl, -1) !== '&' && substr($baseUrl, -1) !== '?') $baseUrl .= '&';
          ?>
          <?php if ($page > 1): ?>
            <a class="pagination-btn pagination-first" href="<?php echo $baseUrl . 'page=1'; ?>">First</a>
            <a class="pagination-btn pagination-prev" href="<?php echo $baseUrl . 'page=' . ($page - 1); ?>">Prev</a>
          <?php endif; ?>
          <?php
            $startPage = max(1, $page - 2);
            $endPage = min($totalPages, $page + 2);
            for ($i = $startPage; $i <= $endPage; $i++):
          ?>
            <a class="pagination-btn<?php echo $i == $page ? ' pagination-active' : ''; ?>" href="<?php echo $baseUrl . 'page=' . $i; ?>"><?php echo $i; ?></a>
          <?php endfor; ?>
          <?php if ($page < $totalPages): ?>
            <a class="pagination-btn pagination-next" href="<?php echo $baseUrl . 'page=' . ($page + 1); ?>">Next</a>
            <a class="pagination-btn pagination-last" href="<?php echo $baseUrl . 'page=' . $totalPages; ?>">Last</a>
          <?php endif; ?>
        </div>
      </div>
      <?php endif; ?>
    </div>

    <!-- Pagination Section -->
    <div class="pagination-container">
      <div class="pagination" id="paginationControls">
        <!-- Pagination buttons generated by JavaScript -->
      </div>
    </div>
  </div>
</main>
                            



    
<?php include 'footer/footer.php'; ?>