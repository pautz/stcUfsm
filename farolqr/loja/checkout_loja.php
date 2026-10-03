<?php
// === DEBUGGER DE ERROS ===
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// ==========================

// Initialize the session
session_start();

// Forçar HSTS (apenas se estiver usando HTTPS)
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");
}

// Ação de logout
if (isset($_GET['logout'])) {
    unset($_SESSION['username_odonto2']);
    unset($_SESSION['loggedin_odonto2']);
    unset($_SESSION['id']);
    header("Location: " . strtok($_SERVER['REQUEST_URI'], '?') . "?token=" . ($_GET['token'] ?? ''));
    exit;
}

$tokenLoja = $_GET['token'] ?? '';

if (empty($tokenLoja)) {
    die("❌ Erro: Token da loja não informado.");
}

$conn = new mysqli("localhost", "u839226731_farol", "Meta6595869!", "u839226731_farol");
if ($conn->connect_error) {
    die("❌ Erro de conexão com o Banco de Dados: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

$mensagemLogin = "";
$usernameInput = "";

// Processar tentativa de login direto com reCAPTCHA e segurança
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fazer_login'])) {
    
    // 1. Verifica reCAPTCHA
    if (empty($_POST['g-recaptcha-response'])) {
        $mensagemLogin = "⚠️ Por favor, confirme que você não é um robô.";
    } else {
        $recaptcha_secret = "6LfEEwUtAAAAAAkOE-uNuJNRPzRAAc5t8ZlHSIYi"; 
        $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $recaptcha_secret . "&response=" . $_POST['g-recaptcha-response']);
        $responseKeys = json_decode($response, true);

        if (intval($responseKeys["success"]) !== 1) {
            $mensagemLogin = "⚠️ Falha na verificação do reCAPTCHA. Tente novamente.";
        }
    }

    if (empty($mensagemLogin)) {
        $usernameInput = trim($_POST['username_login'] ?? '');
        $senhaInput   = $_POST['password_login'] ?? '';

        if (!empty($usernameInput) && !empty($senhaInput)) {
            $stmtCheck = $conn->prepare("SELECT id, username, password FROM odonto2_users WHERE username = ? LIMIT 1");
            $stmtCheck->bind_param("s", $usernameInput);
            $stmtCheck->execute();
            $resCheck = $stmtCheck->get_result();
            $dadosUserLogin = $resCheck->fetch_assoc();
            $stmtCheck->close();

            if ($dadosUserLogin) {
                $hashed_password = $dadosUserLogin['password'];

                if (password_verify($senhaInput, $hashed_password) || $senhaInput === $hashed_password) {
                    $_SESSION["loggedin_odonto2"] = true;
                    $_SESSION["id"] = $dadosUserLogin['id'];
                    $_SESSION['username_odonto2'] = $dadosUserLogin['username'];
                    
                    header("Location: " . $_SERVER['REQUEST_URI']);
                    exit;
                } else {
                    $mensagemLogin = "⚠️ A palavra-passe inserida não é válida.";
                }
            } else {
                $mensagemLogin = "⚠️ Nenhuma conta encontrada com esse nome de utilizador.";
            }
        } else {
            $mensagemLogin = "⚠️ Preencha o utilizador e a palavra-passe.";
        }
    }
}

$compradorUsuario = $_SESSION['username_odonto2'] ?? '';

// Buscar dados da loja de forma segura
$stmtLoja = $conn->prepare("SELECT username_dono, nome_site, valor_padrao, caixa_recebimento, url_retorno FROM sites_parceiros WHERE token_api = ? LIMIT 1");
$stmtLoja->bind_param("s", $tokenLoja);
$stmtLoja->execute();
$resLoja = $stmtLoja->get_result();
$dadosLoja = $resLoja->fetch_assoc();
$stmtLoja->close();

if (!$dadosLoja) {
    die("❌ Erro: Loja ou página de pagamento não encontrada.");
}

$nomeSy           = trim($dadosLoja['username_dono']); 
$nomeLoja         = $dadosLoja['nome_site'];
$valorProduto     = floatval($dadosLoja['valor_padrao']);
$caixaRecebimento = trim($dadosLoja['caixa_recebimento']);
$urlRetorno       = trim($dadosLoja['url_retorno']);

// Tratamento seguro da URL de retorno
if (!empty($urlRetorno) && !filter_var($urlRetorno, FILTER_VALIDATE_URL)) {
    $protocolo = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    $pasta = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $urlRetorno = $protocolo . "://" . $host . ($pasta ? $pasta : '') . "/" . ltrim($urlRetorno, '/');
}

$mensagem = "";
$sucessoPagamento = false;
$idComprovante = 0;

// Processamento Blindado do Pagamento
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pagar'])) {
    
    if (empty($compradorUsuario)) {
        $mensagem = "⚠️ Acesso negado: Você precisa estar logado para efetuar o pagamento.";
    } elseif (empty($caixaRecebimento) || empty($nomeSy)) {
        $mensagem = "⚠️ Erro: Configuração da loja incompleta (dono ou caixa de recebimento ausentes).";
    } elseif ($valorProduto <= 0) {
        $mensagem = "⚠️ Erro: O valor do produto é inválido.";
    } else {
        $conn->begin_transaction();

        try {
            // 1. Bloquear e verificar saldo do comprador com segurança (FOR UPDATE)
            $stmtComp = $conn->prepare("SELECT username, saldo_total FROM identificacao_odonto2 WHERE username = ? FOR UPDATE");
            $stmtComp->bind_param("s", $compradorUsuario);
            $stmtComp->execute();
            $resComp = $stmtComp->get_result();
            $dadosComprador = $resComp->fetch_assoc();
            $stmtComp->close();

            if (!$dadosComprador) {
                throw new Exception("Conta do comprador não encontrada na base de dados.");
            }

            $saldoComprador = floatval($dadosComprador['saldo_total'] ?? 0);

            if ($saldoComprador < $valorProduto) {
                throw new Exception("Saldo insuficiente! Você tem AURA " . number_format($saldoComprador, 2, ',', '.') . " e o produto custa AURA " . number_format($valorProduto, 2, ',', '.') . ".");
            }

            // 2. Confirmar dono e caixa postal exata
            $stmtCaixa = $conn->prepare("SELECT username, caixa_postal, saldo_total FROM identificacao_odonto2 WHERE caixa_postal = ? AND username = ? FOR UPDATE");
            $stmtCaixa->bind_param("ss", $caixaRecebimento, $nomeSy);
            $stmtCaixa->execute();
            $resCaixa = $stmtCaixa->get_result();
            $dadosDestinoCaixa = $resCaixa->fetch_assoc();
            $stmtCaixa->close();

            if (!$dadosDestinoCaixa) {
                throw new Exception("Falha de segurança: A caixa postal de destino não pertence ao proprietário oficial desta loja.");
            }

            if ($compradorUsuario === $nomeSy) {
                throw new Exception("Operação negada: Você não pode efetuar pagamento para a sua própria conta.");
            }

            // 3. Executar débito
            $stmtSub = $conn->prepare("UPDATE identificacao_odonto2 SET saldo_total = saldo_total - ? WHERE username = ?");
            $stmtSub->bind_param("ds", $valorProduto, $compradorUsuario);
            if (!$stmtSub->execute()) {
                throw new Exception("Erro interno ao debitar o saldo.");
            }
            $stmtSub->close();

            // 4. Executar crédito
            $stmtAdd = $conn->prepare("UPDATE identificacao_odonto2 SET saldo_total = saldo_total + ? WHERE caixa_postal = ? AND username = ?");
            $stmtAdd->bind_param("dss", $valorProduto, $caixaRecebimento, $nomeSy);
            if (!$stmtAdd->execute()) {
                throw new Exception("Erro interno ao creditar o saldo na caixa de destino.");
            }
            $stmtAdd->close();

            // 5. Registar o comprovante guardando também o token_loja (Com 6 variáveis e tipos corretos sssdss)
            $stmtIns = $conn->prepare("INSERT INTO confirmado_aura (comprador, dono_loja, nome_site, valor, caixa_destino, token_loja, data_confirmacao) VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $stmtIns->bind_param("sssdss", $compradorUsuario, $nomeSy, $nomeLoja, $valorProduto, $caixaRecebimento, $tokenLoja);
            
            if (!$stmtIns->execute()) {
                throw new Exception("Erro ao registar o comprovante da transação: " . $stmtIns->error);
            }
            
            $idComprovante = $conn->insert_id;
            $stmtIns->close();

            $conn->commit();
            $sucessoPagamento = true;

        } catch (Exception $e) {
            $conn->rollback();
            $mensagem = "❌ Transação rejeitada: " . $e->getMessage();
        }
    }
}

$saldoAtualComprador = 0;
if (!empty($compradorUsuario)) {
    $stmtSaldo = $conn->prepare("SELECT saldo_total FROM identificacao_odonto2 WHERE username = ? LIMIT 1");
    if ($stmtSaldo) {
        $stmtSaldo->bind_param("s", $compradorUsuario);
        $stmtSaldo->execute();
        $resS = $stmtSaldo->get_result();
        if ($rowS = $resS->fetch_assoc()) {
            $saldoAtualComprador = floatval($rowS['saldo_total'] ?? 0);
        }
        $stmtSaldo->close();
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - <?= htmlspecialchars($nomeLoja) ?></title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        body { font-family: sans-serif; background: #f4f6f9; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .card { background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); width: 100%; max-width: 450px; text-align: center; box-sizing: border-box; }
        h2 { color: #5c3dc4; margin-top: 0; }
        .valor { font-size: 32px; font-weight: bold; color: #28a745; margin: 15px 0; }
        .info-conta { background: #f8f9fa; border: 1px solid #e9ecef; padding: 12px; border-radius: 6px; font-size: 13px; color: #555; margin-bottom: 20px; text-align: left; }
        .btn-pagar { background: #28a745; color: #fff; border: none; width: 100%; padding: 14px; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 16px; box-sizing: border-box; text-decoration: none; display: block; margin-bottom: 10px; }
        .btn-pagar:hover { background: #218838; }
        .btn-pdf { background: #007bff; color: #fff; border: none; width: 100%; padding: 14px; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 16px; box-sizing: border-box; text-decoration: none; display: block; margin-bottom: 10px; }
        .btn-pdf:hover { background: #0056b3; }
        .btn-login { background: #5c3dc4; color: #fff; border: none; width: 100%; padding: 12px; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 15px; margin-top: 5px; }
        .btn-login:hover { background: #4a30a0; }
        .btn-voltar { background: #6c757d; color: #fff; text-decoration: none; display: inline-block; width: 100%; padding: 12px; border-radius: 6px; font-weight: bold; margin-top: 10px; box-sizing: border-box; }
        .btn-voltar:hover { background: #5a6268; }
        .msg-erro { background: #f8d7da; color: #721c24; padding: 12px; border-radius: 6px; font-size: 13px; margin-bottom: 15px; border: 1px solid #f5c6cb; text-align: left; }
        .msg-sucesso { background: #e2f0d9; color: #385723; padding: 15px; border-radius: 6px; font-size: 14px; margin-bottom: 15px; border: 1px solid #c9e0b5; text-align: left; }
        .form-group { text-align: left; margin-bottom: 12px; }
        .form-group label { font-size: 13px; font-weight: bold; color: #555; display: block; margin-bottom: 4px; }
        .form-group input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; font-size: 14px; }
        .recaptcha-box { margin: 15px 0; display: flex; justify-content: center; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Checkout: <?= htmlspecialchars($nomeLoja) ?></h2>

        <?php if ($sucessoPagamento): ?>
            <div class="msg-sucesso">
                ✅ <strong>Pagamento realizado com sucesso!</strong><br><br>
                <strong>ID do Comprovante:</strong> <code>#<?= $idComprovante ?></code><br>
                <strong>Comprador:</strong> <?= htmlspecialchars($compradorUsuario) ?><br>
                <strong>Valor:</strong> AURA <?= number_format($valorProduto, 2, ',', '.') ?>
            </div>

            <a href="gerar_pdf.php?id=<?= $idComprovante ?>" target="_blank" class="btn-pdf">📄 Gerar PDF do Comprovante</a>
            <a href="<?= htmlspecialchars($urlRetorno) ?>" class="btn-pagar">🚀 Acessar Site de Retorno</a>

        <?php else: ?>
            <p>Confirme os dados da transação para debitar do seu saldo Aura.</p>
            <div class="valor">AURA <?= number_format($valorProduto, 2, ',', '.') ?></div>

            <?php if (!empty($compradorUsuario)): ?>
                <div class="info-conta">
                    <strong>Sua Conta:</strong> <?= htmlspecialchars($compradorUsuario) ?><br>
                    <strong>Seu Saldo Total:</strong> AURA <?= number_format($saldoAtualComprador, 2, ',', '.') ?><br>
                    <strong>Dono / Caixa Destino:</strong> <code><?= htmlspecialchars($nomeSy) ?></code> / <code><?= htmlspecialchars($caixaRecebimento) ?></code>
                </div>

                <?php if (!empty($mensagem)): ?>
                    <div class="msg-erro"><?= $mensagem ?></div>
                <?php endif; ?>

                <form method="POST">
                    <button type="submit" name="pagar" class="btn-pagar">Pagar com Saldo Aura 💳</button>
                </form>

                <div style="margin-top: 15px; font-size: 12px;">
                    <a href="?token=<?= urlencode($tokenLoja) ?>&logout=1" style="color: #dc3545; text-decoration: none;">Entrar com outra conta (Sair)</a>
                </div>

            <?php else: ?>
                <div class="info-conta" style="text-align: center;">
                    <strong>⚠️ Faça login para prosseguir com o pagamento.</strong>
                </div>

                <?php if (!empty($mensagemLogin)): ?>
                    <div class="msg-erro"><?= $mensagemLogin ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label>Utilizador:</label>
                        <input type="text" name="username_login" value="<?= htmlspecialchars($usernameInput) ?>" required placeholder="Digite o seu username">
                    </div>
                    <div class="form-group">
                        <label>Palavra-passe:</label>
                        <input type="password" name="password_login" required placeholder="Digite a sua senha">
                    </div>

                    <div class="recaptcha-box">
                        <div class="g-recaptcha" data-sitekey="6LfEEwUtAAAAALFN0MhqfugRRhm4q-fcxO-wKND9"></"></div>
                    </div>

                    <button type="submit" name="fazer_login" class="btn-login">Entrar e Pagar 🔓</button>
                </form>
            <?php endif; ?>
        <a href="https://carlitoslocacoes.com/login/register_odonto2.php" class="btn-voltar">Registrar-se</a>
         <a href="https://carlitoslocacoes.com/sys/index.php" class="btn-voltar">Comprar AURA</a>
         <a href="https://carlitoslocacoes.com/" class="btn-voltar">Início</a>
            <a href="javascript:history.back()" class="btn-voltar">Voltar</a>
        <?php endif; ?>
    </div>
</body>
</html>