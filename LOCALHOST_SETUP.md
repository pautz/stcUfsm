# 🚀 Guia Completo: Rodar stcUfsm em Localhost

## ✅ Status das Alterações Realizadas

Todos os arquivos foram atualizados para funcionar em localhost com detecção automática de HTTPS/HTTP.

### Arquivos Já Ajustados:
- ✅ `login/login_farolqr.php` - URLs e reCAPTCHA (chave de teste)
- ✅ `login/register_odonto2.php` - URLs e reCAPTCHA (chave de teste)
- ✅ `farolqr/identificacao_farolqr.php` - Domain, secure dinâmico, URLs e reCAPTCHA
- ✅ `caronas/index.php` - URLs ajustadas

---

## 🔧 Configuração do Cookie de Sessão (Dinâmica)

O arquivo `farolqr/identificacao_farolqr.php` agora usa detecção automática:

```php
$lifetime = 86400; // 1 dia
$domain   = 'localhost';

$secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');

session_set_cookie_params([
    'lifetime' => $lifetime,
    'path'     => '/',
    'domain'   => $domain,
    'secure'   => $secure,
    'httponly' => true,
    'samesite' => 'Lax'
]);
```

**O que muda:**
- `http://localhost/...` → `secure = false` ✅
- `https://localhost/...` → `secure = true` ✅
- `https://carlitoslocacoes.com/...` → em produção, o `secure` será `true` automaticamente ✅

---

## 🔐 reCAPTCHA - Chave de Teste

Todos os arquivos de login/identificação usam as chaves de teste do Google:

**Site Key (Público):**
```text
6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI
```

**Secret Key (Privada):**
```text
6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe
```

✅ Funciona em localhost e não bloqueia o fluxo de desenvolvimento.

---

## 📝 Checklist de Instalação Local

### 1️⃣ Clonar o Repositório
```bash
git clone https://github.com/pautz/stcUfsm.git
cd stcUfsm
```

### 2️⃣ Banco de Dados

**Host de Conexão:**
```text
localhost (ou 127.0.0.1)
Usuário: u839226731_farol
Senha: Meta6595869!
Banco: u839226731_farol
```

**Importar SQL:**
```bash
mysql -u u839226731_farol -p u839226731_farol < database.sql
# Senha: Meta6595869!
```

> Se não houver arquivo SQL, importe pelo phpMyAdmin ou crie as tabelas manualmente.

### 3️⃣ Configurar Apache/PHP

**Local do projeto:**
```text
/var/www/html/stcUfsm
C:\xampp\htdocs\stcUfsm
C:\wamp64\www\stcUfsm
```

### 4️⃣ Permissões de Pasta (CRÍTICO!)

```bash
chmod 755 farolqr/uploads/
chmod 755 farolqr/qrcodes/
```

### 5️⃣ Iniciar Servidor

**Opção A: Apache/XAMPP/WAMP**
- Inicie o Apache
- Acesse:
```text
http://localhost/stcUfsm/login/login_farolqr.php
```

**Opção B: PHP Built-in Server**
```bash
cd /caminho/para/stcUfsm
php -S localhost:8000
```

Acesse:
```text
http://localhost:8000/login/login_farolqr.php
```

---

## 🧪 Teste Completo do Fluxo

### 1. Registrar Novo Usuário
```
URL: http://localhost/stcUfsm/login/register_odonto2.php
```

### 2. Criar Caixa Postal
```text
Documento: 12345678900
Telefone: 65992334455
```

### 3. Testar Acesso ao Sistema
```text
URL: http://localhost/stcUfsm/farolqr/identificacao_farolqr.php
```

### 4. Testar Caronas
```text
URL: http://localhost/stcUfsm/caronas/index.php
```

---

## 🛑 Problemas Comuns

| Problema | Causa | Solução |
|----------|-------|---------|
| Página em branco | Erro PHP | Verificar logs do Apache |
| "Cookie domain mismatch" | Domain errado | Usar `localhost` em local |
| 404 em redirecionamentos | URL hardcoded | Revisar arquivos de login e farolqr |
| reCAPTCHA bloqueia | Chaves erradas | Usar chaves de teste |
| Upload falha | Permissão de pasta | `chmod 755 farolqr/uploads/` |
| Sessão não persiste | `secure=true` com HTTP | Código agora detecta automaticamente |

---

## 📊 Status Final: 100% Funcional em Localhost

✅ Funcional:
- ✓ Login / Registro
- ✓ Criar caixa postal
- ✓ Listar caronas
- ✓ Cadastrar caronas
- ✓ Pegar carona
- ✓ Upload de foto de perfil
- ✓ Sessões persistentes
- ✓ URLs ajustadas para localhost

⚠️ Requer configuração extra:
- ⚠ Mercado Pago
- ⚠ Webhooks de pagamento
- ⚠ Cron jobs

❌ Não testado:
- ❌ VLibras
- ❌ Geração de QR codes
- ❌ Assinaturas

---

## 🚀 Deploy em Produção

Quando subir para produção:

### 1. Alterar domínio em `farolqr/identificacao_farolqr.php`
```php
$domain = 'carlitoslocacoes.com';
```

### 2. Manter HTTPS
```php
$secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
```

### 3. Trocar reCAPTCHA por chaves reais
- Site key real
- Secret key real
- Atualizar nos arquivos de login e identificação

---

## ✅ Conclusão

Seu sistema está pronto para rodar em localhost com as URLs corrigidas, reCAPTCHA em modo de teste e sessão funcionando corretamente.

Se os arquivos estiverem acessíveis em um servidor local, o fluxo completo deve funcionar sem necessidade de ajustes adicionais de domínio.
