<?php
// api_pagar.php - API de Gateway de Pagamento Aura para Sites Externos
header("Content-Type: application/json; charset=UTF-8");

$conn = new mysqli("localhost", "u839226731_farol", "Meta6595869!", "u839226731_farol");
if ($conn->connect_error) {
    echo json_encode(["status" => "erro", "mensagem" => "Erro na conexão com o banco"]);
    exit;
}
$conn->set_charset("utf8mb4");

// 1. Validar Token do Site Parceiro via Cabeçalho HTTP ou POST
$headers = apache_request_headers();
$tokenApi = $headers["Authorization"] ?? $_POST["token_api"] ?? "";

$stmtToken = $conn->prepare("SELECT id, nome_site, caixa_recebimento, url_retorno FROM sites_parceiros WHERE token_api = ? LIMIT 1");
$stmtToken->bind_param("s", $tokenApi);
$stmtToken->execute();
$resToken = $stmtToken->get_result();
$parceiro = $resToken->fetch_assoc();
$stmtToken->close();

if (!$parceiro) {
    echo json_encode(["status" => "erro", "mensagem" => "Não autorizado: Token de API inválido ou ausente."]);
    exit;
}

// 2. Receber dados da cobrança enviados pelo site parceiro
$usuarioComprador = $_POST["username"] ?? "";
$senhaComprador   = $_POST["senha"] ?? "";
$valor            = (int)($_POST["valor"] ?? 0);
$destinoCaixa     = $parceiro["caixa_recebimento"]; // Caixa cadastrada pelo dono da loja

if (empty($usuarioComprador) || empty($senhaComprador) || $valor <= 0) {
    echo json_encode(["status" => "erro", "mensagem" => "Parâmetros incompletos ou valor inválido."]);
    exit;
}

// 3. Buscar dados do comprador (senha e caixa postal)
$stmtUser = $conn->prepare("SELECT u.password, i.caixa_postal, i.saldo_total FROM odonto2_users u JOIN identificacao_odonto2 i ON u.username = i.username WHERE u.username = ? LIMIT 1");
$stmtUser->bind_param("s", $usuarioComprador);
$stmtUser->execute();
$userData = $stmtUser->get_result()->fetch_assoc();
$stmtUser->close();

if (!$userData || !password_verify($senhaComprador, $userData["password"])) {
    echo json_encode(["status" => "erro", "mensagem" => "Usuário ou senha incorretos."]);
    exit;
}

$caixaUsuario = $userData["caixa_postal"];
$saldoAura    = (int)$userData["saldo_total"];

// 4. Validações de Limite e Saldo
$limiteDiario = 1500000;
$hoje = date("Y-m-d");
$stmtTotalDia = $conn->prepare("SELECT SUM(valor) AS total_dia FROM transacoes_aura WHERE remetente = ? AND DATE(data_registro) = ?");
$stmtTotalDia->bind_param("ss", $usuarioComprador, $hoje);
$stmtTotalDia->execute();
$totalDia = (int)($stmtTotalDia->get_result()->fetch_assoc()["total_dia"] ?? 0);
$stmtTotalDia->close();

if ($caixaUsuario === $destinoCaixa) {
    echo json_encode(["status" => "erro", "mensagem" => "Você não pode pagar para si mesmo."]);
} elseif ($saldoAura < $valor) {
    echo json_encode(["status" => "erro", "mensagem" => "Saldo Aura insuficiente."]);
} elseif ($valor > 72000) {
    echo json_encode(["status" => "erro", "mensagem" => "Máximo por transação é 72.000 aura."]);
} elseif (($totalDia + $valor) > $limiteDiario) {
    echo json_encode(["status" => "erro", "mensagem" => "Limite diário excedido."]);
} else {
    // Verificar se a caixa de destino existe
    $stmtDest = $conn->prepare("SELECT username FROM identificacao_odonto2 WHERE caixa_postal = ?");
    $stmtDest->bind_param("s", $destinoCaixa);
    $stmtDest->execute();
    $resDest = $stmtDest->get_result();
    $stmtDest->close();

    if ($resDest->num_rows > 0) {
        $conn->begin_transaction();
        try {
            // Debitar comprador
            $stmtDeb = $conn->prepare("UPDATE identificacao_odonto2 SET saldo_total = saldo_total - ? WHERE caixa_postal = ? AND username = ?");
            $stmtDeb->bind_param("iss", $valor, $caixaUsuario, $usuarioComprador);
            $stmtDeb->execute();
            $stmtDeb->close();

            // Creditar loja parceira
            $stmtCred = $conn->prepare("UPDATE identificacao_odonto2 SET saldo_total = saldo_total + ? WHERE caixa_postal = ?");
            $stmtCred->bind_param("is", $valor, $destinoCaixa);
            $stmtCred->execute();
            $stmtCred->close();

            // Assinatura de segurança
            $assinatura = hash('sha256', $usuarioComprador . $destinoCaixa . $valor . $caixaUsuario . microtime(true));

            // Inserir na tabela oficial de transações
            $stmtInsert = $conn->prepare("INSERT INTO transacoes_aura (remetente, destinatario, valor, caixa_origem, caixa_destino, assinatura) VALUES (?, ?, ?, ?, ?, ?)");
            $stmtInsert->bind_param("ssisss", $usuarioComprador, $destinoCaixa, $valor, $caixaUsuario, $destinoCaixa, $assinatura);
            $stmtInsert->execute();
            $transacaoId = $conn->insert_id;
            $stmtInsert->close();

            // Registrar comprovante
            $stmtComp = $conn->prepare("INSERT INTO comprovantes_aura (remetente, destinatario, valor, caixa_origem, caixa_destino, transacao_id, assinatura) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmtComp->bind_param("ssissis", $usuarioComprador, $destinoCaixa, $valor, $caixaUsuario, $destinoCaixa, $transacaoId, $assinatura);
            $stmtComp->execute();
            $stmtComp->close();

            $conn->commit();

            // Retornar sucesso com URL de retorno para o site parceiro redirecionar o cliente
            echo json_encode([
                "status" => "sucesso",
                "transacao_id" => $transacaoId,
                "assinatura" => $assinatura,
                "url_retorno" => $parceiro["url_retorno"] . "?status=sucesso&transacao_id=" . $transacaoId,
                "mensagem" => "Pagamento processado com sucesso!"
            ]);

        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(["status" => "erro", "mensagem" => "Erro interno ao processar transação: " . $e->getMessage()]);
        }
    } else {
        echo json_encode(["status" => "erro", "mensagem" => "Caixa postal da loja de destino não encontrada no sistema."]);
    }
}
?>