<?php
// Forçar o fuso horário correto de Palmeira das Missões - RS (Brasília)
date_default_timezone_set('America/Sao_Paulo');

include 'conexao.php';

// Definir um token secreto simples para segurança na URL (ex: ?token=meusegredo123)
$token_secreto = "stc_limpeza_segura_2026"; 

$token_recebido = isset($_GET['token']) ? $_GET['token'] : '';

if ($token_recebido !== $token_secreto) {
    http_response_code(403);
    echo "Acesso negado. Token de segurança inválido.";
    exit;
}

// Executar a limpeza de todas as caronas publicadas
$sql_limpeza = "DELETE FROM caronas_ufsm";
if ($conn->query($sql_limpeza) === TRUE) {
    $afetados = $conn->affected_rows;
    echo "Sucesso: Todas as caronas foram limpas/apagadas. Total removido: " . $afetados . " registo(s) em " . date('d/m/Y H:i:s');
} else {
    echo "Erro ao limpar caronas: " . $conn->error;
}

$conn->close();
?>