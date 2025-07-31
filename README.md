# 🛒 Marketplace

Um marketplace moderno e elegante desenvolvido em Laravel, oferecendo uma plataforma completa para vendedores e compradores se conectarem.

## 📋 Sobre o Projeto

Este marketplace foi desenvolvido utilizando Laravel, um dos frameworks PHP mais poderosos e elegantes disponíveis. O projeto visa criar uma experiência de compra e venda intuitiva, segura e escalável.

### ✨ Principais Funcionalidades

-   🏪 **Multi-vendor**: Suporte para múltiplos vendedores
-   🛍️ **Carrinho de Compras**: Sistema completo de carrinho e checkout
-   💳 **Pagamentos**: Integração com gateways de pagamento
-   👤 **Gestão de Usuários**: Sistema de autenticação e perfis
-   📊 **Dashboard Administrativo**: Painel completo para administradores
-   🔍 **Busca Avançada**: Sistema de filtros e busca de produtos
-   📱 **Responsivo**: Interface adaptável para todos os dispositivos
-   🔒 **Segurança**: Implementação de melhores práticas de segurança

## 🚀 Tecnologias Utilizadas

-   **Laravel** - Framework PHP elegante e poderoso
-   **MySQL** - Sistema de gerenciamento de banco de dados
-   **Bootstrap** - Framework CSS para interface responsiva
-   **JavaScript** - Interatividade do front-end
-   **Composer** - Gerenciador de dependências PHP
-   **Artisan** - Interface de linha de comando do Laravel

## 📦 Instalação

### Pré-requisitos

-   PHP >= 8.1
-   Composer
-   MySQL
-   Node.js & npm

### Passos para instalação

1. **Clone o repositório**

```bash
git clone https://github.com/Mazinho1020/marketplace.git
cd marketplace
```

2. **Instale as dependências do Composer**

```bash
composer install
```

3. **Instale as dependências do NPM**

```bash
npm install
```

4. **Configure o ambiente**

```bash
cp .env.example .env
php artisan key:generate
```

5. **Configure o banco de dados**
    - Edite o arquivo `.env` com suas credenciais do banco
    - Execute as migrations:

```bash
php artisan migrate
```

6. **Execute os seeders (opcional)**

```bash
php artisan db:seed
```

7. **Compile os assets**

```bash
npm run dev
```

8. **Inicie o servidor**

```bash
php artisan serve
```

Acesse `http://localhost:8000` para ver o projeto em funcionamento.

## 🛠️ Comandos Úteis

```bash
# Gerar uma nova migration
php artisan make:migration create_products_table

# Criar um novo controller
php artisan make:controller ProductController

# Limpar cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Executar testes
php artisan test
```

## 📁 Estrutura do Projeto

```
marketplace/
├── app/
│   ├── Http/Controllers/
│   ├── Models/
│   └── ...
├── database/
│   ├── migrations/
│   └── seeders/
├── public/
├── resources/
│   ├── views/
│   └── js/
├── routes/
└── ...
```

## 🤝 Contribuindo

Contribuições são sempre bem-vindas! Para contribuir:

1. Faça um Fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📝 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

## 👨‍💻 Desenvolvedor

**Mazinho1020** - [GitHub](https://github.com/Mazinho1020)

## 📞 Contato

Se você tiver alguma dúvida ou sugestão, sinta-se à vontade para entrar em contato!

---

⭐ Se este projeto te ajudou, considere dar uma estrela no repositório!
