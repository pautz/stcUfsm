<?php
session_start();

// Configurações do banco
$host = "127.0.0.1";
$user = "u839226731_farol";
$pass = "Meta6595869!";
$db   = "u839226731_farol";

$cx = new mysqli($host, $user, $pass, $db);
if ($cx->connect_error) {
    die("Falha na conexão: " . $cx->connect_error);
}
$cx->set_charset("utf8");

// Verifica login
if (!isset($_SESSION["loggedin_odonto2"]) || $_SESSION["loggedin_odonto2"] !== true) {
    header("location: https://carlitoslocacoes.com/login/login_farolqr.php");
    exit;
}
$username = $_SESSION["username_odonto2"];

$stmt = $cx->prepare("SELECT assinantenv3 FROM identificacao_odonto2 WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($assinantenv3);
$stmt->fetch();
$stmt->close();

// Confirma se é assinante v3
if ($assinantenv3 != 1) {
    header("location: ../");
    exit;
}


$eq_user = $_SESSION["username_odonto2"];

// Configuração da paginação
$registros_por_pagina = 10;
$pagina_atual = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$offset = ($pagina_atual - 1) * $registros_por_pagina;

// Captura hash da URL (GET)
$hash_filtro = isset($_GET['hash']) ? trim($_GET['hash']) : null;

// Conta registros
if ($hash_filtro) {
    $stmt = $cx->prepare("SELECT COUNT(*) FROM compras WHERE hash_comprovante = ?");
    $stmt->bind_param("s", $hash_filtro);
} else {
    $stmt = $cx->prepare("SELECT COUNT(*) FROM compras");
}

$stmt->execute();
$stmt->bind_result($total_registros);
$stmt->fetch();
$stmt->close();

$total_paginas = ceil($total_registros / $registros_por_pagina);

// Consulta principal
if ($hash_filtro) {
    $stmt = $cx->prepare("SELECT * FROM compras WHERE hash_comprovante = ? ORDER BY id DESC LIMIT ? OFFSET ?");
    $stmt->bind_param("sii", $hash_filtro, $registros_por_pagina, $offset);
} else {
    $stmt = $cx->prepare("SELECT * FROM compras ORDER BY id DESC LIMIT ? OFFSET ?");
    $stmt->bind_param("ii", $registros_por_pagina, $offset);
}

$stmt->execute();
$result = $stmt->get_result();
$compras = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Compras Realizadas</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<style>/* ======== ESTILO GERAL ======== */
body {
  margin: 0;
  font-family: 'Segoe UI', Arial, sans-serif;
  background: linear-gradient(135deg, #ff006e, #d81b60, #880e4f); /* fundo rosa */
  color: #fff;
}

/* ======== NAVBAR ======== */
nav {
  position: fixed;
  top: 0; /* fixo no topo */
  left: 0;
  width: 100%;
  backdrop-filter: blur(8px);
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 40px;
  padding: 15px;
  box-shadow: 0 2px 5px rgba(255, 64, 129, 0.4);
  z-index: 1000;
  background: rgba(255, 0, 110, 0.6); /* rosa translúcido */
}

nav a {
  text-decoration: none;
  color: #fff;
  font-weight: bold;
  transition: color 0.3s ease;
  padding: 8px 12px;
}

nav a:hover {
  color: #ffebee;
}

nav .brand img {
  height: 55px;
  transition: opacity 0.8s ease-in-out;
}

/* ======== CARROSSEL ======== */
.carousel {
  margin-top: 80px; /* espaço abaixo da navbar */
  position: relative;
  width: 100%;
  height: 100vh;
  overflow: hidden;
}

.carousel img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  position: absolute;
  top: 0;
  left: 0;
  opacity: 0;
  transition: opacity 1s ease-in-out;
}

.carousel img.active {
  opacity: 1;
}

/* ======== BOTÕES DO CARROSSEL ======== */
.carousel button {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  border: 1px solid rgba(255, 64, 129, 0.8);
  padding: 12px;
  cursor: pointer;
  border-radius: 50%;
  color: #fff;
  transition: background 0.3s ease, transform 0.3s ease;
  background: rgba(255, 0, 110, 0.6);
}

.carousel button:hover {
  background: rgba(255, 64, 129, 0.9);
  transform: scale(1.1);
}

.prev { left: 20px; }
.next { right: 20px; }

/* ======== FOOTER ======== */
footer {
  backdrop-filter: blur(8px);
  text-align: center;
  padding: 20px;
  box-shadow: 0 -2px 5px rgba(255, 64, 129, 0.4);
  color: #fff;
  background: rgba(255, 0, 110, 0.6);
}

/* ======== RESPONSIVIDADE ======== */
@media (max-width: 768px) {
  nav {
    flex-direction: column;
    gap: 15px;
    padding: 10px;
  }

  nav .brand img {
    height: 45px;
  }

  .carousel {
    height: 60vh; /* menor altura em telas pequenas */
  }

  .carousel button {
    padding: 10px;
  }
}
</style>
<body class="bg-light">
<div class="container mt-5">
  <h3 class="text-center mb-4">Usuário logado: <?= htmlspecialchars($eq_user) ?></h3>
  <h2 class="text-center mb-4">🛒 Compras Realizadas</h2>

  <!-- Formulário de busca por hash -->
  <form method="GET" class="mb-4">
    <div class="input-group">
      <input type="text" name="hash" class="form-control" placeholder="Digite a hash do comprovante" value="<?= htmlspecialchars($hash_filtro) ?>">
      <button class="btn btn-primary" type="submit">Filtrar</button>
      <a href="compras.php" class="btn btn-secondary">Limpar Filtro</a>
    </div>
  </form>

  <?php if (count($compras) > 0): ?>
    <form method="POST" action="marcar_ok.php">
      <table class="table table-bordered">
        <thead class="table-light">
          <tr>
            <th>Marcar</th>
            <th>ID</th>
            <th>Consórcio</th>
            <th>Valor Total</th>
            <th>Entrada</th>
            <th>Hash</th>
            <th>Data</th>
            <th>Quantidade</th>
            <th>Rua</th>
            <th>Bairro</th>
            <th>Número</th>
            <th>Complemento</th>
            <th>Cidade</th>
            <th>Estado</th>
            <th>CEP</th>
            <th>Telefone</th>
            <th>Usuario</th>
            <th>Nickname Tibia</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($compras as $c): ?>
            <tr>
              <td>
                <input type="checkbox" name="entregues[]" value="<?= htmlspecialchars($c['hash_comprovante']) ?>"
                       <?= ($c['status_pagamento'] === 'entregue') ? 'checked' : '' ?>>
              </td>
              <td><?= $c['id'] ?></td>
              <td><?= $c['consorcio_id'] ?></td>
              <td>R$ <?= number_format($c['valor_total'], 2, ',', '.') ?></td>
              <td>R$ <?= number_format($c['entrada'], 2, ',', '.') ?></td>
              <td><?= htmlspecialchars($c['hash_comprovante']) ?></td>
              <td><?= date('d/m/Y H:i', strtotime($c['data_compra'])) ?></td>
              <td><?= htmlspecialchars($c['quantidade']) ?></td>
              <td><?= htmlspecialchars($c['rua']) ?></td>
              <td><?= htmlspecialchars($c['bairro']) ?></td>
               <td><?= htmlspecialchars($c['numero']) ?></td>
               <td><?= htmlspecialchars($c['complemento']) ?></td>
              <td><?= htmlspecialchars($c['cidade']) ?></td>
              <td><?= htmlspecialchars($c['estado']) ?></td>
              <td><?= htmlspecialchars($c['cep']) ?></td>
              <td><?= htmlspecialchars($c['telefone']) ?></td>
              <td><?= htmlspecialchars($c['usuario']) ?></td>
              <td><?= htmlspecialchars($c['nicknamecharacter']) ?></td>
              <td><?= htmlspecialchars($c['status_pagamento'] ?? 'pendente') ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <!-- Palavra-chave obrigatória -->
      <div class="mb-3">
        <label for="keyword" class="form-label">Digite a palavra-chave para confirmar entrega:</label>
        <input type="text" name="keyword" id="keyword" class="form-control" required>
        <small class="text-muted">Exemplo: digite <strong>locotrator</strong> para confirmar.</small>
      </div>

      <div class="text-center">
        <button type="submit" class="btn btn-success">Marcar como Entregue</button>
      </div>
    </form>

    <!-- Paginação -->
   <form method="get" class="mt-3" style="text-align:center;">
  <label for="pagina">Página:</label>
  <select name="pagina" id="pagina" onchange="this.form.submit()" class="form-select" style="max-width:200px;display:inline-block;">
    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
      <option value="<?= $i ?><?= $hash_filtro ? '&hash='.urlencode($hash_filtro) : '' ?>"
        <?= ($i == $pagina_atual) ? 'selected' : '' ?>>
        <?= $i ?> de <?= $total_paginas ?>
      </option>
    <?php endfor; ?>
  </select>
</form>


  <?php else: ?>
    <div class="alert alert-info text-center">Nenhuma compra encontrada.</div>
  <?php endif; ?>
</div>
</body>
</html>
