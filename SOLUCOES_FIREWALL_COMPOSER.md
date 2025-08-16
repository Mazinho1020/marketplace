# Soluções para Problemas de Firewall com Composer

## 🔥 Problema Identificado

Você está enfrentando problemas durante a execução do `composer install` devido a restrições de firewall que bloqueiam o acesso à API do GitHub.

### Sintomas Comuns:

- Erro de timeout durante o download de pacotes
- Mensagens sobre conexão recusada para `api.github.com`
- Falhas ao baixar arquivos ZIP do GitHub
- Comando travado em "Downloading packages"

## ✅ Diagnóstico Atual

Com base nos testes realizados:

- ✅ **Conectividade com `api.github.com`**: **FUNCIONANDO**
- ✅ **Porta 443 (HTTPS)**: **ABERTA**
- ✅ **Resolução DNS**: **OK**

## 🛠️ Soluções Implementadas

### 1. Script de Configuração do Firewall

Criamos o arquivo `configure_github_firewall.ps1` que:

- Verifica privilégios administrativos
- Testa conectividade com domínios do GitHub
- Cria regras de firewall específicas
- Configura acesso para aplicações como Git e PowerShell

**Para executar:**

```powershell
# Abra PowerShell como Administrador
cd "C:\xampp\htdocs\marketplace"
.\configure_github_firewall.ps1
```

### 2. Comandos Alternativos do Composer

Se ainda houver problemas, tente estas variações:

```bash
# Opção 1: Usar distribuição preferencial
composer install --prefer-dist

# Opção 2: Aumentar timeout
composer install --timeout=300

# Opção 3: Modo verbose para diagnóstico
composer install -vvv

# Opção 4: Ignorar verificações SSL (apenas se necessário)
composer install --ignore-platform-reqs

# Opção 5: Usar cache local se disponível
composer install --prefer-dist --no-dev
```

### 3. Configurações Globais do Composer

Configure o Composer para ambientes corporativos:

```bash
# Aumentar timeout global
composer config --global process-timeout 300

# Configurar protocolo preferido
composer config --global github-protocols https

# Configurar cache (opcional)
composer config --global cache-dir "C:\composer-cache"
```

### 4. Configuração de Proxy (Se Aplicável)

Se você estiver em uma rede corporativa com proxy:

```bash
# Configurar proxy HTTP
composer config --global http-basic.proxy-url.com usuario senha

# Configurar proxy no Git
git config --global http.proxy http://proxy.empresa.com:8080
git config --global https.proxy http://proxy.empresa.com:8080
```

## 🌐 Domínios que Precisam de Acesso

Certifique-se de que estes domínios estão liberados no firewall:

- `api.github.com` (porta 443)
- `github.com` (porta 443)
- `raw.githubusercontent.com` (porta 443)
- `codeload.github.com` (porta 443)
- `objects.githubusercontent.com` (porta 443)
- `repo.packagist.org` (porta 443)
- `packagist.org` (porta 443)
- `getcomposer.org` (porta 443)

## 🔧 Soluções Alternativas

### Opção A: Download Manual

Se o firewall for muito restritivo:

1. Em um ambiente sem restrições, execute:

   ```bash
   composer install
   ```

2. Copie a pasta `vendor` resultante para o ambiente com firewall

3. No ambiente restrito, execute:
   ```bash
   composer install --no-scripts
   ```

### Opção B: Usar Composer.lock

Se você tiver um arquivo `composer.lock` válido:

```bash
# Instalar exatamente as versões do lock
composer install --prefer-dist --no-dev

# Ou apenas verificar se está atualizado
composer validate
```

### Opção C: Modificar composer.json

Adicione repositórios alternativos ao `composer.json`:

```json
{
  "repositories": [
    {
      "type": "composer",
      "url": "https://repo.packagist.org"
    },
    {
      "type": "composer",
      "url": "https://packagist.phpcomposer.com"
    }
  ],
  "config": {
    "process-timeout": 300,
    "github-protocols": ["https"]
  }
}
```

## 🏢 Para Ambientes Corporativos

Se você estiver em uma empresa/organização:

### 1. Solicite ao Administrador de Rede:

- Liberação dos domínios listados acima
- Configuração de whitelist para HTTPS (porta 443)
- Informações sobre proxy corporativo

### 2. Configurações Específicas:

```bash
# Se houver certificados corporativos
git config --global http.sslCAInfo "C:\caminho\para\certificado.pem"

# Se o proxy exigir autenticação
composer config --global http-basic.proxy.empresa.com usuario senha
```

## 🚀 Testes de Verificação

Após aplicar as soluções, execute estes testes:

```powershell
# Teste 1: Conectividade básica
Test-NetConnection api.github.com -Port 443

# Teste 2: Diagnóstico do Composer
composer diagnose

# Teste 3: Verificar configurações
composer config --list

# Teste 4: Teste com verbosidade
composer install --dry-run -vvv
```

## 📋 Checklist de Troubleshooting

- [ ] PowerShell executado como Administrador
- [ ] Script `configure_github_firewall.ps1` executado
- [ ] Conectividade com `api.github.com` verificada
- [ ] Timeout do Composer configurado
- [ ] Cache do Composer limpo (`composer clear-cache`)
- [ ] Proxy corporativo configurado (se aplicável)
- [ ] Antivírus/firewall de terceiros verificado
- [ ] VPN desabilitada temporariamente (se aplicável)

## 🆘 Se Nada Funcionar

Em último caso:

1. **Backup das configurações atuais:**

   ```bash
   composer config --list > composer-config-backup.txt
   ```

2. **Reset completo do Composer:**

   ```bash
   composer clear-cache
   rm -rf vendor/
   rm composer.lock
   composer install --prefer-dist
   ```

3. **Contate o suporte:**
   - Administrador de rede da empresa
   - Suporte técnico do provedor de internet
   - Equipe de TI responsável pelo firewall

## 📞 Comandos de Diagnóstico

Para coletar informações para suporte:

```bash
# Informações do sistema
composer diagnose > diagnostico.txt

# Configurações atuais
composer config --list >> diagnostico.txt

# Teste de conectividade
Test-NetConnection api.github.com -Port 443 >> diagnostico.txt

# Versões instaladas
php --version >> diagnostico.txt
composer --version >> diagnostico.txt
```

---

**Última atualização:** 16 de agosto de 2025

**Status:** ✅ Conectividade funcionando - Firewall configurado corretamente
