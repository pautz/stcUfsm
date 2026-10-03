<?php
session_start();
if (!isset($_SESSION["loggedin_odonto3"]) || $_SESSION["loggedin_odonto3"] !== true) {
    header("location: ../login/login_farolqr.php");
    exit;
}

$usuario = $_SESSION["username_odonto2"];
$mensagem = "";

$conn = new mysqli("localhost", "u839226731_farol", "Meta6595869!", "u839226731_farol");
if ($conn->connect_error) die("Erro na conexão: " . $conn->connect_error);
$conn->set_charset("utf8mb4");

// Buscar dados do usuário
$stmtUser = $conn->prepare("SELECT caixa_postal, saldo_total, assinante_opentowork 
                            FROM identificacao_odonto2 WHERE username = ? LIMIT 1");
$stmtUser->bind_param("s", $usuario);
$stmtUser->execute();
$resUser = $stmtUser->get_result();
$userData = $resUser->fetch_assoc();
$stmtUser->close();

if (!$userData) {
    header("Location: https://carlitoslocacoes.com/farolqr/identificacao_farolqr.php");
    exit;
}

$caixaUsuario = $userData["caixa_postal"] ?? null;
$saldoAura    = (int)($userData["saldo_total"] ?? 0);
$assinante    = (int)($userData["assinante_opentowork"] ?? 0);

// Processar clique de compra
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["comprar_assinatura"])) {
    if (empty($caixaUsuario)) {
        $mensagem = "⚠️ Você precisa cadastrar uma caixa postal para continuar.";
    } elseif ($saldoAura < 5) {
        $mensagem = "⚠️ Você precisa ter pelo menos 5 auras para liberar a assinatura. Atualmente você tem {$saldoAura}.";
    } elseif ($assinante === 1) {
        $mensagem = "✅ Você já é assinante OpenToWork!";
    } else {
        $stmtUpdate = $conn->prepare("UPDATE identificacao_odonto2 SET assinante_opentowork = 1 WHERE username = ?");
        $stmtUpdate->bind_param("s", $usuario);
        $stmtUpdate->execute();
        $stmtUpdate->close();
        $mensagem = "🎉 Parabéns! Você comprou a assinatura OpenToWork.";
        $assinante = 1;
    }
}
?>
<!DOCTYPE html>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Assinatura OpenToWork</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<style><style>
  body {
    font-family: Arial, sans-serif;
  }
.btn-comprar {
  display: inline-block;
  background: #28a745;
  color: #fff;
  padding: 15px 30px;
  border-radius: 6px;
  text-decoration: none;
  font-weight: bold;
  font-size: 1.2em;
  transition: background 0.3s ease;
}
.btn-comprar:hover {
  background: #218838;
}
  .card {
    max-width: 600px;
    margin: auto;
  }

  /* Ajustes para telas menores */
  @media (max-width: 768px) {
    h1 {
      font-size: 1.8rem;
    }
    .card-body p {
      font-size: 0.95rem;
    }
    .btn-lg {
      font-size: 1rem;
      padding: 10px 20px;
    }
  }

  @media (max-width: 480px) {
    h1 {
      font-size: 1.5rem;
    }
    .card {
      margin: 10px;
    }
    .card-body p {
      font-size: 0.9rem;
    }
    .btn-lg {
      width: 100%;
      font-size: 0.95rem;
    }
  }
</style>
</style>
<body class="bg-light">
  <div class="container py-5">
    <h1 class="text-center mb-4">Assinatura OpenToWork</h1>
    <div class="card shadow-sm">
      <div class="card-body text-center">
        <p><?= $mensagem ?></p>
        <p><strong>Usuário:</strong> <?= htmlspecialchars($usuario) ?></p>
        <p><strong>Caixa Postal:</strong> <?= htmlspecialchars($caixaUsuario ?? "Não cadastrada") ?></p>
        <p><strong>Saldo de Auras:</strong> <?= $saldoAura ?></p>
        <p><strong>Status:</strong> <?= $assinante === 1 ? "Assinante ativo" : "Não assinante" ?></p>

        <?php if ($assinante !== 1): ?>
          <p class="mt-3 text-primary">
            💡 Você precisa de <strong>5 auras</strong>.
          </p>
          <form method="post">
            <button type="submit" name="comprar_assinatura" class="btn btn-success btn-lg mt-3"
              <?= ($saldoAura < 5 || empty($caixaUsuario)) ? "disabled" : "" ?>>
              Assinar Assinatura OpenToWork
            </button>
            <a href="https://carlitoslocacoes.com/sys/index.php" class="btn-comprar" target="_blank">
  Comprar Aura
</a>
          </form>
          <?php if ($saldoAura < 5): ?>
            <p class="text-danger mt-2">⚠️ Você precisa de pelo menos 5 auras para liberar a  assinatura, lembrando que não gasta aura.</p>
          <?php endif; ?>
          <?php if (empty($caixaUsuario)): ?>
            <p class="text-danger mt-2">⚠️ Cadastre sua caixa postal para continuar.</p>
          <?php endif; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</body>
</html>
