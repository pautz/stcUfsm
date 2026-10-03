<?php
// 🔒 Redirecionar para HTTPS se necessário
if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
    $url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('Location: ' . $url);
    exit();
}

// 🛡️ Cabeçalhos de segurança
header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: no-referrer');
header("Content-Security-Policy: frame-ancestors 'self' https://carlitoslocacoes.com;");
header("X-Powered-By: MeuServidorPHP");

require '../site2/pix_aura/aura_db.php'; // conexão com o banco

$erro = null;
$caixa = $_GET['caixa_postal'] ?? '';
$aura  = intval($_GET['aura'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $caixa = trim($_POST['caixa_postal'] ?? '');
    $aura  = intval($_POST['aura'] ?? 0);

    if ($caixa === '') {
        $erro = "⚠️ Informe a Caixa Postal.";
    } else {
        $stmt = $cx->prepare("SELECT id FROM identificacao_odonto2 WHERE caixa_postal = ?");
        $stmt->bind_param("s", $caixa);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if (!$row) {
            $erro = "❌ Caixa Postal não encontrada.";
        } else {
            // ✅ Redireciona para GET com os parâmetros corretos
            header("Location: ?caixa_postal=" . urlencode($caixa) . "&aura=" . $aura);
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>FarolQR - Recarga Aura</title>
  <style>
    /* estilos iguais ao seu */
  </style>
</head>
<body>
   <style>body {
  margin: 0;
  padding: 0;
  font-family: 'Montserrat', sans-serif;
  background: linear-gradient(135deg, #880e4f, #ad1457, #ff006e); /* degradê rosa */
  color: #f0f0f0;
}

header {
  background: linear-gradient(90deg, #c2185b, #d81b60, #ff006e); /* rosa intenso */
  padding: 20px;
  text-align: center;
  color: #ffd700;
  font-size: 1.6em;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 2px;
  animation: slideDown 1.2s ease-in-out;
}

.container {
  max-width: 1000px;
  margin: 40px auto;
  padding: 20px;
  animation: fadeInUp 1.5s ease-in-out;
}

.card {
  background: #fff;
  border-radius: 12px;
  padding: 30px;
  margin-bottom: 30px;
  color: #333;
  box-shadow: 0 4px 10px rgba(0,0,0,0.2);
  animation: fadeIn 2s ease-in-out;
}

.card h3 {
  margin-top: 0;
  color: #d81b60; /* título em rosa */
}

label {
  display: block;
  margin-top: 15px;
  font-weight: bold;
}

input[type=text], input[type=number] {
  width: 100%;
  padding: 10px;
  margin-top: 5px;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 1em;
  transition: 0.3s;
}

input[type=text]:focus, input[type=number]:focus {
  background-color: #fff;
  box-shadow: 0 0 10px #ec407a; /* brilho rosa */
}

.resumo {
  margin-top: 20px;
  padding: 15px;
  background: #f9f9f9;
  border: 1px solid #ddd;
  border-radius: 8px;
  color: #000;
}

.button {
  margin-top: 20px;
  padding: 12px 20px;
  width: 100%;
  background: linear-gradient(to bottom, #ff4081, #d81b60); /* rosa degradê */
  color: #fff;
  border: 2px solid #ffd700;
  border-radius: 8px;
  font-weight: bold;
  cursor: pointer;
  font-size: 1.1em;
  transition: transform 0.2s ease, background 0.3s ease;
}

.button:hover {
  transform: scale(1.05);
  background: linear-gradient(to bottom, #ec407a, #c2185b);
}

.button-nav {
  display: inline-block;
  margin: 10px;
  padding: 12px 20px;
  background: #d81b60;
  color: #fff;
  border-radius: 8px;
  font-weight: bold;
  cursor: pointer;
  font-size: 1em;
  text-decoration: none;
  transition: background 0.3s ease, transform 0.2s ease;
}

.button-nav:hover {
  background: #ad1457;
  transform: scale(1.05);
}

.erro {
  color: #ff1744;
  font-weight: bold;
  margin-bottom: 15px;
}

footer {
  background: #880e4f;
  color: #ffd700;
  text-align: center;
  padding: 30px 20px;
  border-top: 3px solid #ff006e;
  animation: fadeIn 2s ease-in-out;
}

.quick-buttons {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-top: 15px;
}

.button-small {
  flex: 1 1 100px;
  padding: 10px;
  background: #d81b60;
  color: #fff;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: bold;
  transition: background 0.3s ease, transform 0.2s ease;
}

.button-small:hover {
  background: #ad1457;
  transform: scale(1.05);
}

/* Animações */
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
@keyframes slideDown { from { opacity: 0; transform: translateY(-30px); } to { opacity: 1; transform: translateY(0); } }

/* Responsividade */
@media (max-width: 768px) {
  header {
    font-size: 1.2em;
    padding: 15px;
  }

  .container {
    margin: 20px auto;
    padding: 15px;
  }

  .card {
    padding: 20px;
  }

  .button, .button-nav {
    width: 100%;
    margin: 10px 0;
    text-align: center;
  }
}

@media (max-width: 480px) {
  body {
    font-size: 0.9em;
  }

  header {
    font-size: 1em;
  }

  .card h3 {
    font-size: 1.1em;
  }

  input[type=text], input[type=number] {
    font-size: 0.9em;
  }
}
</style>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">


<header>FarolQR - Recarga Aura</header>

<div class="container">
  <div class="card">
    <h3>💡 O que é Aura?</h3>
    <p>Aura é o crédito utilizado para serviços do FarolQR. Cada unidade de Aura equivale a <strong>R$ 1,00</strong>.</p>
  </div>
<script>
function setAura(valor) {
  const input = document.getElementById('valorAuraInput');
  const spanAura = document.getElementById('valorAura');
  const spanReais = document.getElementById('valorReais');

  input.value = valor;
  spanAura.textContent = valor;
  spanReais.textContent = (valor * 1).toFixed(2).replace('.', ',');
}
</script>

  <div class="card">
    <?php if ($erro): ?>
      <div class="erro"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <form method="POST">
      <label>📮 Caixa Postal:</label>
      <input type="text" name="caixa_postal" value="<?= htmlspecialchars($caixa) ?>" required />

      <label>💰 Quantidade de Aura:</label>
      <input type="number" id="valorAuraInput" name="aura" min="1" max="500000" step="1" value="<?= $aura ?: 30 ?>" />
<div class="quick-buttons">
  <button type="button" class="button-small" onclick="setAura(100)">100 Aura</button>
  <button type="button" class="button-small" onclick="setAura(300)">300 Aura</button>
  <button type="button" class="button-small" onclick="setAura(500)">500 Aura</button>
  <button type="button" class="button-small" onclick="setAura(1000)">1000 Aura</button>
  <button type="button" class="button-small" onclick="setAura(5000)">5000 Aura</button>
</div>

      <div class="resumo">
        <p>Você está adquirindo <span id="valorAura"><?= $aura ?: 30 ?></span> Aura</p>
        <p>Total a pagar: <span id="valorReais"><?= number_format(($aura ?: 30) * 1, 2, ',', '.') ?></span> R$</p>
      </div>

      <button type="submit" class="button">Continuar</button>
    </form>

    <?php if (!$erro && $caixa !== '' && $aura > 0): ?>
      <div class="option">
        <h3>Escolha o método de pagamento:</h3>
        <a href="../site2/pix_aura/gerar_pix.php?caixa_postal=<?= urlencode($caixa) ?>&aura=<?= $aura ?>">💳 Pagar com PIX</a>
        <a href="../site2/pix_aura/gerar_cartao.php?caixa_postal=<?= urlencode($caixa) ?>&aura=<?= $aura ?>">💳 Pagar com Cartão</a>
      </div>
    <?php endif; ?>
  </div>
</div>
  <div class="card">
  <a href="https://carlitoslocacoes.com/farolqr/identificacao_farolqr.php" class="button-nav" target="_blank">🌐 Identificação</a>
  <a href="https://carlitoslocacoes.com/farolqr/balance_transacao.php" class="button-nav" target="_blank">🌐 Banco</a>
</div>
</body>
</html>
