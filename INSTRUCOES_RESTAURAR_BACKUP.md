# 🚀 Instruções para Restaurar o Backup

## 1. Acesse o phpMyAdmin

-   Vá para: http://localhost/phpmyadmin
-   Usuário: root
-   Senha: (deixe em branco)

## 2. Selecione o banco 'meufinanceiro'

-   Clique no banco 'meufinanceiro' na lateral esquerda
-   Se não existir, crie um novo banco com esse nome

## 3. Importe o arquivo SQL

-   Clique na aba "Importar"
-   Clique em "Escolher arquivo"
-   Selecione: C:\Users\leoma\Downloads\meufinanceiro.sql
-   Clique em "Executar"

## 4. Aguarde a importação

-   O sistema irá restaurar todas as tabelas
-   Aguarde até aparecer "Importação finalizada com sucesso"

## 5. Teste o sistema

-   Acesse: http://localhost:8000/login
-   O sistema deve funcionar normalmente

---

**Obs:** Se der erro de charset, configure o banco como utf8mb4_unicode_ci
