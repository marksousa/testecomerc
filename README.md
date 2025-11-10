# Back-end Challenge — Pastelaria (Prazo: 10/11/2025)

## Como inicializar o projeto

Abaixo passos para rodar o projeto localmente usando Laravel Sail (Dockerizado).

Pré-requisitos: Docker e docker-compose.

1. Clonar o repositório

```bash
git clone <seu-repositorio> .
```

2. Instalar dependências

```bash
./vendor/bin/sail composer install
```

3. Copiar .env e gerar APP_KEY

```bash
cp .env.example .env
./vendor/bin/sail artisan key:generate
```

4. Subir containers

```bash
./vendor/bin/sail up -d
```

5. Executar migrations e seeders

```bash
# com sail
./vendor/bin/sail artisan migrate --seed
```

6. Criar link simbólico para storage

```bash
./vendor/bin/sail artisan storage:link
```

7. Preparar diretório de testes para uploads fake
   Alguns testes usam Storage::fake('public') e salvam em storage/framework/testing/disks/public. Se houver erro UnableToCreateDirectory crie o diretório e ajuste permissões:

```bash
./vendor/bin/sail exec laravel.test mkdir -p storage/framework/testing/disks/public
./vendor/bin/sail exec laravel.test chmod -R 777 storage
```

8. Ajustar variáveis de mail para desenvolvimento/testes
   No .env, defina:

```
MAIL_MAILER=log
```

Isso evita envio real de e‑mails em ambiente local/testes.

9. Rodar testes

```bash
./vendor/bin/sail test
```

10. Rodar a aplicação (desenvolvimento)

```bash
./vendor/bin/sail up -d
acessar http://localhost
```

Comandos úteis resumidos

-   Subir containers: ./vendor/bin/sail up -d
-   Parar containers: ./vendor/bin/sail down
-   Rodar testes: ./vendor/bin/sail test
-   Rodar migrations: ./vendor/bin/sail artisan migrate
-   Criar storage testing dir: ./vendor/bin/sail exec laravel.test mkdir -p storage/framework/testing/disks/public

## Visão geral

A necessidade é desenvolver uma API RESTFul para o gerenciamento de pedidos de uma pastelaria utilizando o framework Laravel ou Lúmen.

Não entregar sem fazer todos os testes unitários do início ao fim, foque em testar tudo na aplicação.

## Instruções para entrega

-   Versione, com git, e hospede seu código em algum serviço de sua preferência: github, bitbucket, gitlab ou outro.
-   Crie um README com instruções claras sobre como executar sua obra.
-   Envie um e-mail com o link do seu repositório para andre.silva1@dexian.com
-   Dúvidas podem ser enviadas para o mesmo e-mail acima.

## Sobre o projeto

A API Restful deve contemplar os módulos Cliente, Produto e Pedido, sendo que cada um deverá conter endpoints CRUDL.

As tabelas devem conter as seguintes informações:

-   Clientes nome, e-mail, telefone, data de nascimento, endereço, complemento, bairro, cep, data de cadastro;
-   Produtos nome, preço, foto;
-   Pedidos código do cliente, código do produto, data da criação;

## Requisitos

-   Não devem existir dois clientes com o mesmo e-mail.
-   O produto deve possuir foto.
-   Os dados devem ser validados.
-   O sistema deve conter uma série de tipos de produtos já definidos.
-   O pedido deve contemplar N produtos.
-   O cliente pode contemplar N pedidos.
-   Após a criação do pedido o sistema deve disparar um e-mail para o cliente contendo os detalhes do seu pedido.
-   Os registros devem conter a funcionalidade de soft deleting.
-   Padronização PSR
-   Nomenclatura de classes, métodos e rotas no padrão americano.
-   Testes unitários.
-   Dockerizar a aplicação

## Critérios de avaliação

-   Profundidade do conhecimento e utilização das funcionalidades do framework.
-   Organização do código.
-   Padronização PSR
-   Fidelidade aos requisitos solicitados.
-   Testes Unitários
