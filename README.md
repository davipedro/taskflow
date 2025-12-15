# TaskFlow

Sistema de gestão de tarefas colaborativo desenvolvido com Laravel 12 + Vue 3 + Inertia.js.

## 📋 Índice

- [Características](#-características)
- [Requisitos](#-requisitos)
- [Instalação](#️-instalação)
    - [Instalação Manual](#-instalação-manual)
- [Sistema de Notificações](#-sistema-de-notificações)
    - [Arquitetura de Notificações](#️-arquitetura-de-notificações)
- [Decisões Arquiteturais](#️-decisões-arquiteturais)
    - [Sheets e Dialogs vs. Páginas Dedicadas](#1-sheetsdialogs-vs-páginas-dedicadas-para-crud)
    - [Endpoints para Status e Prioridade](#2-endpoints-dedicados-para-status-e-prioridade)
    - [Estado Inicial Padrão](#3-estado-inicial-padrão-para-tarefas)
    - [Prioridade Padrão](#4-prioridade-padrão-para-tarefas)
    - [Actions e Repositories](#5-arquitetura-com-actions-e-repositories)
    - [Evoluções Futuras](#o-que-faria-diferente-com-mais-tempo)
- [Comandos Úteis](#️-comandos-úteis)
- [Testes](#-testes)
- [Usuário Demo](#-usuário-demo)

---

## 🚀 Características

- ✅ Autenticação completa (registro, login, recuperação de senha)
- ✅ Gestão de projetos (CRUD completo)
- ✅ Gestão de tarefas com estados sequenciais (Pendente → Em Progresso → Concluída)
- ✅ **Atualização rápida de status e prioridade** diretamente na listagem
- ✅ Filtros avançados (status, prioridade, ordenação)
- ✅ Dashboard com estatísticas e tarefas recentes
- ✅ **Notificações por email via queue**
- ✅ Soft delete em projetos e tarefas
- ✅ Autorização baseada em policies
- ✅ Interface com Shadcn/Vue + Tailwind CSS

---

## 📦 Requisitos

- PHP 8.3+
- Composer
- Node.js 18+
- Docker (para Laravel Sail)

---

## ⚙️ Instalação

### 🚀 Setup Rápido (Recomendado - com Docker/Sail)

**Cross-platform (Windows, Mac, Linux):**

```bash
# 1. Clone o repositório
git clone <repository-url>
cd taskflow

# 2. Execute o setup automático
composer run setup:sail
```

**Pronto!** 🎉 O comando acima irá:
- ✅ Copiar `.env.example` → `.env`
- ✅ Instalar dependências PHP (composer)
- ✅ Subir containers Docker (MySQL, Redis, MailPit)
- ✅ Gerar chave da aplicação
- ✅ Executar migrations e seeders
- ✅ Instalar dependências Node.js (npm)
- ✅ Compilar assets frontend

> **⚠️ Se o script automático apresentar erros**, execute os comandos individualmente conforme descrito na seção [Instalação Manual](#-instalação-manual) abaixo.

### 📍 URLs e Acesso

**URLs disponíveis:**
- 🌐 **Aplicação**: http://localhost
- 📧 **MailPit** (emails de teste): http://localhost:8025

**Usuário demo:**
- 📧 Email: `demo@taskflow.com`
- 🔑 Senha: `password`

### 🔧 Iniciar Desenvolvimento

```bash
# Inicia: Servidor Laravel + Queue Worker + Vite (hot reload)
./vendor/bin/sail composer run dev
```

Esse comando único inicia **tudo que você precisa**:
- ✅ Servidor Laravel (http://localhost)
- ✅ Queue Worker (para emails)
- ✅ Vite com hot reload (para frontend)

### 🔄 Reset do Banco de Dados

```bash
# Resetar banco com dados demo
composer run fresh
```

---

## 📦 Instalação Manual

<details>
<summary>Comandos individuais</summary>

### Com Docker (Sail)

```bash
# 1. Clone e entre no diretório
git clone <repository-url>
cd taskflow

# 2. Copiar arquivo de ambiente
cp .env.example .env

# 3. Instalar dependências PHP
composer install --ignore-platform-reqs

# 4. Iniciar containers Docker
./vendor/bin/sail up -d

# 5. Gerar chave da aplicação
./vendor/bin/sail artisan key:generate

# 6. Executar migrations e seeders
./vendor/bin/sail artisan migrate --seed

# 7. Instalar dependências Node.js
./vendor/bin/sail npm install

# 8. Compilar assets
./vendor/bin/sail npm run build

# 9. Iniciar desenvolvimento
./vendor/bin/sail composer run dev
```

### Sem Docker

**Requisitos:** PHP 8.3+, MySQL/PostgreSQL, Composer, Node.js 18+

```bash
# 1. Clone e configure
git clone <repository-url>
cd taskflow
cp .env.example .env

# 2. Configure o banco de dados no .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=taskflow
# DB_USERNAME=seu_usuario
# DB_PASSWORD=sua_senha

# 3. Execute o setup
composer run setup

# 4. Iniciar desenvolvimento
composer run dev
```

</details>

---

## 📧 Sistema de Notificações

O TaskFlow possui um sistema de notificações por email que utiliza **queues** para processamento assíncrono.

### 🔔 Notificações Implementadas

- **Criação de Tarefa**: Email de confirmação enviado ao criador com todos os detalhes da tarefa

### ⚙️ Configuração de Email

#### 📬 Desenvolvimento - **Já configurado!**

O projeto usa **MailPit** via Sail. **Sem configuração adicional necessária**

**Visualize todos os emails enviados:**
```
http://localhost:8025
```

Todos os emails aparecem automaticamente na interface web do MailPit.

### 🔧 Queue Worker

Para que as notificações sejam enviadas, o **queue worker** precisa estar rodando:

#### Desenvolvimento
Caso a queue não tiver sido iniciada via `composer run dev`, inicie manualmente:
```bash
# Iniciar queue worker
./vendor/bin/sail artisan queue:work

# Com configurações adicionais (recomendado)
./vendor/bin/sail artisan queue:work --tries=3 --timeout=60
```

**Importante:** Mantenha o queue worker rodando em um terminal separado enquanto desenvolve.

### 🧪 Testar Notificações

1. **Inicie o queue worker:**
Garanta que o worker esteja rodando:
   ```bash
   ./vendor/bin/sail artisan queue:work
   ```

2. **Crie uma tarefa** via interface web

3. **Verifique o email** no MailPit: http://localhost:8025

### 🔍 Comandos de Monitoramento

```bash
# Ver jobs na fila
./vendor/bin/sail artisan queue:monitor

# Verificar failed jobs
./vendor/bin/sail artisan queue:failed

# Reprocessar failed jobs
./vendor/bin/sail artisan queue:retry all

# Limpar failed jobs
./vendor/bin/sail artisan queue:flush

# Parar workers gracefully
./vendor/bin/sail artisan queue:restart
```

### 🏗️ Arquitetura de Notificações

```
User cria tarefa
    ↓
TaskController::store()
    ↓
StoreTaskRequest (validação)
    ↓
StoreTaskAction::handle()
    ↓
TaskRepository::create() → Task salvo no banco de dados
    ↓
TaskCreated::dispatch($task) → Event disparado
    ↓
SendTaskCreatedNotification → Listener (auto-discovery)
    ↓
SendTaskNotificationJob::dispatch() → Job enfileirado
    ↓
[Queue Worker processa]
    ↓
TaskCreatedMail enviado via SMTP
    ↓
Email recebido
```

**Componentes:**
- **Event**: `TaskCreated` - Disparado ao criar tarefa
- **Listener**: `SendTaskCreatedNotification` - Orquestra a notificação
- **Job**: `SendTaskNotificationJob` - Processa envio assíncrono
- **Mailable**: `TaskCreatedMail` - Template do email
- **View**: `emails.task-created` - HTML do email

---

## 🏗️ Decisões Arquiteturais

### 1. Sheets/Dialogs vs. Páginas Dedicadas para CRUD

**Decisão:** Utilizar componentes `Sheet` (sidebar) e `Dialog` (modal) para criar e editar tarefas, em vez de navegar para páginas dedicadas como `/tasks/create` ou `/tasks/edit/:id`.

**Justificativa:**
- **Contexto do Usuário:** As tarefas são ligadas a projetos. Manter o usuário na página do projeto enquanto gerencia tarefas oferece uma experiência mais fluida e contextual.
- **Eficiência:** Ações rápidas como criar ou editar uma tarefa não exigem um ciclo completo de navegação, o que torna a interface mais ágil.
- **Complexidade Reduzida:** Evita a necessidade de gerenciar o estado da UI entre diferentes páginas, como lembrar de qual projeto o usuário veio.

**Implementação:**
- **Criação e Edição:** Um componente `TaskForm.vue` é renderizado dentro de um `Sheet` (sidebar), que é acionado a partir da página de visualização do projeto (`Projects/Show.vue`) ou da listagem de tarefas.
- **Visualização Rápida:** Um `Dialog` (modal) é usado para exibir os detalhes de uma tarefa sem sair da lista.
- **Rotas de API:** O backend expõe rotas de API RESTful (`POST /projects/{project}/tasks`, `PUT /tasks/{task}`), que são consumidas de forma assíncrona pelos componentes Vue.

**Trade-offs:**

| Aspecto | Sheet/Dialog ✅ | Páginas Dedicadas ❌ |
|---|---|---|
| **Experiência do Usuário (UX)** | Ações rápidas e fluidas, sem perda de contexto. | Navegação disruptiva, requer recarregamento de páginas. |
| **Performance Percebida** | Mais rápido, pois apenas os dados do modal são carregados. | Carregamento de página inteira, potencialmente mais lento. |
| **Deep Linking** | Não há URLs diretas para formulários de edição/criação. | Cada ação possui uma URL única, facilitando o compartilhamento. |
| **Complexidade do Frontend** | Gerenciamento de estado local na página (abrir/fechar modais). | Lógica de UI mais simples, mas mais arquivos de página. |

**Consequências:**
- ✅ Interface de usuário mais moderna e ágil para as operações comuns do sistema.
- ✅ Código do frontend mais componentizado e reutilizável.
- ⚠️ A ausência de URLs dedicadas para os formulários impede o compartilhamento direto de um link para "criar nova tarefa".

---

### 2. Endpoints Dedicados para Status e Prioridade

**Decisão:** Utilizar endpoints dedicados (`PATCH /tasks/{task}/status` e `PATCH /tasks/{task}/priority`) para atualizar o status e a prioridade das tarefas, separadamente do endpoint de edição geral (`PUT /tasks/{task}`).

**Justificativa:**
- **Granularidade:** Mudar o status ou a prioridade são ações de comando com regras de negócio específicas e ocorrem com alta frequência. Separar os endpoints reflete essa distinção.
- **Performance:** Permite o envio de payloads mínimos (ex: `{"status": "in_progress"}`), reduzindo o tráfego de dados.
- **Intenção Clara:** A rota `PATCH /tasks/.../status` deixa a intenção da operação explícita.

**Implementação:**
- **Rotas:** Definição de rotas `patch` específicas em `routes/web.php`.
- **Controller:** Criação de métodos dedicados no `TaskController` (`updateStatus`, `updatePriority`).
- **Actions:** Implementação de `UpdateTaskStatusAction` e `UpdateTaskPriorityAction` para encapsular a lógica de negócio.
- **Frontend:** Componentes como menus dropdown invocam diretamente esses endpoints, proporcionando feedback instantâneo sem abrir o formulário de edição.

**Trade-offs:**

| Aspecto | Endpoints Dedicados ✅ | Endpoint Geral ❌ |
|---|---|---|
| **Lógica de Negócio** | Validação e autorização focadas por ação. | Lógica monolítica e condicional no `UpdateTaskRequest`. |
| **Flexibilidade da UI** | Facilita a criação de interações como arrastar e soltar (Kanban). | Limita as interações a formulários. |
| **Complexidade** | Aumenta o número de rotas e métodos no controller. | Menos endpoints para gerenciar. |

**Consequências:**
- ✅ UI interativa e eficiente para as ações frequentes do usuário.
- ✅ Lógica de transição de estado mais clara e isolada, facilitando testes e manutenção.
- ✅ Base para futuras implementações, como um quadro Kanban.

---

### 3. Estado Inicial Padrão para Tarefas

**Decisão:** Toda nova tarefa é criada com o status `PENDING`. O usuário não pode definir o status durante a criação.

**Justificativa:**
- **Consistência do Ciclo de Vida:** Garante um fluxo de trabalho previsível e uniforme.
- **Prevenção de Erros:** Impede que os usuários criem tarefas em estados incoerentes (por exemplo, uma tarefa já "Concluída").
- **Simplicidade:** Simplifica o formulário de criação ao remover uma decisão do usuário.

**Implementação:**
- **Base de Dados:** O campo `status` na tabela `tasks` tem o valor de `TaskStatus::PENDING` definido como padrão na migration.
- **Action de Criação:** A `StoreTaskAction` garante que o status seja `PENDING`, reforçando a regra de negócio na camada de aplicação.
- **Form Request:** A `StoreTaskRequest` não inclui `status` como um campo validável.

**Trade-offs:**

| Aspecto | Forçar `PENDING` ✅ | Permitir Escolha ❌ |
|---|---|---|
| **Integridade dos Dados** | Garante um fluxo de trabalho consistente. | Risco de estados iniciais inválidos. |
| **Experiência do Usuário** | Formulário mais simples e direto. | Mais um campo para o usuário decidir. |
| **Flexibilidade** | Menos flexível, mas reforça uma regra de negócio. | Mais flexível, mas pode levar a um uso incorreto. |

**Consequências:**
- ✅ O ciclo de vida da tarefa é padronizado, simplificando a lógica de negócio, testes e a UI.
- ✅ Relatórios e dashboards se tornam mais consistentes.

---

### 4. Prioridade Padrão para Tarefas

**Decisão:** Toda nova tarefa recebe a prioridade `MEDIUM` por padrão, a menos que o usuário especifique outra.

**Justificativa:**
- **Prevenção de Nulos:** Evita que tarefas sejam criadas sem prioridade, o que complicaria a lógica de ordenação e a UI.
- **Padrão Neutro:** `MEDIUM` é um valor de partida equilibrado.
- **Redução de Carga Cognitiva:** Torna a definição de prioridade uma ação opcional durante a criação.

**Implementação:**
- **Base de Dados:** A migration da tabela `tasks` define `TaskPriority::MEDIUM` como o valor padrão.
- **Form Request:** A `StoreTaskRequest` também define `MEDIUM` como o valor padrão na camada de validação.
- **Frontend:** O formulário de criação de tarefa pré-seleciona a opção "Medium".

**Trade-offs:**

| Aspecto | Padrão `MEDIUM` ✅ | Sem Padrão / Permitir `null` ❌ |
|---|---|---|
| **Consistência de Dados** | Garante que todas as tarefas tenham uma prioridade. | Leva a tarefas com prioridade nula, exigindo tratamento especial. |
| **Lógica de Backend** | Ordenação e filtragem são mais simples. | Requer lógicas condicionais ou `COALESCE` em queries. |
| **Experiência do Usuário** | Simplifica o formulário, tornando a priorização opcional. | Força uma decisão ou cria tarefas "não priorizadas". |

**Consequências:**
- ✅ Todas as tarefas são organizáveis e comparáveis por prioridade.
- ✅ O processo de criação de tarefas é mais rápido.

---

### 5. Arquitetura com Actions e Repositories

**Decisão:** Adotar um padrão arquitetural que utiliza `Actions` para orquestrar a lógica de negócio e `Repositories` para abstrair o acesso aos dados.

**Justificativa:**
- **Separation of Concerns (SoC):** Isola as responsabilidades: `Controllers` lidam com HTTP, `Actions` com a lógica de negócio, e `Repositories` com a persistência de dados.
- **Reutilização e DRY (Don't Repeat Yourself):** Uma `Action` pode ser reutilizada por diferentes partes do sistema, evitando duplicação de código.
- **Testabilidade:** A arquitetura facilita testes unitários e de integração.
- **Manutenibilidade:** O código se torna mais organizado e previsível.

**Implementação:**
- **Fluxo Típico:** `Controller` → `FormRequest` (Validação) → `Action` (Lógica) → `Repository` (Dados) → `Model` (Eloquent).
- **Actions:** Classes em `app/Actions/` com um método `handle()`, encapsulando uma operação de negócio.
- **Repositories:** Classes em `app/Repositories/` que implementam uma interface e contêm as queries Eloquent.
- **Injeção de Dependência:** O `AppServiceProvider` faz o *bind* das interfaces aos seus repositórios concretos.

**Trade-offs:**

| Aspecto | Actions + Repositories ✅ | Lógica em Controllers/Models ❌ |
|---|---|---|
| **Organização** | Código estruturado, escalável e previsível. | "Fat Controllers" e "Fat Models", difíceis de manter. |
| **Testabilidade** | Permite testes focados de unidades lógicas. | Difícil de testar isoladamente. |
| **Curva de Aprendizagem** | Requer compreensão do padrão; mais arquivos a serem criados. | Mais direto para projetos pequenos, mas não escala bem. |

**Consequências:**
- ✅ Base de código desacoplada e que segue princípios do SOLID.
- ✅ Facilita a colaboração em equipe e a integração de novos desenvolvedores.
- ✅ Aumento da confiabilidade do sistema através de uma suíte de testes abrangente.

---

    ### O que faria diferente com mais tempo

Com mais tempo, o foco seria evoluir a aplicação para uma ferramenta colaborativa mais robusta e dinâmica. As prioridades seriam:

1.  **Quadro Kanban Interativo:** 
Implementar uma visualização com *drag-and-drop* para alterar o status das tarefas. Isso utilizaria os endpoints de API dedicados já existentes e seria combinado com uma UI Otimista para uma experiência de usuário mais fluida.

2.  **Histórico de Atividades (Auditoria):** 
Introduzir um log de auditoria para tarefas, registrando automaticamente as alterações através de *Model Observers* do Laravel. A funcionalidade aumentaria a transparência sobre o progresso e as modificações em cada tarefa.

3.  **Refatoração para State Machine:** 
Substituir a lógica de transição de status por um padrão de projeto de Máquina de Estados. Tecnicamente, essa abordagem torna as regras do fluxo de trabalho mais explícitas, seguras e fáceis de manter ou expandir no futuro.
> Referência: [Refactoring Guru - State Machine](https://refactoring.guru/design-patterns/state)

## 🛠️ Comandos Úteis

### Sail (Docker)
```bash
# Subir containers
./vendor/bin/sail up -d

# Parar containers
./vendor/bin/sail down

# Ver logs
./vendor/bin/sail logs

# Acessar container
./vendor/bin/sail shell
```

### Artisan
```bash
# Migrations
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan migrate:fresh --seed

# Cache
./vendor/bin/sail artisan cache:clear
./vendor/bin/sail artisan config:clear
./vendor/bin/sail artisan route:clear

# Tinker (REPL)
./vendor/bin/sail artisan tinker
```

### Frontend
```bash
# Desenvolvimento (watch mode)
npm run dev

# Build para produção
npm run build

# Linting
npm run lint
```

### Code Quality
```bash
# Laravel Pint (formatação)
./vendor/bin/sail pint

# Verificar sem corrigir
./vendor/bin/sail pint --test
```

---

## 🧪 Testes

### Executar Todos os Testes
```bash
./vendor/bin/sail artisan test
```

### Executar Testes Específicos
```bash
# Por arquivo
./vendor/bin/sail artisan test tests/Feature/TaskControllerTest.php

# Por filtro
./vendor/bin/sail artisan test --filter=TaskNotification

# Com coverage
./vendor/bin/sail artisan test --coverage
```

### Suíte de Testes

**Total: 109 Feature Tests** distribuídos em:
- 21 testes de autenticação (login, registro, verificação de email, redefinição de senha)
- 20 testes de projetos (CRUD completo com autorização)
- 20 testes de tarefas (CRUD completo com autorização)
- 12 testes de filtros e ordenação
- 8 testes de políticas de autorização
- 8 testes de configurações (perfil e senha)
- 7 testes de transição de status
- 7 testes de dashboard e estatísticas
- 5 testes de notificações (Event, Job, Email)
- 1 teste de página inicial

---

## 👤 Usuário Demo

### Credenciais
- **Email**:
```
demo@taskflow.com
```
- **Password**:
```
password
```

### Dados Demo
O seeder cria automaticamente:
- 1 usuário demo
- 3 projetos de exemplo
- 15+ tarefas com diferentes status e prioridades

