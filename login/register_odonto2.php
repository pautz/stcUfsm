<?php
session_start();
date_default_timezone_set('America/Cuiaba');

// Conexão com o banco
define('DB_SERVER', '127.0.0.1');
define('DB_USERNAME', 'u839226731_farol');
define('DB_PASSWORD', 'Meta6595869!');
define('DB_NAME', 'u839226731_farol');

$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Variáveis
$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";
$maior18_err = "";

// Processa o formulário
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Verifica reCAPTCHA
    if(empty($_POST['g-recaptcha-response'])){
        die("Por favor, confirme que você não é um robô.");
    } else {
        $recaptcha_secret = "6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe"; // Secret Key de teste do Google
        $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret="
            .$recaptcha_secret."&response=".$_POST['g-recaptcha-response']);
        $responseKeys = json_decode($response, true);

        if(intval($responseKeys["success"]) !== 1) {
            die("Falha na verificação do reCAPTCHA. Tente novamente.");
        }
    }

    // Valida usuário
    if(empty(trim($_POST["username"]))){
        $username_err = "Digite um nome de usuário.";
    } else{
        $sql = "SELECT id FROM odonto2_users WHERE username = ?";
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            $param_username = trim($_POST["username"]);
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "Este nome de usuário já está em uso.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Erro ao verificar usuário. Tente novamente.";
            }
            mysqli_stmt_close($stmt);
        }
    }

    // Valida senha
    if(empty(trim($_POST["password"]))){
        $password_err = "Digite uma senha.";
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "A senha deve ter pelo menos 6 caracteres.";
    } else{
        $password = trim($_POST["password"]);
    }

    // Confirma senha
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Confirme sua senha.";
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "As senhas não coincidem.";
        }
    }

    // Valida maior de 18 anos
    if(empty($_POST["maior18"])){
        $maior18_err = "É necessário confirmar que você tem 18 anos ou mais.";
    }

    // Se tudo estiver válido, insere no banco
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($maior18_err)){
        $sql = "INSERT INTO odonto2_users (username, password, maior18) VALUES (?, ?, ?)";
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "sss", $param_username, $param_password, $param_maior18);
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT);
            $param_maior18 = 1; // sempre 1 se marcado
            if(mysqli_stmt_execute($stmt)){
                $_SESSION['loggedin_odonto2'] = true;
                $_SESSION['username_odonto2'] = $username;
                header("location: http://localhost/farolqr/identificacao_farolqr.php");
                exit;
            } else{
                echo "Erro ao registrar. Tente novamente.";
            }
            mysqli_stmt_close($stmt);
        }
    }

    mysqli_close($link);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Crie sua Conta Móveis Espectro</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
  <style>
    body {
      font-family: 'Montserrat', sans-serif;
      background: linear-gradient(to right, #880e4f, #ad1457, #c2185b);
      color: #fff;
      text-align: center;
      overflow-x: hidden;
      padding: 40px;
    }
    .wrapper {
      width: 100%; max-width: 500px; margin: auto;
      background: rgba(255, 255, 255, 0.1);
      padding: 30px; border-radius: 15px;
      box-shadow: 0 0 20px rgba(233, 30, 99, 0.4);
      animation: fadeInUp 1.5s ease-in-out;
    }
    h2 { font-weight: 700; margin-bottom: 10px; animation: slideDown 1.2s ease-in-out; }
    p { font-size: 16px; animation: fadeIn 2s ease-in-out; }
    .form-group { text-align: left; margin-bottom: 15px; }
    input[type="text"], input[type="password"] {
      width: 100%; padding: 10px; border: none; border-radius: 8px;
      background-color: rgba(255, 255, 255, 0.9); color: #333; transition: 0.3s;
    }
    input[type="text"]:focus, input[type="password"]:focus {
      background-color: #fff; box-shadow: 0 0 10px #ec407a;
    }
    .btn-xl { padding: 12px 20px; font-size: 16px; border-radius: 10px; width: 48%; margin: 5px; transition: 0.3s; }
    .btn-xl:hover { transform: scale(1.05); }
    .btn-primary { background-color: #d81b60; border-color: #d81b60; }
    .btn-primary:hover { background-color: #ad1457; border-color: #ad1457; }
    .btn-info { background-color: #f06292; border-color: #f06292; }
    .btn-info:hover { background-color: #ec407a; border-color: #ec407a; }
    .btn-success { background-color: #e91e63; border-color: #e91e63; }
    .btn-success:hover { background-color: #c2185b; border-color: #c2185b; }
    .help-block { color: #ff4444; font-size: 0.9em; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes slideDown { from { opacity: 0; transform: translateY(-30px); } to { opacity: 1; transform: translateY(0); } }
    @media (max-width: 600px) {
      .btn-xl { width: 100%; }
      .wrapper { padding: 20px; }
    }
  </style>
</head>
<body>
  <div class="wrapper">
    <h2>Cadastre-se na FarolQR</h2>
    <p>Preencha com seus dados para criar sua conta.</p>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
      <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
        <label>Usuário</label>
        <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
        <span class="help-block"><?php echo $username_err; ?></span>
      </div>
      <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
        <label>Senha</label>
        <input type="password" name="password" class="form-control">
        <span class="help-block"><?php echo $password_err; ?></span>
      </div>
      <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
        <label>Confirme sua senha</label>
        <input type="password" name="confirm_password" class="form-control">
        <span class="help-block"><?php echo $confirm_password_err; ?></span>
      </div>
            <div class="form-group <?php echo (!empty($maior18_err)) ? 'has-error' : ''; ?>">
        <div class="checkbox">
          <label>
            <input type="checkbox" name="maior18" value="1" required>
            Confirmo que tenho 18 anos ou mais
          </label>
        </div>
        <span class="help-block"><?php echo $maior18_err; ?></span>
      </div>

      <!-- reCAPTCHA -->
      <div class="form-group">
        <div class="g-recaptcha" data-sitekey="6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI"></div>
      </div>

      <div class="form-group">
        <input type="submit" class="btn btn-success btn-xl" value="Cadastrar">
        <a href="login_farolqr.php" class="btn btn-info btn-xl">Voltar</a>
      </div>
      <p>Já possui cadastro? <a href="http://localhost/login/login_farolqr.php">Clique aqui para acessar o sistema.</a></p>
    </form>
  </div>

  <!-- VLibras -->
  <div vw class="enabled">
    <div vw-access-button class="active"></div>
    <div vw-plugin-wrapper><div class="vw-plugin-top-wrapper"></div></div>
  </div>
  <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
  <script>new window.VLibras.Widget('https://vlibras.gov.br/app');</script>

  <!-- Script reCAPTCHA -->
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</body>
</html>
