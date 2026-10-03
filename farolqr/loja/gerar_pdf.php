<?php
declare(strict_types=1);

// Em produção, não exiba erros diretamente para o visitante
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
error_reporting(E_ALL);

$idComprovante = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

if (!$idComprovante || $idComprovante <= 0) {
    http_response_code(400);
    exit('Erro: ID do comprovante inválido ou não informado.');
}

/*
|--------------------------------------------------------------------------
| CONFIGURAÇÃO DO BANCO
|--------------------------------------------------------------------------
| Use aqui a nova senha do banco de dados.
*/
$conn = new mysqli("localhost", "u839226731_farol", "Meta6595869!", "u839226731_farol");
if ($conn->connect_error) {
    error_log('Erro de conexão com o banco: ' . $conn->connect_error);
    http_response_code(500);
    exit('Erro interno de conexão com o banco de dados.');
}
$conn->set_charset('utf8mb4');

/*
|--------------------------------------------------------------------------
| BUSCAR COMPROVANTE
|--------------------------------------------------------------------------
*/
$sql = "
    SELECT
        id,
        comprador,
        dono_loja,
        nome_site,
        valor,
        caixa_destino,
        token_loja,
        data_confirmacao
    FROM confirmado_aura
    WHERE id = ?
    LIMIT 1
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    error_log('Erro ao preparar consulta: ' . $conn->error);
    $conn->close();

    http_response_code(500);
    exit('Erro interno ao consultar o comprovante.');
}

$stmt->bind_param('i', $idComprovante);
$stmt->execute();

$resultado = $stmt->get_result();
$dados = $resultado->fetch_assoc();

$stmt->close();
$conn->close();

if (!$dados) {
    http_response_code(404);
    exit('O comprovante solicitado não existe.');
}

/*
|--------------------------------------------------------------------------
| DADOS DO COMPROVANTE
|--------------------------------------------------------------------------
*/
$tokenLoja = (string)($dados['token_loja'] ?? '');
$nomeSite = (string)($dados['nome_site'] ?? '');

$comprador = (string)($dados['comprador'] ?? '');
$donoLoja = (string)($dados['dono_loja'] ?? '');
$valor = (string)($dados['valor'] ?? '');
$caixaDestino = (string)($dados['caixa_destino'] ?? '');
$dataConfirmacao = (string)($dados['data_confirmacao'] ?? '');

/*
|--------------------------------------------------------------------------
| DEFINIR PROTOCOLO E HOST
|--------------------------------------------------------------------------
*/
$isHttps =
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
    (
        isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
        $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https'
    );

$protocolo = $isHttps ? 'https' : 'http';

$host = $_SERVER['HTTP_HOST'] ?? 'carlitoslocacoes.com';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '/farolqr/loja/gerar_pdf.php';

/*
|--------------------------------------------------------------------------
| LINK DO COMPROVANTE
|--------------------------------------------------------------------------
*/
$linkComprovante = $protocolo .
    '://' .
    $host .
    $scriptName .
    '?id=' .
    rawurlencode((string)$dados['id']);

/*
|--------------------------------------------------------------------------
| LINK DA LOJA
|--------------------------------------------------------------------------
*/
$scriptDir = str_replace('\\', '/', dirname($scriptName));

if ($scriptDir === '/' || $scriptDir === '.') {
    $scriptDir = '';
}

$linkToken = '';

if ($tokenLoja !== '') {
    $linkToken = $protocolo .
        '://' .
        $host .
        $scriptDir .
        '/checkout_loja.php?token=' .
        rawurlencode($tokenLoja);
}

/*
|--------------------------------------------------------------------------
| CONTEÚDO DO QR CODE
|--------------------------------------------------------------------------
*/
$textoQr = $linkToken !== ''
    ? $linkToken
    : $linkComprovante;

/*
|--------------------------------------------------------------------------
| URL DO QR CODE (Com tratamento robusto)
|--------------------------------------------------------------------------
*/
$textoQrLimpo = !empty($textoQr) ? $textoQr : $linkComprovante;
$qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=' . urlencode($textoQrLimpo);

/*
|--------------------------------------------------------------------------
| PROTEÇÃO DE SAÍDA HTML
|--------------------------------------------------------------------------
*/
function e(string $valor): string
{
    return htmlspecialchars(
        $valor,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprovante de pagamento</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 24px;
            background: #f2f4f7;
            color: #202124;
            font-family: Arial, Helvetica, sans-serif;
        }

        .comprovante {
            width: 100%;
            max-width: 620px;
            margin: 0 auto;
            padding: 28px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.10);
        }

        h1 {
            margin-top: 0;
            text-align: center;
            font-size: 24px;
        }

        .linha {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            padding: 12px 0;
            border-bottom: 1px solid #eeeeee;
        }

        .rotulo {
            color: #666666;
            font-weight: bold;
        }

        .valor {
            text-align: right;
            overflow-wrap: anywhere;
        }

        .qr-area {
            margin-top: 28px;
            text-align: center;
        }

        .qr-area img {
            display: block;
            width: 250px;
            height: 250px;
            max-width: 100%;
            margin: 16px auto;
            border: 1px solid #dddddd;
        }

        .link {
            display: block;
            margin-top: 12px;
            color: #1769aa;
            font-size: 13px;
            overflow-wrap: anywhere;
            text-decoration: none;
        }

        .link:hover {
            text-decoration: underline;
        }

        .botao {
            display: inline-block;
            margin-top: 18px;
            padding: 12px 18px;
            color: #ffffff;
            background: #1769aa;
            border-radius: 6px;
            text-decoration: none;
        }

        @media print {
            body {
                padding: 0;
                background: #ffffff;
            }

            .comprovante {
                max-width: 100%;
                box-shadow: none;
            }

            .botao {
                display: none;
            }
        }
    </style>
</head>
<body>

<main class="comprovante">
    <h1>Comprovante de pagamento</h1>

    <div class="linha">
        <span class="rotulo">ID:</span>
        <span class="valor"><?= e((string)$dados['id']) ?></span>
    </div>

    <div class="linha">
        <span class="rotulo">Comprador:</span>
        <span class="valor"><?= e($comprador) ?></span>
    </div>

    <div class="linha">
        <span class="rotulo">Dono da loja:</span>
        <span class="valor"><?= e($donoLoja) ?></span>
    </div>

    <div class="linha">
        <span class="rotulo">Site:</span>
        <span class="valor"><?= e($nomeSite) ?></span>
    </div>

    <div class="linha">
        <span class="rotulo">Valor:</span>
        <span class="valor"><?= e($valor) ?></span>
    </div>

    <div class="linha">
        <span class="rotulo">Destino:</span>
        <span class="valor"><?= e($caixaDestino) ?></span>
    </div>

    <div class="linha">
        <span class="rotulo">Data:</span>
        <span class="valor"><?= e($dataConfirmacao) ?></span>
    </div>

    <section class="qr-area">
        <h2>QR Code</h2>

        <img 
            src="<?= e($qrCodeUrl) ?>" 
            alt="QR Code do comprovante"
            width="250"
            height="250"
        >

        <a 
            class="link" 
            href="<?= e($textoQr) ?>" 
            target="_blank" 
            rel="noopener noreferrer"
        >
            <?= e($textoQr) ?>
        </a>

        <a 
            class="link" 
            href="<?= e($linkComprovante) ?>" 
            target="_blank" 
            rel="noopener noreferrer"
            style="margin-top: 8px; color: #555555;"
        >
            Link do Comprovante: <?= e($linkComprovante) ?>
        </a>

        <a
            class="botao"
            href="<?= e($linkComprovante) ?>"
            target="_blank"
            rel="noopener noreferrer"
        >
            Abrir comprovante
        </a>
    </section>
</main>

</body>
</html>