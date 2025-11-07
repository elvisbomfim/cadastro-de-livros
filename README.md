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
- **Docker** e **Docker Compose** (recomendado)
- **Node.js e npm** (instalados localmente na máquina, não no container)
  - Recomendado usar **NVM** (Node Version Manager) para gerenciar versões
  - Node.js 20.19.4+ (testado com v20.19.4)
  - npm 10.8.2+ (testado com 10.8.2)
- OU instalação local completa: 
  - PHP 8.3.25+ (testado com 8.3.25)
  - Composer
  - Node.js 20.19.4+ (testado com v20.19.4)
  - npm 10.8.2+ (testado com 10.8.2)
  - MySQL 8.4+ (testado com 8.4)

### Instalação com Docker (Recomendado)

1. **Instale o Node.js localmente (recomendado usar NVM)**

   **Instalando o NVM:**
   ```bash
   # Linux/Mac
   curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.0/install.sh | bash
   
   # Windows (use nvm-windows)
   # Baixe de: https://github.com/coreybutler/nvm-windows/releases
   ```

   **Instalando Node.js com NVM:**
   ```bash
   nvm install 20.19.4
   nvm use 20.19.4
   nvm alias default 20.19.4
   ```

   **Verificando instalação:**
   ```bash
   node -v  # Deve mostrar v20.19.4
   npm -v   # Deve mostrar 10.8.2+
   ```

2. **Clone o repositório**
```bash
git clone https://github.com/elvisbomfim/cadastro-de-livros.git
cd cadastro-de-livros
```

3. **Configure o ambiente (se necessário)**
```bash
cp .env.example .env
```

4. **Execute o script de inicialização**
```bash
chmod +x start.sh
./start.sh
```

O script `start.sh` irá:
- ✅ Iniciar todos os containers (app, nginx, mysql, redis)
- ✅ Instalar dependências do Composer (dentro do container)
- ✅ Instalar dependências do NPM (localmente na sua máquina)
- ✅ Gerar a chave da aplicação
- ✅ Executar as migrations automaticamente

5. **Acesse a aplicação**
- Frontend: http://localhost:8080
- API: http://localhost:8080/api

6. **Execute os seeders (opcional)**
```bash
docker-compose exec app php artisan db:seed
```

7. **Compile os assets (desenvolvimento)**
```bash
# Execute localmente (não dentro do container)
npm run dev
```

8. **Compile os assets (produção)**
```bash
# Execute localmente (não dentro do container)
npm run build
```

### Instalação Local (sem Docker)

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

### Com Docker
```bash
# Todos os testes
docker-compose exec app php artisan test

# Testes específicos
docker-compose exec app php artisan test --filter NomeDoTeste
```

### Sem Docker
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

O projeto segue o padrão **Conventional Commits** e utiliza ferramentas para garantir a conformidade:

#### Tipos de Commit

- `feat`: Nova funcionalidade
- `fix`: Correção de bug
- `docs`: Documentação
- `style`: Formatação, ponto e vírgula faltando, etc
- `refactor`: Refatoração de código
- `perf`: Melhoria de performance
- `test`: Adicionando testes
- `build`: Mudanças no sistema de build
- `ci`: Mudanças na CI
- `chore`: Mudanças no processo de build ou ferramentas auxiliares
- `revert`: Reverter um commit

#### Formato

```
<tipo>[escopo opcional]: <descrição>

[corpo opcional]

[rodapé opcional]
```

#### Exemplos

```bash
feat(relatorio): implementa sistema de relatórios em PDF
fix(api): corrige validação de dados no endpoint de livros
docs(readme): atualiza instruções de instalação
refactor(repository): melhora estrutura do RelatorioRepository
```

#### Usando Commitizen (Recomendado)

Para facilitar a criação de commits no padrão, use o Commitizen:

```bash
npm run commit
```

Isso abrirá um assistente interativo para criar commits seguindo o padrão Conventional Commits.

#### Validação Automática

O projeto utiliza **Husky** e **Commitlint** para validar automaticamente os commits. Se um commit não seguir o padrão, ele será rejeitado com uma mensagem de erro explicativa.

##### O que é o Husky?

**Husky** é uma ferramenta que facilita o uso de Git hooks. Ele permite executar scripts automaticamente em eventos do Git (como antes de fazer commit, push, etc.).

**No projeto:**
- O Husky está configurado para executar o Commitlint antes de cada commit
- Isso garante que todas as mensagens de commit sigam o padrão Conventional Commits
- Se a mensagem não estiver no formato correto, o commit será bloqueado automaticamente

**Como funciona:**
1. Quando você executa `git commit`, o Husky intercepta o comando
2. O hook `commit-msg` é executado automaticamente
3. O Commitlint valida a mensagem do commit
4. Se válida: o commit é aceito
5. Se inválida: o commit é rejeitado com uma mensagem de erro explicando o problema

**Instalação automática:**
O Husky é instalado automaticamente quando você executa `npm install` através do script `prepare` no `package.json`.

### Scripts Disponíveis

#### Com Docker
```bash
# Desenvolvimento frontend (execute localmente)
npm run dev

# Build de produção (execute localmente)
npm run build

# Testes (dentro do container)
docker-compose exec app php artisan test

# Acessar container
docker-compose exec app bash

# Ver logs
docker-compose logs -f

# Parar containers
docker-compose down

# Reiniciar containers
docker-compose restart
```

#### Sem Docker
```bash
# Desenvolvimento frontend
npm run dev

# Build de produção
npm run build

# Testes
php artisan test
```

> **Nota:** Versões testadas: PHP 8.3.25, Node.js v20.19.4, npm 10.8.2 e MySQL 8.4

## 📝 Licença

Este projeto é privado e de uso pessoal.

## 👤 Autor

**Elvis Bomfim de Souza**
- Email: elvis@bravosite.com.br

---

Desenvolvido com ❤️ usando PHP, Laravel e Vue.js
