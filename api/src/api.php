<?php

require_once __DIR__ . '/controllers.php';

$method = $_SERVER['REQUEST_METHOD'];

match ($method) {
    'GET' => handleGet(),
    'POST' => handlePost(),
    'PUT' => handlePut(),
    'PATCH' => handlePatch(),
    'DELETE' => handleDelete(),
    //Métodos não suportados caem no 405
    default => handleMethodNotAllowed(),
};