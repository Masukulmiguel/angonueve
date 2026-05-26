-- ============================================================
-- ANGONUEVE - Database Schema Update
-- New tables: invoices, payments, client_services
-- ============================================================

USE angonueve_db;

-- ============================================================
-- INVOICES (Facturas)
-- ============================================================
CREATE TABLE IF NOT EXISTS invoices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_no VARCHAR(20) NOT NULL UNIQUE,
    client_id INT NOT NULL,
    client_name VARCHAR(150) NOT NULL,
    client_email VARCHAR(150) NOT NULL,
    client_phone VARCHAR(50),
    client_address TEXT,
    order_id INT,
    service_name VARCHAR(150),
    plan_name VARCHAR(100),
    description TEXT,
    quantity INT DEFAULT 1,
    unit_price DECIMAL(10,2) NOT NULL,
    discount DECIMAL(10,2) DEFAULT 0,
    tax DECIMAL(10,2) DEFAULT 0,
    total DECIMAL(10,2) NOT NULL,
    status ENUM('pending','paid','cancelled','refunded') DEFAULT 'pending',
    due_date DATE,
    payment_method VARCHAR(50),
    paid_at TIMESTAMP NULL,
    notes TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_client (client_id),
    INDEX idx_order (order_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- PAYMENTS (Pagamentos)
-- ============================================================
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_id INT NOT NULL,
    invoice_no VARCHAR(20) NOT NULL,
    client_id INT NOT NULL,
    client_name VARCHAR(150) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    method ENUM('express','iban','referencia') NOT NULL,
    reference VARCHAR(100),
    proof_file VARCHAR(255),
    proof_original_name VARCHAR(255),
    status ENUM('pending','confirmed','rejected') DEFAULT 'pending',
    admin_notes TEXT,
    confirmed_by INT,
    confirmed_at TIMESTAMP NULL,
    rejected_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_invoice (invoice_id),
    INDEX idx_client (client_id),
    INDEX idx_status (status),
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- CLIENT SERVICES (Acesso a servicos)
-- ============================================================
CREATE TABLE IF NOT EXISTS client_services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    invoice_id INT,
    service_slug VARCHAR(50) NOT NULL,
    service_name VARCHAR(100) NOT NULL,
    plan_name VARCHAR(50),
    status ENUM('pending','active','suspended','expired') DEFAULT 'pending',
    activated_at TIMESTAMP NULL,
    expires_at DATE NULL,
    suspended_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_client (client_id),
    INDEX idx_status (status),
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- LOGIN ATTEMPTS (Rate limiting)
-- ============================================================
CREATE TABLE IF NOT EXISTS login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    email VARCHAR(150),
    attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ip (ip_address),
    INDEX idx_attempted_at (attempted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Add new settings for payment methods
-- ============================================================
INSERT INTO settings (setting_key, setting_value, setting_group) VALUES
('payment_express_name', 'Express', 'payment'),
('payment_express_number', ' +244 900 000 000', 'payment'),
('payment_iban', 'AO06004400000000000012345', 'payment'),
('payment_iban_holder', 'ANGONUEVE - Tecnologia Lda', 'payment'),
('payment_iban_bank', 'Banco Nacional de Angola', 'payment'),
('payment_referencia_entity', '99999', 'payment'),
('payment_referencia_ref', 'ANGONUEVE-{ID}', 'payment'),
('bank_name', 'Banco Angolano de Investimentos', 'payment'),
('bank_holder', 'ANGONUEVE Lda', 'payment'),
('bank_nif', '5000000000', 'payment'),
('invoice_prefix', 'INV-', 'invoice'),
('invoice_next_number', '1001', 'invoice')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);
