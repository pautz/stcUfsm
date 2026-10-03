<?php
include 'conexao.php';

// 1. Verificar se a sessão do utilizador está ativa
if (!isset($_SESSION['username_odonto2']) || empty($_SESSION['username_odonto2'])) {
    header("Location: https://carlitoslocacoes.com/login/login_farolqr.php");
    exit;
}

$user_logado = $_SESSION['username_odonto2'];

// 2. Verificar na base de dados se o username existe e se tem caixa_postal vinculada
$sql_check = "SELECT saldo_total, caixa_postal FROM identificacao_odonto2 WHERE username = ?";
$stmt = $conn->prepare($sql_check);
$stmt->bind_param("s", $user_logado);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    session_destroy();
    header("Location: https://carlitoslocacoes.com/login/login_farolqr.php");
    exit;
}

$dados_usuario = $resultado->fetch_assoc();
$stmt->close();

$saldo_total = $dados_usuario['saldo_total'];
$caixa_postal_usuario = $dados_usuario['caixa_postal'];

// 3. Exigir que o utilizador tenha uma caixa_postal preenchida
if (empty($caixa_postal_usuario)) {
    echo "<div style='font-family: Arial; padding: 40px; text-align: center; background: #ffeb3b;'>";
    echo "<div style='max-width: 500px; margin: auto; background: #fff; padding: 20px; border: 3px solid #000;'>";
    echo "<h2 style='color: #d9534f;'>Acesso Restrito</h2>";
    echo "<p>Sua conta (<strong>" . htmlspecialchars($user_logado) . "</strong>) não possui uma <strong>Caixa Postal</strong> vinculada na base de dados.</p>";
    echo "<p>Por favor, configure sua caixa postal ou entre em contato com o suporte.</p>";
    echo "<a href='https://carlitoslocacoes.com/login/logout.php' style='display:inline-block; margin-top:15px; padding:10px 20px; background:#333; color:#ffeb3b; text-decoration:none; font-weight:bold; border:2px solid #000;'>Sair / Tentar Outra Conta</a>";
    echo "</div></div>";
    exit;
}

// 4. Parâmetros de Paginação e Filtro por SIAPE
$limite_por_pagina = 4;
$pagina_atual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina_atual < 1) { $pagina_atual = 1; }
$inicio = ($pagina_atual - 1) * $limite_por_pagina;

$filtro_siape = isset($_GET['filtro_siape']) ? trim($_GET['filtro_siape']) : '';

// 5. Contagem total para paginação (agora inclui as caronas do próprio utilizador)
if (!empty($filtro_siape)) {
    $termo_busca = "%" . $filtro_siape . "%";
    $sql_total = "SELECT COUNT(*) as total FROM caronas_ufsm WHERE vagas > 0 AND motorista LIKE ?";
    $stmt_t = $conn->prepare($sql_total);
    $stmt_t->bind_param("s", $termo_busca);
} else {
    $sql_total = "SELECT COUNT(*) as total FROM caronas_ufsm WHERE vagas > 0";
    $stmt_t = $conn->prepare($sql_total);
}
$stmt_t->execute();
$total_registos = $stmt_t->get_result()->fetch_assoc()['total'];
$stmt_t->close();

$total_paginas = ceil($total_registos / $limite_por_pagina);

// 6. Buscar caronas com Paginação e Filtro por SIAPE (exibe todas as caronas com vagas)
if (!empty($filtro_siape)) {
    $termo_busca = "%" . $filtro_siape . "%";
    $sql_caronas = "SELECT * FROM caronas_ufsm WHERE vagas > 0 AND motorista LIKE ? ORDER BY id DESC LIMIT ? OFFSET ?";
    $stmt_c = $conn->prepare($sql_caronas);
    $stmt_c->bind_param("sii", $termo_busca, $limite_por_pagina, $inicio);
} else {
    $sql_caronas = "SELECT * FROM caronas_ufsm WHERE vagas > 0 ORDER BY id DESC LIMIT ? OFFSET ?";
    $stmt_c = $conn->prepare($sql_caronas);
    $stmt_c->bind_param("ii", $limite_por_pagina, $inicio);
}
$stmt_c->execute();
$caronas = $stmt_c->get_result();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>S.T.C - Sistema de Transporte Cooperativo</title>
    <style>
        body { background-color: #ffeb3b; font-family: Arial, sans-serif; margin: 0; padding: 20px; color: #000; }
        .container { max-width: 600px; margin: auto; background: #fff; padding: 20px; border: 3px solid #000; }
        h1, h2 { text-align: center; color: #333; }
        .btn { display: block; width: 100%; padding: 15px; margin: 10px 0; background-color: #e0e0e0; color: #000; text-align: center; font-size: 20px; font-weight: bold; text-decoration: none; border: 2px solid #000; box-sizing: border-box; }
        .btn:hover { background-color: #d5d5d5; }
        .card { background: #f9f9f9; border: 1px solid #ccc; padding: 15px; margin-bottom: 15px; }
        .card-propria { background: #e8f5e9; border: 2px solid #4CAF50; } /* Destaque para as caronas do próprio utilizador */
        .saldo-box { background: #333; color: #ffeb3b; padding: 10px; text-align: center; font-size: 16px; font-weight: bold; margin-bottom: 15px; border: 2px solid #000; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 5px; }
        .btn-sair { background: #d9534f; color: #fff; padding: 5px 10px; font-size: 14px; text-decoration: none; border: 1px solid #000; }
        .whatsapp-link { display: inline-block; background-color: #25D366; color: white; padding: 8px 12px; text-decoration: none; font-weight: bold; border-radius: 4px; margin-top: 5px; font-size: 14px; border: 1px solid #000; }
        .whatsapp-link:hover { background-color: #1ebe5d; }
        .carona-id { background: #333; color: #ffeb3b; padding: 3px 8px; font-weight: bold; font-size: 15px; display: inline-block; margin-bottom: 10px; border: 1px solid #000; }
        .badge-sua { background: #4CAF50; color: #fff; padding: 3px 8px; font-weight: bold; font-size: 13px; display: inline-block; margin-left: 5px; border: 1px solid #000; }
        
        .filter-box { background: #f1f1f1; border: 2px solid #000; padding: 12px; margin-bottom: 20px; display: flex; gap: 10px; align-items: center; justify-content: center; flex-wrap: wrap; }
        .filter-box input { padding: 8px; font-size: 14px; border: 2px solid #000; width: 160px; }
        .filter-box button { padding: 8px 15px; background: #333; color: #ffeb3b; font-weight: bold; border: 2px solid #000; cursor: pointer; }
        .filter-box a { padding: 8px 12px; background: #d9534f; color: #fff; font-weight: bold; border: 2px solid #000; text-decoration: none; font-size: 14px; }
        
        .pagination { text-align: center; margin-top: 20px; margin-bottom: 10px; }
        .pagination a, .pagination span { display: inline-block; padding: 8px 12px; margin: 0 3px; background: #e0e0e0; color: #000; text-decoration: none; font-weight: bold; border: 2px solid #000; }
        .pagination a:hover { background: #d5d5d5; }
        .pagination .active { background: #333; color: #ffeb3b; }
    </style>
</head>
<body>
<div class="container">
    <h1>Sistema de Caronas - S.T.C</h1>
    
    <div class="saldo-box">
        <span>Usuário: <?php echo htmlspecialchars($user_logado); ?> | Caixa: <?php echo htmlspecialchars($caixa_postal_usuario); ?> | Saldo: R$ <?php echo number_format($saldo_total, 2, ',', '.'); ?></span>
        <a href="https://carlitoslocacoes.com/login/logout.php" class="btn-sair">Sair</a>
    </div>

    <h2>Menu Principal</h2>
    <a href="cadastrar_carona.php" class="btn">Oferecer Carona</a>

    <hr style="margin: 20px 0;">

    <h2>Caronas Disponíveis</h2>

    <!-- Filtro por SIAPE -->
    <form method="GET" action="index.php" class="filter-box">
        <label for="filtro_siape" style="font-weight: bold; font-size: 14px;">Filtrar por SIAPE:</label>
        <input type="text" id="filtro_siape" name="filtro_siape" value="<?php echo htmlspecialchars($filtro_siape); ?>" placeholder="Ex: SIAPE / Motorista">
        <button type="submit">Buscar</button>
        <?php if (!empty($filtro_siape)): ?>
            <a href="index.php">Limpar Filtro</a>
        <?php endif; ?>
    </form>

    <?php if ($caronas->num_rows > 0): ?>
        <?php while($c = $caronas->fetch_assoc()): ?>
            <?php $eh_minha = ($c['motorista'] === $user_logado); ?>
            <div class="card <?php echo $eh_minha ? 'card-propria' : ''; ?>">
                <div>
                    <span class="carona-id">ID da Carona: #<?php echo $c['id']; ?></span>
                    <?php if ($eh_minha): ?>
                        <span class="badge-sua">Sua Carona Cadastrada</span>
                    <?php endif; ?>
                </div>

                <p><strong>Motorista / SIAPE:</strong> <?php echo htmlspecialchars($c['motorista']); ?></p>
                <p><strong>Matrícula:</strong> <?php echo htmlspecialchars($c['matricula']); ?></p>
                <p><strong>Ponto de Partida:</strong> <?php echo htmlspecialchars($c['partida']); ?></p>
                <p><strong>Data da Carona:</strong> <?php echo date('d/m/Y', strtotime($c['data_carona'])); ?></p>
                <p><strong>Horário de Saída:</strong> <span style="color: #d9534f; font-weight: bold;"><?php echo htmlspecialchars($c['horario_saida']); ?></span></p>
                <p><strong>Valor:</strong> R$ <?php echo number_format($c['valor'], 2, ',', '.'); ?></p>
                <p><strong>Vagas Restantes:</strong> <?php echo $c['vagas']; ?></p>
                
                <?php if (!empty($c['telefone'])): ?>
                    <p><strong>Telefone / WhatsApp:</strong> <?php echo htmlspecialchars($c['telefone']); ?></p>
                    <?php if (!$eh_minha): ?>
                        <a href="https://wa.me/55<?php echo preg_replace('/\D/', '', $c['telefone']); ?>?text=Olá,%20peguei%20a%20carona%20(ID:%20<?php echo $c['id']; ?>)%20no%20S.T.C.%20Segue%20o%20comprovante:" target="_blank" class="whatsapp-link">Enviar Comprovante via WhatsApp</a>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if (!$eh_minha): ?>
                    <!-- Botão para pegar carona (apenas para caronas dos outros) -->
                    <form action="pegar_carona.php" method="POST" style="margin-top: 15px;">
                        <input type="hidden" name="carona_id" value="<?php echo $c['id']; ?>">
                        <button type="submit" class="btn" style="background-color: #4CAF50; color: white; padding: 10px; font-size: 16px; cursor: pointer;">Pegar Carona</button>
                    </form>
                <?php else: ?>
                    <p style="margin-top: 15px; text-align: center; font-weight: bold; color: #2e7d32; background: #c8e6c9; padding: 8px; border: 1px solid #4CAF50;">Esta é a carona que você disponibilizou.</p>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>

        <!-- Paginação Inteligente -->
        <?php if ($total_paginas > 1): ?>
            <div class="pagination">
                <?php 
                $param_filtro = !empty($filtro_siape) ? "&filtro_siape=" . urlencode($filtro_siape) : "";
                
                if ($pagina_atual > 1) {
                    echo '<a href="index.php?pagina=' . ($pagina_atual - 1) . $param_filtro . '">&laquo; Anterior</a>';
                }

                $inicio_pag = max(1, $pagina_atual - 2);
                $fim_pag = min($total_paginas, $pagina_atual + 2);

                for ($i = $inicio_pag; $i <= $fim_pag; $i++) {
                    if ($i == $pagina_atual) {
                        echo '<span class="active">' . $i . '</span>';
                    } else {
                        echo '<a href="index.php?pagina=' . $i . $param_filtro . '">' . $i . '</a>';
                    }
                }

                if ($pagina_atual < $total_paginas) {
                    echo '<a href="index.php?pagina=' . ($pagina_atual + 1) . $param_filtro . '">Próxima &raquo;</a>';
                }
                ?>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <p style="text-align: center;">Nenhuma carona encontrada <?php echo !empty($filtro_siape) ? "correspondente ao SIAPE/Motorista '".htmlspecialchars($filtro_siape)."'" : "disponível no momento"; ?>.</p>
        <?php if (!empty($filtro_siape)): ?>
            <div style="text-align: center; margin-top: 10px;">
                <a href="index.php" style="color: #000; font-weight: bold;">Ver todas as caronas</a>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
</body>
</html>