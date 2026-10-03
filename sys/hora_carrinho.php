<?php
session_start();

// Conexão com banco
$conn = new mysqli("127.0.0.1", "u839226731_farol", "Meta6595869!", "u839226731_farol");
if ($conn->connect_error) { die("Conexão falhou: " . $conn->connect_error); }
$conn->set_charset("utf8");

// ----------------------
// SEO do produto individual
// ----------------------
$idProduto = isset($_GET['id']) ? intval($_GET['id']) : 0;
$consulta = "Produto";
$valor = 0;
$imagem = "";

if ($idProduto > 0) {
    $sql = "SELECT consulta, valor_total, imagem 
            FROM consorcio_cadastro 
            WHERE id = ? AND aprovacao = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idProduto);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $consulta = $row['consulta'];
        $valor    = $row['valor_total'];
        $imagem   = $row['imagem'];
    }
    $stmt->close();
}

$titleSEO = $consulta . " - AURA " . number_format($valor, 2, ',', '.');
$descSEO  = "Confira detalhes: " . $consulta . " por apenas AURA " . number_format($valor, 2, ',', '.');
$imgSEO   = !empty($imagem) 
            ? "https://carlitoslocacoes.com/todofarol/site3/cadastro_produto/" . $imagem 
            : "https://carlitoslocacoes.com/todofarol/logo.png";

// ----------------------
// Endereço salvo em cookie
// ----------------------
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET['cep'])) {
    $endereco = [
        'rua'        => trim($_GET['rua'] ?? ''),
        'numero'     => trim($_GET['numero'] ?? ''),
        'bairro'     => trim($_GET['bairro'] ?? ''),
        'cidade'     => trim($_GET['cidade'] ?? ''),
        'estado'     => trim($_GET['estado'] ?? ''),
        'cep'        => trim($_GET['cep'] ?? ''),
        'telefone'   => trim($_GET['telefone'] ?? ''),
        'complemento'=> trim($_GET['complemento'] ?? '')
    ];
    setcookie('endereco', json_encode($endereco), time() + (7*24*60*60), "/");
}

$enderecoCliente = !empty($_COOKIE['endereco']) ? json_decode($_COOKIE['endereco'], true) : [];

// ----------------------
// Carrinho via cookie
// ----------------------
$carrinhoCliente = !empty($_COOKIE['carrinho']) ? json_decode($_COOKIE['carrinho'], true) : [];
if (!is_array($carrinhoCliente)) $carrinhoCliente = [];

// ----------------------
// Filtros de pesquisa
// ----------------------
$search_consulta = $_GET['search_consulta'] ?? '';
if (isset($_GET['search_cidade'])) $_SESSION['search_cidade'] = $_GET['search_cidade'];
if (isset($_GET['search_estado'])) $_SESSION['search_estado'] = $_GET['search_estado'];
$search_lc = $_GET['search_lc'] ?? '';
$search_cidade = $_SESSION['search_cidade'] ?? '';
$search_estado = $_SESSION['search_estado'] ?? '';

// Depois de definir $usuario
// Recupera usuário da sessão
// Recupera usuário da sessão
$usuario = $_SESSION["username_odonto2"] ?? null;
$saldoAura = null;

if ($usuario) {
    $stmtSaldo = $conn->prepare("SELECT saldo_total FROM identificacao_odonto2 WHERE username = ?");
    $stmtSaldo->bind_param("s", $usuario);
    $stmtSaldo->execute();
    $resSaldo = $stmtSaldo->get_result();
    if ($rowSaldo = $resSaldo->fetch_assoc()) {
        $saldoAura = $rowSaldo['saldo_total'];
    } else {
        // Debug: não encontrou saldo
        error_log("Usuário $usuario não tem saldo_total registrado.");
    }
    $stmtSaldo->close();
}


// ----------------------
// Listagem de consórcios
// ----------------------
$limite = 20;
$pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$offset = ($pagina - 1) * $limite;

if ($idProduto > 0) {
    $sql = "SELECT id, consulta, valor_total, cidade, estado, imagem, eq_user, entrada, estoque 
            FROM consorcio_cadastro 
            WHERE aprovacao = 1 AND id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idProduto);
    $stmt->execute();
    $result = $stmt->get_result();
    $totalPaginas = 1;
} else {
    $sql = "SELECT id, consulta, valor_total, cidade, estado, imagem, eq_user, entrada, estoque 
            FROM consorcio_cadastro WHERE aprovacao = 1";
    $params = [];
    $types  = "";

    if ($search_consulta !== '') {
        $likeParam = "%" . str_replace(" ", "%", $search_consulta) . "%";
        $sql .= " AND LOWER(consulta) LIKE LOWER(?)";
        $params[] = $likeParam;
        $types .= "s";
    }
    if ($search_cidade !== '') {
        $sql .= " AND cidade = ?";
        $params[] = $search_cidade;
        $types .= "s";
    }
    if ($search_estado !== '') {
        $sql .= " AND estado = ?";
        $params[] = $search_estado;
        $types .= "s";
    }
    if ($search_lc !== '') {
        $sql .= " AND lc LIKE ?";
        $params[] = "%".$search_lc."%";
        $types .= "s";
    }

    $sql .= " ORDER BY id DESC LIMIT " . intval($limite) . " OFFSET " . intval($offset);
    $stmt = $conn->prepare($sql);
    if (!empty($params)) $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    // contagem total
    $sqlCount = "SELECT COUNT(*) as total FROM consorcio_cadastro WHERE aprovacao = 1";
    if ($search_cidade !== '') { $sqlCount .= " AND cidade = '".$conn->real_escape_string($search_cidade)."'"; }
    if ($search_estado !== '') { $sqlCount .= " AND estado = '".$conn->real_escape_string($search_estado)."'"; }
    $resCount = $conn->query($sqlCount);
    $totalConsorcios = $resCount->fetch_assoc()['total'];
    $totalPaginas = ceil($totalConsorcios / $limite);
}

// ----------------------
// Calcula carrinho
// ----------------------
$subtotal = 0;
$totalFrete = 0;
$itensCarrinho = [];
if (!empty($carrinhoCliente)) {
    foreach ($carrinhoCliente as $idConsorcio => $dados) {
        $sqlConsorcio = "SELECT id, consulta, valor_total, entrada, estoque, imagem 
                         FROM consorcio_cadastro WHERE id = ?";
        $stmt2 = $conn->prepare($sqlConsorcio);
        $stmt2->bind_param("i", $idConsorcio);
        $stmt2->execute();
        $res2 = $stmt2->get_result();
        if ($row = $res2->fetch_assoc()) {
            $row['qtd']   = (int)$dados['qtd'];
            $row['frete'] = $dados['frete'] ?? 0;
            $itensCarrinho[] = $row;
            $subtotal += $row['valor_total'] * $row['qtd'];
            $totalFrete += $row['frete'];
        }
        $stmt2->close();
    }
}
$totalComFrete = $subtotal + $totalFrete;

// ----------------------
// Paginação do carrinho
// ----------------------
$limiteCarrinho = 10;
$paginaCarrinho = isset($_GET['pagina_carrinho']) ? max(1, intval($_GET['pagina_carrinho'])) : 1;
$offsetCarrinho = ($paginaCarrinho - 1) * $limiteCarrinho;
$totalItensCarrinho = count($itensCarrinho);
$totalPaginasCarrinho = $totalItensCarrinho > 0 ? ceil($totalItensCarrinho / $limiteCarrinho) : 1;
$itensPaginaCarrinho = array_slice($itensCarrinho, $offsetCarrinho, $limiteCarrinho);

// ----------------------
// Salva carrinho no banco com token
// ----------------------
$token = bin2hex(random_bytes(16));
$sql = "INSERT INTO carrinhos (token, dados, criado_em) VALUES (?, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $token, json_encode($carrinhoCliente));
$stmt->execute();
$stmt->close();

// ----------------------
// Gera link mágico
// ----------------------
$linkMagico = "https://carlitoslocacoes.com/sys/carrinho_view.php?token=" . $token;


// ----------------------
// Exibe produtos do carrinho
// ----------------------

$conn->close();
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($titleSEO) ?></title>
  <meta name="description" content="<?= htmlspecialchars($descSEO) ?>">

  <!-- Open Graph para redes sociais -->
  <meta property="og:title" content="<?= htmlspecialchars($titleSEO) ?>">
  <meta property="og:description" content="<?= htmlspecialchars($descSEO) ?>">
  <meta property="og:image" content="<?= htmlspecialchars($imgSEO) ?>">
  <meta property="og:url" content="https://carlitoslocacoes.com/todofarol/site2/nossasmaquinas/hora_carrinho.php?id=<?= $idProduto ?>">
  <meta property="og:type" content="product">
</head>

<body>
  <style>/* Fonte e fundo */
body {
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(to bottom right, #880e4f, #d81b60, #ff006e); /* degradê rosa */
  margin: 0;
  padding-top: 80px;
  color: #333;
}

/* Barra de saldo */
.saldo-bar {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  background: #d81b60; /* rosa forte */
  color: #fff;
  padding: 12px;
  text-align: center;
  font-weight: bold;
  z-index: 1000;
  animation: slideDown 1.2s ease-in-out;
}
.saldo-bar-alert {
  background: #fff3f3;
  color: #e91e63;
  font-size: 18px;
}
.login-link {
  color: #ff4081;
  text-decoration: none;
  font-weight: bold;
}

/* Título */
h2 {
  text-align: center;
  color: #c2185b;
  margin-bottom: 20px;
  font-size: 28px;
  animation: fadeIn 2s ease-in-out;
}

/* Botão carrinho */
#toggleCarrinho {
    margin-top: 100px;
  position: fixed;
  top: 20px;
  right: 20px;
  background: #e91e63;
  color: #fff;
  padding: 10px 20px;
  border-radius: 30px;
  border: none;
  cursor: pointer;
  z-index: 1000;
  font-weight: bold;
  transition: background 0.3s ease, transform 0.2s ease;
}
#toggleCarrinho:hover {
  background: #ad1457;
  transform: scale(1.05);
}

/* Container dos formulários */
.filtros {
  display: flex;
  justify-content: center;
  gap: 30px;
  align-items: flex-start;
  flex-wrap: wrap;
  margin: 20px auto;
  max-width: 1200px;
}

/* Formulários */
.form-pesquisa, .form-endereco {
  flex: 1;
  min-width: 350px;
  background: #fff;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  animation: fadeInUp 1.5s ease-in-out;
}
.form-pesquisa input, .form-endereco input {
  font-size: 16px;
  padding: 10px;
  border-radius: 8px;
  border: 1px solid #ccc;
  margin-bottom: 12px;
  width: 100%;
}
.form-pesquisa button, .form-endereco button {
  background: #d81b60;
  color: #fff;
  font-weight: bold;
  cursor: pointer;
  border: none;
  padding: 12px 25px;
  border-radius: 25px;
  font-size: 16px;
  transition: background 0.3s ease, transform 0.2s ease;
}
.form-pesquisa button:hover, .form-endereco button:hover {
  background: #ad1457;
  transform: scale(1.05);
}
.form-endereco {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 15px;
}
.form-endereco-btn {
  grid-column: 1 / span 2;
  text-align: center;
}

/* Container geral dos cards */
.container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 30px;
  padding: 40px;
  max-width: 1400px;
  margin: 0 auto;
}

/* Card */
.card {
  background: #fff;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
  display: flex;
  flex-direction: column;
  align-items: center;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  animation: fadeIn 2s ease-in-out;
}
.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 6px 18px rgba(0,0,0,0.12);
}

/* Imagem */
.card-img img {
  width: 100%;
  height: auto;
  border-radius: 12px;
  object-fit: cover;
  margin-bottom: 20px;
}

/* Texto */
.card-info {
  width: 100%;
  text-align: center;
  flex-grow: 1;
}
.card-info h3 {
  color: #d81b60;
  font-size: 20px;
  margin-bottom: 12px;
}
.card-info p {
  margin: 6px 0;
  font-size: 16px;
  color: #555;
}

/* Botões */
.btn-add {
  background: #e91e63;
  color: #fff;
  padding: 12px 24px;
  border-radius: 30px;
  border: none;
  cursor: pointer;
  font-weight: bold;
  margin-top: 20px;
  transition: background 0.3s ease, transform 0.2s ease;
}
.btn-add:hover {
  background: #ad1457;
  transform: scale(1.05);
}
.btn-home {
  background: #d81b60;
  color: #fff;
  padding: 12px 24px;
  border-radius: 30px;
  border: none;
  cursor: pointer;
  font-weight: bold;
  margin-top: 20px;
}

/* Carrinho lateral */
.carrinho {
  position: fixed;
  top: 0;
  right: -350px;
  width: 350px;
  height: 100%;
  background: #fff;
  border-left: 2px solid #e91e63;
  box-shadow: -4px 0 12px rgba(0,0,0,0.2);
  padding: 20px;
  transition: right 0.3s ease;
  z-index: 999;
}
.carrinho h3 {
  margin-top: 0;
  color: #d81b60;
  text-align: center;
}
.carrinho ul {
  list-style: none;
  padding: 0;
}
.carrinho li {
  margin: 8px 0;
  font-size: 14px;
}
.remove-item {
  color: #e91e63;
  font-weight: bold;
  text-decoration: none;
}
.carrinho-btns {
  display: flex;
  justify-content: center;
  gap: 10px;
  margin-top: 20px;
}
.btn-finalizar, .btn-limpar {
  display: inline-block;
  padding: 10px 20px;
  border-radius: 30px;
  text-decoration: none;
  font-weight: bold;
}
.btn-finalizar {
  background: #d81b60;
  color: #fff;
}
.btn-finalizar:hover {
  background: #ad1457;
}
.btn-limpar {
  background: #ec407a;
  color: #fff;
}
.btn-limpar:hover {
  background: #c2185b;
}

/* Paginação */
.pagination {
  text-align: center;
  margin: 40px 0;
}
.pagination a {
  background-color: #e91e63;
  color: white;
  padding: 10px 18px;
  margin: 5px;
  border-radius: 30px;
  text-decoration: none;
  font-weight: bold;
  transition: background 0.3s ease;
}
.pagination a:hover {
  background-color: #ad1457;
}

/* Animações */
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
@keyframes slideDown { from { opacity: 0; transform: translateY(-30px); } to { opacity: 1; transform: translateY(0); } }

/* Responsividade */
@media (max-width: 768px) {
  .filtros {
    flex-direction: column;
    gap: 20px;
  }
  .form-endereco {
    grid-template-columns: 1fr;
  }
  #toggleCarrinho {
    top: 70px;
    right: 10px;
    padding: 8px 16px;
    font-size: 14px;
  }
  .carrinho {
    width: 100%;
    right: -100%;
  }
  .card {
    min-height: auto;
  }
}
.navbar1{
    margin-top: 100px;
}
</style>
<div class="navbar1">
<a href="https://carlitoslocacoes.com/" class="btn-home">Início</a> 
<a href="https://carlitoslocacoes.com/login/login_farolqr.php" class="btn-home">Entrar</a> 
</div>

<button id="toggleCarrinho">
  🛒 Carrinho (<?= array_sum(array_column($carrinhoCliente, 'qtd')) ?>)
</button>


<!-- Formulário de filtros -->
<div class="filtros">

  <!-- Formulário de pesquisa -->
  <form method="GET" class="form-pesquisa">
  <div>
    <input type="text" name="search_consulta" placeholder="Digite para pesquisar..." 
           value="<?= htmlspecialchars($_GET['search_consulta'] ?? '') ?>">
    <button type="submit">🔍 Pesquisar</button>
    <a href="?"><button type="button">❌ Limpar Pesquisa</button></a>
  </div>
</form>


  <!-- Formulário de endereço -->
 

</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const toggleBtn = document.getElementById("toggleCarrinho");
  const carrinho = document.getElementById("carrinho");
  toggleBtn.addEventListener("click", () => {
    carrinho.style.right = (carrinho.style.right === "0px") ? "-350px" : "0px";
  });
});
</script>

<div class="container">
  <?php if ($result->num_rows > 0): ?>
    <?php foreach ($result->fetch_all(MYSQLI_ASSOC) as $row): ?>
      <?php if ($row["estoque"] > 0): ?>
       <div class="card">
  <div class="card-content">
    <?php if (!empty($row["imagem"])): ?>
      <div class="card-img">
        <img src="<?= htmlspecialchars('../site3/cadastro_produto/' . $row["imagem"]) ?>" alt="Imagem do Bem">
      </div>
    <?php endif; ?>

    <div class="card-info">
     <h3 style="font-size: 32px;"><?= htmlspecialchars_decode($row["consulta"]) ?></h3>
      <p>
        <strong>Preço:</strong> 
        <span style="font-size: 32px; color: orange; font-weight: bold;">
          AURA <?= number_format($row["valor_total"], 2, ',', '.') ?>
        </span>
      </p>
<p> <a href="/todofarol/site2/nossasmaquinas/hora_carrinho.php?id=<?= $row['id'] ?>" style="color:#1e90ff; font-weight:bold; text-decoration:none;"> 🔗 Acessar Produto </a> </p>
      <form method="POST" action="carrinho.php">
  <input type="hidden" name="id" value="<?= $row['id'] ?>">
  <input type="hidden" name="search_cidade" value="<?= htmlspecialchars($cidade) ?>">
  <input type="hidden" name="search_estado" value="<?= htmlspecialchars($estado) ?>">

  <label for="qtd_<?= $row['id'] ?>">Quantidade:</label>
  <input type="number" id="qtd_<?= $row['id'] ?>" name="qtd" 
         min="1" max="<?= $row['estoque'] ?>" value="1" style="width:60px;">

  <button type="submit" class="btn-add">Adicionar ao Carrinho</button>
</form>

    </div>
  </div>
</div>

      <?php endif; ?>
    <?php endforeach; ?>
  <?php else: ?>
    <p class="no-results">Nenhum consórcio encontrado.</p>
  <?php endif; ?>
</div>

<div class="pagination">
  <form method="get" style="display:inline;">
    <label for="pagina">Página:</label>
    <select name="pagina" id="pagina" onchange="this.form.submit()">
      <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
        <option value="<?= $i ?>" <?= $i == $pagina ? 'selected' : '' ?>>
          <?= $i ?> de <?= $totalPaginas ?>
        </option>
      <?php endfor; ?>
    </select>
  </form>
</div>


<!-- Carrinho lateral -->
<div id="carrinho" class="carrinho">
  <h3>🛒 Meu Carrinho</h3>
  <?php if (!empty($itensPaginaCarrinho)): ?>
    <ul>
      <?php foreach ($itensPaginaCarrinho as $item): ?>
        <li>
          <?= htmlspecialchars($item['consulta']) ?> (<?= $item['qtd'] ?>x) 
          - AURA <?= number_format($item['valor_total'] * $item['qtd'], 2, ',', '.') ?>
          <br><small>Frete: AURA <?= number_format($item['frete'], 2, ',', '.') ?></small>
          <a href="remover.php?id=<?= $item['id'] ?>" class="remove-item">X</a>
        </li>
      <?php endforeach; ?>
    </ul>

    <div class="paginacao-carrinho">
  <form method="get" style="display:inline;">
    <label for="pagina_carrinho">Página:</label>
    <select name="pagina_carrinho" id="pagina_carrinho" onchange="this.form.submit()">
      <?php for ($i = 1; $i <= $totalPaginasCarrinho; $i++): ?>
        <option value="<?= $i ?>" <?= $i == $paginaCarrinho ? 'selected' : '' ?>>
          <?= $i ?> de <?= $totalPaginasCarrinho ?>
        </option>
      <?php endfor; ?>
    </select>
  </form>
</div>


    
<p><strong>Total Geral:</strong> AURA <?= number_format($totalComFrete, 2, ',', '.') ?></p>

<div class="carrinho-btns">

  
  <!-- Botão para copiar link -->
  <button onclick="copiarLinkCarrinho()">Copiar Link do Carrinho</button>
  
  <a href="<?= $linkMagico ?>" class="btn-finalizar">Avançar para Endereços</a>
  <a href="limpar.php" class="btn-limpar">Limpar Carrinho</a>
</div>

<script>
function copiarLinkCarrinho() {
  const link = "<?= $linkMagico ?>";
  navigator.clipboard.writeText(link).then(() => {
    alert("Link do carrinho copiado!");
  }).catch(err => {
    alert("Não foi possível copiar o link: " + err);
  });
}
</script>

  <?php else: ?>
    <p class="no-results">Carrinho vazio.</p>
  <?php endif; ?>
</div>

</body>

</html>
