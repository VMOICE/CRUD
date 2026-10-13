<?php

require_once __DIR__ . '/services.php';

// Função respond, espera um array como argumento, e ": void" indica que ele não retona um valor
function respond(array $result): void
{
    // Recebe o status do resultado do serviço, ex: 200 - sucesso e 400 - requesiçao inválida
    http_response_code($result['status']);

    // Se houver um erro, monta um array com a mensagem e o converte em JSON.
    if (isset($result['error'])) {
        echo json_encode(['error' => $result['error']]);
    } else {
        echo json_encode($result['data']);
    }
}

//Throwable: é a interface base dos erros e exceções que podem ser capturados por catch
//$e: contém o objeto que representa o erro
function respondServerError(\Throwable $e): void
{
    // O detalhe do erro vai para o log do servidor, nunca para o cliente.
    // Registra o erro no log do servidor como sting
    error_log((string) $e);

    //define o status em 500 (Internal Server Error) e envia uma mensagem de erro para o JSON
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}

// Lê o corpo da requisição, o ?array permite que o resultado seja um array ou null
function readJsonBody(): ?array
{
    //Lê o corpo da requisição HTTP em JSON e converte o JSON para um valor PHP e converte para uma array associativa
    $input = json_decode(file_get_contents('php://input'), true);

    // Verefica se a connversão gerou um array, se sim, retorna tal array
    return is_array($input) ? $input : null;
}

function handleGet(): void
{
    // Passa o resultado para o respond() que define o status HTTP e produz o JSON
    try {
        respond(getAllUsers());
    } 
    // Captura um erro ou excessão ocorrido no try
    catch (\Throwable $e) {
        respondServerError($e);
    }
}

function handlePost(): void
{
    try {
        respond(createUser(readJsonBody()));
    } catch (\Throwable $e) {
        respondServerError($e);
    }
}

// Put e PATCH chamam a mesma função que é editUser() Porém o PATCH passa a partial: true
//fazendo que  função pule o requerimento de preencher todos campos
function handlePut(): void
{
    try {
        respond(editUser($_GET['id'] ?? null, readJsonBody()));
    } catch (\Throwable $e) {
        respondServerError($e);
    }
}

function handlePatch(): void
{
    try {
        respond(editUser($_GET['id'] ?? null, readJsonBody(), partial: true));
    } catch (\Throwable $e) {
        respondServerError($e);
    }
}

function handleDelete(): void
{
    try {
        respond(removeUser($_GET['id'] ?? null));
    } catch (\Throwable $e) {
        respondServerError($e);
    }
}

function handleMethodNotAllowed(): void
{
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}