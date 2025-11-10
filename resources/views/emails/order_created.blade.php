<x-mail::message>
# Novo Pedido Criado

Olá {{ $order->customer->name }},
O seu pedido {{ $order->id }} foi criado com sucesso.

<x-mail::button :url="''">
Ver Pedido
</x-mail::button>

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>
