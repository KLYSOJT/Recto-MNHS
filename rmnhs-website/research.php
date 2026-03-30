<?php
// Include database connection
include 'connection/db_connection.php';

// Get search parameters
$search_title = isset($_GET['title']) ? trim($_GET['title']) : '';
$search_grade = isset($_GET['grade_level']) ? $_GET['grade_level'] : '';
$search_dept = isset($_GET['department']) ? $_GET['department'] : '';
$search_year = isset($_GET['year_publication']) ? $_GET['year_publication'] : '';
$search_category = isset($_GET['research_category']) ? $_GET['research_category'] : '';

// Build SQL query with filters
$sql = "SELECT * FROM research WHERE 1=1";

// Add filters to SQL query
if (!empty($search_title)) {
  $search_title = $conn->real_escape_string($search_title);
  $sql .= " AND title LIKE '%$search_title%'";
}

if (!empty($search_grade)) {
  $search_grade = $conn->real_escape_string($search_grade);
  $sql .= " AND grade = '$search_grade'";
}

if (!empty($search_dept)) {
  $search_dept = $conn->real_escape_string($search_dept);
  $sql .= " AND department = '$search_dept'";
}

if (!empty($search_year)) {
  $search_year = $conn->real_escape_string($search_year);
  $sql .= " AND year = '$search_year'";
}

if (!empty($search_category)) {
  $search_category = $conn->real_escape_string($search_category);
  $sql .= " AND category = '$search_category'";
}

$sql .= " ORDER BY created_at DESC";

// Execute query and fetch research items
$result = $conn->query($sql);
$filtered_items = [];

if ($result && $result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    // Add additional fields for display
    $row['bg_class'] = (count($filtered_items) % 2 == 0) ? 'light-bg' : 'dark-bg';
    $row['type'] = 'Research'; // Default type
    
    // Construct image path
    if (!empty($row['image'])) {
      $row['image'] = 'uploads/research_images/' . $row['image'];
    } else {
      $row['image'] = 'assets/images/book-cover.png';
    }
    
    // Construct file path
    if (!empty($row['file'])) {
      $row['file'] = 'uploads/research_pdfs/' . $row['file'];
    }
    
    $filtered_items[] = $row;
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>RMNHS Research</title>
  <link rel="stylesheet" href="assets/css/unified.css">
  <link rel="stylesheet" href="index.css">
  <link rel="stylesheet" href="research.css">
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


<!-- Research Bulletin Section -->
<main class="main-content">
  <div class="research-container">
    <h1 class="section-title">Research Bulletin</h1>
    
    <div class="search-section">
      <h2 class="search-title">Search</h2>
      
      <form class="search-form" method="GET" action="">
        <div class="form-group">
          <label for="research-title">Title of the Research</label>
          <input type="text" id="research-title" name="title" placeholder="Enter research title">
        </div>

        <div class="form-group">
          <label for="grade-level">Grade Level</label>
          <select id="grade-level" name="grade_level">
            <option value="">Select Grade Level</option>
            <option value="grade-7">Grade 7</option>
            <option value="grade-8">Grade 8</option>
            <option value="grade-9">Grade 9</option>
            <option value="grade-10">Grade 10</option>
            <option value="grade-11">Grade 11</option>
            <option value="grade-12">Grade 12</option>
          </select>
        </div>

        <div class="form-group">
          <label for="department">Department</label>
          <select id="department" name="department">
            <option value="">Select Department</option>
            <option value="science">Science</option>
            <option value="mathematics">Mathematics</option>
            <option value="english">English</option>
            <option value="social-studies">Social Studies</option>
            <option value="technology">Technology</option>
            <option value="arts">Arts</option>
          </select>
        </div>

        <div class="form-group">
          <label for="year-publication">Year of Publication</label>
          <select id="year-publication" name="year_publication">
            <option value="">Select Year</option>
            <option value="2026">2026</option>
            <option value="2025">2025</option>
            <option value="2024">2024</option>
            <option value="2023">2023</option>
            <option value="2022">2022</option>
            <option value="2021">2021</option>
          </select>
        </div>

        <div class="form-group">
          <label for="research-category">Research Category / Field</label>
          <select id="research-category" name="research_category">
            <option value="">Select Category</option>
            <option value="stem">STEM</option>
            <option value="humanities">Humanities</option>
            <option value="social-science">Social Science</option>
            <option value="applied-science">Applied Science</option>
            <option value="environmental">Environmental</option>
          </select>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn-search">Search</button>
          <button type="reset" class="btn-reset">Reset</button>
        </div>
      </form>
    </div>

    <!-- Research Items Section -->
    <div class="research-items">
      <?php if (empty($filtered_items)): ?>
        <div class="no-results">
          <p>No research items found. Please try different search criteria.</p>
        </div>
      <?php else: ?>
        <?php foreach ($filtered_items as $item): ?>
          <div class="research-item <?php echo $item['bg_class']; ?>">
            <div class="item-image">
              <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['title']; ?>">
            </div>
            <div class="item-content">
              <h3 class="item-title"><?php echo $item['title']; ?></h3>
              <?php if (!empty($item['description'])): ?>
                <p class="item-description"><?php echo $item['description']; ?></p>
              <?php endif; ?>
              <p class="item-meta">
                <span class="meta-item">Grade <?php echo substr($item['grade'], 6); ?> <?php echo ucfirst(str_replace('-', ' ', $item['department'])); ?> Department</span>
              </p>
              <p class="item-meta">
                <span class="meta-item"><?php echo $item['year']; ?></span>
                <span class="meta-item"><?php echo $item['type']; ?></span>
              </p>
              <?php if (!empty($item['file'])): ?>
                <a href="<?php echo $item['file']; ?>" class="btn-view" target="_blank">View</a>
              <?php else: ?>
                <button class="btn-view" onclick="viewResearch(<?php echo $item['id']; ?>, '<?php echo addslashes($item['title']); ?>')">View Details</button>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</main>

<?php include 'footer/footer.php'; ?>