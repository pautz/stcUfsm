<?php
session_start();
if (!isset($_SESSION["loggedin_odonto2"]) || $_SESSION["loggedin_odonto2"] !== true) {
    header("location: ../login/login_farolqr.php");
    exit;
}

$usuario = $_SESSION["username_odonto2"];
$mensagem = "";
$comprovante = null;

$conn = new mysqli("localhost", "u839226731_farol", "Meta6595869!", "u839226731_farol");
if ($conn->connect_error) die("Erro na conexão: " . $conn->connect_error);
$conn->set_charset("utf8mb4");

// 🔍 Buscar caixa_postal e saldo do usuário logado
$stmtUser = $conn->prepare("SELECT caixa_postal, saldo_total FROM identificacao_odonto2 WHERE username = ? LIMIT 1");
$stmtUser->bind_param("s", $usuario);
$stmtUser->execute();
$resUser = $stmtUser->get_result();
$userData = $resUser->fetch_assoc();
$caixaUsuario = $userData["caixa_postal"] ?? null;
$saldoAura = $userData["saldo_total"] ?? 0;
$stmtUser->close();

if (!$userData) {
    header("Location: https://carlitoslocacoes.com/farolqr/identificacao_farolqr.php");
    exit;
}

// ✅ Mensagem pós transação
if (isset($_GET["transacao"]) && isset($_SESSION["comprovante_aura"])) {
    $mensagem = "✅ Transação registrada com sucesso!";
    $comprovante = $_SESSION["comprovante_aura"];
    $qrFile = $_SESSION["qr_file"] ?? null;
    unset($_SESSION["comprovante_aura"]);
}

// 🚀 Processar transferência
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["enviar_aura"])) {
    $destinoCaixa = $_POST["caixa_destino"];
    $valor = (int)$_POST["valor"];
    $senhaInformada = $_POST["senha_usuario"];

    // Verificar senha
    $stmtSenha = $conn->prepare("SELECT password FROM odonto2_users WHERE username = ?");
    $stmtSenha->bind_param("s", $usuario);
    $stmtSenha->execute();
    $resSenha = $stmtSenha->get_result();
    $senhaCorreta = $resSenha->fetch_assoc()["password"] ?? null;
    $stmtSenha->close();

    // Limite diário
    $limiteDiario = 1500000;
    $hoje = date("Y-m-d");
    $stmtTotalDia = $conn->prepare("SELECT SUM(valor) AS total_dia FROM transacoes_aura WHERE remetente = ? AND DATE(data_registro) = ?");
    $stmtTotalDia->bind_param("ss", $usuario, $hoje);
    $stmtTotalDia->execute();
    $resTotalDia = $stmtTotalDia->get_result();
    $totalDia = (int)($resTotalDia->fetch_assoc()["total_dia"] ?? 0);
    $stmtTotalDia->close();

    if (!$senhaCorreta || !password_verify($senhaInformada, $senhaCorreta)) {
        $mensagem = "⚠️ Senha incorreta.";
    } elseif ($valor <= 0 || $saldoAura < $valor) {
        $mensagem = "⚠️ Valor inválido ou saldo insuficiente.";
    } elseif ($valor > 72000) {
        $mensagem = "⚠️ Máximo por transação é 72.000 aura.";
    } elseif (($totalDia + $valor) > $limiteDiario) {
        $mensagem = "⚠️ Limite diário excedido.";
    } else {
        $stmtDest = $conn->prepare("SELECT saldo_total FROM identificacao_odonto2 WHERE caixa_postal = ?");
        $stmtDest->bind_param("s", $destinoCaixa);
        $stmtDest->execute();
        $resDest = $stmtDest->get_result();
        $destData = $resDest->fetch_assoc();
        $stmtDest->close();

        if ($destData) {
            $conn->begin_transaction();
            try {
                // Atualizar saldos
                $stmtDeb = $conn->prepare("UPDATE identificacao_odonto2 SET saldo_total = saldo_total - ? WHERE caixa_postal = ? AND username = ?");
                $stmtDeb->bind_param("iss", $valor, $caixaUsuario, $usuario);
                $stmtDeb->execute();
                $stmtDeb->close();

                $stmtCred = $conn->prepare("UPDATE identificacao_odonto2 SET saldo_total = saldo_total + ? WHERE caixa_postal = ?");
                $stmtCred->bind_param("is", $valor, $destinoCaixa);
                $stmtCred->execute();
                $stmtCred->close();

                // Assinatura
                $assinatura = hash('sha256', $usuario . $destinoCaixa . $valor . $caixaUsuario . microtime(true));

                // Registrar transação
                $stmtInsert = $conn->prepare("INSERT INTO transacoes_aura (remetente, destinatario, valor, caixa_origem, caixa_destino, assinatura) VALUES (?, ?, ?, ?, ?, ?)");
                $stmtInsert->bind_param("ssisss", $usuario, $destinoCaixa, $valor, $caixaUsuario, $destinoCaixa, $assinatura);
                $stmtInsert->execute();
                $transacaoId = $conn->insert_id;
                $stmtInsert->close();

                // Comprovante
                $comprovante = [
                    "id_transacao" => $transacaoId,
                    "remetente" => $usuario,
                    "destinatario" => $destinoCaixa,
                    "valor" => $valor,
                    "caixa_origem" => $caixaUsuario,
                    "caixa_destino" => $destinoCaixa,
                    "data" => date("d/m/Y H:i:s"),
                    "assinatura" => $assinatura
                ];
                $_SESSION["comprovante_aura"] = $comprovante;

                // QR Code permanente
                include "../tickets/2/phpqrcode/qrlib.php";
                $qrcodeUrl = "https://carlitoslocacoes.com/aurascan.php?buscaId=" . $transacaoId;
                $_SESSION["qrcode_url"] = $qrcodeUrl;
                $qrDir = __DIR__ . "/qrcodes/";
                if (!file_exists($qrDir)) mkdir($qrDir, 0777, true);
                $qrFile = $qrDir . "comprovante_" . $transacaoId . ".png";
                QRcode::png($qrcodeUrl, $qrFile, QR_ECLEVEL_L, 6);
                $_SESSION["qr_file"] = "qrcodes/comprovante_" . $transacaoId . ".png";

                // Registrar comprovante
                $stmtComp = $conn->prepare("INSERT INTO comprovantes_aura (remetente, destinatario, valor, caixa_origem, caixa_destino, transacao_id, assinatura) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmtComp->bind_param("ssissis", $usuario, $destinoCaixa, $valor, $caixaUsuario, $destinoCaixa, $transacaoId, $assinatura);
                $stmtComp->execute();
                $stmtComp->close();

                $conn->commit();
                header("Location: " . $_SERVER["PHP_SELF"] . "?transacao=ok");
                exit;
            } catch (Exception $e) {
                $conn->rollback();
                $mensagem = "❌ Erro: " . $e->getMessage();
            }
        } else {
            $mensagem = "⚠️ Caixa postal não encontrada.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>🔐 Painel de Aura</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
 :root {
  --bg-gradient: linear-gradient(135deg, #ff006e, #d81b60, #880e4f); /* fundo rosa */
  --card-bg: #1e1e2f;
  --accent: #ff4081;
  --accent-light: #ff80ab;
  --text-light: #f0f0f0;
  --radius: 16px;
  --shadow: 0 6px 20px rgba(0,0,0,0.4);
  --font-main: 'Segoe UI', 'Roboto', sans-serif;
}

/* ======== GERAL ======== */
body {
  font-family: var(--font-main);
  background: var(--bg-gradient);
  color: var(--text-light);
  margin: 0;
  padding: 40px;
}

.container {
  max-width: 700px;
  margin: auto;
  background: var(--card-bg);
  border-radius: var(--radius);
  box-shadow: var(--shadow);
  padding: 30px;
  text-align: center;
  animation: fadeInUp 1.5s ease-in-out;
}

h2 {
  font-weight: 600;
  margin-bottom: 20px;
  color: var(--accent);
}

.balance, .saldo {
  font-size: 2rem;
  margin: 20px 0;
  color: var(--text-light);
  font-weight: bold;
}

/* ======== FORMULÁRIOS ======== */
form {
  margin-top: 20px;
  text-align: left;
}

input[type="text"],
input[type="number"],
input[type="password"] {
  width: 100%;
  padding: 12px;
  margin: 10px 0;
  border-radius: var(--radius);
  border: none;
  font-size: 1rem;
  background-color: #2c2c3c;
  color: var(--text-light);
}

input::placeholder {
  color: #aaa;
}

button {
  background: linear-gradient(90deg, var(--accent), var(--accent-light));
  color: white;
  border: none;
  padding: 14px;
  border-radius: var(--radius);
  font-size: 1rem;
  cursor: pointer;
  width: 100%;
  font-weight: bold;
  transition: transform 0.2s ease, background 0.3s ease;
}

button:hover {
  transform: scale(1.05);
  background: linear-gradient(90deg, var(--accent-light), var(--accent));
}

/* ======== COMPROVANTE ======== */
.comprovante {
  margin-top: 20px;
  padding: 20px;
  background: rgba(255,255,255,0.05);
  border: 2px dashed var(--accent);
  border-radius: var(--radius);
  box-shadow: var(--shadow);
}

.comprovante h3 {
  color: var(--accent-light);
  margin-bottom: 15px;
}

.comprovante p {
  font-size: 0.95rem;
  color: var(--text-light);
}

.comprovante input {
  background: #2c2c3c;
  color: var(--text-light);
  border-radius: var(--radius);
  border: none;
  margin-top: 8px;
}

img.qr {
  display: block;
  margin: 15px auto;
  border-radius: var(--radius);
  box-shadow: var(--shadow);
}

/* ======== SIDEBAR ======== */
.sidebar {
  position: fixed;
  top: 0;
  left: -220px;
  width: 220px;
  height: 100%;
  background: #2c2c3c;
  padding-top: 60px;
  transition: left 0.3s ease;
  z-index: 1000;
}

.sidebar.active {
  left: 0;
}

.sidebar a {
  display: block;
  color: var(--text-light);
  text-decoration: none;
  padding: 12px 20px;
  font-weight: bold;
}

.sidebar a:hover {
  background: var(--accent);
}

.menu-toggle {
  background: var(--accent);
  color: white;
  padding: 12px 20px;
  cursor: pointer;
  font-size: 1.2rem;
  position: fixed;
  top: 10px;
  left: 10px;
  border-radius: 6px;
  z-index: 1100;
}

/* ======== ANIMAÇÕES ======== */
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}

/* ======== RESPONSIVIDADE ======== */
@media (max-width: 768px) {
  .container {
    padding: 20px;
  }

  .balance, .saldo {
    font-size: 1.5rem;
  }

  .sidebar {
    width: 180px;
  }

  button {
    font-size: 0.9rem;
    padding: 12px;
  }
}

  </style>
</head>
<body>
 <div class="menu-toggle" onclick="toggleMenu()">☰ Menu</div>

  <!-- Menu lateral -->
  <nav class="sidebar" id="sidebar">
    <a href="https://carlitoslocacoes.com/index.php" target="_blank">🏠 Início</a>
    <a href="https://carlitoslocacoes.com/sys/index.php" target="_blank">🛒 Compre AURA</a>
    <a href="https://carlitoslocacoes.com/login/logout.php">🚪 Sair</a>
  </nav>
  <script>
function toggleMenu() {
  document.getElementById("sidebar").classList.toggle("active");
}
</script>
  <div class="container">
      

    <h2>🔐 Painel de Aura</h2>
<p>
  <strong>Caixa Postal:</strong> 
  <span id="caixaPostal"><?= htmlspecialchars(html_entity_decode($caixaUsuario, ENT_QUOTES | ENT_HTML5, 'UTF-8')) ?></span>
  <button class="btn-aura" onclick="copiarCaixaPostal()">📋 Copiar Caixa Postal</button>
</p>

<script>
function copiarCaixaPostal() {
  var texto = document.getElementById("caixaPostal").innerText;
  navigator.clipboard.writeText(texto).then(function() {
    alert("Caixa Postal copiada!");
  }).catch(function() {
    alert("Não foi possível copiar.");
  });
}
</script>

    <?php if ($mensagem): ?>
      <div class="mensagem"><?= $mensagem ?></div>
    <?php endif; ?>

    <?php if ($comprovante): ?>
<div class="comprovante">
  <h3>📄 Comprovante de Transação</h3>
    <p>ID da Transação: <?= $comprovante["id_transacao"] ?></p>
<img class="qr" src="<?= $_SESSION["qr_file"] ?>" alt="QR Comprovante">
  <p><a href="<?= $_SESSION["qrcode_url"] ?>" target="_blank">🔗 Abrir comprovante</a></p>

  <p><strong>Remetente:</strong> <?= htmlspecialchars($comprovante["remetente"]) ?></p>
  <p><strong>Caixa Origem:</strong> <?= htmlspecialchars($comprovante["caixa_origem"]) ?></p>
  <p><strong>Caixa Destino:</strong> <?= htmlspecialchars($comprovante["caixa_destino"]) ?></p>
  <p><strong>Quantidade:</strong> <?= $comprovante["valor"] ?> aura</p>
  <p><strong>Data:</strong> <?= $comprovante["data"] ?></p>

  <!-- Assinatura com campo de copia e cola -->
  <p><strong>Assinatura:</strong></p>
  <input type="text" id="assinatura" value="<?= htmlspecialchars($comprovante["assinatura"]) ?>" readonly style="width:100%;padding:8px;">
  <button onclick="copiarAssinatura()">📋 Copiar Assinatura</button>
  <button onclick="window.print()">🖨️ Imprimir Comprovante</button>
</div>

<script>
function copiarAssinatura() {
  var campo = document.getElementById("assinatura");
  campo.select();
  campo.setSelectionRange(0, 99999); // para mobile
  document.execCommand("copy");
  alert("Assinatura copiada!");
}
</script>
<?php endif; ?>


    <div class="saldo">✨ Saldo total de aura: <strong><?= $saldoAura ?></strong></div>

    <h3>📦 Enviar Aura</h3>
    <form method="POST">
      <input type="text" name="caixa_destino" placeholder="Caixa postal destino" required>
      <input type="number" name="valor" placeholder="Quantidade de aura a enviar" required>
      <input type="password" name="senha_usuario" placeholder="Sua senha para confirmar" required>
      <input type="hidden" name="enviar_aura" value="1">
      <button>⚡ Confirmar e Enviar Aura</button>
    </form>
  </div>
  
</body>
</html>

