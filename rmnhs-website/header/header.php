  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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

<style>
.main-header {
  background-color: #5d0000; /* Dark maroon base */
  background-image: url('userimages/bgheader.png');
  color: white;
  padding: clamp(8px, 2vw, 20px) clamp(10px, 5vw, 50px);
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  max-width: 1200px;
  margin: 0 auto;
  flex-wrap: wrap;
  gap: clamp(5px, 2vw, 20px);
}

/* Brand Section */
.brand-section {
  display: flex;
  align-items: center;
  gap: clamp(5px, 2vw, 25px);
}

.vertical-divider {
  width: clamp(1px, 0.3vw, 3px);
  height: clamp(20px, 5vw, 50px);
  background-color: white;
  margin: 0 clamp(5px, 2vw, 25px);
}

.school-name {
  font-family: 'Times New Roman', serif;
  font-size: clamp(1rem, 4vw, 2.5rem);
  line-height: 1.1;
  text-shadow: 2px 2px 4px rgba(0,0,0,0.7);
  margin: 0;
}

/* Contact Section */
.contact-info {
  display: flex;
  gap: clamp(10px, 3vw, 30px);
}

.contact-item {
  display: flex;
  align-items: center;
  gap: clamp(4px, 1.5vw, 12px);
}

.icon-circle {
  background-color: white;
  color: #333;
  width: clamp(14px, 3vw, 20px);
  height: clamp(14px, 3vw, 20px);
  border-radius: 50%;
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: clamp(8px, 1.5vw, 12px);
}

.contact-text {
  display: flex;
  flex-direction: column;
}

.contact-text .label {
  font-size: clamp(8px, 1.5vw, 11px);
  font-weight: bold;
}

.contact-text .value {
  font-size: clamp(7px, 1.3vw, 10px);
}

/* Tablet: 768px */
@media (max-width: 768px) {
  .main-header {
    padding: 12px 15px;
  }
  
  .header-content {
    flex-direction: column;
    text-align: center;
    gap: 10px;
  }
  
  .brand-section {
    flex-direction: column;
    gap: 5px;
  }
  
  .vertical-divider {
    width: 30px;
    height: 2px;
    margin: 5px 0;
  }
  
  .school-name {
    font-size: clamp(1.2rem, 5vw, 2rem);
  }
  
  .contact-info {
    flex-direction: row;
    gap: 15px;
    flex-wrap: wrap;
    justify-content: center;
  }
  
  .contact-item {
    gap: 6px;
  }
}

/* Mobile: 480px */
@media (max-width: 480px) {
  .main-header {
    padding: 10px 12px;
  }
  
  .vertical-divider {
    width: 25px;
    height: 2px;
  }
  
  .school-name {
    font-size: clamp(1rem, 4vw, 1.5rem);
  }
  
  .contact-info {
    flex-direction: column;
    gap: 8px;
  }
  
  .contact-item {
    gap: 5px;
  }
  
  .icon-circle {
    width: 16px;
    height: 16px;
    font-size: 8px;
  }
  
  .contact-text .label {
    font-size: 9px;
  }
  
  .contact-text .value {
    font-size: 8px;
  }
}

/* Small Mobile: 360px */
@media (max-width: 360px) {
  .main-header {
    padding: 8px 10px;
  }
  
  .school-name {
    font-size: clamp(0.9rem, 3.5vw, 1.2rem);
  }
  
  .contact-info {
    gap: 6px;
  }
}

/* Dropdown Menu Styles */
.dropdown {
  position: relative;
}

.dropdown-menu {
  list-style: none;
  padding: 0;
  margin: 10px 0 0 0;
  background-color: white;
  border: 1px solid #ddd;
  border-radius: 5px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  display: none;
  position: absolute;
  top: 100%;
  left: 0;
  min-width: 200px;
  z-index: 1000;
}

.dropdown:hover .dropdown-menu {
  display: block;
}

.dropdown-menu li {
  margin: 10;
}

.dropdown-menu a {
  display: block;
  padding: 12px 16px;
  text-decoration: none;
  color: #333;
  transition: all 0.3s ease;
  cursor: pointer;
}

.dropdown-menu a:hover {
  background-color: #f0f0f0;
  color: #5d0000;
  padding-left: 20px;
}

.dropdown-submenu {
  position: relative;
}

.dropdown-submenu-list {
  list-style: none;
  padding: 0;
  margin: 0;
  background-color: white;
  border: 1px solid #ddd;
  border-radius: 5px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  display: none;
  position: absolute;
  top: 0;
  left: 100%;
  min-width: 200px;
  z-index: 1001;
}

.dropdown-submenu:hover .dropdown-submenu-list {
  display: block;
}

.dropdown-submenu-list li {
  margin: 0;
}

.dropdown-submenu-list a {
  display: block;
  padding: 12px 16px;
  text-decoration: none;
  color: #333;
  transition: all 0.3s ease;
  cursor: pointer;
}

.dropdown-submenu-list a:hover {
  background-color: #f0f0f0;
  color: #5d0000;
  padding-left: 20px;
}
</style>
