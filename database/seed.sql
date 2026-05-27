-- ============================================================
-- ANGONUEVE - Seed Data
-- ============================================================

USE angonueve_db;

-- ============================================================
-- Default Settings
-- ============================================================
INSERT IGNORE INTO settings (setting_key, setting_value, setting_group) VALUES
('site_name', 'ANGONUEVE', 'general'),
('site_email', 'geral@angonueve.co', 'general'),
('site_phone', '935 603 163', 'general'),
('site_address', 'Luanda, Angola', 'general'),
('whatsapp_number', '244935603163', 'contact'),
('whatsapp_message', 'Olá ANGONUEVE!', 'contact'),
('whatsapp_api_token', '', 'whatsapp'),
('whatsapp_phone_number_id', '', 'whatsapp'),
('whatsapp_business_account_id', '', 'whatsapp'),
('whatsapp_webhook_verify_token', 'angonueve_wa_verify', 'whatsapp'),
('whatsapp_api_version', 'v22.0', 'whatsapp'),
('payment_express_name', 'Express', 'payment'),
('payment_express_number', '+244 900 000 000', 'payment'),
('payment_iban', 'AO06004400000000000012345', 'payment'),
('payment_iban_holder', 'ANGONUEVE - Tecnologia Lda', 'payment'),
('payment_iban_bank', 'Banco Nacional de Angola', 'payment'),
('payment_referencia_entity', '99999', 'payment'),
('payment_referencia_ref', 'ANGONUEVE-{ID}', 'payment'),
('bank_name', 'Banco Angolano de Investimentos', 'payment'),
('bank_holder', 'ANGONUEVE Lda', 'payment'),
('bank_nif', '5000000000', 'payment'),
('invoice_prefix', 'INV-', 'invoice'),
('invoice_next_number', '1001', 'invoice'),
('ai_site_price', '15000', 'general'),
('ai_site_price_label', 'Kz 15.000 (pagamento único)', 'general'),
('mail_driver', 'mail', 'email'),
('smtp_host', '', 'email'),
('smtp_port', '587', 'email'),
('smtp_user', '', 'email'),
('smtp_pass', '', 'email'),
('smtp_encryption', 'tls', 'email'),
('mail_from', 'geral@angonueve.co', 'email'),
('mail_from_name', 'ANGONUEVE', 'email');

-- ============================================================
-- Services
-- ============================================================
INSERT IGNORE INTO services_db (slug, name, description, short_description, icon, features, monthly_price, yearly_price, onetime_price, sort_order) VALUES

('hospedagem', 'Hospedagem de Sites',
'A ANGONUEVE oferece soluções completas de hospedagem de sites com alta disponibilidade e desempenho. Os nossos planos incluem painel de controlo intuitivo, certificados SSL gratuitos e backups automáticos diários. Garantimos uptime de 99.9% para o seu site com servidores otimizados e monitorização 24/7. Suporte técnico especializado disponível para ajudar na migração e configuração do seu site. Ideal para sites institucionais, lojas virtuais e blogs com tráfego crescente.',
'Hospedagem de sites com alta disponibilidade, SSL gratuito, backups diários e suporte técnico especializado.',
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

('criacao-sites', 'Criação de Sites Profissionais',
'Desenvolvimento de websites profissionais e responsivos, adaptados às necessidades específicas de cada Cliente. Design personalizado, otimização para motores de busca (SEO), integração com redes sociais, formulários de contacto e painel de gestão de conteúdo. Ideais para empresas, lojas virtuais, portfolios e projetos pessoais que desejam uma presença online de destaque.',
'Websites profissionais e responsivos com design personalizado, SEO e painel de gestão de conteúdo incluído.',
'fa-code',
'["Design responsivo","Otimização SEO","Redes sociais integradas","Formulários de contacto","Painel de gestão","Domínio e hospedagem incluídos"]',
NULL, NULL, 95000.00, 4);
