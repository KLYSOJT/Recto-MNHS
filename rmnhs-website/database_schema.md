# RMNHS Website Database Schema

## Overview
This document outlines the current MySQL database schema extracted from `rmnhs.sql` and PHP code analysis. The database is named `rmnhs` and uses MariaDB/MySQL with `utf8mb4_general_ci` charset.

**Total Tables:** 13
- Core content tables for announcements, news, memos, research, etc.
- Admin verification table.
- No users/admins table (likely session-based or hardcoded).
- All tables use InnoDB, auto-increment INT PKs, timestamps where applicable.

**Migration to Supabase (PostgreSQL) Notes:**
- Replace `INT AUTO_INCREMENT PRIMARY KEY` → `SERIAL PRIMARY KEY` or `BIGSERIAL`.
- `timestamp DEFAULT CURRENT_TIMESTAMP` → `TIMESTAMPTZ DEFAULT NOW()`.
- `VARCHAR(255)` → `TEXT` or `VARCHAR(255)`.
- `TEXT` → `TEXT`.
- `DATE` → `DATE`.
- Indexes: Add `CREATE INDEX` on frequent query fields (e.g., created_at DESC).
- Enable RLS (Row-Level Security) for tables post-migration.
- Uploads use filesystem paths; store in Supabase Storage, reference public URLs.
- Run schema in Supabase SQL editor.

## Table Details

### 1. announcement
```sql
CREATE TABLE announcement (
  id SERIAL PRIMARY KEY,
  image VARCHAR(255) NOT NULL,
  announcement_posts TEXT NOT NULL,
  created_at TIMESTAMPTZ DEFAULT NOW()
);
```
- Purpose: School announcements with images/text.

### 2. news
```sql
CREATE TABLE news (
  id SERIAL PRIMARY KEY,
  image VARCHAR(255) NOT NULL,
  news_posts TEXT NOT NULL,
  created_at TIMESTAMPTZ DEFAULT NOW()
);
```

### 3. featured_videos
```sql
CREATE TABLE featured_videos (
  id SERIAL PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  filename VARCHAR(255),
  url VARCHAR(500),
  created_at TIMESTAMPTZ DEFAULT NOW()
);
```
- Supports local files or external URLs.

### 4. school_memorandum
```sql
CREATE TABLE school_memorandum (
  id SERIAL PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  date DATE NOT NULL,
  description TEXT,
  file VARCHAR(255),
  created_at TIMESTAMPTZ DEFAULT NOW()
);
```

### 5. division_memorandum
```sql
CREATE TABLE division_memorandum (
  id SERIAL PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  date DATE NOT NULL,
  description TEXT,
  file VARCHAR(255),
  created_at TIMESTAMPTZ DEFAULT NOW()
);
```

### 6. deped_order
```sql
CREATE TABLE deped_order (
  id SERIAL PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  date DATE NOT NULL,
  description TEXT,
  file VARCHAR(255),
  created_at TIMESTAMPTZ DEFAULT NOW()
);
```

### 7. deped_memorandum (inferred from code)
```sql
CREATE TABLE deped_memorandum (
  id SERIAL PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  date DATE NOT NULL,
  description TEXT,
  file VARCHAR(255),
  created_at TIMESTAMPTZ DEFAULT NOW()
);
```

### 8. research
```sql
CREATE TABLE research (
  id SERIAL PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  grade VARCHAR(50) NOT NULL,
  department VARCHAR(100) NOT NULL,
  year VARCHAR(4) NOT NULL,
  category VARCHAR(100) NOT NULL,
  image VARCHAR(255),
  file VARCHAR(255),
  created_at TIMESTAMPTZ DEFAULT NOW()
);
```

### 9. transparency
```sql
CREATE TABLE transparency (
  id SERIAL PRIMARY KEY,
  type VARCHAR(50) NOT NULL,
  title VARCHAR(255) NOT NULL,
  date DATE NOT NULL,
  description TEXT,
  file VARCHAR(255),
  created_at TIMESTAMPTZ DEFAULT NOW()
);
```

### 10. learning_materials
```sql
CREATE TABLE learning_materials (
  id SERIAL PRIMARY KEY,
  grade VARCHAR(20) NOT NULL,
  subject VARCHAR(150) NOT NULL,
  file VARCHAR(255) NOT NULL,
  path VARCHAR(500) NOT NULL,
  filesize INT,
  created_at TIMESTAMPTZ DEFAULT NOW()
);
```

### 11. organizational_structure
```sql
CREATE TABLE organizational_structure (
  id SERIAL PRIMARY KEY,
  department VARCHAR(100) NOT NULL UNIQUE,
  mime VARCHAR(100),
  image VARCHAR(255),
  pdf VARCHAR(255),
  pdf_mime VARCHAR(100),
  created_at TIMESTAMPTZ DEFAULT NOW(),
  updated_at TIMESTAMPTZ DEFAULT NOW()
);
```

### 12. recognized_organization
```sql
CREATE TABLE recognized_organization (
  id SERIAL PRIMARY KEY,
  org_name VARCHAR(255) NOT NULL,
  date_established DATE,
  adviser_name VARCHAR(255),
  mime VARCHAR(100),
  image VARCHAR(255),
  pdf VARCHAR(255),
  pdf_mime VARCHAR(100),
  created_at TIMESTAMPTZ DEFAULT NOW(),
  updated_at TIMESTAMPTZ DEFAULT NOW()
);
```

### 13. admin_verification
```sql
CREATE TABLE admin_verification (
  id SERIAL PRIMARY KEY,
  email VARCHAR(255) NOT NULL,
  token VARCHAR(64) NOT NULL,
  verified BOOLEAN DEFAULT FALSE,
  created_at TIMESTAMPTZ DEFAULT NOW()
);
```

## Sample Data
- `announcement` and `news` have 2 sample rows each.
- Others empty.

## Indexes Recommendations (Supabase)
```sql
-- Common for lists (latest first)
CREATE INDEX idx_created_at ON announcement(created_at DESC);
CREATE INDEX idx_created_at ON news(created_at DESC);
-- Repeat for other tables...
```

## Next Steps for Supabase Migration
1. Copy-paste Supabase SQL above into SQL editor.
2. Update PHP queries: mysqli → Supabase JS client (supabase-js), rewrite INSERT/SELECT.
3. Handle file uploads via Supabase Storage.
4. Add auth tables if needed (Supabase Auth for admins).
5. Test queries.

This schema covers all codebase references.

