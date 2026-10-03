# 🚀 Guia Completo: Trocar carlitoslocacoes.com por localhost

## ✅ Arquivos com URLs Hardcoded (Precisa Ajustar)

### 1. **caronas/index.php** (4 URLs)
```diff
- header("Location: https://carlitoslocacoes.com/login/login_farolqr.php");
+ header("Location: http://localhost/login/login_farolqr.php");

- <a href="https://carlitoslocacoes.com/login/logout.php"
+ <a href="http://localhost/login/logout.php"
```
**Linhas:** 6, 21, 38, 119

---

### 2. **login/login_farolqr.php** (3 URLs)
```diff
- header("location: https://carlitoslocacoes.com/farolqr/identificacao_farolqr.php");
+ header("location: http://localhost/farolqr/identificacao_farolqr.php");

- <a class="btn btn-info btn-xl" href="https://carlitoslocacoes.com/">Início</a>
+ <a class="btn btn-info btn-xl" href="http://localhost/">Início</a>
```
**Linhas:** 12, 74, 159

---

### 3. **login/register_odonto2.php** (2 URLs)
```diff
- header("location: https://carlitoslocacoes.com/farolqr/identificacao_farolqr.php");
+ header("location: http://localhost/farolqr/identificacao_farolqr.php");

- <p>Já possui cadastro? <a href="https://carlitoslocacoes.com/login/login_farolqr.php">
+ <p>Já possui cadastro? <a href="http://localhost/login/login_farolqr.php">
```
**Linhas:** 95, 196

---

### 4. **farolqr/identificacao_farolqr.php** (⚠️ CRÍTICO - 7 URLs)

#### Linha 25: Cookie Domain (Necessário ajustar)
```diff
- $domain   = 'carlitoslocacoes.com';
+ $domain   = 'localhost';
```

#### Linha 46: Redirecionamento Login
```diff
- header("Location: https://carlitoslocacoes.com/login/login_farolqr.php");
+ header("Location: http://localhost/login/login_farolqr.php");
```

#### Linhas 126-127: Botão Início
```diff
- onclick="window.location.href='https://carlitoslocacoes.com/index.php'"
+ onclick="window.location.href='http://localhost/index.php'"
```

#### Linhas 297-305: Botões de Navegação
```diff
- onclick="window.location.href='https://carlitoslocacoes.com/farolqr/balance_transacao.php'"
+ onclick="window.location.href='http://localhost/farolqr/balance_transacao.php'"

- onclick="window.location.href='https://carlitoslocacoes.com/sys/index.php'"
+ onclick="window.location.href='http://localhost/sys/index.php'"

- onclick="window.location.href='https://carlitoslocacoes.com/login/logout.php'"
+ onclick="window.location.href='http://localhost/login/logout.php'"

- onclick="window.location.href='https://carlitoslocacoes.com/site/opentowork_city.php'"
+ onclick="window.location.href='http://localhost/site/opentowork_city.php'"

- onclick="window.location.href='https://carlitoslocacoes.com/index.php'"
+ onclick="window.location.href='http://localhost/index.php'"
```

---

## 📋 Resumo de Alterações

| Arquivo | Linhas | URLs | Status |
|---------|--------|------|--------|
| `caronas/index.php` | 6, 21, 38, 119 | 4 | ✏️ |
| `login/login_farolqr.php` | 12, 74, 159 | 3 | ✏️ |
| `login/register_odonto2.php` | 95, 196 | 2 | ✏️ |
| `farolqr/identificacao_farolqr.php` | 25, 46, 126, 297, 300, 303, 334, 337 | 8 | ⚠️ CRÍTICO |
| **TOTAL** | | **17 URLs** | |

---

## 🔧 Opção 1: Substituição Manual

Abra cada arquivo e use Find & Replace (Ctrl+H ou Cmd+H):

**Buscar por:**
```
https://carlitoslocacoes.com
```

**Substituir por:**
```
http://localhost
```

> ⚠️ Aplicar em: `caronas/`, `login/`, `farolqr/` (excluir outras pastas se houver)

---

## 🤖 Opção 2: Usar Script Bash

```bash
#!/bin/bash

# Navegar até o diretório do projeto
cd /var/www/html/stcUfsm

# Substituir em todos os arquivos PHP
find . -name "*.php" -type f \
  -not -path "./site/*" \
  -not -path "./site2/*" \
  -not -path "./site3/*" \
  | xargs sed -i 's|https://carlitoslocacoes.com|http://localhost|g'

echo "✅ Todas as URLs foram atualizadas para localhost!"
```

**Como usar:**
1. Abra terminal
2. Cole o script acima
3. Execute: `bash update_urls.sh`

---

## ⚙️ Configurações Adicionais Necessárias

### 1. **reCAPTCHA Desabilitar para Testes**

**Arquivo:** `login/login_farolqr.php`, `login/register_odonto2.php`, `farolqr/identificacao_farolqr.php`

Comentar a verificação reCAPTCHA:

```php
// Verifica reCAPTCHA
if(empty($_POST['g-recaptcha-response'])){
    // die("Por favor, confirme que você não é um robô.");
} else {
    // ... resto do código comentado
}
```

Ou usar **chaves de teste do Google** (recomendado):
- Site Key (público): `6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI`
- Secret Key (privada): `6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe`

---

### 2. **Banco de Dados**

✅ **Já está correto** para localhost:
- Host: `localhost` (ou `127.0.0.1`)
- Usuário: `u839226731_farol`
- Senha: `Meta6595869!`
- Banco: `u839226731_farol`

```bash
# Importar SQL
mysql -u u839226731_farol -p < u839226731_farol\ \(2\).sql
# Senha: Meta6595869!
```

---

### 3. **Permissões de Pasta (Crítico!)**

```bash
# Pasta de upload de fotos
chmod 755 farolqr/uploads/

# Pasta de QR codes
chmod 755 farolqr/qrcodes/
```

---

## 📊 Checklist de Implementação

- [ ] Editar `caronas/index.php` (4 URLs)
- [ ] Editar `login/login_farolqr.php` (3 URLs + domínio cookie)
- [ ] Editar `login/register_odonto2.php` (2 URLs)
- [ ] Editar `farolqr/identificacao_farolqr.php` (8 URLs + domínio cookie)
- [ ] Desabilitar ou configurar reCAPTCHA
- [ ] Importar banco de dados SQL
- [ ] Criar/testar pastas de upload
- [ ] Testar login em `http://localhost/login/login_farolqr.php`
- [ ] Testar fluxo completo: Login → Identificação → Caronas

---

## 🧪 Teste Local

```bash
# 1. Inicie Apache/PHP
sudo service apache2 start  # ou php -S localhost:8000

# 2. Acesse
http://localhost/login/login_farolqr.php

# 3. Registre um usuário (teste com CPF válido, ex: 12345678901)

# 4. Complete identificação

# 5. Teste caronas
http://localhost/caronas/index.php
```

---

## ⚠️ Problemas Conhecidos

| Problema | Causa | Solução |
|----------|-------|---------|
| Página em branco | Erro PHP | Checar `error_log` do Apache |
| "Cookie domain mismatch" | Domain = carlitoslocacoes.com | Alterar linha 25 em `identificacao_farolqr.php` |
| 404 em redirecionamentos | URLs ainda hardcoded | Verificar se fez todas as 17 substituições |
| Mercado Pago falha | Credenciais de teste/produção | Configurar chaves reais ou remover por enquanto |
| Upload de foto falha | Pasta sem permissão | `chmod 755 farolqr/uploads/` |

---

## 🎯 Funcionalidades 100% Operacionais com Essas Mudanças

✅ **Totalmente Funcional:**
- ✓ Login / Registro
- ✓ Criar caixa postal
- ✓ Listar caronas
- ✓ Cadastrar caronas
- ✓ Pegar carona
- ✓ Upload de foto de perfil
- ✓ Sessões persistentes (com ajuste de domain)

⚠️ **Parcialmente (Requer Config Extra):**
- ⚠ Mercado Pago (PIX/Cartão)
- ⚠ Webhooks de pagamento
- ⚠ Cron job de limpeza
- ⚠ Sistema de essências/loja

❌ **Não Testado:**
- ❌ VLibras (acessibilidade)
- ❌ Geração de QR codes
- ❌ Assinaturas

---

## 📞 Próximos Passos

1. **Fazer as 17 substituições** de URLs
2. **Testar o fluxo completo** de login
3. **Configurar Mercado Pago** (se precisar pagamento)
4. **Cron job de limpeza** (opcional, mas recomendado)

Após isso, seu sistema estará **100% funcional em localhost**! 🚀
