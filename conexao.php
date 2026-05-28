<?php

$host = "127.0.0.1";
$user = "root";
$password = "aluno";
$database = "banco_noite";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Erro na conexão: " . mysqli_connect_error());
}

?>