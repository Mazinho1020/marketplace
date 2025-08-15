@extends('layouts.comerciante')

@section('title', 'Nova Exceção de Horário')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-plus text-warning"></i>
                        Nova Exceção de Horário
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('comerciantes.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('comerciantes.horarios.index', $empresaId) }}">Horários</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('comerciantes.horarios.excecoes.index', $empresaId) }}">Exceções</a></li>
                            <li class="breadcrumb-item active">Nova</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('comerciantes.horarios.excecoes.index', $empresaId) }}" 
                       class="btn btn-secondary">
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
                        <i class="fas fa-calendar-alt"></i> Configurar Exceção
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('comerciantes.horarios.excecoes.store', $empresaId) }}">
                        @csrf

                        <!-- Data Específica -->
                        <div class="form-group mb-3">
                            <label for="data_excecao" class="form-label required">Data da Exceção</label>
                            <input type="date" 
                                   class="form-control @error('data_excecao') is-invalid @enderror" 
                                   id="data_excecao" 
                                   name="data_excecao" 
                                   value="{{ old('data_excecao') }}"
                                   min="{{ now()->toDateString() }}"
                                   required>
                            @error('data_excecao')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Selecione a data específica para esta exceção
                            </small>
                        </div>

                        <!-- Sistema -->
                        <div class="form-group mb-3">
                            <label for="sistema" class="form-label required">Sistema</label>
                            <select class="form-control @error('sistema') is-invalid @enderror" 
                                    id="sistema" name="sistema" required>
                                <option value="">Selecione o sistema</option>
                                @foreach($sistemas as $sistema)
                                    <option value="{{ $sistema }}" {{ old('sistema') == $sistema ? 'selected' : '' }}>
                                        {{ $sistema }}
                                    </option>
                                @endforeach
                            </select>
                            @error('sistema')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                TODOS: horário geral | PDV: atendimento presencial | ONLINE: loja virtual | FINANCEIRO: departamento financeiro
                            </small>
                        </div>

                        <!-- Status Fechado -->
                        <div class="form-group mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="fechado" name="fechado" 
                                       onchange="toggleHorarios()" {{ old('fechado') ? 'checked' : '' }}>
                                <label class="form-check-label" for="fechado">
                                    Empresa fechada nesta data
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                Marque se a empresa não funcionará nesta data (ex: feriado)
                            </small>
                        </div>

                        <!-- Horários (só aparece se não estiver fechado) -->
                        <div id="horarios-container">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="hora_abertura" class="form-label required">Hora de Abertura</label>
                                        <input type="time" 
                                               class="form-control @error('hora_abertura') is-invalid @enderror" 
                                               id="hora_abertura" 
                                               name="hora_abertura" 
                                               value="{{ old('hora_abertura') }}"
                                               required>
                                        @error('hora_abertura')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="hora_fechamento" class="form-label required">Hora de Fechamento</label>
                                        <input type="time" 
                                               class="form-control @error('hora_fechamento') is-invalid @enderror" 
                                               id="hora_fechamento" 
                                               name="hora_fechamento" 
                                               value="{{ old('hora_fechamento') }}"
                                               required>
                                        @error('hora_fechamento')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Observações -->
                        <div class="form-group mb-4">
                            <label for="observacoes" class="form-label">Observações</label>
                            <textarea class="form-control @error('observacoes') is-invalid @enderror" 
                                      id="observacoes" 
                                      name="observacoes" 
                                      rows="3"
                                      placeholder="Motivo da exceção (ex: Natal, Ano Novo, Evento especial, etc.)">{{ old('observacoes') }}</textarea>
                            @error('observacoes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Botões -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('comerciantes.horarios.excecoes.index', $empresaId) }}" 
                               class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Salvar Exceção
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Ajuda -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-question-circle"></i> Sobre Exceções
                    </h6>
                </div>
                <div class="card-body">
                    <h6>O que são exceções?</h6>
                    <p class="small text-muted">
                        Exceções são horários especiais que <strong>substituem</strong> temporariamente 
                        os horários padrão em datas específicas.
                    </p>

                    <h6>Exemplos de uso:</h6>
                    <ul class="small">
                        <li><strong>Feriados:</strong> Natal, Ano Novo, etc.</li>
                        <li><strong>Eventos:</strong> Black Friday com horário estendido</li>
                        <li><strong>Manutenção:</strong> Fechamento para reformas</li>
                        <li><strong>Horários especiais:</strong> Finais de semana diferenciados</li>
                    </ul>

                    <div class="alert alert-info small">
                        <i class="fas fa-info-circle"></i>
                        <strong>Importante:</strong> As exceções sempre têm prioridade sobre os horários padrão.
                    </div>
                </div>
            </div>

            <!-- Exemplos de Exceções Comuns -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-calendar"></i> Exemplos Comuns
                    </h6>
                </div>
                <div class="card-body">
                    <div class="small">
                        <strong>Natal (25/12):</strong><br>
                        Sistema: TODOS<br>
                        Status: Fechado<br>
                        Obs: Feriado Nacional
                        <hr>
                        
                        <strong>Black Friday:</strong><br>
                        Sistema: ONLINE<br>
                        Horário: 00:00 às 23:59<br>
                        Obs: Promoção especial
                        <hr>
                        
                        <strong>Véspera de Ano Novo:</strong><br>
                        Sistema: TODOS<br>
                        Horário: 08:00 às 14:00<br>
                        Obs: Meio expediente
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleHorarios() {
    const fechado = document.getElementById('fechado').checked;
    const container = document.getElementById('horarios-container');
    const horaAbertura = document.getElementById('hora_abertura');
    const horaFechamento = document.getElementById('hora_fechamento');
    
    if (fechado) {
        container.style.display = 'none';
        horaAbertura.required = false;
        horaFechamento.required = false;
        horaAbertura.value = '';
        horaFechamento.value = '';
    } else {
        container.style.display = 'block';
        horaAbertura.required = true;
        horaFechamento.required = true;
    }
}

// Executar na carga da página
document.addEventListener('DOMContentLoaded', function() {
    toggleHorarios();
});
</script>
@endpush
