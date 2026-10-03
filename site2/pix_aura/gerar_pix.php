<?php
require 'aura_db.php';

// Recebe parâmetros via GET
$caixa = filter_var($_GET['caixa_postal'] ?? '', FILTER_SANITIZE_STRING);
$aura  = intval($_GET['aura'] ?? 0);

// Validação básica
if ($caixa === '' || $aura < 1 || $aura > 500000) {
    http_response_code(400);
    die("⚠️ Dados inválidos. Informe uma Caixa Postal válida e um valor entre 1 e 500.000.");
}

// Confirma se a Caixa Postal existe na tabela correta
$stmt = $cx->prepare("SELECT id FROM identificacao_odonto2 WHERE caixa_postal = ?");
$stmt->bind_param("s", $caixa);
$stmt->execute();
$result = $stmt->get_result();
if (!$result->fetch_assoc()) {
    http_response_code(404);
    die("❌ Caixa Postal não encontrada no banco.");
}

// Cálculo do valor (sempre no backend)
$preco_por_aura = 1;
$reais = round($aura * $preco_por_aura, 2);

// Gera identificador único
$unique_id = uniqid('pix_', true);

// Verifica se já existe transação com mesmo unique_id (proteção contra replay)
$stmt = $cx->prepare("SELECT id FROM pedidos_aura WHERE unique_id = ?");
$stmt->bind_param("s", $unique_id);
$stmt->execute();
if ($stmt->get_result()->fetch_assoc()) {
    die("⚠️ Transação duplicada detectada.");
}

// Configuração da API Mercado Pago
$access_token = 'APP_USR-7481674538521032-053114-b60e738d3a1ae58c84d8cc4c4086355c-157818820';
$descricao = "FarolQR.com - compra de $aura aura para caixa postal $caixa";

$dados = [
    'transaction_amount' => $reais,
    'description' => $descricao,
    'payment_method_id' => 'pix',
    'payer' => [
        'email' => $unique_id . '@carlitoslocacoes.com',
        'first_name' => $caixa,
        'last_name' => 'aura: ' . (string)$aura
    ]
];

// Requisição cURL
$ch = curl_init('https://api.mercadopago.com/v1/payments');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dados));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $access_token,
    'Content-Type: application/json',
    'X-Idempotency-Key: ' . $unique_id
]);
$resposta = curl_exec($ch);

if ($resposta === false) {
    error_log("Erro cURL: " . curl_error($ch));
    die("❌ Erro na comunicação com o Mercado Pago.");
}
curl_close($ch);

$resultado = json_decode($resposta, true);
if (!isset($resultado['id'])) {
    error_log("Resposta inválida Mercado Pago: " . $resposta);
    die("❌ Erro ao gerar pagamento Pix.");
}

$pix_id = $resultado['id'];
$transacao = $resultado['point_of_interaction']['transaction_data'] ?? null;
$qr = $transacao['qr_code_base64'] ?? '';
$link = $transacao['ticket_url'] ?? '';
$copiacola = $transacao['qr_code'] ?? '';

// Salva no banco
$stmt = $cx->prepare("INSERT INTO pedidos_aura 
  (caixa_postal, quantidade, valor_reais, pix_id, unique_id, copia_cola, link_pix, status, ip_cliente) 
  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

$status = 'pendente';
$ip_cliente = $_SERVER['REMOTE_ADDR'] ?? 'desconhecido';
$stmt->bind_param("sidssssss", $caixa, $aura, $reais, $pix_id, $unique_id, $copiacola, $link, $status, $ip_cliente);
$stmt->execute();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Pagamento Pix - Aura</title>
  <style>
    body { font-family:'Segoe UI',sans-serif; background:#f9f9f9; margin:0; padding:20px; text-align:center; color:#333; }
    .container { max-width:500px; margin:auto; background:#fff; padding:25px; border-radius:10px; box-shadow:0 0 10px rgba(0,0,0,0.05); }
    h2 { margin-bottom:10px; color:#444; }
    .info p { margin:8px 0; font-size:1rem; }
    .qr img { max-width:300px; margin:20px 0; border-radius:8px; }
    .copiar { background:#f0f0f0; padding:12px; border-radius:6px; word-break:break-word; font-size:0.95rem; margin-bottom:10px; }
    button { background:#4CAF50; color:white; border:none; padding:12px 20px; font-size:1rem; border-radius:6px; cursor:pointer; }
    button:hover { background:#45a049; }
    a { display:inline-block; margin-top:15px; color:#0077cc; text-decoration:none; font-weight:bold; }
    a:hover { text-decoration:underline; }
  </style>
</head>
<body>
  <div class="container">
    <h2>Pagamento via Pix</h2>
    <div class="info">
      <p>📦 Caixa Postal: <strong><?= htmlspecialchars($caixa) ?></strong></p>
      <p>✨ Aura: <strong><?= $aura ?></strong></p>
      <p>💰 Valor: <strong>R$ <?= number_format($reais, 2, ',', '.') ?></strong></p>
    </div>

    <?php if ($qr): ?>
      <div class="qr">
        <img src="data:image/png;base64,<?= $qr ?>" alt="QR Code Pix">
      </div>
    <?php endif; ?>

    <?php if ($copiacola): ?>
      <div class="copiar" id="pixCode"><?= htmlspecialchars($copiacola) ?></div>
      <button onclick="copiarPix()">📋 Copiar código Pix</button>
    <?php endif; ?>

    <?php if ($link): ?>
      <a href="<?= htmlspecialchars($link) ?>" target="_blank">🔗 Abrir link do pagamento</a>
    <?php endif; ?>
  </div>

  <script>
    function copiarPix() {
      const texto = document.getElementById("pixCode").innerText;
      navigator.clipboard.writeText(texto).then(() => {
        alert("✅ Código Pix copiado!");
      });
    }
  </script>
</body>
</html>
