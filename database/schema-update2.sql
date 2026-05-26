-- ============================================================
-- ANGONUEVE - Database Schema Update 2
-- New tables: payslips, contracts, services_db
-- ============================================================

USE angonueve_db;

-- ============================================================
-- PAYSLIPS (Folhas de Pagamento)
-- ============================================================
CREATE TABLE IF NOT EXISTS payslips (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    employee_name VARCHAR(255),
    position VARCHAR(255),
    salary DECIMAL(12,2),
    month_year VARCHAR(7),
    bonus DECIMAL(12,2) DEFAULT 0.00,
    deductions DECIMAL(12,2) DEFAULT 0.00,
    net_salary DECIMAL(12,2),
    status ENUM('paid','pending') DEFAULT 'pending',
    generated_at DATETIME,
    paid_at DATETIME NULL,
    notes TEXT NULL,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_employee (employee_id),
    INDEX idx_month_year (month_year),
    INDEX idx_status (status),
    FOREIGN KEY (employee_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- CONTRACTS (Contratos)
-- ============================================================
CREATE TABLE IF NOT EXISTS contracts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    service_slug VARCHAR(100),
    service_name VARCHAR(255),
    contract_number VARCHAR(50) UNIQUE,
    start_date DATE,
    end_date DATE NULL,
    value DECIMAL(12,2),
    payment_frequency ENUM('monthly','yearly','onetime') DEFAULT 'monthly',
    status ENUM('active','expired','cancelled') DEFAULT 'active',
    description TEXT NULL,
    file_path VARCHAR(255) NULL,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_client (client_id),
    INDEX idx_status (status),
    INDEX idx_contract_number (contract_number),
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- SERVICES DB (Serviços dinâmicos a partir da base de dados)
-- ============================================================
CREATE TABLE IF NOT EXISTS services_db (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(100) UNIQUE,
    name VARCHAR(255),
    description TEXT,
    short_description VARCHAR(500),
    icon VARCHAR(100),
    features TEXT,
    image VARCHAR(255) NULL,
    monthly_price DECIMAL(10,2) NULL,
    yearly_price DECIMAL(10,2) NULL,
    onetime_price DECIMAL(10,2) NULL,
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_slug (slug),
    INDEX idx_active (is_active),
    INDEX idx_sort (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Seed data for services_db
-- ============================================================
INSERT IGNORE INTO services_db (slug, name, description, short_description, icon, features, monthly_price, yearly_price, onetime_price, sort_order) VALUES

('hospedagem', 'Hospedagem de Sites',
'A ANGONUEVE oferece soluções completas de hospedagem de sites com alta disponibilidade e desempenho. Os nossos planos incluem painel de controlo intuitivo, certificados SSL gratuitos e backups automáticos diários. Garantimos uptime de 99.9% para o seu site com servidores otimizados e monitorização 24/7. Suporte técnico especializado disponível para ajudar na migração e configuração do seu site. Ideal para sites institucionais, lojas virtuais e blogs com tráfego crescente.',
'Hospedagem de sites com alta disponibilidade, SSL gratuito, backups diários e suporte técnico especializado. Ideal para sites institucionais e lojas virtuais.',
'fa-server',
'["Painel de controlo cPanel","SSL gratuito incluído","Backups diários automáticos","Uptime 99.9%","Suporte técnico 24/7","Migração gratuita"]',
7500.00, 75000.00, NULL, 1),

('dominios', 'Registo de Domínios',
'Registe o seu domínio com a ANGONUEVE e garanta a presença online da sua marca. Oferecemos registo de domínios .co, .ao, .com, .net, .org e muitas outras extensões. O processo de registo é rápido e simples, com renovação automática disponível para evitar a perda do domínio. Inclui gestão de DNS, proteção WHOIS e redirecionamento de subdomínios. Aproveite preços competitivos e suporte dedicado para todas as suas necessidades de domínio.',
'Registo de domínios .co, .ao, .com, .net, .org e outras extensões com gestão de DNS e proteção WHOIS incluída.',
'fa-globe',
'["Múltiplas extensões disponíveis","Gestão de DNS avançada","Proteção WHOIS","Renovação automática","Redirecionamento de subdomínios","Suporte dedicado"]',
3500.00, 35000.00, NULL, 2),

('email-corporativo', 'Email Corporativo',
'Contas de email profissional personalizado com o domínio do Cliente. Inclui painel de gestão avançado, calendário, contactos, tarefas, proteção anti-spam e suporte a IMAP/POP3/SMTP. Sincronização multi-dispositivo com acesso via webmail, Outlook e dispositivos móveis. Armazenamento generoso por conta e gestão centralizada para administradores.',
'Email profissional personalizado com domínio próprio, anti-spam, webmail e sincronização multi-dispositivo.',
'fa-envelope',
'["Contas com domínio próprio","Anti-spam avançado","Webmail e Outlook","Sincronização multi-dispositivo","Painel de gestão","Suporte IMAP/POP3/SMTP"]',
3000.00, 30000.00, NULL, 3),

('criacao-websites', 'Criação de Websites',
'Desenvolvimento de websites profissionais e responsivos, adaptados às necessidades específicas de cada Cliente. Design personalizado, otimização para motores de busca (SEO), integração com redes sociais, formulários de contacto e painel de gestão de conteúdo. Ideais para empresas, lojas virtuais, portfolios e projetos pessoais que desejam uma presença online de destaque.',
'Websites profissionais e responsivos com design personalizado, SEO e painel de gestão de conteúdo incluído.',
'fa-code',
'["Design responsivo","Otimização SEO","Redes sociais integradas","Formulários de contacto","Painel de gestão","Domínio e hospedagem incluídos"]',
NULL, NULL, 95000.00, 4);

-- Note: Add 'payslips' and 'contracts' permissions to the application permission list
-- as needed (no database table change required for permissions).

-- ============================================================
-- PASSWORD RESETS (Recuperação de Password)
-- ============================================================
CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    expires_at DATETIME NOT NULL,
    used TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
