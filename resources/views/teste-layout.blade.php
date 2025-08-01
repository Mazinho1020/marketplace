@extends('layouts.app')

@section('title', 'Teste de Layout')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-body">
                    <h2 class="text-center mb-4">
                        <i class="fas fa-cog text-primary me-2"></i>
                        Teste de Layout - Sistema Marketplace
                    </h2>
                    
                    <div class="alert alert-success">
                        <h5><i class="fas fa-check-circle me-2"></i>Layout Laravel funcionando!</h5>
                        <p class="mb-0">Se você consegue ver esta página, o sistema Laravel + Blade está funcionando corretamente.</p>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6><i class="fas fa-database text-info me-2"></i>Banco de Dados</h6>
                                    <p class="small mb-0">Conectado: <span class="badge bg-success">{{ DB::connection()->getDatabaseName() }}</span></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6><i class="fas fa-code text-warning me-2"></i>Laravel</h6>
                                    <p class="small mb-0">Versão: <span class="badge bg-info">{{ app()->version() }}</span></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6><i class="fas fa-shield-alt text-success me-2"></i>Debug</h6>
                                    <p class="small mb-0">Status: <span class="badge bg-{{ config('app.debug') ? 'warning' : 'success' }}">{{ config('app.debug') ? 'ON' : 'OFF' }}</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="text-center">
                        <h6>Navegação do Sistema:</h6>
                        <div class="btn-group flex-wrap" role="group">
                            <a href="/admin/config" class="btn btn-primary">
                                <i class="fas fa-cog me-1"></i>
                                Configurações
                            </a>
                            <a href="/teste-config" class="btn btn-success">
                                <i class="fas fa-cog me-1"></i>
                                Config Simples
                            </a>
                            <a href="/admin/config-simple" class="btn btn-warning">
                                <i class="fas fa-tools me-1"></i>
                                Config Básica
                            </a>
                            <a href="/login" class="btn btn-secondary">
                                <i class="fas fa-sign-in-alt me-1"></i>
                                Login
                            </a>
                            <a href="/fidelidade" class="btn btn-info">
                                <i class="fas fa-heart me-1"></i>
                                Fidelidade
                            </a>
                            <a href="/teste-layout.html" class="btn btn-warning">
                                <i class="fas fa-vial me-1"></i>
                                Teste HTML
                            </a>
                        </div>
                    </div>
                    
                    @if(session()->has('usuario_id'))
                    <div class="alert alert-info mt-3">
                        <h6><i class="fas fa-user me-2"></i>Sessão Ativa</h6>
                        <p class="mb-0">Usuário: <strong>{{ session('usuario_nome', 'N/A') }}</strong> | Email: <strong>{{ session('usuario_email', 'N/A') }}</strong></p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    console.log('✅ Teste de Layout carregado!');
    console.log('📊 Sessão:', @json(session()->all()));
</script>
@endpush
