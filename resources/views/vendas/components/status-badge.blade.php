@php
$statusConfig = [
    'rascunho' => ['class' => 'bg-secondary', 'text' => 'Rascunho', 'icon' => 'fa-edit'],
    'pendente' => ['class' => 'bg-warning', 'text' => 'Pendente', 'icon' => 'fa-clock'],
    'confirmado' => ['class' => 'bg-success', 'text' => 'Confirmado', 'icon' => 'fa-check'],
    'processando' => ['class' => 'bg-info', 'text' => 'Processando', 'icon' => 'fa-cogs'],
    'separando' => ['class' => 'bg-primary', 'text' => 'Separando', 'icon' => 'fa-boxes'],
    'enviado' => ['class' => 'bg-purple', 'text' => 'Enviado', 'icon' => 'fa-truck'],
    'entregue' => ['class' => 'bg-teal', 'text' => 'Entregue', 'icon' => 'fa-check-circle'],
    'cancelado' => ['class' => 'bg-danger', 'text' => 'Cancelado', 'icon' => 'fa-times-circle'],
    'devolvido' => ['class' => 'bg-dark', 'text' => 'Devolvido', 'icon' => 'fa-undo'],
];

$config = $statusConfig[$status] ?? ['class' => 'bg-light text-dark', 'text' => ucfirst($status), 'icon' => 'fa-question'];
@endphp

<span class="badge {{ $config['class'] }}">
    <i class="fa {{ $config['icon'] }} me-1"></i>
    {{ $config['text'] }}
</span>