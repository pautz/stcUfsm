<?php
session_start();

$conn = new mysqli("127.0.0.1", "u839226731_farol", "Meta6595869!", "u839226731_farol");
if ($conn->connect_error) { die("Conexão falhou: " . $conn->connect_error); }
$conn->set_charset("utf8");

$token = $_GET['token'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($token)) {
    $sql = "SELECT dados FROM carrinhos WHERE token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
        $carrinho = json_decode($row['dados'], true);

        foreach ($carrinho as $id => &$dados) {
            $dados['rua']          = trim($_POST['rua'] ?? '');
            $dados['numero']       = trim($_POST['numero'] ?? '');
            $dados['bairro']       = trim($_POST['bairro'] ?? '');
            $dados['cidade']       = trim($_POST['cidade'] ?? '');
            $dados['estado']       = trim($_POST['estado'] ?? '');
            $dados['cep']          = trim($_POST['cep'] ?? '');
            $dados['telefone']     = trim($_POST['telefone'] ?? '');
            $dados['complemento']  = trim($_POST['complemento'] ?? '');
            // Novo campo NicknameCharacter
            $dados['nicknamecharacter'] = trim($_POST['nicknamecharacter'] ?? '');
        }

        $sqlUpdate = "UPDATE carrinhos SET dados = ? WHERE token = ?";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        $jsonCarrinho = json_encode($carrinho);
        $stmtUpdate->bind_param("ss", $jsonCarrinho, $token);
        $stmtUpdate->execute();
        $stmtUpdate->close();
    }

    $stmt->close();
}

$conn->close();
header("Location: carrinho_view.php?token=" . urlencode($token));
exit;
?>
