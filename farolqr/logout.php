<?php
// Inicializa a sessão
session_start();

// Inclui a conexão com o banco
require_once "config.php";

// Remove a sessão ativa do banco, se existir
if (isset($_SESSION["id"])) {
    $sql_delete = "DELETE FROM active_sessions WHERE user_id = ?";
    if ($stmt_delete = mysqli_prepare($link, $sql_delete)) {
        mysqli_stmt_bind_param($stmt_delete, "i", $_SESSION["id"]);
        mysqli_stmt_execute($stmt_delete);
        mysqli_stmt_close($stmt_delete);
    }
}

// Limpa todas as variáveis de sessão
$_SESSION = array();

// Destroi a sessão
session_destroy();

// Redireciona para a página inicial ou login
header("location: ../index.php");
exit;
?>
