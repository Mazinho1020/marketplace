@extends('layouts.comerciante')

@section('title', 'Pagamento Cancelado')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-times-circle fa-5x text-danger"></i>
                    </div>
                    <h2 class="text-danger mb-3">Pagamento Cancelado</h2>
                    <p class="lead mb-4">
                        O pagamento foi cancelado. Você pode tentar novamente a qualquer momento.
                    </p>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <a href="{{ route('comerciantes.planos.planos') }}" class="btn btn-primary">
                            <i class="fas fa-redo me-2"></i>Tentar Novamente
                        </a>
                        <a href="{{ route('comerciantes.planos.dashboard') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Voltar ao Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection