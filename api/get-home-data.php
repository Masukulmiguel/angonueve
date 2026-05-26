<?php
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $employees = [];
    $stmt = $pdo->prepare("SELECT id, name, position, function_desc, photo, email FROM users WHERE role = 'employee' AND status = 'active' ORDER BY name ASC");
    $stmt->execute();
    while ($row = $stmt->fetch()) {
        $employees[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'position' => $row['position'] ?: 'Membro da Equipa',
            'bio' => $row['function_desc'] ?: '',
            'photo' => $row['photo'] ? (UPLOAD_URL . '/employees/' . $row['photo']) : '',
            'email' => $row['email'] ?: ''
        ];
    }

    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM users WHERE role = 'client' AND status = 'active'");
    $stmt->execute();
    $clientCount = (int)$stmt->fetch()['total'];

    $clients = [];
    if ($clientCount > 0) {
        $stmt = $pdo->prepare("SELECT id, name FROM users WHERE role = 'client' AND status = 'active' ORDER BY name ASC LIMIT 20");
        $stmt->execute();
        while ($row = $stmt->fetch()) {
            $clients[] = [
                'id' => $row['id'],
                'name' => $row['name']
            ];
        }
    }

    $testimonials = [];
    $stmt = $pdo->prepare("SELECT id, name, photo FROM users WHERE role = 'client' AND status = 'active' ORDER BY RAND() LIMIT 3");
    $stmt->execute();
    $sampleClients = $stmt->fetchAll();

    $testimonialTexts = [
        'A ANGONUEVE transformou a nossa presença online. O suporte é excelente e os serviços são de alta qualidade. Recomendo a qualquer empresa que queira profissionalizar a sua presença digital.',
        'Excelente serviço de hospedagem! Desde que migrámos para a ANGONUEVE, o nosso site nunca esteve tão rápido e estável. O suporte técnico é rápido e eficiente.',
        'Contratámos a criação do nosso site institucional e o resultado superou as expectativas. Equipa profissional, entrega no prazo e um design moderno que nos orgulha.'
    ];

    if (count($sampleClients) > 0) {
        foreach ($sampleClients as $i => $client) {
            $photo = '';
            if (!empty($client['photo'])) {
                $photoPath = UPLOAD_DIR . '/clients/' . $client['photo'];
                if (file_exists($photoPath)) {
                    $photo = UPLOAD_URL . '/clients/' . $client['photo'];
                }
            }
            $testimonials[] = [
                'id' => $client['id'],
                'name' => $client['name'],
                'photo' => $photo,
                'text' => $testimonialTexts[$i] ?? 'Serviço excelente! Recomendo a ANGONUEVE para todas as soluções web.',
                'rating' => 5
            ];
        }
    } else {
        $fallback = [
            ['name' => 'Ana Monteiro', 'text' => 'A ANGONUEVE transformou a nossa presença online. O suporte é excelente e os serviços são de alta qualidade. Recomendo a qualquer empresa que queira profissionalizar a sua presença digital.', 'company' => 'Monteiro & Associados'],
            ['name' => 'Carlos Ferreira', 'text' => 'Excelente serviço de hospedagem! Desde que migrámos para a ANGONUEVE, o nosso site nunca esteve tão rápido e estável. O suporte técnico é rápido e eficiente.', 'company' => 'Ferreira Tech'],
            ['name' => 'Joana Lopes', 'text' => 'Contratámos a criação do nosso site institucional e o resultado superou as expectativas. Equipa profissional, entrega no prazo e um design moderno que nos orgulha.', 'company' => 'Lopes & Cia']
        ];
        foreach ($fallback as $fb) {
            $testimonials[] = [
                'id' => 0,
                'name' => $fb['name'],
                'photo' => '',
                'text' => $fb['text'],
                'rating' => 5
            ];
        }
    }

    $cpanelFeatures = [
        [
            'icon' => 'fa-folder-open',
            'title' => 'Gestor de Ficheiros',
            'description' => 'Gerencie todos os ficheiros do seu site directamente pelo painel, sem necessidade de FTP.'
        ],
        [
            'icon' => 'fa-database',
            'title' => 'Bases de Dados MySQL',
            'description' => 'Crie e administre bases de dados MySQL com phpMyAdmin integrado.'
        ],
        [
            'icon' => 'fa-envelope',
            'title' => 'Contas de Email',
            'description' => 'Crie contas de email profissionais com webmail, filtros anti-spam e encaminhamento.'
        ],
        [
            'icon' => 'fa-globe',
            'title' => 'Gestão de Domínios',
            'description' => 'Adicione domínios, subdomínios e redireccionamentos com poucos cliques.'
        ],
        [
            'icon' => 'fa-shield-alt',
            'title' => 'SSL & Segurança',
            'description' => 'Instale certificados SSL grátis (Let\'s Encrypt) e proteja o seu site com firewalls.'
        ],
        [
            'icon' => 'fa-download',
            'title' => 'Backups Automáticos',
            'description' => 'Backups diários automáticos com restauro rápido em caso de necessidade.'
        ],
        [
            'icon' => 'fa-rocket',
            'title' => 'Instalador Softaculous',
            'description' => 'Instale WordPress, Joomla, PrestaShop e mais de 400 scripts com 1 clique.'
        ],
        [
            'icon' => 'fa-chart-bar',
            'title' => 'Estatísticas Detalhadas',
            'description' => 'Acompanhe o tráfego do seu site com Awstats, Webalizer e logs detalhados.'
        ],
        [
            'icon' => 'fa-code',
            'title' => 'PHP Selector',
            'description' => 'Escolha a versão PHP ideal para o seu site entre múltiplas versões disponíveis.'
        ],
        [
            'icon' => 'fa-user-friends',
            'title' => 'Contas FTP',
            'description' => 'Crie contas FTP para acesso seguro e delegue gestão a terceiros.'
        ],
        [
            'icon' => 'fa-tachometer-alt',
            'title' => 'Gestor de Cache',
            'description' => 'Acelere o seu site com sistemas de cache integrados e optimização automática.'
        ],
        [
            'icon' => 'fa-cog',
            'title' => 'Configuração Avançada',
            'description' => 'Aceda a configurações avançadas de DNS, .htaccess e muito mais.'
        ]
    ];

    echo json_encode([
        'success' => true,
        'employees' => $employees,
        'clientCount' => $clientCount,
        'hasClients' => $clientCount > 0,
        'clients' => $clients,
        'testimonials' => $testimonials,
        'cpanelFeatures' => $cpanelFeatures
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Erro ao carregar dados'
    ]);
}
