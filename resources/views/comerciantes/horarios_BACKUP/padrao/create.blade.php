@extends('comerciantes.layout')

@section('title', 'Novo Horário Padrão')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="fas fa-plus mr-2"></i>Novo Horário Padrão</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('comerciantes.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('comerciantes.horarios.index') }}">Horários</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('comerciantes.horarios.padrao') }}">Padrão</a></li>
                            <li class="breadcrumb-item active">Novo</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('comerciantes.horarios.padrao') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulário -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-week"></i> Informações do Horário
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('comerciantes.horarios.padrao.store') }}">
                        @csrf

                        <!-- Dia da Semana -->
                        <div class="form-group">
                            <label for="dia_semana_id" class="form-label required">Dia da Semana</label>
                            <select class="form-control @error('dia_semana_id') is-invalid @enderror" 
                                    id="dia_semana_id" name="dia_semana_id" required>
                                <option value="">Selecione o dia da semana</option>
                                @foreach($diasSemana as $dia)
                                    <option value="{{ $dia->id }}" {{ old('dia_semana_id') == $dia->id ? 'selected' : '' }}>
                                        {{ $dia->nome }}
                                    </option>
                                @endforeach
                            </select>
                            @error('dia_semana_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Sistema -->
                        <div class="form-group">
                            <label for="sistema" class="form-label required">Sistema</label>
                            <select class="form-control @error('sistema') is-invalid @enderror" 
                                    id="sistema" name="sistema" required>
                                <option value="">Selecione o sistema</option>
                                @foreach($sistemas as $sist)
                                    <option value="{{ $sist }}" {{ old('sistema') == $sist ? 'selected' : '' }}>
                                        {{ $sist }}
                                    </option>
                                @endforeach
                            </select>
                            @error('sistema')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                TODOS = Horário geral | PDV = Ponto de Venda | FINANCEIRO = Sistema Financeiro | ONLINE = Loja Virtual
                            </small>
                        </div>

                        <!-- Status de Funcionamento -->
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" 
                                       id="aberto" name="aberto" value="1" 
                                       {{ old('aberto') ? 'checked' : '' }}
                                       onchange="toggleHorarios()">
                                <label class="custom-control-label" for="aberto">
                                    <strong>Funcionamento Ativo</strong>
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                Marque se o estabelecimento funciona neste dia/sistema
                            </small>
                        </div>

                        <!-- Horários (mostrado apenas se aberto) -->
                        <div id="horariosContainer" style="display: none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="hora_abertura" class="form-label required">Horário de Abertura</label>
                                        <input type="time" class="form-control @error('hora_abertura') is-invalid @enderror" 
                                               id="hora_abertura" name="hora_abertura" 
                                               value="{{ old('hora_abertura') }}">
                                        @error('hora_abertura')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="hora_fechamento" class="form-label required">Horário de Fechamento</label>
                                        <input type="time" class="form-control @error('hora_fechamento') is-invalid @enderror" 
                                               id="hora_fechamento" name="hora_fechamento" 
                                               value="{{ old('hora_fechamento') }}">
                                        @error('hora_fechamento')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Observações -->
                        <div class="form-group">
                            <label for="observacoes" class="form-label">Observações</label>
                            <textarea class="form-control @error('observacoes') is-invalid @enderror" 
                                      id="observacoes" name="observacoes" rows="3" 
                                      placeholder="Observações adicionais (opcional)">{{ old('observacoes') }}</textarea>
                            @error('observacoes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Botões -->
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Salvar Horário
                            </button>
                            <a href="{{ route('comerciantes.horarios.padrao') }}" class="btn btn-secondary ml-2">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Informações Laterais -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle"></i> Dicas
                    </h6>
                </div>
                <div class="card-body">
                    <h6>Sistemas Disponíveis:</h6>
                    <ul class="list-unstyled">
                        <li><strong>TODOS:</strong> Horário geral da empresa</li>
                        <li><strong>PDV:</strong> Sistema de Ponto de Venda</li>
                        <li><strong>FINANCEIRO:</strong> Módulo financeiro</li>
                        <li><strong>ONLINE:</strong> Loja virtual/online</li>
                    </ul>

                    <hr>

                    <h6>Como Funciona:</h6>
                    <ul class="list-unstyled small">
                        <li>• Configure horários diferentes para cada sistema</li>
                        <li>• Horários se repetem semanalmente</li>
                        <li>• Exceções sobrescrevem horários padrão</li>
                        <li>• Sistema específico tem prioridade sobre "TODOS"</li>
                    </ul>
                </div>
            </div>

            <!-- Horários Existentes -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-list"></i> Horários Cadastrados
                    </h6>
                </div>
                <div class="card-body">
                    <p class="small text-muted">Verificar conflitos antes de salvar</p>
                    
                    <!-- Aqui poderia haver uma lista dos horários já cadastrados -->
                    <div class="text-center text-muted">
                        <i class="fas fa-clock fa-2x mb-2"></i>
                        <p class="small">Lista será carregada dinamicamente</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function toggleHorarios() {
    const aberto = document.getElementById('aberto').checked;
    const container = document.getElementById('horariosContainer');
    const horaAbertura = document.getElementById('hora_abertura');
    const horaFechamento = document.getElementById('hora_fechamento');
    
    if (aberto) {
        container.style.display = 'block';
        horaAbertura.required = true;
        horaFechamento.required = true;
    } else {
        container.style.display = 'none';
        horaAbertura.required = false;
        horaFechamento.required = false;
        horaAbertura.value = '';
        horaFechamento.value = '';
    }
}

// Inicializar estado dos horários
document.addEventListener('DOMContentLoaded', function() {
    toggleHorarios();
});

// Validação de horários
document.getElementById('hora_fechamento').addEventListener('change', function() {
    const abertura = document.getElementById('hora_abertura').value;
    const fechamento = this.value;
    
    if (abertura && fechamento && abertura >= fechamento) {
        alert('O horário de fechamento deve ser posterior ao de abertura!');
        this.focus();
    }
});
</script>
@endsection
