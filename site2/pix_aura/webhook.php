<?php
require 'aura_db.php';
date_default_timezone_set('America/Sao_Paulo');

// Captura corpo JSON (POST)
$input = file_get_contents('php://input');
$evento = json_decode($input, true);

// Captura ID do pagamento via POST ou GET
$id_pagamento = $_GET['id'] ?? ($evento['data']['id'] ?? null);

// Log básico para depuração
file_put_contents('log_webhook.txt', date('Y-m-d H:i:s') . " | ID recebido: $id_pagamento\n", FILE_APPEND);

echo "🆔 ID do pagamento recebido: $id_pagamento<br>";

if (!$id_pagamento) {
    echo "❌ Nenhum pagamento informado<br>";
    exit;
}

// Consulta à API do Mercado Pago
$access_token = getenv('MP_ACCESS_TOKEN') ?: 'APP_USR-7481674538521032-053114-b60e738d3a1ae58c84d8cc4c4086355c-157818820';
$url = "https://api.mercadopago.com/v1/payments/$id_pagamento";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $access_token]);
$resposta = curl_exec($ch);
curl_close($ch);

$pagamento = json_decode($resposta, true);
$status = $pagamento['status'] ?? '';
$pix_id = $pagamento['id'] ?? '';

echo "🔎 Pix ID: $pix_id<br>";
echo "🔎 Status: $status<br>";

if (!$pix_id) {
    echo "⚠️ Pix ID ausente na resposta da API<br>";
    exit;
}

// Atualiza status em carrinho e tabelas relacionadas
$stmtCarrinho = $cx->prepare("UPDATE pagamentos_carrinho_2025 SET status = ? WHERE pix_id = ?");
$stmtCarrinho->bind_param("ss", $status, $pix_id);
$stmtCarrinho->execute();
$stmtCarrinho->close();

$tabelas = ['pedidos_aura', 'pedidos_assinatura', 'pagamentos_destaque'];
foreach ($tabelas as $tabela) {
    $stmt = $cx->prepare("UPDATE $tabela SET status = ? WHERE pix_id = ?");
    $stmt->bind_param("ss", $status, $pix_id);
    $stmt->execute();
    $stmt->close();
}

$stmtReserva = $cx->prepare("UPDATE reservas_pix SET pago = IF(? = 'approved', 1, 0) WHERE pix_id = ?");
$stmtReserva->bind_param("ss", $status, $pix_id);
$stmtReserva->execute();
$stmtReserva->close();

if ($status === 'approved') {
    // Verifica se já foi registrado
    $stmtCheck = $cx->prepare("SELECT COUNT(*) AS total FROM pagamentos_recebidos WHERE pix_id = ?");
    $stmtCheck->bind_param("s", $pix_id);
    $stmtCheck->execute();
    $jaRegistrado = $stmtCheck->get_result()->fetch_assoc()['total'] ?? 0;
    $stmtCheck->close();

    if ($jaRegistrado == 0) {
        $valor = 0;
        $caixa = '';

        // Busca dados da reserva
        $stmtReservaInfo = $cx->prepare("SELECT quarto_id, valor FROM reservas_pix WHERE pix_id = ?");
        $stmtReservaInfo->bind_param("s", $pix_id);
        $stmtReservaInfo->execute();
        $reserva = $stmtReservaInfo->get_result()->fetch_assoc();
        $stmtReservaInfo->close();

        if ($reserva) {
            $valor = floatval($reserva['valor']);
            $quarto_id = intval($reserva['quarto_id']);

            $stmtUser = $cx->prepare("SELECT eq_user FROM quartos WHERE id = ?");
            $stmtUser->bind_param("i", $quarto_id);
            $stmtUser->execute();
            $username = $stmtUser->get_result()->fetch_assoc()['eq_user'] ?? '';
            $stmtUser->close();

            $stmtCaixa = $cx->prepare("SELECT caixa_postal FROM identificacao_odonto2 WHERE username = ?");
            $stmtCaixa->bind_param("s", $username);
            $stmtCaixa->execute();
            $caixa = $stmtCaixa->get_result()->fetch_assoc()['caixa_postal'] ?? '';
            $stmtCaixa->close();
        } else {
            // Busca dados do pedido aura
            $stmtInfo = $cx->prepare("SELECT valor_reais AS valor, caixa_postal FROM pedidos_aura WHERE pix_id = ?");
            $stmtInfo->bind_param("s", $pix_id);
            $stmtInfo->execute();
            $dados = $stmtInfo->get_result()->fetch_assoc();
            $stmtInfo->close();

            $valor = floatval($dados['valor'] ?? 0);
            $caixa = $dados['caixa_postal'] ?? '';
        }

        echo "🧾 Valor detectado: $valor<br>";
        echo "📦 Caixa postal detectada: $caixa<br>";

        if ($caixa) {
            // Registra pagamento
            $stmtLog = $cx->prepare("INSERT INTO pagamentos_recebidos (pix_id, caixa_postal, valor, data) VALUES (?, ?, ?, NOW())");
            $stmtLog->bind_param("ssd", $pix_id, $caixa, $valor);
            $stmtLog->execute();
            $stmtLog->close();
            echo "📥 Pagamento registrado em pagamentos_recebidos<br>";

            // Credita Aura
            $preco_por_aura = 1;
            $aura_creditada = intval($valor / $preco_por_aura);

            if ($aura_creditada > 0) {
                $stmtSaldo = $cx->prepare("UPDATE identificacao_odonto2 SET saldo_total = saldo_total + ? WHERE caixa_postal = ?");
                $stmtSaldo->bind_param("is", $aura_creditada, $caixa);
                $stmtSaldo->execute();
                $stmtSaldo->close();
                echo "✨ $aura_creditada Aura creditada para caixa postal $caixa<br>";
            }
        } else {
            echo "⚠️ Dados inválidos ou caixa postal ausente<br>";
        }
    } else {
        echo "⚠️ Pagamento já registrado anteriormente<br>";
    }

    // Envia e-mail de notificação
    $to = "carlitopautz@live.com";
    $subject = "Pagamento aprovado - Pix ID $pix_id";
    $message = "Olá,\n\nO pagamento com Pix ID $pix_id foi aprovado.\n\nValor: R$ $valor\nCaixa postal: $caixa\n\nAtenciosamente,\nSistema Aura";
    $headers = "From: carlitopautz@carlitoslocacoes.com\r\n" .
               "Reply-To: carlitopautz@carlitoslocacoes.com\r\n" .
               "X-Mailer: PHP/" . phpversion();

    if (mail($to, $subject, $message, $headers)) {
        echo "📧 E-mail enviado para $to<br>";
    } else {
        echo "❌ Falha ao enviar e-mail<br>";
    }

    // Ativa assinatura
    $stmt3 = $cx->prepare("SELECT username FROM pedidos_assinatura WHERE pix_id = ?");
    $stmt3->bind_param("s", $pix_id);
    $stmt3->execute();
    $user = $stmt3->get_result()->fetch_assoc()['username'] ?? '';
    $stmt3->close();

    if ($user) {
        $stmt4 = $cx->prepare("UPDATE identificacao_odonto2 SET assinantenv3 = 1, data_assinatura = NOW() WHERE username = ?");
        $stmt4->bind_param("s", $user);
        $stmt4->execute();
        $stmt4->close();
        echo "✅ Usuário '$user' ativado como assinante<br>";
    }

    // Destaca produto
    $stmt5 = $cx->prepare("SELECT numeroEtiqueta, username FROM pagamentos_destaque WHERE pix_id = ? AND status = 'pendente'");
    $stmt5->bind_param("s", $pix_id);
    $stmt5->execute();
    $destaque = $stmt5->get_result()->fetch_assoc();
    $stmt5->close();

    if ($destaque) {
        $etiqueta = intval($destaque['numeroEtiqueta']);
        $usuario = $destaque['username'];

        $cx->begin_transaction();
        try {
            $stmt7 = $cx->prepare("UPDATE pagamentos_destaque SET status = 'approved' WHERE pix_id = ?");
            $stmt7->bind_param("s", $pix_id);
            $stmt7->execute();
            $stmt7->close();

            $stmt6 = $cx->prepare("UPDATE cadastro_produto SET destacar = 1 WHERE numeroEtiqueta = ?");
            $stmt6->bind_param("i", $etiqueta);
            $stmt6->execute();
            $stmt6->close();

                        $cx->commit();
            echo "🌟 Produto destacado para etiqueta $etiqueta<br>";
        } catch (Exception $e) {
            $cx->rollback();
            echo "❌ Erro ao destacar produto: " . $e->getMessage() . "<br>";
        }
    }
} elseif ($status === 'pending') {
    echo "⏳ Pagamento ainda pendente<br>";
} elseif ($status === 'rejected') {
    echo "❌ Pagamento rejeitado<br>";
} elseif (in_array($status, ['refund', 'chargeback'])) {
    echo "💸 Pagamento reembolsado ou estornado<br>";
} else {
    echo "⚠️ Status desconhecido: $status<br>";
}
?>
