<?php
include 'conexao.php';

if (!isset($_SESSION['username_odonto2']) || empty($_SESSION['username_odonto2'])) {
    header("Location: login.php");
    exit;
}

$user_logado = $_SESSION['username_odonto2'];
$comprovante_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$comprovante_id) {
    header("Location: index.php");
    exit;
}

// Buscar detalhes do bilhete/comprovante e da carona (incluindo data_carona e horario_saida)
$sql = "SELECT p.*, c.id AS carona_id_real, c.motorista, c.matricula, c.partida, c.data_carona, c.horario_saida, c.valor, c.telefone 
        FROM caronas_passageiros_ufsm p 
        JOIN caronas_ufsm c ON p.carona_id = c.id 
        WHERE p.id = ? AND p.passageiro = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $comprovante_id, $user_logado);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Comprovante não encontrado ou sem permissão.";
    exit;
}

$dados = $result->fetch_assoc();
$stmt->close();

// Formatar a data para o padrão brasileiro (DD/MM/AAAA)
$data_formatada = date('d/m/Y', strtotime($dados['data_carona']));

// URL para gerar o QR Code com todos os dados atualizados
$qr_data = urlencode("STC-UFSM | Carona ID: " . $dados['carona_id_real'] . " | Data: " . $data_formatada . " | Horário: " . $dados['horario_saida'] . " | Comprovante: " . $dados['codigo_validacao'] . " | Passageiro: " . $dados['passageiro'] . " | Motorista: " . $dados['motorista'] . " | Valor: R$ " . number_format($dados['valor'], 2, ',', '.'));
$qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . $qr_data;
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Comprovante de Reserva - S.T.C</title>
    <style>
        body { background-color: #ffeb3b; font-family: Arial, sans-serif; margin: 0; padding: 20px; color: #000; }
        .container { max-width: 500px; margin: auto; background: #fff; padding: 25px; border: 3px solid #000; text-align: center; }
        h1 { color: #333; margin-bottom: 5px; }
        .qrcode { margin: 20px 0; border: 2px dashed #000; padding: 10px; display: inline-block; background: #f9f9f9; }
        .info { text-align: left; background: #f1f1f1; padding: 15px; border: 1px solid #ccc; margin: 15px 0; font-size: 15px; }
        .btn-voltar { display: block; width: 100%; padding: 10px; margin-top: 10px; background-color: #e0e0e0; color: #000; text-align: center; font-size: 16px; text-decoration: none; border: 2px solid #000; box-sizing: border-box; }
        .btn-voltar:hover { background-color: #d5d5d5; }
        .whatsapp-btn { display: block; background-color: #25D366; color: white; padding: 12px; text-decoration: none; font-weight: bold; border: 2px solid #000; margin-top: 10px; box-sizing: border-box; }
        .whatsapp-btn:hover { background-color: #1ebe5d; }
    </style>
</head>
<body>
<div class="container">
    <h1>Comprovante S.T.C</h1>
    <p>Reserva e pagamento efetuados com sucesso!</p>

    <div class="qrcode">
        <img src="<?php echo $qr_url; ?>" alt="QR Code de Validação">
        <p style="font-size: 12px; margin: 8px 0 0 0; font-weight: bold;"><?php echo htmlspecialchars($dados['codigo_validacao']); ?></p>
    </div>

    <div class="info">
        <p><strong>ID da Carona:</strong> <span style="color: #d9534f; font-weight: bold;">#<?php echo $dados['carona_id_real']; ?></span></p>
        <p><strong>Passageiro:</strong> <?php echo htmlspecialchars($dados['passageiro']); ?></p>
        <p><strong>Motorista:</strong> <?php echo htmlspecialchars($dados['motorista']); ?></p>
        <p><strong>Ponto de Partida:</strong> <?php echo htmlspecialchars($dados['partida']); ?></p>
        <p><strong>Data da Carona:</strong> <?php echo $data_formatada; ?></p>
        <p><strong>Horário de Saída:</strong> <span style="color: #d9534f; font-weight: bold;"><?php echo htmlspecialchars($dados['horario_saida']); ?></span></p>
        <p><strong>Veículo (Matrícula):</strong> <?php echo htmlspecialchars($dados['matricula']); ?></p>
        <p><strong>Valor Pago:</strong> R$ <?php echo number_format($dados['valor'], 2, ',', '.'); ?></p>
    </div>

    <?php if (!empty($dados['telefone'])): ?>
        <a href="https://wa.me/55<?php echo preg_replace('/\D/', '', $dados['telefone']); ?>?text=Olá,%20peguei%20a%20carona%20(ID:%20<?php echo $dados['carona_id_real']; ?>)%20para%20o%20dia%20<?php echo $data_formatada; ?>%20às%20<?php echo urlencode($dados['horario_saida']); ?>%20no%20S.T.C.%20Segue%20o%20comprovante%20(Código:%20<?php echo $dados['codigo_validacao']; ?>):%20https://carlitoslocacoes.com/caronas/comprovante.php?id=<?php echo $comprovante_id; ?>" target="_blank" class="whatsapp-btn">Enviar Comprovante ao Motorista via WhatsApp</a>
    <?php endif; ?>

    <a href="index.php" class="btn-voltar">Voltar ao Início</a>
</div>
</body>
</html>