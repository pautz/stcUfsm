<?php
// === DEBUGGER DE ERROS 500 ===
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// =============================

session_start();

// O login principal do utilizador vem exclusivamente da sessão username_odonto2
$loginUsuario = $_SESSION['username_odonto2'] ?? '';

$conn = new mysqli("localhost", "u839226731_farol", "Meta6595869!", "u839226731_farol");
if ($conn->connect_error) {
    die("Erro crítico na conexão com o Banco de Dados: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// 1. Garantir que a tabela sites_parceiros existe
$conn->query("CREATE TABLE IF NOT EXISTS sites_parceiros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username_dono VARCHAR(100) NOT NULL,
    nome_site VARCHAR(100) NOT NULL,
    url_retorno VARCHAR(255) NOT NULL,
    caixa_recebimento VARCHAR(50) NOT NULL,
    token_api VARCHAR(64) UNIQUE NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

// 2. Garantir que a coluna 'valor_padrao' existe
$checaColuna = $conn->query("SHOW COLUMNS FROM sites_parceiros LIKE 'valor_padrao'");
if ($checaColuna->num_rows == 0) {
    $conn->query("ALTER TABLE sites_parceiros ADD COLUMN valor_padrao DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER caixa_recebimento");
}

// 3. Verificar se o username_odonto2 possui cadastro correspondente na tabela identificacao_odonto2
$caixaDoUsuario = "";
$usernameOdonto2 = "";
$sessaoValida = false;

if (!empty($loginUsuario)) {
    // Adicionado 'assinaturanv3' na consulta SQL
    $stmtUser = $conn->prepare("SELECT username, caixa_postal, assinantenv3 FROM identificacao_odonto2 WHERE username = ? LIMIT 1");
    if ($stmtUser) {
        $stmtUser->bind_param("s", $loginUsuario);
        $stmtUser->execute();
        $resUser = $stmtUser->get_result();
        if ($rowUser = $resUser->fetch_assoc()) {
            $usernameOdonto2  = $rowUser['username'];
            $caixaDoUsuario   = $rowUser['caixa_postal'] ?? '';
            
            // Atribuindo a assinatura nível 3 (ajuste conforme o valor retornado, ex: 1)
            $assinaturaNv3    = $rowUser['assinantenv3'] ?? 0; 
            
            // Opcional: Validar se é exatamente 1 para considerar a sessão válida
            if ($assinaturaNv3 == 1) {
                $sessaoValida = true;
            } else {
                $sessaoValida = false; // ou outra regra de negócio
            }
        }
        $stmtUser->close();
    }
}

$mensagem = "";
$tipoErro = false;

// 4. Processar exclusão de loja
if (isset($_GET["excluir"]) && $sessaoValida) {
    $idLojaExcluir = intval($_GET["excluir"]);

    $stmtFind = $conn->prepare("SELECT token_api, username_dono FROM sites_parceiros WHERE id = ? LIMIT 1");
    $stmtFind->bind_param("i", $idLojaExcluir);
    $stmtFind->execute();
    $resFind = $stmtFind->get_result();
    
    if ($lojaParaExcluir = $resFind->fetch_assoc()) {
        if ($lojaParaExcluir['username_dono'] === $usernameOdonto2) {
            $arquivoFisico = __DIR__ . "/" . $lojaParaExcluir['token_api'];
            
            if (file_exists($arquivoFisico)) {
                @unlink($arquivoFisico);
            }

            $stmtDel = $conn->prepare("DELETE FROM sites_parceiros WHERE id = ?");
            $stmtDel->bind_param("i", $idLojaExcluir);
            if ($stmtDel->execute()) {
                $mensagem = "🗑️ Loja excluída com sucesso!";
                $tipoErro = false;
            } else {
                $mensagem = "❌ Erro ao excluir a loja do banco de dados.";
                $tipoErro = true;
            }
            $stmtDel->close();
        } else {
            $mensagem = "⚠️ Ação negada: Você só pode excluir suas próprias lojas!";
            $tipoErro = true;
        }
    }
    $stmtFind->close();
}

// 5. Processar criação da loja
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["criar"])) {
    $usernameDono      = trim($usernameOdonto2);
    $nomeSite          = trim($_POST["nome_site"]);
    $urlRetorno        = trim($_POST["url_retorno"]);
    $caixaRecebimento  = trim($caixaDoUsuario);
    $valorPadrao       = intval($_POST["valor_padrao"] ?? 0);

    if (!$sessaoValida) {
        $mensagem = "⚠️ Erro: Sessão inválida ou o utilizador não está registado na tabela 'identificacao_odonto2'!";
        $tipoErro = true;
    } elseif (empty($caixaRecebimento)) {
        $mensagem = "⚠️ Erro: Nenhuma caixa postal foi encontrada na sua identificação odonto2!";
        $tipoErro = true;
    } elseif (empty($nomeSite) || empty($urlRetorno)) {
        $mensagem = "⚠️ Preencha todos os campos obrigatórios!";
        $tipoErro = true;
    } elseif ($valorPadrao <= 0) {
        $mensagem = "⚠️ Erro: O valor do produto deve ser um número inteiro positivo (maior que zero)!";
        $tipoErro = true;
    } else {
        // Validação rigorosa do limite de 10 lojas por utilizador
        $stmtCount = $conn->prepare("SELECT COUNT(*) as total FROM sites_parceiros WHERE username_dono = ?");
        $stmtCount->bind_param("s", $usernameDono);
        $stmtCount->execute();
        $resCount = $stmtCount->get_result()->fetch_assoc();
        $totalLojasUsuario = intval($resCount['total'] ?? 0);
        $stmtCount->close();

        if ($totalLojasUsuario >= 10) {
            $mensagem = "❌ Você já atingiu o limite máximo de 10 lojas cadastradas!";
            $tipoErro = true;
        } else {
            $slugLoja = preg_replace('/[^a-z0-9]/', '_', strtolower($nomeSite));
            $tokenApi = $slugLoja . "_" . substr(md5(uniqid()), 0, 6) . ".php";

            $stmt = $conn->prepare("INSERT INTO sites_parceiros (username_dono, nome_site, url_retorno, caixa_recebimento, valor_padrao, token_api) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssds", $usernameDono, $nomeSite, $urlRetorno, $caixaRecebimento, $valorPadrao, $tokenApi);

            if ($stmt->execute()) {
                $stmt->close();

                // Conteúdo gerado para a página individual da loja
                $conteudoArquivoLoja = '<?php
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
    <title>Pagamento - ' . htmlspecialchars($nomeSite, ENT_QUOTES, 'UTF-8') . '</title>
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
</html>';

                $caminhoArquivo = __DIR__ . "/" . $tokenApi;
                if (@file_put_contents($caminhoArquivo, $conteudoArquivoLoja) !== false) {
                    $mensagem = "✅ Loja blindada criada com sucesso! Página gerada: <code>$tokenApi</code>";
                    $tipoErro = false;
                } else {
                    $conn->query("DELETE FROM sites_parceiros WHERE token_api = '$tokenApi'");
                    $mensagem = "❌ Erro crítico: O PHP não tem permissão de escrita para criar o arquivo <code>$tokenApi</code>.";
                    $tipoErro = true;
                }
            } else {
                $mensagem = "❌ Erro ao registrar a loja no banco de dados: " . $conn->error;
                $tipoErro = true;
            }
        }
    }
}

// 6. BUSCAR APENAS AS LOJAS DO UTILIZADOR
$resultadoLojas = null;
$totalLojasCadastradas = 0;
if ($sessaoValida) {
    $stmtList = $conn->prepare("SELECT * FROM sites_parceiros WHERE username_dono = ? ORDER BY id DESC");
    $stmtList->bind_param("s", $usernameOdonto2);
    $stmtList->execute();
    $resultadoLojas = $stmtList->get_result();
    $totalLojasCadastradas = $resultadoLojas->num_rows;
    $stmtList->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerenciador de Lojas Parceiras - Aura</title>
    <style>
        body { font-family: sans-serif; background: #f4f6f9; display: flex; flex-direction: column; align-items: center; min-height: 100vh; margin: 0; padding: 20px; box-sizing: border-box; }
        .container { width: 100%; max-width: 750px; }
        .box { background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); margin-bottom: 25px; }
        h2, h3 { color: #5c3dc4; text-align: center; margin-top: 0; }
        label { display: block; margin-top: 12px; font-weight: bold; font-size: 13px; color: #555; }
        input, .input-view { width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; font-size: 14px; }
        .input-view { background: #e9ecef; color: #495057; border: 1px solid #ced4da; }
        button { background: #5c3dc4; color: #fff; border: none; width: 100%; padding: 12px; margin-top: 20px; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 15px; }
        button:hover { background: #4a30a0; }
        .msg-sucesso { background: #e2f0d9; color: #385723; padding: 12px; border-radius: 6px; font-size: 13px; text-align: center; margin-top: 15px; border: 1px solid #c9e0b5; }
        .msg-erro { background: #f8d7da; color: #721c24; padding: 12px; border-radius: 6px; font-size: 13px; text-align: center; margin-top: 15px; border: 1px solid #f5c6cb; }
        table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e9ecef; font-size: 13px; }
        th { background: #5c3dc4; color: #fff; }
        tr:hover { background: #f8f9fa; }
        .btn-link { color: #5c3dc4; font-weight: bold; text-decoration: none; margin-right: 8px; }
        .btn-link:hover { text-decoration: underline; }
        .btn-excluir { color: #dc3545; font-weight: bold; text-decoration: none; }
        .btn-excluir:hover { text-decoration: underline; }
        .contador-lojas { text-align: center; font-size: 13px; color: #666; margin-bottom: 15px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="box">
            <h2>Cadastrar Nova Loja 🛍️</h2>

            <?php if (!$sessaoValida): ?>
                <div class="msg-erro" style="margin-bottom: 15px;">⚠️ Atenção: A sessão <code>username_odonto2</code> (<code><?= htmlspecialchars($loginUsuario) ?></code>) não está ativa ou não possui registo correspondente na tabela <code>identificacao_odonto2</code>.</div>
            <?elseif ($totalLojasCadastradas >= 10): ?>
                <div class="msg-erro" style="margin-bottom: 15px;">⚠️ Você atingiu o limite máximo de 10 lojas criadas. Elimine uma loja existente para cadastrar nova.</div>
            <?php endif; ?>

            <form method="POST">
                <label>Sessão (username_odonto2):</label>
                <input type="text" class="input-view" value="<?= htmlspecialchars($loginUsuario) ?>" readonly>

                <label>Identificação Verificada no Banco:</label>
                <input type="text" class="input-view" value="<?= htmlspecialchars($usernameOdonto2) ?>" readonly placeholder="Não encontrado em identificacao_odonto2">

                <label>Caixa de Recebimento (Automático):</label>
                <input type="text" class="input-view" value="<?= htmlspecialchars($caixaDoUsuario) ?>" readonly placeholder="Caixa postal não vinculada">

                <label>Nome da Loja / Produto:</label>
                <input type="text" name="nome_site" required placeholder="Ex: Consultoria Odontológica">

                <label>Valor do Produto/Serviço (Apenas inteiros positivos):</label>
                <input type="number" step="1" min="1" name="valor_padrao" required placeholder="Ex: 150">

                <label>URL de Retorno (Pós-Pagamento):</label>
                <input type="url" name="url_retorno" required placeholder="https://seudominio.com/sucesso">

                <button type="submit" name="criar" <?= (!$sessaoValida || empty($caixaDoUsuario) || $totalLojasCadastradas >= 10) ? 'disabled style="background: #ccc; cursor: not-allowed;"' : '' ?>>Cadastrar Loja</button>
            </form>

            <?php if (!empty($mensagem)): ?>
                <div class="<?= $tipoErro ? 'msg-erro' : 'msg-sucesso' ?>">
                    <?= $mensagem ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="box">
            <h3>Suas Lojas Cadastradas 📋</h3>
            <div class="contador-lojas">Total utilizado: <?= $totalLojasCadastradas ?> de 10 lojas permitidas.</div>

            <?php if ($sessaoValida && $resultadoLojas && $resultadoLojas->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Loja</th>
                            <th>Valor</th>
                            <th>Caixa</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($loja = $resultadoLojas->fetch_assoc()): 
                            $protocolo = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
                            $host = $_SERVER['HTTP_HOST'];
                            $pastaAtual = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
                            $linkLoja = "$protocolo://$host$pastaAtual/" . $loja['token_api'];
                        ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($loja['nome_site']) ?></strong></td>
                                <td>AURA <?= number_format($loja['valor_padrao'], 0, '', '.') ?></td>
                                <td><code><?= htmlspecialchars($loja['caixa_recebimento']) ?></code></td>
                                <td>
                                    <a href="<?= $linkLoja ?>" target="_blank" class="btn-link">Acessar 🔗</a>
                                    <a href="?excluir=<?= $loja['id'] ?>" class="btn-excluir" onclick="return confirm('Tem certeza que deseja excluir esta loja e remover a página de pagamento?')">Excluir 🗑️</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="text-align: center; color: #777; font-size: 14px; margin: 10px 0;">Ainda não tem nenhuma loja cadastrada ou sessão inválida.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>