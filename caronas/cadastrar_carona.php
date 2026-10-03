<?php
include 'conexao.php';

// 1. Verificar se a sessão do utilizador está ativa
if (!isset($_SESSION['username_odonto2']) || empty($_SESSION['username_odonto2'])) {
    header("Location: login.php");
    exit;
}

$user_logado = $_SESSION['username_odonto2'];

// 2. VERIFICAÇÃO OBRIGATÓRIA DA CAIXA POSTAL
$sql_check = "SELECT caixa_postal FROM identificacao_odonto2 WHERE username = ?";
$stmt_chk = $conn->prepare($sql_check);
$stmt_chk->bind_param("s", $user_logado);
$stmt_chk->execute();
$res_chk = $stmt_chk->get_result();

if ($res_chk->num_rows === 0 || empty($res_chk->fetch_assoc()['caixa_postal'])) {
    echo "<div style='font-family: Arial; padding: 40px; text-align: center; background: #ffeb3b;'>";
    echo "<div style='max-width: 500px; margin: auto; background: #fff; padding: 20px; border: 3px solid #000;'>";
    echo "<h2 style='color: #d9534f;'>Acesso Restrito</h2>";
    echo "<p>Sua conta (<strong>" . htmlspecialchars($user_logado) . "</strong>) não possui uma <strong>Caixa Postal</strong> vinculada e não pode cadastrar caronas.</p>";
    echo "<a href='index.php' style='display:inline-block; margin-top:15px; padding:10px 20px; background:#333; color:#ffeb3b; text-decoration:none; font-weight:bold; border:2px solid #000;'>Voltar ao Início</a>";
    echo "</div></div>";
    exit;
}
$stmt_chk->close();

$mensagem = "";

// 3. VERIFICAR SE O UTILIZADOR JÁ ATINGIU O LIMITE DE 6 CARONAS ATIVAS
$sql_limite = "SELECT COUNT(*) as total FROM caronas_ufsm WHERE motorista = ?";
$stmt_l = $conn->prepare($sql_limite);
$stmt_l->bind_param("s", $user_logado);
$stmt_l->execute();
$res_limite = $stmt_l->get_result()->fetch_assoc();
$stmt_l->close();

if ($res_limite['total'] >= 6) {
    echo "<!DOCTYPE html>
    <html lang='pt-BR'>
    <head>
        <meta charset='UTF-8'>
        <title>Limite Atingido - S.T.C</title>
        <style>
            body { background-color: #ffeb3b; font-family: Arial, sans-serif; margin: 0; padding: 20px; color: #000; }
            .container { max-width: 500px; margin: auto; background: #fff; padding: 25px; border: 3px solid #000; text-align: center; }
            .erro { color: #d9534f; background: #f2dede; border: 1px solid #ebccd1; padding: 15px; font-weight: bold; margin-bottom: 20px; }
            .btn-voltar { display: block; width: 100%; padding: 12px; background-color: #e0e0e0; color: #000; text-align: center; font-size: 16px; font-weight: bold; text-decoration: none; border: 2px solid #000; box-sizing: border-box; }
            .btn-voltar:hover { background-color: #d5d5d5; }
        </style>
    </head>
    <body>
    <div class='container'>
        <h1>Limite Atingido</h1>
        <div class='erro'>Atenção: Você já possui 6 caronas cadastradas e atingiu o limite máximo permitido por usuário.</div>
        <a href='index.php' class='btn-voltar'>Voltar ao Início</a>
    </div>
    </body>
    </html>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $matricula = trim($_POST['matricula']);
    $partida = trim($_POST['partida']);
    $data_carona = trim($_POST['data_carona']);
    $horario_saida = trim($_POST['horario_saida']);
    $telefone = trim($_POST['telefone']);

    $valor_bruto = filter_input(INPUT_POST, 'valor', FILTER_VALIDATE_INT);
    $vagas_bruto = filter_input(INPUT_POST, 'vagas', FILTER_VALIDATE_INT);

    if (!empty($matricula) && !empty($partida) && !empty($data_carona) && !empty($horario_saida) && !empty($telefone) && 
        $valor_bruto !== false && $valor_bruto > 0 && 
        $vagas_bruto !== false && $vagas_bruto > 0) {
        
        $valor = (int)$valor_bruto;
        $vagas = (int)$vagas_bruto;

        $sql = "INSERT INTO caronas_ufsm (motorista, matricula, partida, data_carona, horario_saida, valor, vagas, telefone) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssiis", $user_logado, $matricula, $partida, $data_carona, $horario_saida, $valor, $vagas, $telefone);
        
        if ($stmt->execute()) {
            header("Location: index.php");
            exit;
        } else {
            $mensagem = "Erro ao cadastrar carona no banco de dados.";
        }
        $stmt->close();
    } else {
        $mensagem = "Erro: Todos os campos devem ser preenchidos corretamente!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Oferecer Carona - S.T.C</title>
    <style>
        body { background-color: #ffeb3b; font-family: Arial, sans-serif; margin: 0; padding: 20px; color: #000; }
        .container { max-width: 500px; margin: auto; background: #fff; padding: 20px; border: 3px solid #000; }
        h1 { text-align: center; color: #333; }
        label { display: block; margin-top: 12px; font-weight: bold; }
        input { width: 100%; padding: 10px; margin-top: 5px; font-size: 16px; border: 2px solid #000; box-sizing: border-box; }
        .btn { display: block; width: 100%; padding: 12px; margin-top: 20px; background-color: #4CAF50; color: white; text-align: center; font-size: 18px; font-weight: bold; border: 2px solid #000; cursor: pointer; }
        .btn:hover { background-color: #45a049; }
        .btn-voltar { display: block; width: 100%; padding: 10px; margin-top: 10px; background-color: #e0e0e0; color: #000; text-align: center; font-size: 16px; text-decoration: none; border: 2px solid #000; box-sizing: border-box; }
        .erro { color: #d9534f; background: #f2dede; border: 1px solid #ebccd1; padding: 10px; text-align: center; margin-top: 15px; font-weight: bold; }
        .user-info { background: #333; color: #ffeb3b; padding: 8px; text-align: center; font-weight: bold; border: 2px solid #000; margin-bottom: 15px; }
    </style>
    <script>
        function apenasInteiroPositivo(e) {
            const charCode = (e.which) ? e.which : e.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                e.preventDefault();
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
<div class="container">
    <h1>Oferecer Carona</h1>
    
    <div class="user-info">
        Motorista: <?php echo htmlspecialchars($user_logado); ?>
    </div>

    <?php if (!empty($mensagem)): ?>
        <div class="erro"><?php echo $mensagem; ?></div>
    <?php endif; ?>

    <form action="cadastrar_carona.php" method="POST">
        <label>Matrícula do Veículo:</label>
        <input type="text" name="matricula" required placeholder="Ex: ABC-1234">

        <label>Ponto de Partida:</label>
        <input type="text" name="partida" required placeholder="Ex: Campus UFSM Frederico Westphalen">

        <label>Data da Carona:</label>
        <input type="date" name="data_carona" required min="<?php echo date('Y-m-d'); ?>">

        <label>Horário de Saída:</label>
        <input type="time" name="horario_saida" required>

        <label>Valor (Apenas Número Inteiro Positivo em R$):</label>
        <input type="number" name="valor" required min="1" step="1" onkeypress="return apenasInteiroPositivo(event)" placeholder="Ex: 5">

        <label>Vagas Disponíveis (Número Inteiro):</label>
        <input type="number" name="vagas" required min="1" step="1" onkeypress="return apenasInteiroPositivo(event)" placeholder="Ex: 3">

        <label>Telefone / WhatsApp para Contato:</label>
        <input type="text" name="telefone" required placeholder="Ex: (55) 99999-9999">

        <button type="submit" class="btn">Cadastrar Carona</button>
    </form>

    <a href="index.php" class="btn-voltar">Voltar ao Início</a>
</div>
</body>
</html>