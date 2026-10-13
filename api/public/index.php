<?php
//nesta ordem: (1) aplica os headers de CORS, (2) responde ao preflight OPTIONS, e (3) roteia pela URL.

require_once __DIR__ . '/../config/config.php';

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

//Vererficação de CORS
//Se a origem estiver presente nas origens permitidas do config.php passa para o navegador a origem
//senão não faz nada
in_array($origin, $allowedOrigins) ?
    header("Access-Control-Allow-Origin: $origin") : null;
//Passa os métodos permitidos e que content-type pode ser usado em requesições preflight
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
// Passa que a resposta deve ser em JSON
header('Content-Type: application/json');

// se a requesição for "OPTIONS" retornar 204 (No content) e encerra a execução
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// extrai a uri requisitada em tokens para analise posterior
$uri = strtok($_SERVER['REQUEST_URI'], '?');

// Checa se o $uri bate com os caminhos declarados, se for /api/users carrega /../src/api.php
match ($uri) {
    '/api/users' => require __DIR__ . '/../src/api.php',
    default => notFound(),
};

// Se a uri não for encontrada retorna erro 404 (Not found)
function notFound(): void
{
    http_response_code(404);
    echo json_encode(['error' => 'Not found']);
}