<?php
session_start();

$usuarioLogado = $_SESSION["username_loggedin"];
$conn = new mysqli("127.0.0.1", "root", "", "u839226731_farol");
if ($conn->connect_error) die("Erro na conexão: " . $conn->connect_error);
$conn->set_charset("utf8mb4");

function formatarData($dt) {
  return date("d/m/Y H:i:s", strtotime($dt));
}

// 🔍 Filtro por ID via URL
$buscaId = intval($_GET["buscaId"] ?? 0);

// 📊 Transações
if ($buscaId > 0) {
  // Busca apenas a transação com o ID informado
  $stmtTrans = $conn->prepare("
    SELECT t.id, t.remetente, t.destinatario, t.valor, t.caixa_origem, t.caixa_destino, t.data_transacao,
           i.caixa_postal AS caixa_origem_real
    FROM transacoes_aura t
    LEFT JOIN identificacao_odonto2 i ON t.remetente = i.username
    WHERE t.id = ?
  ");
  $stmtTrans->bind_param("i", $buscaId);
  $stmtTrans->execute();
  $resTransacoes = $stmtTrans->get_result();
} else {
  $resTransacoes = false;
}

// 🛒 Pedidos
if ($buscaId > 0) {
  // Busca apenas o pedido com o ID informado
  $stmtPedidos = $conn->prepare("
    SELECT id, titulo, valor_aura, nome_comprador, data_hora, entregue,
           estado, cidade, bairro, rua, cep, contato, cpf
    FROM pedidos_livros
    WHERE id = ?
  ");
  $stmtPedidos->bind_param("i", $buscaId);
  $stmtPedidos->execute();
  $resPedidos = $stmtPedidos->get_result();
} else {
  $resPedidos = false;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>AuraScan COPs Explorer 🔍</title>
  <style>
    body { font-family: sans-serif; background: #eef; padding: 30px; }
    .container { max-width: 960px; margin: auto; background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 0 12px #ccc; }
    h1, h2 { text-align: center; color: #5c3dc4; margin-bottom: 10px; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 13px; }
    th, td { padding: 8px; border: 1px solid #ccc; text-align: left; }
    th { background: #f0f0ff; color: #5c3dc4; }
    .valido { color: green; font-weight: bold; }
    .divergente { color: red; font-weight: bold; }
  </style>
</head>
<body>
  <div class="container">
    <h1>AuraScan 🚀 Visualizador COPs</h1>

    <?php if ($buscaId > 0): ?>
      <h2>📊 Transação do ID <?= $buscaId ?></h2>
      <?php if ($resTransacoes && $resTransacoes->num_rows > 0): ?>
      <table>
        <tr>
          <th>ID</th><th>Remetente</th><th>Destinatário</th><th>Valor</th>
          <th>Caixa Origem Real</th><th>Caixa Origem (Registrada)</th><th>Caixa Destino</th><th>Data</th><th>🔒 Verificação</th>
        </tr>
        <?php while ($t = $resTransacoes->fetch_assoc()): ?>
        <tr>
          <td><?= $t["id"] ?></td>
          <td><?= $t["remetente"] ?></td>
          <td><?= $t["destinatario"] ?></td>
          <td><?= $t["valor"] ?> aura</td>
          <td><?= $t["caixa_origem_real"] ?? "—" ?></td>
          <td><?= $t["caixa_origem"] ?></td>
          <td><?= $t["caixa_destino"] ?></td>
          <td><?= formatarData($t["data_transacao"]) ?></td>
          <td class="<?= ($t["caixa_origem_real"] === $t["caixa_origem"]) ? 'valido' : 'divergente' ?>">
            <?= ($t["caixa_origem_real"] === $t["caixa_origem"]) ? "✅ Válido" : "⚠️ Divergente" ?>
          </td>
        </tr>
        <?php endwhile; ?>
      </table>
      <?php else: ?>
        <p style="text-align:center; color:red;">⚠️ Nenhuma transação encontrada com o ID <?= $buscaId ?>.</p>
      <?php endif; ?>

      <h2>🛒 Pedido do ID <?= $buscaId ?></h2>
      <?php if ($resPedidos && $resPedidos->num_rows > 0): ?>
      <table>
        <tr>
          <th>ID</th><th>Livro</th><th>Valor</th><th>Comprador</th><th>Data</th><th>Entregue</th>
          <th>Estado</th><th>Cidade</th><th>Bairro</th><th>Rua</th><th>CEP</th><th>Contato</th><th>CPF</th>
        </tr>
        <?php while ($c = $resPedidos->fetch_assoc()): ?>
        <tr>
          <td><?= $c["id"] ?></td>
          <td><?= $c["titulo"] ?></td>
          <td><?= $c["valor_aura"] ?> aura</td>
          <td><?= $c["nome_comprador"] ?></td>
          <td><?= formatarData($c["data_hora"]) ?></td>
          <td><?= $c["entregue"] ? "✅ Sim" : "⏳ Não" ?></td>
          <td><?= $c["estado"] ?></td>
          <td><?= $c["cidade"] ?></td>
          <td><?= $c["bairro"] ?></td>
          <td><?= $c["rua"] ?></td>
          <td><?= $c["cep"] ?></td>
          <td><?= $c["contato"] ?></td>
          <td><?= $c["cpf"] ?></td>
        </tr>
        <?php endwhile; ?>
      </table>
      <?php else: ?>
        <p style="text-align:center; color:red;">⚠️ Nenhum pedido encontrado com o ID <?= $buscaId ?>.</p>
      <?php endif; ?>
    <?php else: ?>
      <p style="text-align:center;">Insira um ID na URL, exemplo: <strong>?buscaId=10</strong></p>
    <?php endif; ?>
  </div>
</body>
</html>
