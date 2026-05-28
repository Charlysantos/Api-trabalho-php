<?php

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

include("conexao.php");

$method = $_SERVER['REQUEST_METHOD'];

$id = isset($_GET['id']) ? intval($_GET['id']) : null;

# =====================================
# FUNÇÃO DE RESPOSTA
# =====================================

function resposta($status, $dados) {
    http_response_code($status);
    echo json_encode($dados, JSON_UNESCAPED_UNICODE);
    exit();
}

# =====================================
# FUNÇÃO PARA VALIDAR USUÁRIO
# =====================================

function usuarioExiste($conn, $id) {
    $sql = "SELECT * FROM usuarios WHERE id = $id";
    $result = mysqli_query($conn, $sql);

    return mysqli_num_rows($result) > 0;
}

# =====================================
# POST - CRIAR USUÁRIO
# =====================================

if ($method == "POST") {

    $data = json_decode(file_get_contents("php://input"), true);

    if (
        empty($data['nome']) ||
        empty($data['sobrenome']) ||
        empty($data['email']) ||
        empty($data['telefone'])
    ) {
        resposta(400, [
            "error" => "Todos os campos são obrigatórios."
        ]);
    }

    $nome = mysqli_real_escape_string($conn, $data['nome']);
    $sobrenome = mysqli_real_escape_string($conn, $data['sobrenome']);
    $email = mysqli_real_escape_string($conn, $data['email']);
    $telefone = mysqli_real_escape_string($conn, $data['telefone']);

    $sql = "INSERT INTO usuarios
    (nome, sobrenome, email, telefone)
    VALUES
    ('$nome', '$sobrenome', '$email', '$telefone')";

    $result = mysqli_query($conn, $sql);

    if ($result) {

        resposta(201, [
            "message" => "Usuário criado com sucesso.",
            "id" => mysqli_insert_id($conn)
        ]);

    } else {

        resposta(500, [
            "error" => "Erro ao criar usuário."
        ]);
    }
}

# =====================================
# GET ALL - LISTAR TODOS
# =====================================

if ($method == "GET" && $id == null) {

    $sql = "SELECT * FROM usuarios";

    $result = mysqli_query($conn, $sql);

    $usuarios = [];

    while ($usuario = mysqli_fetch_assoc($result)) {
        $usuarios[] = $usuario;
    }

    resposta(200, $usuarios);
}

# =====================================
# GET BY ID - BUSCAR POR ID
# =====================================

if ($method == "GET" && $id != null) {

    if (!usuarioExiste($conn, $id)) {

        resposta(404, [
            "error" => "Usuário não encontrado."
        ]);
    }

    $sql = "SELECT * FROM usuarios WHERE id = $id";

    $result = mysqli_query($conn, $sql);

    $usuario = mysqli_fetch_assoc($result);

    resposta(200, $usuario);
}

# =====================================
# PUT - ATUALIZAR USUÁRIO
# =====================================

if ($method == "PUT") {

    if ($id == null) {

        resposta(400, [
            "error" => "ID obrigatório."
        ]);
    }

    if (!usuarioExiste($conn, $id)) {

        resposta(404, [
            "error" => "Usuário não encontrado."
        ]);
    }

    $data = json_decode(file_get_contents("php://input"), true);

    if (
        empty($data['nome']) ||
        empty($data['sobrenome']) ||
        empty($data['email']) ||
        empty($data['telefone'])
    ) {

        resposta(400, [
            "error" => "Todos os campos são obrigatórios."
        ]);
    }

    $nome = mysqli_real_escape_string($conn, $data['nome']);
    $sobrenome = mysqli_real_escape_string($conn, $data['sobrenome']);
    $email = mysqli_real_escape_string($conn, $data['email']);
    $telefone = mysqli_real_escape_string($conn, $data['telefone']);

    $sql = "UPDATE usuarios SET
        nome = '$nome',
        sobrenome = '$sobrenome',
        email = '$email',
        telefone = '$telefone'
        WHERE id = $id";

    $result = mysqli_query($conn, $sql);

    if ($result) {

        resposta(200, [
            "message" => "Usuário atualizado com sucesso."
        ]);

    } else {

        resposta(500, [
            "error" => "Erro ao atualizar usuário."
        ]);
    }
}

# =====================================
# DELETE - REMOVER USUÁRIO
# =====================================

if ($method == "DELETE") {

    if ($id == null) {

        resposta(400, [
            "error" => "ID obrigatório."
        ]);
    }

    if (!usuarioExiste($conn, $id)) {

        resposta(404, [
            "error" => "Usuário não encontrado."
        ]);
    }

    $sql = "DELETE FROM usuarios WHERE id = $id";

    $result = mysqli_query($conn, $sql);

    if ($result) {

        resposta(200, [
            "message" => "Usuário deletado com sucesso."
        ]);

    } else {

        resposta(500, [
            "error" => "Erro ao deletar usuário."
        ]);
    }
}

# =====================================
# ROTA NÃO ENCONTRADA
# =====================================

resposta(404, [
    "error" => "Rota não encontrada."
]);

?>