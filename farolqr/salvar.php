<?php
ini_set('display_errors',1);
error_reporting(E_ALL);

session_start();

$conn = new mysqli("localhost","u839226731_farol","Meta6595869!","u839226731_farol");
if($conn->connect_error) die("Erro: ".$conn->connect_error);
$conn->set_charset("utf8mb4");

$usuario = isset($_SESSION["usuario"]) ? $_SESSION["usuario"] : null;

// 🔄 Criar novo UID (resetar)
if(isset($_GET["novo"]) && $_GET["novo"]==1){
    $novoUID = uniqid();

    if($usuario && trim($usuario)!==""){
        // Usuário logado → UID vinculado
        $stmt = $conn->prepare("INSERT INTO essencias(uid,usuario,valor,usado,atualizado_em) VALUES(?, ?, 0, 0, NOW())");
        $stmt->bind_param("ss", $novoUID, $usuario);
    } else {
        // Sem login → UID anônimo
        $stmt = $conn->prepare("INSERT INTO essencias(uid,usuario,valor,usado,atualizado_em) VALUES(?, NULL, 0, 0, NOW())");
        $stmt->bind_param("s", $novoUID);
    }

    $stmt->execute();
    echo $novoUID;
    exit;
}

// ✨ Atualizar Essência
if(isset($_POST["uid"]) && isset($_POST["segundos"]) && !isset($_POST["acao"])){
    $uid = $_POST["uid"];
    $segundos = intval($_POST["segundos"]);

    // 🛡️ Anti-burlamento: só aceita incremento de 1 segundo por chamada
    if($segundos !== 1){
        echo "⚠️ Incremento inválido.";
        exit;
    }

    // Incremento correto → 1 Essência em 30 dias
    $incremento = 0.0000003858;

    $stmt = $conn->prepare("SELECT valor FROM essencias WHERE uid=?");
    $stmt->bind_param("s", $uid);
    $stmt->execute();
    $res = $stmt->get_result();

    if($row = $res->fetch_assoc()){
        $valor = floatval($row["valor"]);
        $valor += $incremento; // sempre 1 segundo

        $stmt2 = $conn->prepare("UPDATE essencias SET valor=?, atualizado_em=NOW() WHERE uid=?");
        $stmt2->bind_param("ds", $valor, $uid);
        $stmt2->execute();

        echo number_format($valor, 12, '.', '');
    } else {
        echo "UID não encontrado";
    }
    exit;
}

// 🔄 Trocar Essência por Aura
if(isset($_POST["acao"]) && $_POST["acao"]==="trocar"){
    $quantidade = 1; // 🛡️ Anti-burlamento: sempre 1 por vez
    $uid = $_POST["uid"] ?? null;

    if($uid){
        $stmt = $conn->prepare("SELECT valor, usuario FROM essencias WHERE uid=?");
        $stmt->bind_param("s", $uid);
        $stmt->execute();
        $res = $stmt->get_result();
        if($row = $res->fetch_assoc()){
            $valor = floatval($row["valor"]);
            $usuario = $row["usuario"];

            if($valor >= $quantidade){
                $novoValor = $valor - $quantidade;
                $stmt2 = $conn->prepare("UPDATE essencias SET valor=? WHERE uid=?");
                $stmt2->bind_param("ds", $novoValor, $uid);
                $stmt2->execute();

                $stmt3 = $conn->prepare("INSERT INTO auras(usuario,quantidade,criado_em) VALUES(?, ?, NOW())");
                $stmt3->bind_param("si", $usuario, $quantidade);
                $stmt3->execute();

                echo "✅ Conversão realizada: 1 Essência → 1 Aura";
            } else {
                echo "⚠️ Saldo insuficiente.";
            }
        } else {
            echo "UID inválido.";
        }
    }
    exit;
}
?>
