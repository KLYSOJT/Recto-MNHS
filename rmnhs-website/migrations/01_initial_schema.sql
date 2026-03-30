-- Supabase Migration: Initial Schema for RMNHS Website
-- Run in Supabase SQL Editor

BEGIN;

-- Enable RLS on tables after creation
SET search_path = public;

-- 1. announcement
CREATE TABLE IF NOT EXISTS announcement (
  id BIGSERIAL PRIMARY KEY,
  image TEXT NOT NULL,
  announcement_posts TEXT NOT NULL,
  created_at TIMESTAMPTZ DEFAULT NOW()
);
ALTER TABLE announcement ENABLE ROW LEVEL SECURITY;
CREATE POLICY "Public read announcements" ON announcement FOR SELECT USING (true);

-- 2. news
CREATE TABLE IF NOT EXISTS news (
  id BIGSERIAL PRIMARY KEY,
  image TEXT NOT NULL,
  news_posts TEXT NOT NULL,
  created_at TIMESTAMPTZ DEFAULT NOW()
);
ALTER TABLE news ENABLE ROW LEVEL SECURITY;
CREATE POLICY "Public read news" ON news FOR SELECT USING (true);

-- 3. featured_videos
CREATE TABLE IF NOT EXISTS featured_videos (
  id BIGSERIAL PRIMARY KEY,
  title TEXT NOT NULL,
  description TEXT,
  filename TEXT,
  url TEXT,
  created_at TIMESTAMPTZ DEFAULT NOW()
);
ALTER TABLE featured_videos ENABLE ROW LEVEL SECURITY;
CREATE POLICY "Public read videos" ON featured_videos FOR SELECT USING (true);

-- 4. school_memorandum
CREATE TABLE IF NOT EXISTS school_memorandum (
  id BIGSERIAL PRIMARY KEY,
  title TEXT NOT NULL,
  \"date\" DATE NOT NULL,
  description TEXT,
  file TEXT,
  created_at TIMESTAMPTZ DEFAULT NOW()
);

-- 5. division_memorandum
CREATE TABLE IF NOT EXISTS division_memorandum (
  id BIGSERIAL PRIMARY KEY,
  title TEXT NOT NULL,
  \"date\" DATE NOT NULL,
  description TEXT,
  file TEXT,
  created_at TIMESTAMPTZ DEFAULT NOW()
);

-- 6. deped_order
CREATE TABLE IF NOT EXISTS deped_order (
  id BIGSERIAL PRIMARY KEY,
  title TEXT NOT NULL,
  \"date\" DATE NOT NULL,
  description TEXT,
  file TEXT,
  created_at TIMESTAMPTZ DEFAULT NOW()
);

-- 7. deped_memorandum
CREATE TABLE IF NOT EXISTS deped_memorandum (
  id BIGSERIAL PRIMARY KEY,
  title TEXT NOT NULL,
  \"date\" DATE NOT NULL,
  description TEXT,
  file TEXT,
  created_at TIMESTAMPTZ DEFAULT NOW()
);

-- 8. research
CREATE TABLE IF NOT EXISTS research (
  id BIGSERIAL PRIMARY KEY,
  title TEXT NOT NULL,
  grade TEXT NOT NULL,
  department TEXT NOT NULL,
  year TEXT NOT NULL,
  category TEXT NOT NULL,
  image TEXT,
  file TEXT,
  created_at TIMESTAMPTZ DEFAULT NOW()
);

-- 9. transparency
CREATE TABLE IF NOT EXISTS transparency (
  id BIGSERIAL PRIMARY KEY,
  type TEXT NOT NULL,
  title TEXT NOT NULL,
  \"date\" DATE NOT NULL,
  description TEXT,
  file TEXT,
  created_at TIMESTAMPTZ DEFAULT NOW()
);

-- 10. learning_materials
CREATE TABLE IF NOT EXISTS learning_materials (
  id BIGSERIAL PRIMARY KEY,
  grade TEXT NOT NULL,
  subject TEXT NOT NULL,
  file TEXT NOT NULL,
  path TEXT NOT NULL,
  filesize INTEGER,
  created_at TIMESTAMPTZ DEFAULT NOW()
);

-- 11. organizational_structure
CREATE TABLE IF NOT EXISTS organizational_structure (
  id BIGSERIAL PRIMARY KEY,
  department TEXT NOT NULL UNIQUE,
  mime TEXT,
  image TEXT,
  pdf TEXT,
  pdf_mime TEXT,
  created_at TIMESTAMPTZ DEFAULT NOW(),
  updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- 12. recognized_organization
CREATE TABLE IF NOT EXISTS recognized_organization (
  id BIGSERIAL PRIMARY KEY,
  org_name TEXT NOT NULL,
  date_established DATE,
  adviser_name TEXT,
  mime TEXT,
  image TEXT,
  pdf TEXT,
  pdf_mime TEXT,
  created_at TIMESTAMPTZ DEFAULT NOW(),
  updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- 13. admin_verification
CREATE TABLE IF NOT EXISTS admin_verification (
  id BIGSERIAL PRIMARY KEY,
  email TEXT NOT NULL,
  token TEXT NOT NULL,
  verified BOOLEAN DEFAULT FALSE,
  created_at TIMESTAMPTZ DEFAULT NOW()
);

-- Indexes for performance
CREATE INDEX IF NOT EXISTS idx_announcement_created_at ON announcement (created_at DESC);
CREATE INDEX IF NOT EXISTS idx_news_created_at ON news (created_at DESC);
CREATE INDEX IF NOT EXISTS idx_featured_videos_created_at ON featured_videos (created_at DESC);
CREATE INDEX IF NOT EXISTS idx_school_memorandum_created_at ON school_memorandum (created_at DESC);
CREATE INDEX IF NOT EXISTS idx_division_memorandum_created_at ON division_memorandum (created_at DESC);
CREATE INDEX IF NOT EXISTS idx_deped_order_created_at ON deped_order (created_at DESC);
CREATE INDEX IF NOT EXISTS idx_deped_memorandum_created_at ON deped_memorandum (created_at DESC);
CREATE INDEX IF NOT EXISTS idx_research_created_at ON research (created_at DESC);
CREATE INDEX IF NOT EXISTS idx_transparency_created_at ON transparency (created_at DESC);
CREATE INDEX IF NOT EXISTS idx_learning_materials_created_at ON learning_materials (created_at DESC);
CREATE INDEX IF NOT EXISTS idx_org_structure_created_at ON organizational_structure (created_at DESC);
CREATE INDEX IF NOT EXISTS idx_recognized_org_created_at ON recognized_organization (created_at DESC);
CREATE INDEX IF NOT EXISTS idx_admin_verification_created_at ON admin_verification (created_at DESC);

COMMIT;

-- Post-migration: Insert sample data if needed from rmnhs.sql
-- Update PHP connection to Supabase (use supabase-js client)

