-- Initialize MySQL database for Tom's Music School Laravel
-- This script runs automatically when the MySQL container is first created

-- Set character set
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- Create database if it doesn't exist (usually created by environment variables)
-- This is a fallback
CREATE DATABASE IF NOT EXISTS toms_music_school 
    CHARACTER SET utf8mb4 
    COLLATE utf8mb4_unicode_ci;

-- Grant privileges (user is created by environment variables)
-- This ensures the user has proper permissions
GRANT ALL PRIVILEGES ON toms_music_school.* TO 'toms_user'@'%';
FLUSH PRIVILEGES;

