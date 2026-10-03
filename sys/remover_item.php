<?php
session_start();
$conn = new mysqli("127.0.0.1","u839226731_farol","Meta6595869!","u839226731_farol");
if ($conn->connect_error) { die("Conexão falhou: " . $conn->connect_error); }

$token = $_GET['token'] ?? '';
$id    = $_GET['id'] ?? '';

$sql = "SELECT dados FROM carrinhos WHERE token = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $token);
$stmt->execute();
$res = $stmt->get_result();

if ($row = $res->fetch_assoc()) {
    $carrinho = json_decode($row['dados'], true);
    unset($carrinho[$id]); // remove o item pelo ID
    $novoJson = json_encode($carrinho, JSON_UNESCAPED_UNICODE);
    $sqlUpdate = "UPDATE carrinhos SET dados=? WHERE token=?";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bind_param("ss", $novoJson, $token);
    $stmtUpdate->execute();
    $stmtUpdate->close();
}
$stmt->close();
$conn->close();

// Redireciona de volta para o carrinho
header("Location: carrinho_view.php?token=$token");
exit;
?>
