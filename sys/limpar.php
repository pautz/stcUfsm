<?php
session_start();

// Se veio cidade ou estado pelo GET ou POST, atualiza a sessão
if (isset($_GET['search_cidade'])) {
    $_SESSION['cidade'] = $_GET['search_cidade'];
}
if (isset($_POST['search_cidade'])) {
    $_SESSION['cidade'] = $_POST['search_cidade'];
}

if (isset($_GET['search_estado'])) {
    $_SESSION['estado'] = $_GET['search_estado'];
}
if (isset($_POST['search_estado'])) {
    $_SESSION['estado'] = $_POST['search_estado'];
}

// Recupera cidade e estado da sessão
$cidade = $_SESSION['cidade'] ?? null;
$estado = $_SESSION['estado'] ?? null;

// Apaga cookie do carrinho
setcookie('carrinho', '', time() - 3600, "/");

// Monta URL de redirecionamento
$url = "hora_carrinho.php";
$params = [];

if ($cidade) $params[] = "search_cidade=" . urlencode($cidade);
if ($estado) $params[] = "search_estado=" . urlencode($estado);

if (!empty($params)) {
    $url .= "?" . implode("&", $params);
}

// Redireciona de volta para a página principal do carrinho mantendo cidade e estado
header("Location: $url");
exit;
