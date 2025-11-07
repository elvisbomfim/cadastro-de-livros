# 📚 Sistema de Cadastro de Livros

Sistema completo para gerenciamento de livros, autores e assuntos, desenvolvido com Laravel e Vue.js, seguindo os princípios de Domain-Driven Design (DDD).

## 🚀 Tecnologias

### Backend
- **Laravel 12** - Framework PHP
- **Laravel Sanctum** - Autenticação API
- **DomPDF** - Geração de relatórios em PDF
- **Pest** - Framework de testes
- **MySQL** - Banco de dados

### Frontend
- **Vue.js 3** - Framework JavaScript
- **Vue Router** - Roteamento
- **Tailwind CSS 4** - Framework CSS
- **Axios** - Cliente HTTP
- **Chart.js** - Gráficos e visualizações
- **Vite** - Build tool

## 📋 Funcionalidades

### Gestão de Entidades
- ✅ Cadastro, edição e exclusão de **Livros**
- ✅ Cadastro, edição e exclusão de **Autores**
- ✅ Cadastro, edição e exclusão de **Assuntos**
- ✅ Relacionamentos muitos-para-muitos entre Livros, Autores e Assuntos

### Relatórios em PDF
- 📄 **Ficha Detalhada de Livro** - Informações completas de um livro específico
- 📊 **Livros por Categoria** - Relatório agrupado por assuntos/categorias
- 👤 **Relatórios por Autor** - Listagem de livros organizados por autor

### API REST
- Endpoints completos para CRUD de todas as entidades
- Respostas em JSON estruturadas
- Validação de dados

## 🏗️ Arquitetura

O projeto segue os princípios de **Domain-Driven Design (DDD)**:

```
src/Core/
├── Domain/
│   ├── Entity/          # Entidades de domínio
│   ├── ValueObject/     # Objetos de valor
│   ├── Repository/      # Interfaces de repositório
│   ├── UseCase/         # Casos de uso
│   └── Events/          # Eventos de domínio
app/
├── Http/Controllers/    # Controladores HTTP
├── Repositories/        # Implementações dos repositórios
└── Models/              # Modelos Eloquent
```

## 📦 Instalação

### Pré-requisitos
- PHP 8.2+
- Composer
- Node.js 18+
- MySQL 8.0+

### Passos

1. **Clone o repositório**
```bash
git clone https://github.com/elvisbomfim/cadastro-de-livros.git
cd cadastro-de-livros
```

2. **Instale as dependências PHP**
```bash
composer install
```

3. **Instale as dependências Node**
```bash
npm install
```

4. **Configure o ambiente**
```bash
cp .env.example .env
php artisan key:generate
```

5. **Configure o banco de dados no arquivo `.env`**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cadastro_livros
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

6. **Execute as migrations**
```bash
php artisan migrate
```

7. **Execute os seeders (opcional)**
```bash
php artisan db:seed
```

8. **Compile os assets**
```bash
npm run build
# ou para desenvolvimento
npm run dev
```

9. **Inicie o servidor**
```bash
php artisan serve
```

## 🧪 Testes

Execute os testes com Pest:

```bash
# Todos os testes
php artisan test

# Testes específicos
php artisan test --filter NomeDoTeste
```

## 📡 API Endpoints

### Livros
- `GET /api/livros` - Lista todos os livros
- `POST /api/livros` - Cria um novo livro
- `GET /api/livros/{id}` - Busca um livro específico
- `PUT /api/livros/{id}` - Atualiza um livro
- `DELETE /api/livros/{id}` - Remove um livro

### Autores
- `GET /api/autores` - Lista todos os autores
- `POST /api/autores` - Cria um novo autor
- `GET /api/autores/{id}` - Busca um autor específico
- `PUT /api/autores/{id}` - Atualiza um autor
- `DELETE /api/autores/{id}` - Remove um autor

### Assuntos
- `GET /api/assuntos` - Lista todos os assuntos
- `POST /api/assuntos` - Cria um novo assunto
- `GET /api/assuntos/{id}` - Busca um assunto específico
- `PUT /api/assuntos/{id}` - Atualiza um assunto
- `DELETE /api/assuntos/{id}` - Remove um assunto

### Relatórios
- `GET /api/relatorios/livros-por-categoria` - Dados para relatório por categoria
- `GET /api/relatorios/livro/{id}/ficha` - Dados da ficha detalhada
- `GET /api/relatorios/por-autor` - Dados do relatório por autor
- `GET /relatorios/livros-por-categoria/pdf` - PDF de livros por categoria
- `GET /relatorios/livro/{id}/ficha/pdf` - PDF da ficha detalhada
- `GET /relatorios/por-autor/pdf` - PDF do relatório por autor

## 🗄️ Estrutura do Banco de Dados

O sistema utiliza:
- **Views** para consultas otimizadas
- **Stored Procedures** para relatórios complexos
- Relacionamentos muitos-para-muitos através de tabelas pivot

## 🛠️ Desenvolvimento

### Estrutura de Commits

O projeto segue o padrão **Conventional Commits**:

```
feat(escopo): descrição da funcionalidade
fix(escopo): descrição da correção
docs(escopo): atualização de documentação
```

### Scripts Disponíveis

```bash
# Desenvolvimento frontend
npm run dev

# Build de produção
npm run build

# Testes
php artisan test
```

## 📝 Licença

Este projeto é privado e de uso pessoal.

## 👤 Autor

**Elvis Bomfim de Souza**
- Email: elvis@bravosite.com.br

---

Desenvolvido com ❤️ usando Laravel e Vue.js
