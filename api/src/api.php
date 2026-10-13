<?php

// Arquivo responsavel por passar os métodos http
require_once __DIR__ . '/controllers.php';

// Obtém o método HTTP da requisição e guarda no $method
$method = $_SERVER['REQUEST_METHOD'];

//Verefica qual handler será chamado com base no método da requesição HTTP
match ($method) {
    'GET' => handleGet(),
    'POST' => handlePost(),
    'PUT' => handlePut(),
    'PATCH' => handlePatch(),
    'DELETE' => handleDelete(),
    //Métodos não suportados caem no 405
    default => handleMethodNotAllowed(),
};