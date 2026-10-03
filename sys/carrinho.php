<?php
session_start();

// Conexão com banco
$servername = "127.0.0.1";
$username   = "u839226731_farol";
$password   = "Meta6595869!";
$dbname     = "u839226731_farol";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { 
    die("Conexão falhou: " . $conn->connect_error); 
}
$conn->set_charset("utf8");

// Recebe dados do formulário
$id            = intval($_POST['id'] ?? 0);
$qtd           = isset($_POST['qtd']) ? max(1, intval($_POST['qtd'])) : 1;
$cidadePost    = $_POST['search_cidade'] ?? null;
$estadoPost    = $_POST['search_estado'] ?? null;
$ruaPost       = $_POST['rua'] ?? null;
$numeroPost    = $_POST['numero'] ?? null;
$bairroPost    = $_POST['bairro'] ?? null;
$cidadePostEnd = $_POST['cidade'] ?? null;
$estadoPostEnd = $_POST['estado'] ?? null;
$cepPost       = $_POST['cep'] ?? null;
$telefonePost  = $_POST['telefone'] ?? null;
$complementoPost = $_POST['complemento'] ?? null;

// Atualiza sessão com cidade e estado (filtros de busca)
if ($cidadePost !== null) $_SESSION['cidade'] = $cidadePost;
if ($estadoPost !== null) $_SESSION['estado'] = $estadoPost;

$cidade = $_SESSION['cidade'] ?? '';
$estado = $_SESSION['estado'] ?? '';

// Se não veio id válido, volta
if ($id <= 0) {
    $url = "hora_carrinho.php";
    $params = [];
    if ($cidade !== '') $params[] = "search_cidade=" . urlencode($cidade);
    if ($estado !== '') $params[] = "search_estado=" . urlencode($estado);
    if (!empty($params)) $url .= "?" . implode("&", $params);
    header("Location: $url");
    exit;
}

// Busca estoque e dados do produto
$sql = "SELECT estoque, peso, comprimento, largura, altura, cep 
        FROM consorcio_cadastro WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$estoque     = intval($row['estoque'] ?? 0);
$peso        = floatval($row['peso'] ?? 1);
$comprimento = floatval($row['comprimento'] ?? 20);
$largura     = floatval($row['largura'] ?? 20);
$altura      = floatval($row['altura'] ?? 20);
$cepOrigem   = $row['cep'] ?? "78000000";
$stmt->close();
$conn->close();

// --- Cálculo do frete via API Correios (exemplo fictício) ---
$token = "SEU_TOKEN_CORREIOS";
$apiUrl = "https://api.correios.com.br/frete/calcular";
$data = [
    "cepOrigem"   => $cepOrigem,
    "cepDestino"  => $cepPost,
    "peso"        => $peso,
    "comprimento" => $comprimento,
    "largura"     => $largura,
    "altura"      => $altura,
    "servico"     => "SEDEX"
];

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $token",
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);
$freteUnitario = floatval($result['valor'] ?? 0);

// Lê cookie existente
$carrinho = [];
if (!empty($_COOKIE['carrinho'])) {
    $carrinho = json_decode($_COOKIE['carrinho'], true);
    if (!is_array($carrinho)) $carrinho = [];
}

// Quantidade atual
$qtdAtual = isset($carrinho[$id]['qtd']) ? intval($carrinho[$id]['qtd']) : 0;

// Nova quantidade sem ultrapassar estoque
$novaQtd = min($qtdAtual + $qtd, $estoque);

// Só adiciona se houver estoque
if ($novaQtd > 0) {
    $carrinho[$id] = [
        'qtd'        => $novaQtd,
        'frete'      => $freteUnitario * $novaQtd,
        'rua'        => $ruaPost,
        'numero'     => $numeroPost,
        'complemento'=> $complementoPost,
        'bairro'     => $bairroPost,
        'cidade'     => $cidadePostEnd,
        'estado'     => $estadoPostEnd,
        'cep'        => $cepPost,
        'telefone'   => $telefonePost
    ];
}

// Atualiza cookie
setcookie('carrinho', json_encode($carrinho), time() + (7*24*60*60), "/");

// --- Salva carrinho no banco com token para link mágico ---
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Conexão falhou: " . $conn->connect_error); }
$conn->set_charset("utf8");

$tokenCarrinho = bin2hex(random_bytes(16));
$sql = "INSERT INTO carrinhos (token, dados, criado_em) VALUES (?, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $tokenCarrinho, json_encode($carrinho));
$stmt->execute();
$stmt->close();
$conn->close();

// Gera link mágico
$linkMagico = "https://carlitoslocacoes.com/carrinho_view.php?token=" . $tokenCarrinho;
echo "Seu link mágico: <a href='$linkMagico'>$linkMagico</a>";

// Redireciona para hora_carrinho.php (se quiser manter fluxo normal)
$url = "hora_carrinho.php";
$params = [];
if ($cidade !== '') $params[] = "search_cidade=" . urlencode($cidade);
if ($estado !== '') $params[] = "search_estado=" . urlencode($estado);
if (!empty($params)) $url .= "?" . implode("&", $params);
header("Location: $url");
exit;
?>
