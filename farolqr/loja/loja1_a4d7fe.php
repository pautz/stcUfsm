<?php
$tokenMeuArquivo = basename(__FILE__);
$nomeLoja = "Loja";
$valorProduto = 0;
$caixaDestino = "";
$urlRetorno = "";

$connLoja = new mysqli("localhost", "u839226731_farol", "Meta6595869!", "u839226731_farol");
if (!$connLoja->connect_error) {
    $connLoja->set_charset("utf8mb4");
    $stmtLoja = $connLoja->prepare("SELECT nome_site, valor_padrao, caixa_recebimento, url_retorno FROM sites_parceiros WHERE token_api = ? LIMIT 1");
    if ($stmtLoja) {
        $stmtLoja->bind_param("s", $tokenMeuArquivo);
        $stmtLoja->execute();
        $resLoja = $stmtLoja->get_result();
        if ($dadosLoja = $resLoja->fetch_assoc()) {
            $nomeLoja     = $dadosLoja["nome_site"] ?? "Loja";
            $valorProduto = intval($dadosLoja["valor_padrao"] ?? 0);
            $caixaDestino = $dadosLoja["caixa_recebimento"] ?? "";
            $urlRetorno   = $dadosLoja["url_retorno"] ?? "";
        }
        $stmtLoja->close();
    }
    $connLoja->close();
}

if ($valorProduto <= 0) {
    die("❌ Loja não encontrada ou valor inválido.");
}

$urlCheckoutSistema = "https://carlitoslocacoes.com/farolqr/loja/checkout_loja.php?token=" . urlencode($tokenMeuArquivo);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamento - loja1</title>
    <style>
        body { font-family: sans-serif; background: #f4f6f9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card { background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); width: 100%; max-width: 400px; text-align: center; }
        h2 { color: #5c3dc4; margin-bottom: 10px; }
        .valor { font-size: 28px; font-weight: bold; color: #28a745; margin: 20px 0; }
        p { color: #555; font-size: 14px; margin-bottom: 25px; }
        .btn-pagar { background: #5c3dc4; color: #fff; border: none; width: 100%; padding: 14px; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 16px; text-decoration: none; display: inline-block; box-sizing: border-box; }
        .btn-pagar:hover { background: #4a30a0; }
    </style>
</head>
<body>
    <div class="card">
        <h2><?= htmlspecialchars($nomeLoja, ENT_QUOTES, "UTF-8") ?></h2>
        <p>Confirme os detalhes do seu pedido abaixo para prosseguir com o pagamento seguro.</p>
        <div class="valor">AURA <?= number_format($valorProduto, 0, "", ".") ?></div>
        <a href="<?= $urlCheckoutSistema ?>" class="btn-pagar">Confirmar e Pagar 💳</a>
    </div>
</body>
</html>