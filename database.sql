

-- Step 1: Create the database (skip if you already created it in phpMyAdmin)
CREATE DATABASE IF NOT EXISTS `cinestream_db`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

-- Step 2: Select the database to use
USE `cinestream_db`;


-- Step 3: Drop the users table if it already exists (for a clean start)
-- WARNING: This deletes all existing user data if run again
DROP TABLE IF EXISTS `users`;


-- Step 4: Create the users table
CREATE TABLE `users` (
  `id`            INT          NOT NULL AUTO_INCREMENT,   -- Unique ID for each user
  `name`          VARCHAR(100) NOT NULL,                  -- User's full name
  `email`         VARCHAR(150) NOT NULL UNIQUE,           -- Email (must be unique)
  `password`      VARCHAR(255) NOT NULL,                  -- Hashed password (bcrypt)
  `is_subscribed` TINYINT(1)   NOT NULL DEFAULT 0,        -- 0 = not subscribed, 1 = subscribed
  `created_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP, -- Auto-set on insert
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Step 5: Insert sample/test users (optional — for easy testing)
-- Password for both test accounts is: password123
-- (This hash was generated with PHP's password_hash('password123', PASSWORD_DEFAULT))
INSERT INTO `users` (`name`, `email`, `password`, `is_subscribed`) VALUES
(
  'Test User',
  'test@example.com',
  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',  -- password: password
  0  -- Not subscribed
),
(
  'Premium User',
  'premium@example.com',
  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',  -- password: password
  1  -- Subscribed
);

