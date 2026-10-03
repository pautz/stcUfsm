<?php
session_start();

$host = "127.0.0.1";
$usuario = "u839226731_farol";
$senha = "Meta6595869!";
$banco = "u839226731_farol";

$cx = new mysqli($host, $usuario, $senha, $banco);
if ($cx->connect_error) {
    die("Falha na conexão: " . $cx->connect_error);
}
$cx->set_charset("utf8");

$palavra_chave = "1996br";
$eq_user = $_SESSION["username_odonto2"] ?? null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $keyword = $_POST['keyword'] ?? '';
    $hashes = $_POST['entregues'] ?? [];

    echo "<!DOCTYPE html><html lang='pt-BR'><head>
          <meta charset='UTF-8'>
          <title>Resultado da Entrega</title>
          <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css'>
          </head><body class='bg-light'><div class='container mt-5'>";

    if ($keyword !== $palavra_chave) {
        echo "<div class='alert alert-danger text-center'>❌ Palavra-chave incorreta. Operação cancelada.</div>";
    } elseif (count($hashes) === 0) {
        echo "<div class='alert alert-warning text-center'>⚠️ Nenhum pagamento selecionado.</div>";
    } else {
        // Normaliza os valores
        $hashes = array_map('trim', $hashes);
        $hashes = array_map('strtolower', $hashes);

        $placeholders = implode(',', array_fill(0, count($hashes), '?'));
        $types = str_repeat('s', count($hashes));

        // Buscar saldo do usuário na tabela identificacao_odonto2
        $sqlSaldo = "SELECT saldo_total FROM identificacao_odonto2 WHERE username = ?";
        $stmtSaldo = $cx->prepare($sqlSaldo);
        $stmtSaldo->bind_param("s", $eq_user);
        $stmtSaldo->execute();
        $resultSaldo = $stmtSaldo->get_result();
        $saldoUsuario = $resultSaldo->fetch_assoc()["saldo_total"] ?? 0;
        $stmtSaldo->close();

        // Buscar valores e quantidades de cada compra (somente as não entregues)
        $sqlItens = "SELECT id, valor_total, quantidade 
                     FROM compras 
                     WHERE TRIM(LOWER(hash_comprovante)) IN ($placeholders)
                     AND status_pagamento <> 'entregue'";
        $stmtItens = $cx->prepare($sqlItens);
        $stmtItens->bind_param($types, ...$hashes);
        $stmtItens->execute();
        $resultItens = $stmtItens->get_result();

        $entregues = [];
        $naoEntregues = [];

        while ($row = $resultItens->fetch_assoc()) {
            $id = $row["id"];
            $valorUnitario = $row["valor_total"];
            $quantidade = $row["quantidade"];

            // Valor total da compra considerando quantidade
            $valorCompra = $valorUnitario * $quantidade;

            if ($saldoUsuario >= $valorCompra) {
                // Apenas valida, não desconta saldo
                $cx->query("UPDATE compras 
                            SET status_pagamento = 'entregue' 
                            WHERE id = $id");

                $entregues[] = $id;
            } else {
                $naoEntregues[] = [
                    "id" => $id,
                    "valor" => $valorCompra
                ];
            }
        }
        $stmtItens->close();

        // Mensagens de resultado
        if (!empty($entregues)) {
            echo "<div class='alert alert-success text-center'>
                    ✅ Compras entregues com sucesso: " . implode(", ", $entregues) . "
                  </div>";
        }
        if (!empty($naoEntregues)) {
            echo "<div class='alert alert-danger text-center'>
                    ❌ Saldo insuficiente para as seguintes compras:
                  </div><ul class='list-group mt-3'>";
            foreach ($naoEntregues as $item) {
                echo "<li class='list-group-item'>
                        Compra #{$item['id']} - Valor total: R$ " . number_format($item['valor'], 2, ',', '.') . "
                      </li>";
            }
            echo "</ul>";
        }

        // Mostrar saldo atual (sem alteração)
        echo "<div class='alert alert-info text-center mt-3'>
                💰 Saldo atual: R$ " . number_format($saldoUsuario, 2, ',', '.') . "
              </div>";
    }

    echo "<div class='text-center mt-4'>
            <a href='compras.php' class='btn btn-primary'>⬅️ Voltar para Compras</a>
          </div></div></body></html>";
}
?>
