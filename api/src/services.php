<?php

require_once __DIR__ . '/validation.php';
require_once __DIR__ . '/data.php';

function getAllUsers(): array
{
    $data = loadData();

    return ['data' => ['users' => $data['users']], 'status' => 200];
}

function createUser(?array $input): array
{
    // Se $input não for uma array retorna 400 e diz ser inválido
    if (!is_array($input)) {
        return ['error' => 'Invalid JSON body', 'status' => 400];
    }

    // Verefica se nos carmpos requeridos há um erro, se sim interrompe operação
    $error = validateRequiredFields($input, ['name', 'age', 'email']);
    if ($error) {
        return ['error' => $error, 'status' => 400];
    }

    // Verefica os valores, como nome não vazio, idade dentro do intervalo aceito e formato do email
    $error = validateUserFields($input);
    if ($error) {
        return ['error' => $error, 'status' => 400];
    }

    // se não houver erro chama a camada de dados, remove espaços do nome e converte idade para inteiro
    $user = insertUser([
        'name' => trim($input['name']),
        'age' => (int) $input['age'],
        'email' => $input['email'],
    ]);

    // retorna dados se tudo ocorrer com sucesso
    return ['data' => $user, 'status' => 201];
}

// Partial indica se a atualização é parcial (put)
// Mixed permite a funnção receber o valor original da query string
function editUser(mixed $id, ?array $input, bool $partial = false): array
{
    // Valida o ID do usuário, se houver erro retorna 400
    $error = validateUserId($id);
    if ($error) {
        return ['error' => $error, 'status' => 400];
    }

    // Se não for array retorna erro
    if (!is_array($input)) {
        return ['error' => 'Invalid JSON body', 'status' => 400];
    }

    // Valida todos os campos no PUT
    if (!$partial) {
        $error = validateRequiredFields($input, ['name', 'age', 'email']);
        if ($error) {
            return ['error' => $error, 'status' => 400];
        }
    }

    $error = validateUserFields($input);
    if ($error) {
        return ['error' => $error, 'status' => 400];
    }

    $allowed = ['name', 'age', 'email'];
    $fields = array_intersect_key($input, array_flip($allowed));

    if (empty($fields)) {
        return ['error' => 'At least one of name, age or email must be sent', 'status' => 400];
    }

    if (isset($fields['name'])) {
        $fields['name'] = trim($fields['name']);
    }

    if (isset($fields['age'])) {
        $fields['age'] = (int) $fields['age'];
    }

    $user = updateUser((int) $id, $fields);

    if ($user === null) {
        return ['error' => 'User not found', 'status' => 404];
    }

    return ['data' => $user, 'status' => 200];
}

//Remove o usuário do ID recebido e devolve o usuário excluído dentro de 'deleted' => $user
function removeUser(mixed $id): array
{
    $error = validateUserId($id);
    if ($error) {
        return ['error' => $error, 'status' => 400];
    }

    $user = deleteUser((int) $id);

    if ($user === null) {
        return ['error' => 'User not found', 'status' => 404];
    }

    return ['data' => ['deleted' => $user], 'status' => 200];
}