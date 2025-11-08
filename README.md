# Back-end Challenge — Pastelaria (Prazo: 10/11/2025)

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
