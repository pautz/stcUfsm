<?php
session_start();

// Recupera filtros
if (isset($_GET['search_cidade'])) $_SESSION['search_cidade'] = $_GET['search_cidade'];
if (isset($_POST['search_cidade'])) $_SESSION['search_cidade'] = $_POST['search_cidade'];
if (isset($_GET['search_estado'])) $_SESSION['search_estado'] = $_GET['search_estado'];
if (isset($_POST['search_estado'])) $_SESSION['search_estado'] = $_POST['search_estado'];

$search_cidade = $_SESSION['search_cidade'] ?? null;
$search_estado = $_SESSION['search_estado'] ?? null;

// Lê cookie atual
$carrinho = [];
if (!empty($_COOKIE['carrinho'])) {
    $carrinho = json_decode($_COOKIE['carrinho'], true);
    if (!is_array($carrinho)) $carrinho = [];
}

$id = intval($_GET['id'] ?? $_POST['id'] ?? 0);

if ($id > 0 && isset($carrinho[$id])) {
    // Se carrinho guarda apenas número
    if (is_numeric($carrinho[$id])) {
        if ($carrinho[$id] > 1) {
            $carrinho[$id]--;
        } else {
            unset($carrinho[$id]);
        }
    }
    // Se carrinho guarda array com 'qtd'
    elseif (is_array($carrinho[$id]) && isset($carrinho[$id]['qtd'])) {
        if ($carrinho[$id]['qtd'] > 1) {
            $carrinho[$id]['qtd']--;
        } else {
            unset($carrinho[$id]);
        }
    }
}

// Atualiza cookie
setcookie('carrinho', json_encode($carrinho), time() + (7*24*60*60), "/");

// Redireciona
$url = "hora_carrinho.php";
$params = [];
if ($search_cidade) $params[] = "search_cidade=" . urlencode($search_cidade);
if ($search_estado) $params[] = "search_estado=" . urlencode($search_estado);
if (!empty($params)) $url .= "?" . implode("&", $params);

header("Location: $url");
exit;
