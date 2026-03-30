<?php
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

function isAnyActive($hrefs) {
  foreach ($hrefs as $h) {
    if (isActive($h)) {
      return true;
    }
  }

  return false;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>RMNHS History Profile</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  
  
  <!--  Navigation Bar -->
    <nav class="navbar">
    <div class="logo-container">
      <img src="userimages/rectologo.png" alt="RMNHS Logo" class="logo-img">
      <span class="logo-text">RMNHS</span>
    </div>

    <form action="search.php" method="get" class="search-container">
      <input type="text" name="query" placeholder="Search" value="<?php echo isset($_GET['query']) ? htmlspecialchars($_GET['query']) : ''; ?>">
      <button type="submit"><i class="fa fa-search"></i></button>
    </form>

    <button class="menu-toggle" type="button" aria-label="Toggle navigation" aria-expanded="false">
      <i class="fas fa-bars"></i>
    </button>

    <ul class="nav-links">
      <li><a href="index.php" <?php echo isActive('index.php') ? 'class="active"' : ''; ?>>Home</a></li>
      <li class="dropdown">
        <a href="#" onclick="return false" <?php echo isAnyActive(array('department.php','recognized-structure.php','history.php','mvc.php')) ? 'class="active"' : ''; ?>>About <i class="arrow down"></i></a>
        <ul class="dropdown-menu">
          <li>
            <a href="department.php" <?php echo isActive('department.php') ? 'class="active"' : ''; ?>>Organizational Structure</a>
          </li>
          <li><a href="recognized-structure.php" <?php echo isActive('recognized-structure.php') ? 'class="active"' : ''; ?>>Recognized Organization </a></li>
          <li><a href="history.php" <?php echo isActive('history.php') ? 'class="active"' : ''; ?>>History Profile</a></li>
          <li><a href="mvc.php" <?php echo isActive('mvc.php') ? 'class="active"' : ''; ?>>VMC</a></li>
        </ul>
      </li>
      <li class="dropdown">
        <a href="#" onclick="return false" <?php echo isAnyActive(array('usermemo.php?type=School','usermemo.php?type=Division','usermemo.php?type=deped','usermemo.php?type=depedorder','learning-materials.php?grade=7','learning-materials.php?grade=8','learning-materials.php?grade=9','learning-materials.php?grade=10','learning-materials.php?grade=11','learning-materials.php?grade=12')) ? 'class="active"' : ''; ?>>Resources <i class="arrow down"></i></a>
        <ul class="dropdown-menu">
          <li><a href="usermemo.php?type=School" <?php echo isActive('usermemo.php?type=School') ? 'class="active"' : ''; ?>>School Memorandum</a></li>
          <li><a href="usermemo.php?type=Division" <?php echo isActive('usermemo.php?type=Division') ? 'class="active"' : ''; ?>>Division Memorandum</a></li>
          <li><a href="usermemo.php?type=deped" <?php echo isActive('usermemo.php?type=deped') ? 'class="active"' : ''; ?>>DepEd Memorandum</a></li>
          <li><a href="usermemo.php?type=depedorder" <?php echo isActive('usermemo.php?type=depedorder') ? 'class="active"' : ''; ?>>DepEd Order</a></li>
          <li class="dropdown-submenu">
            <a href="#" onclick="return false" <?php echo isAnyActive(array('learning-materials.php?grade=7','learning-materials.php?grade=8','learning-materials.php?grade=9','learning-materials.php?grade=10','learning-materials.php?grade=11','learning-materials.php?grade=12')) ? 'class="active"' : ''; ?>>Learning Materials <i class="arrow down"></i></a>
            <ul class="dropdown-submenu-list">
              <li><a href="learning-materials.php?grade=7" <?php echo isActive('learning-materials.php?grade=7') ? 'class="active"' : ''; ?>>Grade 7</a></li>
              <li><a href="learning-materials.php?grade=8" <?php echo isActive('learning-materials.php?grade=8') ? 'class="active"' : ''; ?>>Grade 8</a></li>
              <li><a href="learning-materials.php?grade=9" <?php echo isActive('learning-materials.php?grade=9') ? 'class="active"' : ''; ?>>Grade 9</a></li>
              <li><a href="learning-materials.php?grade=10" <?php echo isActive('learning-materials.php?grade=10') ? 'class="active"' : ''; ?>>Grade 10</a></li>
              <li><a href="learning-materials.php?grade=11" <?php echo isActive('learning-materials.php?grade=11') ? 'class="active"' : ''; ?>>Grade 11</a></li>
              <li><a href="learning-materials.php?grade=12" <?php echo isActive('learning-materials.php?grade=12') ? 'class="active"' : ''; ?>>Grade 12</a></li>
            </ul>
          </li>
        </ul>
      </li>
      <li class="dropdown">
        <a href="#" onclick="return false" <?php echo isAnyActive(array('transparency-info.php','transparency.php?type=SPTA','transparency.php?type=SSLG','transparency.php?type=BSP','transparency.php?type=GSP','transparency.php?type=TR','transparency.php?type=MOOE','transparency.php?type=REDCROSS','transparency.php?type=PROCUREMENT_BULLETIN','transparency.php?type=APP','transparency.php?type=AWARD_OF_CONTRACTS','transparency.php?type=BAC','transparency.php?type=BID_BULLETIN','transparency.php?type=INVITATION_TO_BID','transparency.php?type=PHILGEPS','transparency.php?type=PROCUREMENT_REPORTS','procurement.php')) ? 'class="active"' : ''; ?> >Transparency <i class="arrow down"></i></a>
        <ul class="dropdown-menu">
          <li><a href="transparency-info.php" <?php echo isActive('transparency-info.php') ? 'class="active"' : ''; ?>>Transparency Information</a></li>
          <li class="dropdown-submenu">
            <a href="#" onclick="return false" <?php echo isAnyActive(array('transparency.php?type=PROCUREMENT_BULLETIN','transparency.php?type=APP','transparency.php?type=AWARD_OF_CONTRACTS','transparency.php?type=BAC','transparency.php?type=BID_BULLETIN','transparency.php?type=INVITATION_TO_BID','transparency.php?type=PHILGEPS','transparency.php?type=PROCUREMENT_REPORTS','procurement.php')) ? 'class="active"' : ''; ?>>Procurement Bulletin <i class="arrow down"></i></a>
            <ul class="dropdown-submenu-list">
              <li><a href="transparency.php?type=APP" <?php echo isActive('transparency.php?type=APP') ? 'class="active"' : ''; ?>>APP</a></li>
              <li><a href="transparency.php?type=AWARD_OF_CONTRACTS" <?php echo isActive('transparency.php?type=AWARD_OF_CONTRACTS') ? 'class="active"' : ''; ?>>Award of Contracts</a></li>
              <li><a href="transparency.php?type=BAC" <?php echo isActive('transparency.php?type=BAC') ? 'class="active"' : ''; ?>>Bid and Awards Committee</a></li>
              <li><a href="transparency.php?type=BID_BULLETIN" <?php echo isActive('transparency.php?type=BID_BULLETIN') ? 'class="active"' : ''; ?>>Bid Bulletin</a></li>
              <li><a href="transparency.php?type=INVITATION_TO_BID" <?php echo isActive('transparency.php?type=INVITATION_TO_BID') ? 'class="active"' : ''; ?>>Invitation to Bid</a></li>
              <li><a href="transparency.php?type=PHILGEPS" <?php echo isActive('transparency.php?type=PHILGEPS') ? 'class="active"' : ''; ?>>PhilGEPS</a></li>
              <li><a href="transparency.php?type=PROCUREMENT_REPORTS" <?php echo isActive('transparency.php?type=PROCUREMENT_REPORTS') ? 'class="active"' : ''; ?>>Procurement Reports</a></li>
            </ul>
          </li>
          <li><a href="transparency.php?type=SPTA" <?php echo isActive('transparency.php?type=SPTA') ? 'class="active"' : ''; ?>>SPTA</a></li>
          <li><a href="transparency.php?type=SSLG" <?php echo isActive('transparency.php?type=SSLG') ? 'class="active"' : ''; ?>>SSLG</a></li>
          <li><a href="transparency.php?type=BSP" <?php echo isActive('transparency.php?type=BSP') ? 'class="active"' : ''; ?>>BSP</a></li>
          <li><a href="transparency.php?type=GSP" <?php echo isActive('transparency.php?type=GSP') ? 'class="active"' : ''; ?>>GSP</a></li>
          <li><a href="transparency.php?type=TR" <?php echo isActive('transparency.php?type=TR') ? 'class="active"' : ''; ?>>TR</a></li>
          <li><a href="transparency.php?type=MOOE" <?php echo isActive('transparency.php?type=MOOE') ? 'class="active"' : ''; ?>>MOOE</a></li>
          <li><a href="transparency.php?type=REDCROSS" <?php echo isActive('transparency.php?type=REDCROSS') ? 'class="active"' : ''; ?>>Red Cross</a></li>
        </ul>
        
      </li>
      <li><a href="research.php" <?php echo isActive('research.php') ? 'class="active"' : ''; ?>>Research</a></li>
      <li>
        <a href="location.php" <?php echo isActive('location.php') ? 'class="active"' : ''; ?>>Location</a>
      </li>
    </ul>
    
    <div class="admin-icon-container">
      <a href="admin/login.php" class="admin-icon-link" title="Admin Panel"><i class="fa fa-user-shield"></i></a>
    </div>
  </nav>

  <style>
body {
  font-family: 'Roboto', sans-serif;
}

.navbar {
    display: flex;
  align-items: center;
  padding: 10px 20px;
  background-color: #fff;
  font-family: 'Roboto', sans-serif;
  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
  position: sticky;
  top: 0;
  z-index: 1000;
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

/* Admin Icon Styling */
.admin-icon-container {
  margin-left: auto;
  display: flex;
  align-items: center;
}

.admin-icon-link {
  text-decoration: none;
  color: #333;
  font-size: 1.2rem;
  transition: all 0.3s ease;
  padding: 18px 12px;
  border-radius: 5px;
}

.admin-icon-link:hover {
  background-color: #5d000000; /* Dark Maroon */
  color: #800000;
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
  min-width: 200px;
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
  background-color: #ffffff;
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
  left: 100%; /* Move to right side */
  background-color: white;
  min-width: 200px;
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
  list-style: none;
  padding: 10px 0;
  margin: 0;
  border-radius: 5px;
  margin-left: 0px;
}

.dropdown-submenu:hover .dropdown-submenu-list {
  display: block;
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
  background-color: #ffffff;
  color: #5d0000;
  padding-left: 25px;
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

.menu-toggle {
  display: none;
  border: none;
  background: transparent;
  color: #5d0000;
  font-size: 1.4rem;
  cursor: pointer;
  margin-left: auto;
  padding: 8px 10px;
}

/* Mobile responsive - all phone sizes */
  @media (max-width: 768px) {
    .navbar {
      flex-wrap: wrap;
      gap: 6px;
      padding: 6px 8px;
      z-index: 9999;
    }

  .logo-container {
    margin-right: 0;
    gap: 6px;
  }

  .logo-text {
    font-size: 0.95rem;
    line-height: 1;
  }

  .logo-img {
    height: 30px;
    margin-right: 0;
  }

  .menu-toggle {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    padding: 5px 7px;
  }

  .search-container {
    order: 3;
    width: 100%;
    margin: 0;
    display: none;
    padding: 3px 10px;
    border-radius: 14px;
  }

  .search-container input {
    width: 100%;
    font-size: 0.86rem;
  }

  .search-container button {
    font-size: 0.85rem;
    padding: 3px 5px;
  }

.nav-links {
    order: 4;
    width: 100%;
    display: none;
    flex-direction: column;
    align-items: stretch;
    gap: 4px;
    padding: 4px 0 0;
    margin: 0;
    flex: 0 0 100%;
    overflow: visible;
  }

  .nav-links.mobile-open {
    display: flex;
  }

  .search-container.mobile-open {
    display: flex;
  }

  .admin-icon-container.mobile-open {
    display: flex;
  }

  .nav-links > li > a {
    display: block;
    padding: 7px 9px;
    border-radius: 5px;
    font-size: 0.88rem;
    line-height: 1.2;
  }

  .dropdown {
    width: 100%;
    position: relative;
  }

  .dropdown-menu {
    position: static;
    display: none;
    width: 100%;
    min-width: unset;
    max-width: 100%;
    box-shadow: none;
    border-radius: 0;
    padding: 0;
    margin: 2px 0 0 0;
    background: #ffffff;
    border-left: 3px solid #800000;
    z-index: auto;
  }

  .dropdown-menu li {
    width: 100%;
    display: block;
  }

  .dropdown-menu li a {
    display: block;
    width: 100%;
    white-space: normal;
    padding: 7px 9px 7px 16px;
    font-size: 0.84rem;
    line-height: 1.2;
    color: #333;
  }

  .dropdown-menu li a:hover {
    background-color: #e8d8d8;
    color: #5d0000;
    padding-left: 16px;
  }

  .dropdown-submenu {
    position: relative;
    width: 100%;
  }

  .dropdown-submenu-list {
    position: static;
    display: none;
    width: 100%;
    min-width: unset;
    max-width: 100%;
    box-shadow: none;
    border-radius: 0;
    padding: 0;
    margin: 2px 0 0 0;
    background: #ffffff;
    border-left: 3px solid #5d0000;
    z-index: auto;
  }

  .dropdown-submenu-list li {
    width: 100%;
    display: block;
  }

  .dropdown-submenu-list li a {
    display: block;
    width: 100%;
    white-space: normal;
    padding: 7px 9px 7px 24px;
    font-size: 0.82rem;
    line-height: 1.2;
    color: #333;
  }

  .dropdown-submenu-list li a:hover {
    background-color: #e0d0d0;
    color: #5d0000;
    padding-left: 24px;
  }

.dropdown.mobile-open > .dropdown-menu,
  .dropdown-submenu.mobile-open > .dropdown-submenu-list {
    display: block;
    animation: slideDown 0.3s ease-out;
  }

  @keyframes slideDown {
    from {
      opacity: 0;
      transform: translateY(-10px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .admin-icon-container {
    order: 5;
    width: 100%;
    margin-left: 0;
    display: none;
    justify-content: flex-start;
    padding-top: 4px;
  }

  .admin-icon-link {
    padding: 6px 8px;
    font-size: 0.9rem;
  }

  .dropdown:not(.mobile-open):hover .dropdown-menu,
  .dropdown-submenu:not(.mobile-open):hover .dropdown-submenu-list {
    display: none;
  }
}
  
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
  var menuToggle = document.querySelector('.menu-toggle');
  var navLinks = document.querySelector('.nav-links');

  var searchContainer = document.querySelector('.search-container');
  var adminIconContainer = document.querySelector('.admin-icon-container');

  if (menuToggle && navLinks) {
    menuToggle.addEventListener('click', function () {
      var isOpen = navLinks.classList.toggle('mobile-open');
      if (searchContainer) searchContainer.classList.toggle('mobile-open', isOpen);
      if (adminIconContainer) adminIconContainer.classList.toggle('mobile-open', isOpen);
      menuToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });
  }

  function handleMobileDropdowns() {
    if (window.innerWidth > 768) {
      document.querySelectorAll('.dropdown, .dropdown-submenu').forEach(function (item) {
        item.classList.remove('mobile-open');
      });
      return;
    }

    document.querySelectorAll('.dropdown > a, .dropdown-submenu > a').forEach(function (trigger) {
      if (trigger.dataset.mobileBound === 'true') return;

      trigger.addEventListener('click', function (e) {
        if (window.innerWidth > 768) return;

        var parent = trigger.parentElement;
        if (!parent) return;

        var menu = parent.querySelector(':scope > .dropdown-menu, :scope > .dropdown-submenu-list');
        if (!menu) return;

        e.preventDefault();

        var isOpen = parent.classList.contains('mobile-open');

        Array.from(parent.parentElement.children).forEach(function (sib) {
          if (sib !== parent && sib.classList) {
            sib.classList.remove('mobile-open');
          }
        });

        if (isOpen) {
          parent.classList.remove('mobile-open');
        } else {
          parent.classList.add('mobile-open');
        }
      });

      trigger.dataset.mobileBound = 'true';
    });
  }

  handleMobileDropdowns();
  window.addEventListener('resize', handleMobileDropdowns);
});
</script>

</body>
</html>
