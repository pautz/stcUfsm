<?php
include 'conexao.php';

// 1. Verificar se a sessão do utilizador está ativa
if (!isset($_SESSION['username_odonto2']) || empty($_SESSION['username_odonto2'])) {
    header("Location: login.php");
    exit;
}

$user_logado = $_SESSION['username_odonto2'];

// 2. VERIFICAÇÃO OBRIGATÓRIA DA CAIXA POSTAL E SALDO DO PASSAGEIRO
$sql_user = "SELECT saldo_total, caixa_postal FROM identificacao_odonto2 WHERE username = ?";
$stmt_u = $conn->prepare($sql_user);
$stmt_u->bind_param("s", $user_logado);
$stmt_u->execute();
$res_u = $stmt_u->get_result();

if ($res_u->num_rows === 0) {
    session_destroy();
    header("Location: login.php");
    exit;
}

$dados_usuario = $res_u->fetch_assoc();
$stmt_u->close();

$saldo_atual = $dados_usuario['saldo_total'];
$caixa_postal_usuario = $dados_usuario['caixa_postal'];

// Se não tiver caixa postal, bloqueia o acesso
if (empty($caixa_postal_usuario)) {
    echo "<div style='font-family: Arial; padding: 40px; text-align: center; background: #ffeb3b;'>";
    echo "<div style='max-width: 500px; margin: auto; background: #fff; padding: 20px; border: 3px solid #000;'>";
    echo "<h2 style='color: #d9534f;'>Acesso Restrito</h2>";
    echo "<p>Sua conta (<strong>" . htmlspecialchars($user_logado) . "</strong>) não possui uma <strong>Caixa Postal</strong> vinculada e não pode pegar caronas.</p>";
    echo "<a href='index.php' style='display:inline-block; margin-top:15px; padding:10px 20px; background:#333; color:#ffeb3b; text-decoration:none; font-weight:bold; border:2px solid #000;'>Voltar ao Início</a>";
    echo "</div></div>";
    exit;
}

// 3. Obter o ID da carona via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$carona_id = filter_input(INPUT_POST, 'carona_id', FILTER_VALIDATE_INT);

if (!$carona_id) {
    header("Location: index.php");
    exit;
}

// 4. Buscar detalhes da carona
$sql_carona = "SELECT * FROM caronas_ufsm WHERE id = ?";
$stmt_c = $conn->prepare($sql_carona);
$stmt_c->bind_param("i", $carona_id);
$stmt_c->execute();
$res_c = $stmt_c->get_result();

if ($res_c->num_rows === 0) {
    echo "Carona não encontrada.";
    exit;
}

$carona = $res_c->fetch_assoc();
$stmt_c->close();

// Validações de segurança adicionais
if ($carona['motorista'] === $user_logado) {
    echo "Você não pode pegar a sua própria carona.";
    exit;
}

if ($carona['vagas'] <= 0) {
    echo "Esta carona já está lotada.";
    exit;
}

$valor_carona = $carona['valor'];
$motorista_carona = $carona['motorista'];

if ($saldo_atual < $valor_carona) {
    echo "<div style='font-family: Arial; padding: 40px; text-align: center; background: #ffeb3b;'>";
    echo "<div style='max-width: 500px; margin: auto; background: #fff; padding: 20px; border: 3px solid #000;'>";
    echo "<h2 style='color: #d9534f;'>Saldo Insuficiente</h2>";
    echo "<p>Você não tem saldo suficiente para pagar esta carona.</p>";
    echo "<p><strong>Seu Saldo:</strong> R$ " . number_format($saldo_atual, 2, ',', '.') . "<br><strong>Valor da Carona:</strong> R$ " . number_format($valor_carona, 2, ',', '.') . "</p>";
    echo "<a href='index.php' style='display:inline-block; margin-top:15px; padding:10px 20px; background:#333; color:#ffeb3b; text-decoration:none; font-weight:bold; border:2px solid #000;'>Voltar</a>";
    echo "</div></div>";
    exit;
}

// 5. Iniciar Transação do Banco de Dados para garantir consistência
$conn->begin_transaction();

try {
    // A. Descontar o saldo do passageiro
    $sql_up_passageiro = "UPDATE identificacao_odonto2 SET saldo_total = saldo_total - ? WHERE username = ?";
    $stmt_p = $conn->prepare($sql_up_passageiro);
    $stmt_p->bind_param("ds", $valor_carona, $user_logado);
    $stmt_p->execute();
    $stmt_p->close();

    // B. Creditar/Adicionar o saldo ao motorista
    $sql_up_motorista = "UPDATE identificacao_odonto2 SET saldo_total = saldo_total + ? WHERE username = ?";
    $stmt_m = $conn->prepare($sql_up_motorista);
    $stmt_m->bind_param("ds", $valor_carona, $motorista_carona);
    $stmt_m->execute();
    $stmt_m->close();

    // C. Decrementar 1 vaga na carona
    $sql_up_vagas = "UPDATE caronas_ufsm SET vagas = vagas - 1 WHERE id = ?";
    $stmt_vg = $conn->prepare($sql_up_vagas);
    $stmt_vg->bind_param("i", $carona_id);
    $stmt_vg->execute();
    $stmt_vg->close();

    // D. Gerar código de validação único para o comprovante
    $codigo_validacao = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));

    // E. Inserir registo na tabela de passageiros/comprovantes
    $sql_comp = "INSERT INTO caronas_passageiros_ufsm (carona_id, passageiro, codigo_validacao) VALUES (?, ?, ?)";
    $stmt_cp = $conn->prepare($sql_comp);
    $stmt_cp->bind_param("iss", $carona_id, $user_logado, $codigo_validacao);
    $stmt_cp->execute();
    $comprovante_id = $conn->insert_id;
    $stmt_cp->close();

    // Confirmar a transação com sucesso
    $conn->commit();

    // Redirecionar para o comprovante gerado
    header("Location: comprovante.php?id=" . $comprovante_id);
    exit;

} catch (Exception $e) {
    // Se ocorrer algum erro, desfaz todas as alterações
    $conn->rollback();
    echo "Erro ao processar a reserva da carona: " . $e->getMessage();
}

$conn->close();
?>