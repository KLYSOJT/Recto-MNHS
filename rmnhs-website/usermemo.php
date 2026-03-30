<?php
require_once 'connection/db_connection.php';

$type = isset($_GET['type']) ? strtolower($_GET['type']) : 'school';
$typeNames = [
  'school' => 'SCHOOL MEMORANDUM',
  'division' => 'DIVISION MEMORANDUM',
  'deped' => 'DEPED MEMORANDUM',
  'depedorder' => 'DEPED ORDER',
];
$displayName = $typeNames[$type] ?? 'Memorandum';

// Fetch memos from appropriate database table based on type
$memos = [];
if ($type === 'division') {
  $table_name = 'division_memorandum';
} elseif ($type === 'deped') {
  $table_name = 'deped_memorandum';
} elseif ($type === 'depedorder') {
  // new table for DepEd orders; create the table via admin scripts or manually
  $table_name = 'deped_order';
} else {
  $table_name = 'school_memorandum';
}
$sql = "SELECT id, title, description, date, file FROM " . $table_name . " ORDER BY date DESC";
$result = $conn->query($sql);

if ($result) {
  while ($row = $result->fetch_assoc()) {
    $memos[] = $row;
  }
}
?>  
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $displayName; ?> - RMNHS Resources</title>
  <link rel="stylesheet" href="usermemo.css">
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


<!-- Main Content Section -->
<main class="main-content">
  <div class="container">
    <!-- Section Title Header -->
    <div class="section-title-container">
      <h1 class="section-title"><?php echo $displayName; ?></h1>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
      <div class="filter-controls">
        <select class="filter-dropdown" id="yearFilter">
          <option value="">Year</option>
          <option value="2026">2026</option>
          <option value="2025">2025</option>
          <option value="2024">2024</option>
        </select>

        <select class="filter-dropdown" id="monthFilter">
          <option value="">Month</option>
          <option value="01">January</option>
          <option value="02">February</option>
          <option value="03">March</option>
          <option value="04">April</option>
          <option value="05">May</option>
          <option value="06">June</option>
          <option value="07">July</option>
          <option value="08">August</option>
          <option value="09">September</option>
          <option value="10">October</option>
          <option value="11">November</option>
          <option value="12">December</option>
        </select>
      </div>

      <div class="search-box">
        <input type="text" class="search-input" placeholder="Search" id="searchInput">
        <button class="search-btn"><i class="fas fa-search"></i></button>
      </div>
    </div>

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
            <td colspan="4" style="text-align:center; padding:20px;"></td>
          </tr>
          <?php else: ?>
            <?php foreach ($memos as $memo): ?>
            <tr>
              <td><?php echo date('m-d-Y', strtotime($memo['date'])); ?></td>
              <td><?php echo htmlspecialchars($memo['title']); ?></td>
              <td><?php echo htmlspecialchars(substr($memo['description'], 0, 100)); ?></td>
              <td>
                <?php if ($memo['file']): ?>
                  <a href="uploads/memorandum/<?php echo htmlspecialchars($memo['file']); ?>" class="download-link" target="_blank">View</a>
                <?php else: ?>
                  <span style="color:#999;">No file</span>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php endif; ?> 
        </tbody>
      </table>
    </div>

    <!-- Pagination Section -->
    <div class="pagination-container">
      <div class="pagination" id="paginationControls">
        <!-- Pagination buttons generated by JavaScript -->
      </div>
    </div>
  </div>
</main>

<script>
const ROWS_PER_PAGE = 10;
let currentPage = 1;
let allRows = [];
let filteredRows = [];

document.addEventListener('DOMContentLoaded', function() {
  const yearFilter = document.getElementById('yearFilter');
  const monthFilter = document.getElementById('monthFilter');
  const searchInput = document.getElementById('searchInput');
  const searchBtn = document.querySelector('.search-btn');
  const tableBody = document.getElementById('memoTableBody');

  // Utility: create a "no data" row (kept off DOM until needed)
  function createNoDataRow() {
    const tr = document.createElement('tr');
    tr.className = 'no-data';
    const td = document.createElement('td');
    td.colSpan = 4;
    td.style.textAlign = 'center';
    td.style.padding = '20px';
    td.textContent = 'No memorandums found';
    tr.appendChild(td);
    return tr;
  }

  // Get all data rows (exclude any placeholder rows)
  allRows = Array.from(tableBody.querySelectorAll('tr')).filter(row => row.cells.length === 4);

  filteredRows = [...allRows];
  currentPage = 1;
  updatePagination();

  function filterMemos() {
    const year = yearFilter.value;
    const month = monthFilter.value;
    const searchTerm = searchInput.value.toLowerCase();

    filteredRows = allRows.filter(row => {
      const dateCell = row.cells[0].textContent.trim();
      const titleCell = row.cells[1].textContent.toLowerCase();
      const descCell = row.cells[2].textContent.toLowerCase();

      // Year filter: date format is mm-dd-YYYY
      if (year && !dateCell.endsWith(year)) return false;

      // Month filter: check leading month (two digits)
      if (month && !dateCell.startsWith(month)) return false;

      // Search filter: title or description
      if (searchTerm && !titleCell.includes(searchTerm) && !descCell.includes(searchTerm)) return false;

      return true;
    });

    currentPage = 1;
    updatePagination();
  }

  function updatePagination() {
    displayPage();
    generatePaginationButtons();
  }

  function displayPage() {
    const start = (currentPage - 1) * ROWS_PER_PAGE;
    const end = start + ROWS_PER_PAGE;

    // Hide all data rows
    allRows.forEach(row => row.style.display = 'none');

    // Remove any existing no-data row
    const existingNo = tableBody.querySelector('.no-data');
    if (existingNo) existingNo.remove();

    if (filteredRows.length === 0) {
      // Show a single "no data" row
      tableBody.appendChild(createNoDataRow());
    } else {
      // Show rows for current page
      filteredRows.slice(start, end).forEach(row => row.style.display = '');
    }
  }

  function generatePaginationButtons() {
    const paginationControls = document.getElementById('paginationControls');
    paginationControls.innerHTML = '';

    const totalPages = Math.ceil(filteredRows.length / ROWS_PER_PAGE);

    if (totalPages <= 1) {
      paginationControls.style.display = 'none';
      return;
    }

    paginationControls.style.display = 'flex';

    const makeBtn = (text, onClick, disabled) => {
      const a = document.createElement('a');
      a.href = '#';
      a.className = 'pagination-btn';
      a.textContent = text;
      if (disabled) {
        a.style.opacity = '0.5';
        a.style.pointerEvents = 'none';
      }
      a.onclick = (e) => { e.preventDefault(); if (!disabled) onClick(); };
      return a;
    };

    // First
    paginationControls.appendChild(makeBtn('First', () => { currentPage = 1; updatePagination(); }, currentPage === 1));
    // Prev
    paginationControls.appendChild(makeBtn('Prev', () => { if (currentPage > 1) { currentPage--; updatePagination(); } }, currentPage === 1));

    // Page buttons (max 5 shown)
    const maxPagesToShow = 5;
    let startPage = 1;
    let endPage = totalPages;
    if (totalPages > maxPagesToShow) {
      startPage = Math.max(1, currentPage - 2);
      endPage = Math.min(totalPages, currentPage + 2);
      if (startPage > 1) {
        const dots = document.createElement('span'); dots.textContent = '...'; dots.style.padding = '0 8px'; paginationControls.appendChild(dots);
      }
    }

    for (let i = startPage; i <= endPage; i++) {
      const btn = document.createElement('a');
      btn.href = '#';
      btn.className = 'pagination-btn' + (i === currentPage ? ' pagination-active' : '');
      btn.textContent = i;
      btn.onclick = (e) => { e.preventDefault(); currentPage = i; updatePagination(); };
      paginationControls.appendChild(btn);
    }

    if (endPage < totalPages) {
      const dots = document.createElement('span'); dots.textContent = '...'; dots.style.padding = '0 8px'; paginationControls.appendChild(dots);
    }

    // Last
    paginationControls.appendChild(makeBtn('Last', () => { currentPage = totalPages; updatePagination(); }, currentPage === totalPages));
  }

  // Wire up controls
  yearFilter.addEventListener('change', filterMemos);
  monthFilter.addEventListener('change', filterMemos);
  searchInput.addEventListener('input', filterMemos);
  if (searchBtn) searchBtn.addEventListener('click', (e) => { e.preventDefault(); filterMemos(); });
});
</script>

<?php include 'footer/footer.php'; ?>