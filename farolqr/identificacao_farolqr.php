<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Função para validar CPF
function validarCPF($cpf) {
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    if (strlen($cpf) != 11) return false;
    if (preg_match('/^(.)\1{10}$/', $cpf)) return false;

    for ($t = 9; $t < 11; $t++) {
        $soma = 0;
        for ($i = 0; $i < $t; $i++) {
            $soma += $cpf[$i] * (($t + 1) - $i);
        }
        $digito = ((10 * $soma) % 11) % 10;
        if ($cpf[$t] != $digito) return false;
    }
    return true;
}

// Sessão persistente
$lifetime = 86400; // 1 dia
$domain   = 'localhost';

ini_set('session.cookie_lifetime', $lifetime);
ini_set('session.gc_maxlifetime', $lifetime);

session_set_cookie_params([
    'lifetime' => $lifetime,
    'path'     => '/',
    'domain'   => $domain,
    'secure'   => true,
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_start();

// Verifica login
if (
    !isset($_SESSION["loggedin_odonto2"]) || $_SESSION["loggedin_odonto2"] !== true ||
    empty($_SESSION["username_odonto2"])
) {
    header("Location: https://localhost/login/login_farolqr.php");
    exit;
}

$usuario = $_SESSION["username_odonto2"];

// Conexão
$conn = new mysqli("localhost", "root", "", "u839226731_farol");
$conn->set_charset("utf8mb4");

$mensagem = "";
$caixaExistente = null;

// Verifica se já existe caixa postal
$stmtVerifica = $conn->prepare("SELECT caixa_postal, documento, telefone, orcid, foto_perfil, data_criacao 
                                FROM identificacao_odonto2 WHERE username = ?");
$stmtVerifica->bind_param("s", $usuario);
$stmtVerifica->execute();
$resultVerifica = $stmtVerifica->get_result();
$caixaExistente = $resultVerifica->fetch_assoc();
$stmtVerifica->close();

// 🔐 reCAPTCHA Secret Key
$secretKey = "6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe";

// Criar caixa postal
if (isset($_POST['criar_caixa_postal'])) {
    // Verifica reCAPTCHA
    $recaptchaResponse = $_POST['g-recaptcha-response'];
    $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$recaptchaResponse}");
    $captchaSuccess = json_decode($verify);

    if ($captchaSuccess->success != true) {
        $mensagem = "❌ Falha na verificação reCAPTCHA. Tente novamente.";
    } else {
        if ($caixaExistente) {
            $mensagem = "⚠️ Você já possui uma caixa postal.";
        } else {
            $documento = trim($_POST["documento"]);
            $telefone = trim($_POST["telefone"]);
            $fotoPerfil = null;

            // Validação CPF
            if (!validarCPF($documento)) {
                $mensagem = "❌ Documento inválido. Informe um CPF válido.";
            } else {
                if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
                    $ext = strtolower(pathinfo($_FILES['foto_perfil']['name'], PATHINFO_EXTENSION));
                    $permitidas = ['jpg','jpeg','png','gif'];
                    if (in_array($ext, $permitidas)) {
                        $nomeArquivo = 'perfil_' . uniqid() . '.' . $ext;
                        $caminho = 'uploads/' . $nomeArquivo;
                        if (!is_dir('uploads')) mkdir('uploads');
                        move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $caminho);
                        $fotoPerfil = $caminho;
                    } else {
                        $mensagem = "❌ Tipo de arquivo inválido.";
                    }
                }

                // Gerar código único
                do {
                    $codigo = 'FAROLQR_' . substr(md5($usuario . microtime(true) . random_int(1000, 9999)), 0, 10);
                    $stmtCheck = $conn->prepare("SELECT 1 FROM identificacao_odonto2 WHERE caixa_postal = ?");
                    $stmtCheck->bind_param("s", $codigo);
                    $stmtCheck->execute();
                    $stmtCheck->store_result();
                    $existe = $stmtCheck->num_rows > 0;
                    $stmtCheck->close();
                } while ($existe);

                $stmt = $conn->prepare("INSERT INTO identificacao_odonto2 
                    (username, documento, telefone, foto_perfil, caixa_postal, data_criacao) 
                    VALUES (?, ?, ?, ?, ?, NOW())");
                $stmt->bind_param("sssss", $usuario, $documento, $telefone, $fotoPerfil, $codigo);
                $stmt->execute();
                $stmt->close();
             $mensagem = "📬 Caixa postal criada com sucesso: <strong>$codigo</strong>";

echo <<<HTML
    <button class="btn-login" onclick="window.location.href='https://localhost/caronas'">
        Início
    </button>
HTML;


            }
        }
    }
}

// Atualizar documento
if (isset($_POST['editar_documento'])) {
    $novoDocumento = trim($_POST["novo_documento"]);
    if (!validarCPF($novoDocumento)) {
        $mensagem = "❌ Documento inválido. Informe um CPF válido.";
    } else {
        $stmtUpdate = $conn->prepare("UPDATE identificacao_odonto2 SET documento = ? WHERE username = ?");
        $stmtUpdate->bind_param("ss", $novoDocumento, $usuario);
        $stmtUpdate->execute();
        $stmtUpdate->close();
        $mensagem = "✅ Documento atualizado com sucesso.";
    }
}
?>

<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>📬 Caixa Postal Odonto2</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
     body { 
  font-family: Arial, sans-serif; 
  background: linear-gradient(135deg, #ff006e, #d81b60, #880e4f); /* fundo rosa */
  padding: 20px; 
  color: #fff;
}

.container { 
  max-width: 600px; 
  margin: auto; 
  background: #1e1e2f; /* card escuro para contraste */
  padding: 20px; 
  border-radius: 12px; 
  box-shadow: 0 6px 20px rgba(0,0,0,0.4); 
}

h2 { 
  color: #ff4081; 
  text-align: center; 
  font-weight: bold;
}

label { 
  display: block; 
  margin-top: 10px; 
  font-weight: bold; 
  color: #ff80ab;
}

input[type="text"], input[type="file"] {
  width: 100%; 
  padding: 10px; 
  margin-top: 5px;
  border: none; 
  border-radius: 6px;
  background: #2c2c3c; 
  color: #fff;
}

input::placeholder {
  color: #aaa;
}

button {
  width: 100%; 
  padding: 12px; 
  margin-top: 15px;
  background: linear-gradient(90deg, #e91e63, #ad1457); /* degradê rosa */
  color: #fff;
  border: none; 
  border-radius: 28px;
  cursor: pointer; 
  font-size: 16px; 
  font-weight: bold;
  transition: transform 0.2s ease, background 0.3s ease;
}

button:hover { 
  transform: scale(1.05);
  background: linear-gradient(90deg, #ec407a, #c2185b);
}

.mensagem { 
  margin-top: 20px; 
  text-align: center; 
  font-size: 16px; 
  color: #ff80ab; 
}

.caixa {
  background: rgba(255,255,255,0.05); 
  margin-top: 30px; 
  padding: 15px;
  border-left: 5px solid #ff4081; 
  border-radius: 8px;
}

.caixa strong { 
  display: block; 
  font-size: 18px; 
  color: #fff;
}

.caixa small { 
  display: block; 
  margin-top: 5px; 
  color: #ccc; 
}

form.editar { 
  margin-top: 15px; 
}

img.foto-perfil {
  max-width: 150px; 
  border-radius: 8px;
  margin-top: 10px; 
  display: block;
  box-shadow: 0 0 10px rgba(233,30,99,0.6);
}

.btn-login {
  background: #d81b60; 
  color: #fff;
  padding: 10px; 
  border-radius: 6px;
  font-size: 16px; 
  margin-top: 20px;
  width: 100%; 
  border: none;
  font-weight: bold;
  transition: background 0.3s ease, transform 0.2s ease;
}

.btn-login:hover { 
  background: #ad1457; 
  transform: scale(1.05);
}

    </style>
</head>
<body>
    <div class="container">
       <h2>Bem-vindo, <?= htmlspecialchars($_SESSION["username_odonto2"]) ?></h2>

       <form method="POST" action="" enctype="multipart/form-data">
            <label for="documento">Documento CPF:</label>
            <input type="text" name="documento" id="documento" placeholder="Digite seu documento" required>
            <label for="telefone">Telefone:</label>
            <input type="text" name="telefone" id="telefone" placeholder="Digite seu telefone" required>
            <label for="foto_perfil">Foto de Perfil:</label>
            <input type="file" name="foto_perfil" id="foto_perfil" accept="image/*">

            <!-- reCAPTCHA -->
            <div class="g-recaptcha" data-sitekey="6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI"></div>

            <input type="hidden" name="criar_caixa_postal" value="1">
            <button type="submit">📬 Criar Caixa Postal</button>
        </form>
 <button class="btn-login" onclick="window.location.href='https://localhost/farolqr/balance_transacao.php'">
                    Banco
                </button>
                <button class="btn-login" onclick="window.location.href='https://localhost/sys/index.php'">
                    Comprar Aura
                </button>
                 <button class="btn-login" onclick="window.location.href='https://localhost/login/logout.php'">
            🔐 Sair
        </button>
        <!-- Botões extras -->
       
            
        <?php if ($mensagem): ?>
            <div class="mensagem"><?= $mensagem ?></div>
        <?php endif; ?>

        <?php if ($caixaExistente): ?>
            <div class="caixa">
                <strong>📦 Caixa Postal: <?= htmlspecialchars($caixaExistente['caixa_postal']) ?></strong>
                <small>🧾 Documento CPF: <?= htmlspecialchars($caixaExistente['documento']) ?></small>
                <small>📞 Telefone: <?= htmlspecialchars($caixaExistente['telefone']) ?></small>
                <small>🕒 Criado em: <?= $caixaExistente['data_criacao'] ?></small>

                <?php if (!empty($caixaExistente['foto_perfil'])): ?>
                    <img src="../../../farolqr/<?= htmlspecialchars($caixaExistente['foto_perfil']) ?>" alt="Foto de perfil" class="foto-perfil">
                <?php endif; ?>

                <form method="POST" class="editar">
                    <input type="text" name="novo_documento" placeholder="Atualizar documento" required>
                    <input type="hidden" name="editar_documento" value="1">
                    <button type="submit">✏️ Editar Documento</button>
                </form>

               
                
                
                
                <button class="btn-login" onclick="window.location.href='https://localhost/site/opentowork_city.php'">
                    OpenToWork Assinantes
                </button>
                <button class="btn-login" onclick="window.location.href='https://localhost/caronas'">
            Início
        </button>

       
             
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
