<?php
session_start();

// Conexão com banco
$servername = "127.0.0.1";
$username   = "u839226731_farol";
$password   = "Meta6595869!";
$dbname     = "u839226731_farol";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Conexão falhou: " . $conn->connect_error); }
$conn->set_charset("utf8");

// Usuário da sessão
$usuario = $_SESSION['username_odonto2'] ?? "";

// Verifica se possui caixa_postal
$sqlCaixa = "SELECT caixa_postal FROM identificacao_odonto2 WHERE username = ?";
$stmtCaixa = $conn->prepare($sqlCaixa);
$stmtCaixa->bind_param("s", $usuario);
$stmtCaixa->execute();
$resCaixa = $stmtCaixa->get_result();
$dadosCaixa = $resCaixa->fetch_assoc();
$stmtCaixa->close();

if (!$dadosCaixa || empty($dadosCaixa['caixa_postal'])) {
    header("Location: https://carlitoslocacoes.com/farolqr/identificacao_farolqr.php");
    exit;
}

// Recebe token da URL
$token = $_GET['token'] ?? '';

$sql = "SELECT dados, criado_em 
        FROM carrinhos 
        WHERE token = ? 
        AND criado_em > NOW() - INTERVAL 1 DAY";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $token);
$stmt->execute();
$res = $stmt->get_result();
?>

<!DOCTYPE html>
<style>body {
  font-family: 'Tahoma', sans-serif;
  background: linear-gradient(135deg, #880e4f, #d81b60, #ff006e); /* degradê rosa */
  color: #f0f0f0;
  line-height: 1.8;
  padding-top: 70px;
  box-sizing: border-box;
}

h2 {
  text-align: center;
  color: #e91e63; /* título rosa */
  animation: fadeIn 1.5s ease-in-out;
}

table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 15px;
  animation: fadeInUp 1.5s ease-in-out;
}

table th, table td {
  padding: 12px;
  border: 1px solid #ddd;
  text-align: center;
}

table th {
  background: #d81b60; /* cabeçalho rosa */
  color: #fff;
}

table img {
  max-width: 80px;
  height: auto;
  border-radius: 8px;
  box-shadow: 0 0 8px rgba(233,30,99,0.5);
}

.error {
  color: #ff1744;
  text-align: center;
  font-weight: bold;
}

.form-endereco {
  margin-top: 20px;
}

.form-endereco input {
  width: 100%;
  padding: 8px;
  margin: 5px 0;
  border: 1px solid #d81b60;
  border-radius: 5px;
  background: #fff;
  color: #000;
  transition: box-shadow 0.3s ease;
}

.form-endereco input:focus {
  box-shadow: 0 0 10px #ec407a;
}

/* Placeholder em preto */
.form-endereco input::placeholder {
  color: #000 !important;
  opacity: 1 !important;
}

.btn {
  display: inline-block;
  padding: 10px 15px;
  margin: 5px;
  background: linear-gradient(to right, #e91e63, #ad1457);
  color: #fff;
  text-decoration: none;
  border-radius: 5px;
  font-weight: bold;
  transition: transform 0.2s ease, background 0.3s ease;
}

.btn:hover {
  transform: scale(1.05);
  background: linear-gradient(to right, #ec407a, #c2185b);
}

/* Responsividade */
@media (max-width: 768px) {
  table, thead, tbody, th, td, tr {
    display: block;
  }

  thead tr {
    display: none;
  }

  tr {
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 10px;
    background: rgba(255,255,255,0.05);
  }

  td {
    text-align: left;
    padding-left: 50%;
    position: relative;
    border: none;
    border-bottom: 1px solid #eee;
  }

  td::before {
    position: absolute;
    left: 10px;
    top: 10px;
    white-space: nowrap;
    font-weight: bold;
    color: #d81b60;
    content: attr(data-label);
  }

  td:last-child {
    border-bottom: none;
  }
}

/* Animações */
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
</style>
</head>
<body>
<div class="container">
<?php
if ($row = $res->fetch_assoc()) {
    $carrinho = json_decode($row['dados'], true);
    echo "<h2>Seu Carrinho</h2>";
    if (!empty($carrinho)) {

        $limiteCarrinho = 5;
        $paginaCarrinho = isset($_GET['pagina_carrinho']) ? max(1, intval($_GET['pagina_carrinho'])) : 1;
        $offsetCarrinho = ($paginaCarrinho - 1) * $limiteCarrinho;
        $totalItensCarrinho = count($carrinho);
        $totalPaginasCarrinho = $totalItensCarrinho > 0 ? ceil($totalItensCarrinho / $limiteCarrinho) : 1;
        $itensPaginaCarrinho = array_slice($carrinho, $offsetCarrinho, $limiteCarrinho, true);

        echo "<table>";
echo "<tr>
        <th>Imagem</th>
        <th>Título</th>
        <th>ID Produto</th>
        <th>Quantidade</th>
        <th>Preço Unitário</th>
        <th>Total Item</th>
        <th>Frete</th>
        <th>CEP</th>
        <th>Rua</th>
        <th>Cidade</th>
        <th>Estado</th>
        <th>Número</th>
        <th>Bairro</th>
        <th>Complemento</th>
        <th>Character</th>
        <th>Remover</th>
        <th>Alterar Endereço</th>
      </tr>";

$subtotal = 0;
$totalFrete = 0;
foreach ($itensPaginaCarrinho as $id => $dados) {
    // Busca preço, título e imagem no banco
    $sqlProduto = "SELECT valor_total, consulta, imagem FROM consorcio_cadastro WHERE id = ?";
    $stmtProduto = $conn->prepare($sqlProduto);
    $stmtProduto->bind_param("i", $id);
    $stmtProduto->execute();
    $resProduto = $stmtProduto->get_result();
    $precoUnitario = 0;
    $tituloProduto = "";
    $imagemProduto = "";
    if ($rowProduto = $resProduto->fetch_assoc()) {
        $precoUnitario = $rowProduto['valor_total'];
        $tituloProduto = $rowProduto['consulta'];
        $imagemProduto = $rowProduto['imagem'];
    }
    $stmtProduto->close();

    $qtd = $dados['qtd'];
    $totalItem = $precoUnitario * $qtd;
    $frete = $dados['frete'] ?? 0;

    echo "<tr>";
    echo "<td data-label='Imagem'><img src='../../site3/cadastro_produto/".htmlspecialchars($imagemProduto)."' alt='".htmlspecialchars($tituloProduto)."'></td>";
    echo "<td data-label='Título'>" . htmlspecialchars($tituloProduto) . "</td>";
    echo "<td data-label='ID Produto'>" . htmlspecialchars($id) . "</td>";
    echo "<td data-label='Quantidade'>" . htmlspecialchars($qtd) . "</td>";
    echo "<td data-label='Preço Unitário'>AURA " . number_format($precoUnitario, 2, ',', '.') . "</td>";
    echo "<td data-label='Total Item'>AURA " . number_format($totalItem, 2, ',', '.') . "</td>";
    echo "<td data-label='Frete'>AURA " . number_format($frete, 2, ',', '.') . "</td>";
    echo "<td data-label='CEP'>" . htmlspecialchars($dados['cep']) . "</td>";
    echo "<td data-label='Rua'>" . htmlspecialchars($dados['rua']) . "</td>";
    echo "<td data-label='Cidade'>" . htmlspecialchars($dados['cidade']) . "</td>";
    echo "<td data-label='Estado'>" . htmlspecialchars($dados['estado']) . "</td>";
    echo "<td data-label='Número'>" . htmlspecialchars($dados['numero']) . "</td>";
    echo "<td data-label='Bairro'>" . htmlspecialchars($dados['bairro']) . "</td>";
    echo "<td data-label='Complemento'>" . htmlspecialchars($dados['complemento']) . "</td>";
    echo "<td data-label='Character'>" . htmlspecialchars($dados['nicknamecharacter'] ?? '') . "</td>";
    echo "<td data-label='Remover'><a href='remover_item.php?token=$token&id=$id' class='btn' style='background:#e74c3c;'>X</a></td>";
    echo "<td data-label='Alterar Endereço'><a href='atualizar_endereco_item.php?token=$token&id=$id' class='btn' style='background:#28a745;'>Alterar</a></td>";
    echo "</tr>";

    $subtotal += $totalItem;
    $totalFrete += $frete;
}
echo "</table>";

        $totalComFrete = $subtotal + $totalFrete;

        echo "<p>Página $paginaCarrinho de $totalPaginasCarrinho</p>";
        if ($paginaCarrinho > 1) {
            echo "<a href='carrinho_view.php?token=$token&pagina_carrinho=".($paginaCarrinho-1)."' class='btn'>Anterior</a> ";
        }
        if ($paginaCarrinho < $totalPaginasCarrinho) {
            echo "<a href='carrinho_view.php?token=$token&pagina_carrinho=".($paginaCarrinho+1)."' class='btn'>Próxima</a>";
        }

        echo "<p><strong>Subtotal:</strong> AURA " . number_format($subtotal, 2, ',', '.') . "</p>";
        echo "<p><strong>Total de Fretes:</strong> AURA " . number_format($totalFrete, 2, ',', '.') . "</p>";
        echo "<p><strong>Total Geral:</strong> AURA " . number_format($totalComFrete, 2, ',', '.') . "</p>";

                $dadosExemplo = reset($carrinho);
       echo "<div class='form-endereco'>
        <h3>Alterar Endereço de Todos os Itens</h3>
        <form method='post' action='atualizar_endereco.php?token=$token&tipo=all'>
            <input type='text' name='rua' placeholder='Rua' value='".htmlspecialchars($dadosExemplo['rua'] ?? '')."'>
            <input type='text' name='numero' placeholder='Número' value='".htmlspecialchars($dadosExemplo['numero'] ?? '')."'>
            <input type='text' name='bairro' placeholder='Bairro' value='".htmlspecialchars($dadosExemplo['bairro'] ?? '')."'>
            <input type='text' name='cidade' placeholder='Cidade' value='".htmlspecialchars($dadosExemplo['cidade'] ?? '')."'>
            <input type='text' name='estado' placeholder='Estado' value='".htmlspecialchars($dadosExemplo['estado'] ?? '')."'>
            <input type='text' name='cep' placeholder='CEP' value='".htmlspecialchars($dadosExemplo['cep'] ?? '')."'>
            <input type='text' name='telefone' placeholder='Telefone' value='".htmlspecialchars($dadosExemplo['telefone'] ?? '')."'>
            <input type='text' name='complemento' placeholder='Complemento' value='".htmlspecialchars($dadosExemplo['complemento'] ?? '')."'>
            <!-- Novo campo NicknameCharacter -->
            <input type='text' name='nicknamecharacter' placeholder='Nickname Character' value='".htmlspecialchars($dadosExemplo['nicknamecharacter'] ?? '')."'>

            <button type='submit' class='btn'>Salvar Endereço para Todos</button>
        </form>
      </div>";


        // Botões de ação
        $linkMagico = "https://carlitoslocacoes.com/sys/carrinho_view.php?token=" . $token;
        echo "<div class='carrinho-btns'>
                <button onclick=\"navigator.clipboard.writeText('$linkMagico').then(()=>alert('Link copiado!'))\" class='btn'>Copiar Link do Carrinho</button>
                <a href='finalizar.php?token=$token' class='btn'>Finalizar Compra</a>
                <a href='limpar.php?token=$token' class='btn'>Limpar Carrinho</a>
              </div>";
    } else {
        echo "<p class='error'>Carrinho vazio.</p>";
    }
} else {
    echo "<p class='error'>Carrinho expirado ou inválido.</p>";
}
$stmt->close();
$conn->close();
?>
</div>
</body>
</html>
