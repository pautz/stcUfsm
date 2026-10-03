<?php
session_start();
$conn = new mysqli("localhost","u839226731_farol","Meta6595869!","u839226731_farol");
if($conn->connect_error) die("Erro: ".$conn->connect_error);
$conn->set_charset("utf8mb4");


$usuario = $_SESSION["username_odonto2"] ?? null;
if(!$usuario || trim($usuario)===""){
    header("Location: https://carlitoslocacoes.com/login/login_farolqr.php");
    exit;
}
// 🔄 Trocar Essência por Aura
if(isset($_POST["acao"]) && $_POST["acao"]==="trocar"){
    header('Content-Type: application/json; charset=utf-8');
    $quantidade = 1;
    $uidAntigo = $_POST["uid"] ?? null;

    if(!$usuario || trim($usuario)===""){
        echo json_encode(["status"=>"erro","mensagem"=>"⚠️ É necessário estar logado para converter."]);
        exit;
    }

    if($uidAntigo){
        // Anti-burlamento: só pega último UID do usuário logado
        $stmt = $conn->prepare("SELECT valor, usuario FROM essencias WHERE uid=? ORDER BY id DESC LIMIT 1");
        $stmt->bind_param("s", $uidAntigo);
        $stmt->execute();
        $res = $stmt->get_result();

        if($row = $res->fetch_assoc()){
            $valor = floatval($row["valor"]);

            if($valor >= $quantidade){

    // Verifica se o usuário existe em identificacao_odonto2
    $stmtCheck = $conn->prepare("SELECT 1 FROM identificacao_odonto2 WHERE username=?");
    $stmtCheck->bind_param("s", $usuario);
    $stmtCheck->execute();
    $resCheck = $stmtCheck->get_result();

    if(!$resCheck->fetch_assoc()){
        echo json_encode([
            "status"=>"erro",
            "mensagem"=>"⚠️ Usuário não possui identificação em identificacao_odonto2 para receber Aura."
        ]);
        exit;
    }

    // Se chegou aqui, pode descontar
    $restante = $valor - $quantidade;

    // Zera UID antigo
    $stmt2 = $conn->prepare("UPDATE essencias SET valor=0 WHERE uid=?");
    $stmt2->bind_param("s", $uidAntigo);
    $stmt2->execute();

    // Gera novo UID com saldo restante
    $novoUid = uniqid("ess_", true);
    $stmt3 = $conn->prepare("INSERT INTO essencias (uid, valor, usuario, atualizado_em) VALUES (?, ?, ?, NOW())");
    $stmt3->bind_param("sis", $novoUid, $restante, $usuario);
    $stmt3->execute();

    // Atualiza Aura
    $stmt4 = $conn->prepare("UPDATE identificacao_odonto2 SET saldo_total = saldo_total + ? WHERE username=?");
    $stmt4->bind_param("is", $quantidade, $usuario);
    $stmt4->execute();

    // Auditoria
    $stmt5 = $conn->prepare("INSERT INTO auditoria_conversao (uid_antigo, uid_novo, usuario, quantidade, datahora) VALUES (?, ?, ?, ?, NOW())");
    $stmt5->bind_param("sssi", $uidAntigo, $novoUid, $usuario, $quantidade);
    $stmt5->execute();

    echo json_encode([
        "status"=>"ok",
        "mensagem"=>"✅ Conversão realizada: 1 Essência → 1 Aura",
        "novo_uid"=>$novoUid
    ]);
} else {
    echo json_encode(["status"=>"erro","mensagem"=>"⚠️ Saldo insuficiente."]);
}
        } else {
            echo json_encode(["status"=>"erro","mensagem"=>"⚠️ UID inválido ou não pertence ao usuário."]);
        }
    }
    exit;
}

// ▶️ Iniciar → buscar último UID ou criar novo
if(isset($_GET["acao"]) && $_GET["acao"]==="iniciar"){
    if($usuario && trim($usuario)!==""){
        $stmt = $conn->prepare("SELECT uid FROM essencias WHERE usuario=? ORDER BY id DESC LIMIT 1");
        $stmt->bind_param("s", $usuario);
    } else {
        $stmt = $conn->prepare("SELECT uid FROM essencias WHERE usuario IS NULL ORDER BY id DESC LIMIT 1");
    }
    $stmt->execute();
    $res = $stmt->get_result();
    if($row = $res->fetch_assoc()){
        echo $row["uid"];
    } else {
        $novoUID = uniqid("uid_", true);
        if($usuario && trim($usuario)!==""){
            $stmt2 = $conn->prepare("INSERT INTO essencias(uid,usuario,valor,usado,atualizado_em) VALUES(?, ?, 0, 0, NOW())");
            $stmt2->bind_param("ss", $novoUID, $usuario);
        } else {
            $stmt2 = $conn->prepare("INSERT INTO essencias(uid,usuario,valor,usado,atualizado_em) VALUES(?, NULL, 0, 0, NOW())");
            $stmt2->bind_param("s", $novoUID);
        }
        $stmt2->execute();
        echo $novoUID;
    }
    exit;
}

// ✨ Atualizar Essência (mineração)
if(isset($_POST["uid"]) && isset($_POST["segundos"]) && !isset($_POST["acao"])){
    $uid = $_POST["uid"];
    $segundos = intval($_POST["segundos"]);

    // Anti-burlamento: só aceita incremento de 1 segundo
    if($segundos !== 1){ echo "⚠️ Incremento inválido."; exit; }

    // Anti-burlamento: valida UID pertence ao usuário ou é anônimo válido
    if($usuario && trim($usuario)!==""){
        $stmtCheck = $conn->prepare("SELECT id FROM essencias WHERE uid=? AND usuario=? ORDER BY id DESC LIMIT 1");
        $stmtCheck->bind_param("ss", $uid, $usuario);
    } else {
        $stmtCheck = $conn->prepare("SELECT id FROM essencias WHERE uid=? AND usuario IS NULL ORDER BY id DESC LIMIT 1");
        $stmtCheck->bind_param("s", $uid);
    }
    $stmtCheck->execute();
    if(!$stmtCheck->get_result()->fetch_assoc()){
        echo "⚠️ UID inválido."; 
        exit;
    }

    // Incremento calibrado para 7 Essências em 30 dias
    $incremento = 0.00000270042;

    $stmt = $conn->prepare("SELECT valor FROM essencias WHERE uid=? ORDER BY id DESC LIMIT 1");
    $stmt->bind_param("s", $uid);
    $stmt->execute();
    $res = $stmt->get_result();
    if($row = $res->fetch_assoc()){
        $valor = floatval($row["valor"]) + $incremento;
        $stmt2 = $conn->prepare("UPDATE essencias SET valor=?, atualizado_em=NOW() WHERE uid=?");
        $stmt2->bind_param("ds", $valor, $uid);
        $stmt2->execute();
        echo number_format($valor, 12, '.', '');
    } else { echo "UID não encontrado"; }
    exit;
}
?>



<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Essências</title>
<style>
body{font-family:Arial;background:#1e3c72;color:#fff;text-align:center;padding:30px;}
button{padding:10px 20px;font-size:16px;margin:5px;}
#mensagem{margin-top:15px;font-weight:bold;}
input{padding:8px;margin:5px;}
</style>
</head>
<body>
<h2>🌌 Sistema de Essências</h2>
<p>UID atual: <span id="uid"><?php echo $uid ?: '---'; ?></span></p>
<p>Tempo online: <span id="tempo">0</span> segundos</p>
<p>Valor Essência: <span id="valor"><?php echo number_format($valorInicial,7,'.',''); ?></span></p>

<form id="essenciaForm" method="POST" action="essencias.php" style="display:none;">
  <input type="hidden" name="uid" id="formUid" value="<?php echo $uid; ?>">
  <input type="hidden" name="segundos" id="formSegundos">
</form>

<button onclick="iniciar()">▶️ Iniciar</button>

<!-- Formulário para conversão -->
<h3>Converter Essência em Aura</h3>
<form id="trocaForm" method="POST" action="essencias.php" onsubmit="return converter(event)">
  <input type="hidden" name="acao" value="trocar">
  <label>Digite o UID para converter:</label><br>
  <input type="text" name="uid" id="trocaUid" placeholder="uid_xxxxx" required>
  <br>
  <button type="submit">Converter 1 Essência em 1 Aura</button>
</form>

<div id="mensagem"></div>

<script>
let segundos = 0;
let uidAtual = document.getElementById("uid").textContent.trim();
let intervalo = null;

function iniciar(){
  if(intervalo) return;
  if(uidAtual === "---" || uidAtual === ""){
    fetch("essencias.php?acao=iniciar")
      .then(resp => resp.text())
      .then(uid => {
        uidAtual = uid.trim();
        document.getElementById("uid").textContent = uidAtual;
        document.getElementById("formUid").value = uidAtual;
        iniciarContador();
      });
  } else {
    iniciarContador();
  }
}

function iniciarContador(){
  intervalo = setInterval(() => {
    segundos++;
    document.getElementById("tempo").textContent = segundos;
    enviarForm();
  }, 1000);
}

function resetarUID(){
  fetch("essencias.php?novo=1")
    .then(resp => resp.text())
    .then(novoUID => {
      uidAtual = novoUID.trim();
      document.getElementById("uid").textContent = uidAtual;
      document.getElementById("valor").textContent = "0.0000000";
      segundos = 0;
      document.getElementById("tempo").textContent = segundos;
      document.getElementById("formUid").value = uidAtual;
      intervalo = null;
    });
}

function enviarForm(){
  if(uidAtual === "" || uidAtual === "---") return;
  document.getElementById("formSegundos").value = 1;
  fetch("essencias.php", {
    method: "POST",
    body: new FormData(document.getElementById("essenciaForm"))
  })
  .then(resp => resp.text())
  .then(valorAtualizado => {
    if(valorAtualizado.startsWith("⚠️") || valorAtualizado.startsWith("UID")) return;
    document.getElementById("valor").textContent = valorAtualizado;
  });
}

function converter(event){
    event.preventDefault();

    fetch("essencias.php", {
        method: "POST",
        body: new FormData(document.getElementById("trocaForm"))
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById("mensagem").innerHTML = data.mensagem;

        if(data.status==="ok" && data.novo_uid){
            // Atualiza o UID atual exibido na tela
            document.getElementById("uid").textContent = data.novo_uid;

            // Atualiza variáveis e formulários ocultos
            uidAtual = data.novo_uid;
            document.getElementById("trocaUid").value = data.novo_uid;
            document.getElementById("formUid").value = data.novo_uid;
        }
    })
    .catch(err => {
        console.error(err);
        document.getElementById("mensagem").innerHTML = "Erro na conversão.";
    });

    return false;
}

</script>
</body>
<script>
document.addEventListener("DOMContentLoaded", () => {
    iniciar(); // dispara automaticamente ao carregar a página
});
</script>


<iframe src="https://carlitoslocacoes.com/banner_lateral/iframe.php" 
        width="100%" 
        height="600" 
        frameborder="0" 
        style="border:0; overflow:hidden;">
</iframe>
</html>
