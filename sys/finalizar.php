<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Conexão com banco
$conn = new mysqli("127.0.0.1", "u839226731_farol", "Meta6595869!", "u839226731_farol");
if ($conn->connect_error) { die("Conexão falhou: " . $conn->connect_error); }
$conn->set_charset("utf8mb4");

$usuario = $_SESSION['username_odonto2'] ?? "";

// Inicializa carrinho
$carrinhoCliente = [];

// Se veio pelo token (link mágico), busca endereço no carrinho
if (!empty($_GET['token'])) {
    $sqlCarrinho = "SELECT dados FROM carrinhos WHERE token = ?";
    $stmtCarrinho = $conn->prepare($sqlCarrinho);
    $stmtCarrinho->bind_param("s", $_GET['token']);
    $stmtCarrinho->execute();
    $resCarrinho = $stmtCarrinho->get_result();

    if ($rowCarrinho = $resCarrinho->fetch_assoc()) {
        $carrinhoCliente = json_decode($rowCarrinho['dados'], true);
    }
    $stmtCarrinho->close();
} else {
    // Fallback: carrinho normal via cookies
    $carrinhoCliente = !empty($_COOKIE['carrinho']) ? json_decode($_COOKIE['carrinho'], true) : [];
}

if (!is_array($carrinhoCliente) || empty($carrinhoCliente)) {
    die("Carrinho vazio!");
}

// Busca saldo do usuário
$sqlSaldo = "SELECT saldo_total FROM identificacao_odonto2 WHERE username = ?";
$stmtSaldo = $conn->prepare($sqlSaldo);
$stmtSaldo->bind_param("s", $usuario);
$stmtSaldo->execute();
$resSaldo = $stmtSaldo->get_result();
$saldoData = $resSaldo->fetch_assoc();
$stmtSaldo->close();
$saldoAtual = (float)($saldoData['saldo_total'] ?? 0);

// Busca itens do carrinho
$itensCarrinho = [];
$totalCompra = 0;

foreach ($carrinhoCliente as $idConsorcio => $dados) {
    $sqlConsorcio = "SELECT id, consulta, valor_total, entrada, estoque 
                     FROM consorcio_cadastro WHERE id = ?";
    $stmt = $conn->prepare($sqlConsorcio);
    $stmt->bind_param("i", $idConsorcio);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
        $estoqueAtual = (int)$row['estoque'];
        $qtd   = (int)($dados['qtd'] ?? 1);
        $frete = (float)($dados['frete'] ?? 0);

        if ($estoqueAtual < $qtd) {
            die("Estoque insuficiente para {$row['consulta']} (Estoque: $estoqueAtual, Solicitado: $qtd)");
        }

        $row['qtd']   = $qtd;
        $row['frete'] = $frete;
        $itensCarrinho[] = $row;

        $totalCompra += ($row['valor_total'] * $row['qtd']) + $row['frete'];
    }
    $stmt->close();
}

// Verifica saldo
if ($saldoAtual < $totalCompra) {
    die("Saldo insuficiente!");
}

// Gera hash único para a compra
$hashCompra = hash('sha256', $usuario . microtime(true));
$status = "pendente";

// Insere cada item na tabela compras com endereço específico
// Insere cada item na tabela compras com endereço específico
foreach ($itensCarrinho as $item) {
    $dadosItem = $carrinhoCliente[$item['id']] ?? [];

    $ruaItem        = $dadosItem['rua'] ?? '';
    $numeroItem     = $dadosItem['numero'] ?? '';
    $complementoItem= $dadosItem['complemento'] ?? '';
    $bairroItem     = $dadosItem['bairro'] ?? '';
    $cidadeItem     = $dadosItem['cidade'] ?? '';
    $estadoItem     = $dadosItem['estado'] ?? '';
    $cepItem        = $dadosItem['cep'] ?? '';
    $telefoneItem   = $dadosItem['telefone'] ?? '';
    $characterItem  = $dadosItem['nicknamecharacter'] ?? ''; // novo campo

    $sqlCompra = "INSERT INTO compras 
    (usuario, consorcio_id, valor_total, entrada, frete, quantidade, hash_comprovante, data_compra, rua, numero, complemento, bairro, cidade, estado, cep, telefone, nicknamecharacter, status_pagamento) 
    VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sqlCompra);
    $stmt->bind_param("sidddisssssssssss", 
        $usuario, $item['id'], $item['valor_total'], $item['entrada'], $item['frete'], $item['qtd'], $hashCompra,
        $ruaItem, $numeroItem, $complementoItem, $bairroItem, $cidadeItem, $estadoItem, $cepItem, $telefoneItem, $characterItem, $status
    );
    $stmt->execute();
    $stmt->close();

    // Atualiza estoque
    $sqlEstoque = "UPDATE consorcio_cadastro SET estoque = estoque - ? WHERE id = ?";
    $stmtEstoque = $conn->prepare($sqlEstoque);
    $stmtEstoque->bind_param("ii", $item['qtd'], $item['id']);
    $stmtEstoque->execute();
    $stmtEstoque->close();
}


// Atualiza saldo do usuário
$novoSaldo = $saldoAtual - $totalCompra;
$sqlUpdateSaldo = "UPDATE identificacao_odonto2 SET saldo_total = ? WHERE username = ?";
$stmtUpdate = $conn->prepare($sqlUpdateSaldo);
$stmtUpdate->bind_param("ds", $novoSaldo, $usuario);
$stmtUpdate->execute();
$stmtUpdate->close();

// Limpa carrinho (cookies)
setcookie('carrinho', '', time() - 3600, "/");

$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Compra Finalizada</title>
  <style>
    body { font-family: 'Poppins', sans-serif; background:#f9f9f9; padding:40px; }
    .comprovante { max-width:600px; margin:0 auto; background:#fff; padding:30px; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.1); }
    h2 { text-align:center; color:#00796b; }
    p { font-size:16px; margin:8px 0; }
    .hash { font-weight:bold; color:#e74c3c; word-break:break-all; }
  </style>
</head>
<body>
  <div class="comprovante">
    <h2>✅ Compra Finalizada</h2>
    <p><strong>Usuário:</strong> <?= htmlspecialchars($usuario) ?></p>
    <p><strong>Total da Compra:</strong> R$ <?= number_format($totalCompra, 2, ',', '.') ?></p>
    <p><strong>Status:</strong> <?= htmlspecialchars($status) ?></p>
    <p><strong>Comprovante (hash):</strong> <span class="hash"><?= $hashCompra ?></span></p>
    <p><strong>Saldo Atualizado:</strong> R$ <?= number_format($novoSaldo, 2, ',', '.') ?></p>
    <p><em>Os endereços foram gravados individualmente para cada item.</em></p>
  </div>
</body>
</html>
