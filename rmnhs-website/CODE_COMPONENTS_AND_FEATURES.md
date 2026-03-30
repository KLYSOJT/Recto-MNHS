# RMNHS Website - Code Components and Features

## 📋 Project Overview
**Recto Memorial National High School (RMNHS) Website** - A comprehensive educational institution website with public-facing content and a powerful admin management system.

- **Technology Stack**: PHP, MySQL, JavaScript, CSS3, HTML5
- **Authentication**: Email-based admin verification system with SMTP
- **Content Management**: Full-featured CMS with multiple modules
- **Responsiveness**: Mobile-friendly design with adaptive layouts

---

## 🎯 Main Public Pages

### Core Pages
1. **Home Page** (`index.php`)
   - Featured videos carousel
   - Announcements section with pagination
   - News feed with pagination
   - School information and contact details

2. **About Section**
   - **Organizational Structure** (`department.php`)
     - 8 department cards (TLE, Math, English, Science, Filipino, AP, MAPEH, Values Education)
     - Modal display with department PDFs and images
   - **History Profile** (`history.php`)
     - Bilingual content (English & Filipino)
     - Language toggle functionality
     - Historical narrative with images
   - **Vision, Mission, Core values** (`mvc.php`)
     - Dropdown-based display
     - Custom styling with gradient headers
   - **Recognized Organizations** (`recognized-structure.php`)
     - Organization information display
     - Image and document support

3. **Facilities Page** (`facilities.php`)
   - Grid-based facility cards
   - Image-based card displays
   - Brightness filter effects
   - Interactive card hover states

4. **Location Page** (`location.php`)
   - School location information
   - Map integration support

5. **Resources Section**
   - **Memorandums** (`usermemo.php`)
     - School Memorandums
     - Division Memorandums
     - DepEd Memorandums
     - DepEd Orders
     - PDF document management
   - **Learning Materials** (`learning-materials.php`)
     - Organized by grade level (7-12)
     - Subject-based categorization
     - PDF file downloads

6. **Research/Research Bulletin** (`research.php`)
   - Advanced filtering (grade, department, year, category)
   - Research publication management
   - PDF and image uploads
   - Searchable database

7. **Transparency Module** (`transparency.php`, `transparency-info.php`)
   - Multiple document types:
     - SPTA (School Parents and Teachers Association)
     - SSLG (Supreme Secondary Learners Government)
     - BSP (Boy Scout of the Philippines)
     - GSP (Girl Scouts of the Philippines)
     - The Rectorian (School Magazine)
     - MOOE (Maintenance and Other Operating Expenses)
     - Red Cross
     - Procurement Bulletin (APP, Award of Contracts, BAC, Bid Bulletin, etc.)
   - Year and month filtering
   - Document categorization

8. **Procurement Page** (`procurement.php`)
   - Procurement bulletin management
   - Document uploads and filtering
   - Display by type and date

9. **Search Page** (`search.php`)
   - Global search across all content
   - Results grouped by type
   - Links to relevant pages

---

## 🛡️ Admin Dashboard & Authentication

### Authentication System
- **Login Page** (`admin/login.php`)
  - Email-based login with security key
  - "Remember this device" option
  - Glass-morphism UI design

- **Verification System** (`admin/verify_admin.php`)
  - Email verification token generation
  - Token validation
  - Secure session management
  - SMTP email integration (Gmail)
  - Database token storage

- **Credentials**
  - Email: `ojtstudents2026@gmail.com`
  - Password: `123` (hardcoded for verification)

### Admin Home Dashboard (`admin/home.php`)
- Overview of announcements, news, and videos
- Display of current content
- Quick statistics
- File size management for uploads

---

## 📝 Content Management Modules

### 1. Announcements Management
**Files**: `admin/publish_announcement.php`, `admin/edit_item.php`, `admin/delete_item.php`
- Create announcements with image attachments
- Image upload validation (JPG, PNG, GIF, WebP - Max 5MB)
- Edit existing announcements
- Delete announcements with image cleanup
- Database storage in `announcement` table
- Upload directory: `uploads/announcements/`

### 2. News Management
**Files**: `admin/publish_news.php`
- Create news posts with images
- Same image validation as announcements
- Edit and delete functionality
- Database storage in `news` table
- Upload directory: `uploads/news/`

### 3. Featured Videos Management
**Files**: `admin/publish_video.php`, `admin/edit_item.php`
- Upload video files (MP4, WebM, OGG, AVI, MKV - Max 100MB)
- Add external video URLs (YouTube, Vimeo, Google Drive)
- Auto-conversion to embed-friendly URLs
- Title and description metadata
- Database storage in `featured_videos` table
- Upload directory: `uploads/featured_videos/`

---

## 🔬 Research Module

**Files**: `admin/research/research.php`, `admin/research/save_research.php`, `admin/research/load_research.php`, `admin/research/delete_research.php`

### Features:
- Research title input
- Grade level selection (7-12)
- Department categorization (Science, Mathematics, English, Social Studies, Technology, Arts)
- Year of publication (2021-2026)
- Research category/field (Game, STEM, etc.)
- Image upload for research thumbnail
- PDF upload for research document
- Database storage in `research` table
- Upload directory: `uploads/research_images/` and `uploads/research_pdfs/`

### Frontend Filtering:
- Search by title
- Filter by grade level
- Filter by department
- Filter by year
- Filter by category

---

## 📚 Memorandum Management Module

**Files**: `admin/memorandum/` directory with separate handlers for each type

### Types:
1. **School Memorandums** (`school-memo.php`, `save_memo.php`, `load_memo.php`)
2. **Division Memorandums** (`division-memo.php`, `save_division_memo.php`, `load_division_memo.php`)
3. **DepEd Memorandums** (`deped-memo.php`, `save_deped_memo.php`, `load_deped_memo.php`)
4. **DepEd Orders** (`deped-order.php`, `save_deped_order.php`, `load_deped_order.php`)

### Features for Each Type:
- Title input
- Date selection
- Description/notes
- PDF file upload (validation for PDF only)
- Table view with sorting
- Edit functionality with optional file replacement
- Delete with confirmation
- Search/filter capabilities
- Database storage in separate tables per type
- Upload directory: `uploads/memorandum/`

---

## 🏢 Organizational Structure Module

**Files**: `admin/organization/structure.php`, `admin/organization/get_org_structure.php`, `admin/organization/structure_image.php`

### Features:
- 8 Primary Departments:
  - TLE Department
  - Mathematics Department
  - English Department
  - Science Department
  - Filipino Department
  - AP Department
  - MAPEH Department
  - Values Education Department

### Management:
- Upload/update department images
- Manage accomplishment report PDFs
- Multiple PDFs per department (stored in `organizational_structure_pdfs` table)
- Image and PDF MIME type tracking
- Frontend display as modal popups
- Upload directory: `uploads/organizational-structure/`

---

## 🎓 Recognized Organizations Module

**Files**: `admin/recognized-structure/recognized.php`, `admin/organization/get_recognized_structure.php`

### Features:
- Organization name management
- Date established tracking
- Adviser name recording
- Organization logo/image upload
- PDF document storage (charter, bylaws, etc.)
- Create, read, update operations
- Database storage in `recognized_organization` table
- Upload directory: `uploads/recognized-organization/`

---

## 📊 Transparency & SSLG Module

**Files**: `admin/transparency/transparency.php`, `admin/transparency/transparency-info.php`, `admin/transparency/sslg.php`

### Document Types Managed:
1. **Financial Transparency**
   - SPTA (School Parents and Teachers Association)
   - SSLG (Supreme Secondary Learners Government)
   - BSP (Boy Scout of the Philippines)
   - GSP (Girl Scouts of the Philippines)
   - The Rectorian
   - MOOE (Maintenance and Other Operating Expenses)
   - Red Cross

2. **Procurement Documents**
   - APP (Annual Procurement Plan)
   - Award of Contracts
   - BAC (Bid and Awards Committee)
   - Bid Bulletin
   - Invitation to Bid
   - PhilGEPS
   - Procurement Reports

### Features:
- PDF document uploads (validated)
- Title, date, and description metadata
- Upload, edit, delete functionality
- Type categorization
- Database storage in `transparency` table
- Upload directory: `uploads/transparency/`
- Pagination support (10 items per page)
- Year and month filtering

---

## 📖 Learning Materials Module

**Files**: `admin/learning-materials/grade-level.php`

### Organization:
- **By Grade Level**: 7, 8, 9, 10, 11, 12
- **By Subject**: Organized in subdirectories
- **File Type**: PDF documents only

### Features:
- Upload PDFs with subject assignment
- Grade and subject selection
- File size tracking
- Edit grade/subject and optionally replace file
- Delete files with directory cleanup
- Database storage in `learning_materials` table
- Upload directory: `uploads/learning-materials/{grade}/{subject}/`

---

## 🗂️ Database Schema

### Tables Structure:

1. **announcement**
   - id, image, announcement_posts, created_at

2. **news**
   - id, image, news_posts, created_at

3. **featured_videos**
   - id, title, description, filename, url, created_at

4. **research**
   - id, title, grade, department, year, category, image, pdf, created_at

5. **school_memorandum**
   - id, title, date, description, file, created_at

6. **division_memorandum**
   - id, title, date, description, file, created_at

7. **deped_memorandum**
   - id, title, date, description, file, created_at

8. **deped_order**
   - id, title, date, description, file, created_at

9. **organizational_structure**
   - id, department, image, mime, created_at

10. **organizational_structure_pdfs**
    - id, org_structure_id, pdf_filename, pdf_mime, created_at

11. **recognized_organization**
    - id, org_name, date_established, adviser_name, mime, image, pdf, pdf_mime, created_at, updated_at

12. **transparency**
    - id, type, title, date, description, file, created_at

13. **learning_materials**
    - id, grade, subject, file, path, filesize, created_at

14. **admin_verification**
    - id, email, token, verified, created_at

---

## 🎨 UI/UX Components

### Layout Components
1. **Navigation Bar** (`navbar/navbar.php`)
   - Logo and branding
   - Search functionality
   - Dropdown menus for multi-level navigation
   - Active page highlighting
   - Mobile menu toggle

2. **Admin Navigation** (`admin/admin-navbar/navbar.php`)
   - Similar structure to public navbar
   - Adjusted links for admin pages
   - Dropdown for subpages

3. **Header Component** (`header/header.php`)
   - School name and branding
   - Contact information (phone, email)
   - Background image
   - Responsive design with clamp() sizing

4. **Footer Component** (`footer/footer.php`)
   - Logo section (4-grid layout)
   - Facilities links
   - Contact information
   - Social media icons (Facebook, Gmail, Google Maps)
   - Copyright notice

### CSS Stylesheets

1. **Unified CSS** (`assets/css/unified.css`)
   - CSS custom properties/variables
   - Base styles and resets
   - Common component styles
   - Responsive layout utilities
   - Color scheme and typography

2. **Home CSS** (`assets/css/home.css`)
   - Admin dashboard styling
   - Card layouts
   - Upload zone styling
   - Form elements

3. **Login CSS** (`assets/css/login.css`)
   - Glass-morphism design
   - Brand panel styling
   - Form animations
   - Responsive login layout

4. **Page-Specific Stylesheets**:
   - `department.css` - Department cards and modals
   - `facilities.css` - Facility cards grid
   - `history.css` - History section with images
   - `location.css` - Location page styling
   - `mvc.css` - Vision/Mission/Core values display
   - `index.css` - Home page styling
   - `learning-materials.css` - Materials layout
   - `research.css` - Research bulletin styling
   - `transparency.css` - Transparency module styling
   - `procurement.css` - Procurement page styling
   - `recognized.css` - Recognized organizations styling
   - `usermemo.css` - Memorandum display
   - `admin/research/research.css` - Research management
   - `admin/memorandum/memo.css` - Memo management
   - `admin/organization/structure.css` - Org structure management
   - `admin/transparency/transparency.css` - Transparency management

---

## 🔌 Utility & Helper Files

### Database & Configuration
1. **Database Connection** (`connection/db_connection.php`)
   - MySQL connection setup
   - Database: `rmnhs`
   - Admin verification table creation
   - UTF-8 charset configuration

2. **SMTP Configuration** (`connection/smtp_config.php`)
   - Gmail SMTP settings
   - App password authentication
   - Email sender configuration
   - Security notes

### Data Fetching
1. **Announcements/News Fetcher** (`includes/fetch_announcements_news.php`)
   - AJAX endpoint for loading announcements
   - AJAX endpoint for loading news
   - Returns JSON format
   - Image path generation
   - Text truncation

### Email Management
1. **PHPMailer Integration** (`phpmailer/` directory)
   - Complete PHPMailer library
   - SMTP email handling
   - OAuth token support
   - Multiple language files

---

## 📤 Upload Systems

### Upload Directories
```
uploads/
├── announcements/        - Announcement images
├── news/                 - News images
├── featured_videos/      - Video files (MP4, WebM, OGG, etc.)
├── research_images/      - Research thumbnails
├── research_pdfs/        - Research documents
├── organizational-structure/ - Department images and PDFs
├── recognized-organization/  - Organization logos and documents
├── learning-materials/   - Grade/Subject/PDF files
├── memorandum/          - Memo PDFs (all types)
└── transparency/        - Transparency document PDFs
```

### File Validation Rules:
- **Images**: JPG, JPEG, PNG, GIF, WebP (Max 5MB)
- **Videos**: MP4, WebM, OGG, AVI, MKV (Max 100MB)
- **PDFs**: PDF only (validated by MIME type)
- **Documents**: Organized in hierarchical structures

---

## 🔐 Security Features

1. **Admin Verification**
   - Email-based verification tokens
   - Token expiration mechanism
   - One-time use verification links
   - Database token storage

2. **File Validation**
   - MIME type checking
   - File extension validation
   - Size limit enforcement
   - Directory path sanitization

3. **Database Security**
   - Prepared statements with parameter binding
   - SQL injection prevention
   - User input sanitization
   - Real escape string fallbacks

4. **Session Management**
   - Admin login verification
   - Session-based access control
   - Token-based email verification

---

## 🎯 API Endpoints

### AJAX/JSON Endpoints
1. `/admin/publish_announcement.php` - POST announcement with image
2. `/admin/publish_news.php` - POST news with image
3. `/admin/publish_video.php` - POST video (file or URL)
4. `/admin/edit_item.php` - POST edit announcement/news/video
5. `/admin/delete_item.php` - POST delete announcement/news/video
6. `/admin/search.php` - GET search results
7. `/admin/memorandum/save_memo.php` - POST memorandum management
8. `/admin/memorandum/load_memo.php` - GET memorandum list
9. `/admin/research/save_research.php` - POST research item
10. `/admin/research/load_research.php` - GET research list
11. `/admin/organization/get_org_structure.php` - GET department info
12. `/admin/organization/get_recognized_structure.php` - GET organization info
13. `/admin/transparency/transparency.php` - POST/DELETE transparency items
14. `/admin/verify_admin.php` - POST login & GET token verification
15. `/includes/fetch_announcements_news.php` - GET announcements/news (JSON)

---

## 📱 Frontend Features

### Dynamic Content
- Pagination (announcements, news, transparency)
- Language toggle (English/Filipino on history page)
- Modal popups (department information, resources)
- Search functionality (global across all content)
- Advanced filtering (research, learning materials)
- Dropdown menus with sub-menus
- Carousel/Slider (featured videos)

### Interactive Elements
- Hover effects on cards
- Smooth transitions
- Loading states
- Form validation
- Error handling
- Success notifications
- Confirmation dialogs

---

## 🛠️ Development Notes

### Configuration Points
- Database connection details in `connection/db_connection.php`
- SMTP credentials in `connection/smtp_config.php`
- File upload limits in individual handlers
- CSS variables in stylesheet headers

### Dependencies
- PHPMailer for email handling
- MySQL for database
- Font Awesome for icons
- Google Fonts for typography

### Important Considerations
- All timestamps stored as `CURRENT_TIMESTAMP`
- Images and PDFs organized hierarchically
- Database tables created on-demand in some modules
- UTF-8 encoding throughout
- Responsive design using CSS Grid and Flexbox
- Mobile-first approach with clamp() for sizing

---

## 📊 Statistics

### Public Pages: 10+
### Admin Management Modules: 7
### Database Tables: 14
### Upload Categories: 9
### Content Types: 8 (Announcements, News, Videos, Research, Memos, Organizations, Learning Materials, Transparency)
### File Types Supported: 10+ (Images, Videos, PDFs)

---

*Last Updated: March 30, 2026*
*Generated from comprehensive codebase scan*
 


 tset