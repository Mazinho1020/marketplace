@extends('layouts.app')

@section('title', 'Configurações - Teste Simples')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-cog me-2"></i>Configurações do Sistema - Versão Simplificada</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>Teste de Configurações</h6>
                        <p class="mb-0">Esta é uma versão simplificada da página de configurações para teste.</p>
                    </div>
                    
                    @if(!empty($configsByGroup))
                        <div class="row">
                            @foreach($configsByGroup as $groupName => $groupConfigs)
                                <div class="col-md-6 mb-4">
                                    <div class="card border">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">
                                                <i class="{{ $groupConfigs[0]->grupo_icone ?? 'fas fa-cog' }} me-2"></i>
                                                {{ $groupName ?? 'Sem Grupo' }}
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach($groupConfigs as $config)
                                                <div class="mb-3 border-bottom pb-2">
                                                    <label class="form-label">
                                                        <strong>{{ $config->chave }}</strong>
                                                        @if($config->obrigatorio)
                                                            <span class="text-danger">*</span>
                                                        @endif
                                                    </label>
                                                    
                                                    @if($config->tipo === 'boolean')
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input" 
                                                                   {{ $config->valor ? 'checked' : '' }} disabled>
                                                            <span class="form-check-label">
                                                                {{ $config->valor ? 'Sim' : 'Não' }}
                                                            </span>
                                                        </div>
                                                    @else
                                                        <input type="text" class="form-control form-control-sm" 
                                                               value="{{ $config->valor ?? $config->valor_padrao ?? '' }}" 
                                                               readonly>
                                                    @endif
                                                    
                                                    @if($config->descricao)
                                                        <small class="text-muted">{{ $config->descricao }}</small>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <h6><i class="fas fa-exclamation-triangle me-2"></i>Nenhuma configuração encontrada</h6>
                            <p class="mb-0">Não foram encontradas configurações para exibir.</p>
                        </div>
                    @endif
                    
                    <div class="text-center mt-4">
                        <a href="/admin/config" class="btn btn-primary">
                            <i class="fas fa-cog me-1"></i>
                            Ver Página Completa
                        </a>
                        <a href="/teste-layout" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>
                            Voltar ao Teste
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
