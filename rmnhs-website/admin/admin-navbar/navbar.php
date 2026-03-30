<?php
// helper functions for highlighting the current page
if (!function_exists('isActive')) {
function isActive($href) {
  $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
  $currentQuery = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
  parse_str($currentQuery ?? '', $currentParams);

  $hrefPath = parse_url($href, PHP_URL_PATH) ?: $href;
  $hrefQuery = parse_url($href, PHP_URL_QUERY);
  parse_str($hrefQuery ?? '', $hrefParams);

  if (basename($hrefPath) !== basename($currentPath)) {
    return false;
  }

  foreach ($hrefParams as $k => $v) {
    if (!isset($currentParams[$k]) || $currentParams[$k] != $v) {
      return false;
    }
  }

  return true;
}
}

if (!function_exists('isAnyActive')) {
function isAnyActive($hrefs) {
  foreach ($hrefs as $h) {
    if (isActive($h)) {
      return true;
    }
  }
  return false;
}
}
?>

 <!--  Navigation Bar -->
  <nav class="navbar">
    <div class="logo-container">
      <img src="/rmnhs-website/admin/admin-navbar/rectologo.png" alt="RMNHS Logo" class="logo-img">
      <span class="logo-text">RMNHS</span>
    </div>

    <div class="search-container">
      <input type="text" placeholder="Search">
      <button type="submit"><i class="fa fa-search"></i></button>
    </div>

    <ul class="nav-links">
      <li><a href="/rmnhs-website/admin/home.php" <?php echo isActive('/rmnhs-website/admin/home.php') ? 'class="active"' : ''; ?>>Home</a></li>
      <li class="dropdown">
        <a href="#" onclick="return false" <?php echo isAnyActive(array('/rmnhs-website/admin/organization/structure.php','/rmnhs-website/admin/recognized-structure/recognized.php')) ? 'class="active"' : ''; ?>>About <i class="arrow down"></i></a>
        <ul class="dropdown-menu">
          <li class="dropdown-submenu">
            <a href="/rmnhs-website/admin/organization/structure.php" <?php echo isActive('/rmnhs-website/admin/organization/structure.php') ? 'class="active"' : ''; ?>>Organizational Structure</a>
          </li>
          <li><a href="/rmnhs-website/admin/recognized-structure/recognized.php" <?php echo isActive('/rmnhs-website/admin/recognized-structure/recognized.php') ? 'class="active"' : ''; ?>>Recognized Structure</a></li>
          
        </ul>
      </li>
      <li class="dropdown">
        <a href="#" onclick="return false" <?php echo isAnyActive(array('/rmnhs-website/admin/memorandum/school-memo.php','/rmnhs-website/admin/memorandum/division-memo.php','/rmnhs-website/admin/memorandum/deped-memo.php','/rmnhs-website/admin/memorandum/deped-order.php','/rmnhs-website/admin/learning-materials/grade-level.php')) ? 'class="active"' : ''; ?>>Resources <i class="arrow down"></i></a>
        <ul class="dropdown-menu">
          <li><a href="/rmnhs-website/admin/memorandum/school-memo.php" <?php echo isActive('/rmnhs-website/admin/memorandum/school-memo.php') ? 'class="active"' : ''; ?>>School Memorandum</a></li>
          <li><a href="/rmnhs-website/admin/memorandum/division-memo.php" <?php echo isActive('/rmnhs-website/admin/memorandum/division-memo.php') ? 'class="active"' : ''; ?>>Division Memorandum</a></li>
          <li><a href="/rmnhs-website/admin/memorandum/deped-memo.php" <?php echo isActive('/rmnhs-website/admin/memorandum/deped-memo.php') ? 'class="active"' : ''; ?>>DepEd Memorandum</a></li>
          <li><a href="/rmnhs-website/admin/memorandum/deped-order.php" <?php echo isActive('/rmnhs-website/admin/memorandum/deped-order.php') ? 'class="active"' : ''; ?>>DepEd Order</a></li>
          <li>
            <a href="/rmnhs-website/admin/learning-materials/grade-level.php" <?php echo isActive('/rmnhs-website/admin/learning-materials/grade-level.php') ? 'class="active"' : ''; ?>>Learning Materials</a>
          </li>
        </ul>
      </li>
      <li class="dropdown">
        <a href="#" onclick="return false" <?php echo isAnyActive(array(

            '/rmnhs-website/admin/transparency/transparency.php?type=APP',
            '/rmnhs-website/admin/transparency/transparency.php?type=AWARD_OF_CONTRACTS',
            '/rmnhs-website/admin/transparency/transparency.php?type=BAC',
            '/rmnhs-website/admin/transparency/transparency.php?type=BID_BULLETIN',
            '/rmnhs-website/admin/transparency/transparency.php?type=INVITATION_TO_BID',
            '/rmnhs-website/admin/transparency/transparency.php?type=PHILGEPS',
            '/rmnhs-website/admin/transparency/transparency.php?type=PROCUREMENT_REPORTS',
            '/rmnhs-website/admin/transparency/transparency.php?type=SPTA',
            '/rmnhs-website/admin/transparency/transparency.php?type=SSLG',
            '/rmnhs-website/admin/transparency/transparency.php?type=BSP',
            '/rmnhs-website/admin/transparency/transparency.php?type=GSP',
            '/rmnhs-website/admin/transparency/transparency.php?type=TR',
            '/rmnhs-website/admin/transparency/transparency.php?type=MOOE',
            '/rmnhs-website/admin/transparency/transparency.php?type=REDCROSS'
        )) ? 'class="active"' : ''; ?>>Transparency <i class="arrow down"></i></a>
        <ul class="dropdown-menu">
          <li class="dropdown-submenu">
            <span class="submenu-header">Procurement Bulletin <i class="arrow down"></i></span>
            <ul class="dropdown-submenu-list">
              <li><a href="/rmnhs-website/admin/transparency/transparency.php?type=APP" <?php echo isActive('/rmnhs-website/admin/transparency/transparency.php?type=APP') ? 'class="active"' : ''; ?>>APP</a></li>
              <li><a href="/rmnhs-website/admin/transparency/transparency.php?type=AWARD_OF_CONTRACTS" <?php echo isActive('/rmnhs-website/admin/transparency/transparency.php?type=AWARD_OF_CONTRACTS') ? 'class="active"' : ''; ?>>Award of Contracts</a></li>
              <li><a href="/rmnhs-website/admin/transparency/transparency.php?type=BAC" <?php echo isActive('/rmnhs-website/admin/transparency/transparency.php?type=BAC') ? 'class="active"' : ''; ?>>Bid and Awards Committee</a></li>
              <li><a href="/rmnhs-website/admin/transparency/transparency.php?type=BID_BULLETIN" <?php echo isActive('/rmnhs-website/admin/transparency/transparency.php?type=BID_BULLETIN') ? 'class="active"' : ''; ?>>Bid Bulletin</a></li>
              <li><a href="/rmnhs-website/admin/transparency/transparency.php?type=INVITATION_TO_BID" <?php echo isActive('/rmnhs-website/admin/transparency/transparency.php?type=INVITATION_TO_BID') ? 'class="active"' : ''; ?>>Invitation to Bid</a></li>
              <li><a href="/rmnhs-website/admin/transparency/transparency.php?type=PHILGEPS" <?php echo isActive('/rmnhs-website/admin/transparency/transparency.php?type=PHILGEPS') ? 'class="active"' : ''; ?>>PhilGEPS</a></li>
              <li><a href="/rmnhs-website/admin/transparency/transparency.php?type=PROCUREMENT_REPORTS" <?php echo isActive('/rmnhs-website/admin/transparency/transparency.php?type=PROCUREMENT_REPORTS') ? 'class="active"' : ''; ?>>Procurement Reports</a></li>
            </ul>
          </li>
          <li><a href="/rmnhs-website/admin/transparency/transparency.php?type=SPTA" <?php echo isActive('/rmnhs-website/admin/transparency/transparency.php?type=SPTA') ? 'class="active"' : ''; ?>>SPTA</a></li>
          <li><a href="/rmnhs-website/admin/transparency/transparency.php?type=SSLG" <?php echo isActive('/rmnhs-website/admin/transparency/transparency.php?type=SSLG') ? 'class="active"' : ''; ?>>SSLG</a></li>
          <li><a href="/rmnhs-website/admin/transparency/transparency.php?type=BSP" <?php echo isActive('/rmnhs-website/admin/transparency/transparency.php?type=BSP') ? 'class="active"' : ''; ?>>BSP</a></li>
          <li><a href="/rmnhs-website/admin/transparency/transparency.php?type=GSP" <?php echo isActive('/rmnhs-website/admin/transparency/transparency.php?type=GSP') ? 'class="active"' : ''; ?>>GSP</a></li>
          <li><a href="/rmnhs-website/admin/transparency/transparency.php?type=TR" <?php echo isActive('/rmnhs-website/admin/transparency/transparency.php?type=TR') ? 'class="active"' : ''; ?>>TR</a></li>
          <li><a href="/rmnhs-website/admin/transparency/transparency.php?type=MOOE" <?php echo isActive('/rmnhs-website/admin/transparency/transparency.php?type=MOOE') ? 'class="active"' : ''; ?>>MOOE</a></li>
          <li><a href="/rmnhs-website/admin/transparency/transparency.php?type=REDCROSS" <?php echo isActive('/rmnhs-website/admin/transparency/transparency.php?type=REDCROSS') ? 'class="active"' : ''; ?>>Red Cross</a></li>
        </ul>
      </li>
      <li><a href="/rmnhs-website/admin/research/research.php" <?php echo isActive('/rmnhs-website/admin/research/research.php') ? 'class="active"' : ''; ?>>Research</a></li>
      
      <li class="logout-item">
        <a href="/rmnhs-website/index.php" title="Logout"><i class="fa fa-sign-out"></i></a>
      </li>
    </ul>
  </nav>


<style>

.navbar {
  display: flex;
  align-items: center;
  padding: 10px 20px;
  background-color: #fff;
  font-family: 'Roboto', sans-serif;
  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

/* Logo Styling */
.logo-container {
  display: flex;
  align-items: center;
  margin-right: 30px;
}

.logo-img {
  height: 50px;
  margin-right: 10px;
}

.logo-text {
  font-weight: bold;
  font-style: italic;
  font-size: 1.5rem;
}

/* Search Bar Styling */
.search-container {
  display: flex;
  align-items: center;
  border: 1px solid #800000; /* Maroon border */
  border-radius: 20px;
  padding: 5px 15px;
  margin-right: 40px;
}

.search-container input {
  border: none;
  outline: none;
  font-size: 1rem;
}

/* Nav Links Styling */
.nav-links {
  list-style: none;
  display: flex;
  gap: 25px;
  align-items: center;
  flex: 1;
}

.nav-links a {
  text-decoration: none;
  color: #333;
  font-size: 1rem;
  font-weight: 500;
  transition: all 0.3s ease;
}

.nav-links a:hover {
  background-color: #5d0000; /* Dark Maroon */
  color: white;
  padding: 8px 16px;
  border-radius: 5px;
}

/* Active State (Maroon box) */
.nav-links a.active {
  background-color: #5d0000; /* Dark Maroon */
  color: white;
  padding: 8px 16px;
  border-radius: 5px;
}


/* Dropdown Menu Styling */
.dropdown {
  position: relative;
}

.dropdown-menu {
  display: none;
  position: absolute;
  top: 100%;
  left: 0;
  background-color: white;
  min-width: 250px;
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
  list-style: none;
  padding: 10px 0;
  margin: 0;
  border-radius: 5px;
  z-index: 1000;
}

.dropdown:hover .dropdown-menu {
  display: block;
}

.dropdown-menu li {
  position: relative;
}

.dropdown-menu li a {
  display: block;
  padding: 12px 20px;
  color: #333;
  text-decoration: none;
  font-size: 0.95rem;
  transition: all 0.3s ease;
}

.dropdown-menu li a:hover {
  background-color: #f0f0f0;
  color: #5d0000;
  padding-left: 25px;
}

/* Submenu Styling */
.dropdown-submenu {
  position: relative;
}

.dropdown-submenu-list {
  display: none;
  position: absolute;
  top: 0;
  left: 100%;
  background-color: white;
  min-width: 200px;
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
  list-style: none;
  padding: 10px 0;
  margin: 0;
  border-radius: 5px;

}

.dropdown-submenu:hover .dropdown-submenu-list {
  display: block;
}

.submenu-header {
  display: block;
  padding: 12px 20px;
  color: #333;
  font-size: 0.95rem;
  cursor: default;
  user-select: none;
}

.submenu-header .arrow {
  border: solid #333;
  border-width: 0 2px 2px 0;
  display: inline-block;
  padding: 3px;
  margin-left: 5px;
  transform: translateY(-2px) rotate(45deg);
}

.dropdown-submenu-list li a {
  display: block;
  padding: 12px 20px;
  color: #333;
  text-decoration: none;
  font-size: 0.9rem;
  transition: all 0.3s ease;
}

.dropdown-submenu-list li a:hover {
  background-color: #f0f0f0;
  color: #5d0000;
  padding-right: 25px;
}

/* Simple CSS Arrow for Dropdowns */
.arrow {
  border: solid #333;
  border-width: 0 2px 2px 0;
  display: inline-block;
  padding: 3px;
  margin-left: 5px;
  transform: translateY(-2px) rotate(45deg);
}

a.active .arrow {
  border-color: white;
}
    </style>