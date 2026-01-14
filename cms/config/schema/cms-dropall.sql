-- Drop all tables from cms.sql
-- Note: Tables must be dropped in the correct order due to foreign key constraints

DROP TABLE IF EXISTS articles_tags;
DROP TABLE IF EXISTS articles;
DROP TABLE IF EXISTS tags;
DROP TABLE IF EXISTS users;
