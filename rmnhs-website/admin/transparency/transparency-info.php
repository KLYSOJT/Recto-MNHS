<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Transparency Information - RMNHS Admin</title>
  <link rel="stylesheet" href="transparency-info.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>
<body>

  <!-- Navigation Bar -->
  <nav class="navbar">
    <div class="logo-container">
      <img src="../../userimages/rectologo.png" alt="RMNHS Logo" class="logo-img">
      <span class="logo-text">RMNHS</span>
    </div>

    <div class="search-container">
      <input type="text" placeholder="Search">
      <button type="submit"><i class="fa fa-search"></i></button>
    </div>

    <ul class="nav-links">
      <li><a href="home.php">Home</a></li>
      <li class="dropdown">
        <a href="#" onclick="return false">About <i class="arrow down"></i></a>
        <ul class="dropdown-menu">
          <li class="dropdown-submenu">
            <a href="organizational-structure.php">Organizational Structure <i class="arrow down"></i></a>
            <ul class="dropdown-submenu-list">
              <li><a href="#">TLE Department</a></li>
              <li><a href="#">Math Department</a></li>
              <li><a href="#">English Department</a></li>
            </ul>
          </li>
          <li><a href="#">Recognized Structure</a></li>
        </ul>
      </li>
      <li><a href="admin-research/research.php">Research</a></li>
      <li class="dropdown">
        <a href="#" onclick="return false">Resources <i class="arrow down"></i></a>
        <ul class="dropdown-menu">
          <li><a href="admin-resources/school_memorandum.php">School Memorandum</a></li>
          <li><a href="admin-resources/division_memorandum.php">Division Memorandum</a></li>
        </ul>
      </li>
      <li class="dropdown">
        <a href="#" onclick="return false" class="active">Transparency <i class="arrow down"></i></a>
        <ul class="dropdown-menu">
          <li><a href="../transparency/transparency-info.php">Transparency Information</a></li>
          <li><a href="transparency.php?type=SPTA">SPTA</a></li>
          <li><a href="transparency.php?type=SSLG">SSLG</a></li>
          <li><a href="transparency.php?type=BSP">BSP</a></li>
          <li><a href="transparency.php?type=GSP">GSP</a></li>
          <li><a href="transparency.php?type=TR">TR</a></li>
          <li><a href="transparency.php?type=MOOE">MOOE</a></li>
          <li><a href="transparency.php?type=REDCROSS">Red Cross</a></li>
        </ul>
      </li>
      <li>
        <a href="#">Location</a>
      </li>
      <li class="logout-item">
        <a href="login.php" title="Logout"><i class="fa fa-sign-out"></i></a>
      </li>
    </ul>
  </nav>

  <!-- Main Content -->
  <main class="main-content">
    <div class="transparency-container">
      <div class="page-header">
        <h1>Transparency Information</h1>
      </div>
      <!-- Content intentionally left blank -->
    </div>
  </main>
</body>
</html>