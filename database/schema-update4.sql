-- ============================================================
-- ANGONUEVE - Schema Update: generated_sites
-- ============================================================
USE angonueve_db;

CREATE TABLE IF NOT EXISTS generated_sites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    session_id VARCHAR(64) NULL,
    prompt_text TEXT NOT NULL,
    generated_html LONGTEXT NOT NULL,
    tokens_used INT DEFAULT 0,
    status ENUM('draft', 'pending_payment', 'paid') DEFAULT 'draft',
    invoice_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user (user_id),
    INDEX idx_session (session_id),
    INDEX idx_status (status),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO settings (setting_key, setting_value, setting_group) VALUES
('ai_site_price', '15000', 'general'),
('ai_site_price_label', 'Kz 15.000 (pagamento único)', 'general')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);
