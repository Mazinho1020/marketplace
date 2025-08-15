@extends('layouts.comerciante')

@section('title', 'Pagamento Realizado com Sucesso')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-check-circle fa-5x text-success"></i>
                    </div>
                    <h2 class="text-success mb-3">Pagamento Realizado!</h2>
                    <p class="lead mb-4">
                        Seu plano foi ativado com sucesso. Agora você pode aproveitar todos os recursos disponíveis.
                    </p>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <a href="{{ route('comerciantes.planos.dashboard') }}" class="btn btn-primary">
                            <i class="fas fa-tachometer-alt me-2"></i>Ir para Dashboard
                        </a>
                        <a href="{{ route('comerciantes.dashboard') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-home me-2"></i>Página Inicial
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection