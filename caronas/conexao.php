<?php
// Ativar exibição de erros para diagnóstico
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

$host = 'localhost';
$usuario_db = 'u839226731_farol';
$senha_db = 'Meta6595869!';
$banco = 'u839226731_farol';

$conn = new mysqli($host, $usuario_db, $senha_db, $banco);

// Definir o charset para evitar problemas com acentuação
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}
?>