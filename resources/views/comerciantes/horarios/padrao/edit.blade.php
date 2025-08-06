@extends('comerciantes.layout')

@section('title', 'Editar Horário Padrão')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-edit text-primary"></i>
                        Editar Horário Padrão
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('comerciantes.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('comerciantes.horarios.index', $empresaId) }}">Horários</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('comerciantes.horarios.padrao.index', $empresaId) }}">Padrão</a></li>
                            <li class="breadcrumb-item active">Editar</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('comerciantes.horarios.padrao.index', $empresaId) }}" 
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
                        <i class="fas fa-calendar-week"></i> Editar Horário
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('comerciantes.horarios.padrao.update', [$empresaId, $horario->id]) }}">
                        @csrf
                        @method('PUT')

                        <!-- Dia da Semana -->
                        <div class="form-group mb-3">
                            <label for="dia_semana" class="form-label required">Dia da Semana</label>
                            <select class="form-control @error('dia_semana') is-invalid @enderror" 
                                    id="dia_semana" name="dia_semana" required>
                                <option value="">Selecione o dia da semana</option>
                                @foreach($diasSemana as $numero => $nome)
                                    <option value="{{ $numero }}" 
                                            {{ (old('dia_semana') ?? $horario->dia_semana) == $numero ? 'selected' : '' }}>
                                        {{ $nome }}
                                    </option>
                                @endforeach
                            </select>
                            @error('dia_semana')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Sistema -->
                        <div class="form-group mb-3">
                            <label for="sistema" class="form-label required">Sistema</label>
                            <select class="form-control @error('sistema') is-invalid @enderror" 
                                    id="sistema" name="sistema" required>
                                <option value="">Selecione o sistema</option>
                                @foreach($sistemas as $sistema)
                                    <option value="{{ $sistema }}" 
                                            {{ (old('sistema') ?? $horario->sistema) == $sistema ? 'selected' : '' }}>
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
                                       onchange="toggleHorarios()" 
                                       {{ (old('fechado') ?? $horario->fechado) ? 'checked' : '' }}>
                                <label class="form-check-label" for="fechado">
                                    Empresa fechada neste dia
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                Marque se a empresa não funciona neste dia
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
                                               value="{{ old('hora_abertura') ?? $horario->hora_abertura }}"
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
                                               value="{{ old('hora_fechamento') ?? $horario->hora_fechamento }}"
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
                                      placeholder="Observações sobre este horário (opcional)">{{ old('observacoes') ?? $horario->observacoes }}</textarea>
                            @error('observacoes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Botões -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('comerciantes.horarios.padrao.index', $empresaId) }}" 
                               class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Atualizar Horário
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Informações do Horário -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle"></i> Informações
                    </h6>
                </div>
                <div class="card-body">
                    <h6>Horário Atual:</h6>
                    <p class="text-muted">
                        <strong>{{ $horario->nome_dia_semana }}</strong><br>
                        Sistema: {{ $horario->sistema }}<br>
                        Status: {{ $horario->fechado ? 'Fechado' : 'Aberto' }}<br>
                        @if(!$horario->fechado)
                            Horário: {{ $horario->horario_formatado }}
                        @endif
                    </p>

                    <hr>

                    <h6>Últimas Alterações:</h6>
                    <small class="text-muted">
                        Criado em: {{ $horario->created_at->format('d/m/Y H:i') }}<br>
                        @if($horario->updated_at != $horario->created_at)
                            Atualizado em: {{ $horario->updated_at->format('d/m/Y H:i') }}
                        @endif
                    </small>
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
