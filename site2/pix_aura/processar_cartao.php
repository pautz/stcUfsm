<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'aura_db.php';

$caixa = $_POST['caixa_postal'] ?? '';
$aura  = intval($_POST['aura'] ?? 0);
$token = $_POST['token'] ?? '';
$email = $_POST['email'] ?? '';
$payment_method_id = $_POST['payment_method_id'] ?? '';

if ($caixa === '' || $aura < 1 || $token === '' || $payment_method_id === '' || $email === '') {
    die("⚠️ Dados inválidos.");
}

$preco_por_aura = 1;
$reais = round($aura * $preco_por_aura, 2);
$unique_id = uniqid('card_', true);

$access_token = 'APP_USR-7481674538521032-053114-b60e738d3a1ae58c84d8cc4c4086355c-157818820'; // substitua pelo seu Access Token privado
$descricao = "FarolQR.Com Compra de $aura aura para caixa postal $caixa";

// sempre à vista
$installments = 1;

$dados = [
    'transaction_amount' => $reais,
    'token' => $token,
    'description' => $descricao,
    'installments' => $installments,
    'payment_method_id' => $payment_method_id,
    'payer' => [
        'email' => $email,
        'first_name' => $caixa,
        'last_name' => 'aura: ' . (string)$aura
    ]
];

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
    die("❌ Erro de conexão com Mercado Pago: " . curl_error($ch));
}
curl_close($ch);

$resultado = json_decode($resposta, true);
$status = $resultado['status'] ?? 'erro';
$card_id = $resultado['id'] ?? 'sem_id';

$stmt = $cx->prepare("INSERT INTO pedidos_aura 
  (caixa_postal, quantidade, valor_reais, pix_id, unique_id, copia_cola, link_pix, status, ip_cliente, payment_method_id, installments) 
  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

if (!$stmt) {
    die("Erro no prepare: " . $cx->error);
}

$ip_cliente = $_SERVER['REMOTE_ADDR'] ?? 'desconhecido';
$copia_cola = '';
$link_pix   = '';

$stmt->bind_param(
  "sidsssssssi", 
  $caixa, $aura, $reais, $card_id, $unique_id, $copia_cola, $link_pix, $status, $ip_cliente, $payment_method_id, $installments
);

if (!$stmt->execute()) {
    die("Erro na execução: " . $stmt->error);
}
$stmt->close();

echo "<h2>Resultado do Pagamento</h2>";
echo "<p>Status: <strong>$status</strong></p>";
echo "<p>Caixa Postal: $caixa</p>";
echo "<p>Aura: $aura</p>";
echo "<p>Valor total: R$ " . number_format($reais, 2, ',', '.') . "</p>";
echo "<p>Pagamento à vista (1x)</p>";

echo "<pre>Resposta Mercado Pago:\n";
print_r($resultado);
echo "</pre>";
?>
