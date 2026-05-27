-- ============================================================
-- ANGONUEVE - Schema Update 5: WhatsApp Cloud API
-- ============================================================

USE angonueve_db;

-- ============================================================
-- WHATSAPP CONVERSATIONS (Conversas)
-- ============================================================
CREATE TABLE IF NOT EXISTS whatsapp_conversations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_phone VARCHAR(20) NOT NULL,
    client_name VARCHAR(100) DEFAULT '',
    last_message TEXT,
    last_time DATETIME,
    status ENUM('active','archived') DEFAULT 'active',
    unread INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_phone (client_phone),
    INDEX idx_unread (unread),
    INDEX idx_last_time (last_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- WHATSAPP MESSAGES (Mensagens individuais)
-- ============================================================
CREATE TABLE IF NOT EXISTS whatsapp_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conversation_id INT NOT NULL,
    wa_message_id VARCHAR(100),
    direction ENUM('incoming','outgoing') NOT NULL,
    content TEXT,
    content_type VARCHAR(20) DEFAULT 'text',
    status VARCHAR(20) DEFAULT 'sent',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_conversation (conversation_id),
    INDEX idx_wa_message (wa_message_id),
    FOREIGN KEY (conversation_id) REFERENCES whatsapp_conversations(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- WhatsApp Cloud API Settings
-- ============================================================
INSERT INTO settings (setting_key, setting_value, setting_group) VALUES
('whatsapp_api_token', '', 'whatsapp'),
('whatsapp_phone_number_id', '', 'whatsapp'),
('whatsapp_business_account_id', '', 'whatsapp'),
('whatsapp_webhook_verify_token', 'angonueve_wa_verify', 'whatsapp'),
('whatsapp_api_version', 'v22.0', 'whatsapp')
ON DUPLICATE KEY UPDATE setting_group = VALUES(setting_group);
