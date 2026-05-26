-- ============================================================
-- ANGONUEVE - Database Schema Update 3
-- New table: newsletter
-- ============================================================

USE angonueve_db;

-- ============================================================
-- NEWSLETTER (Subscrições de Email)
-- ============================================================
CREATE TABLE IF NOT EXISTS newsletter (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
