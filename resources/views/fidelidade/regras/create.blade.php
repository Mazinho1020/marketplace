@extends('layouts.app')

@section('title', 'Nova Regra de Cashback')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="fas fa-plus text-primary me-2"></i>
                        Nova Regra de Cashback
                    </h1>
                    <p class="text-muted mb-0">Configure uma nova regra para distribuição de cashback</p>
                </div>
                <div>
                    <a href="{{ route('fidelidade.regras.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>
                        Voltar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulário -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Configuração da Regra</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('fidelidade.regras.store') }}" method="POST">
                        @csrf

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Tipo de Regra *</label>
                                <select name="tipo_regra" class="form-select @error('tipo_regra') is-invalid @enderror"
                                    required>
                                    <option value="">Selecione o tipo</option>
                                    <option value="categoria" {{ old('tipo_regra')==='categoria' ? 'selected' : '' }}>
                                        Por Categoria</option>
                                    <option value="produto" {{ old('tipo_regra')==='produto' ? 'selected' : '' }}>Por
                                        Produto</option>
                                    <option value="dia_semana" {{ old('tipo_regra')==='dia_semana' ? 'selected' : '' }}>
                                        Por Dia da Semana</option>
                                    <option value="horario" {{ old('tipo_regra')==='horario' ? 'selected' : '' }}>Por
                                        Horário</option>
                                    <option value="primeira_compra" {{ old('tipo_regra')==='primeira_compra'
                                        ? 'selected' : '' }}>Primeira Compra</option>
                                </select>
                                @error('tipo_regra')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Percentual de Cashback (%) *</label>
                                <input type="number" name="percentual_cashback"
                                    class="form-control @error('percentual_cashback') is-invalid @enderror" step="0.01"
                                    min="0" max="100" value="{{ old('percentual_cashback') }}" required>
                                @error('percentual_cashback')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Valor Máximo de Cashback</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" name="valor_maximo_cashback"
                                        class="form-control @error('valor_maximo_cashback') is-invalid @enderror"
                                        step="0.01" min="0" value="{{ old('valor_maximo_cashback') }}">
                                </div>
                                <small class="text-muted">Deixe em branco para sem limite</small>
                                @error('valor_maximo_cashback')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Empresa ID</label>
                                <input type="number" name="empresa_id"
                                    class="form-control @error('empresa_id') is-invalid @enderror"
                                    value="{{ old('empresa_id', 1) }}">
                                @error('empresa_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Condições específicas por tipo -->
                        <div class="row mb-4" id="condicoes-especificas" style="display: none;">
                            <!-- Dia da Semana -->
                            <div class="col-md-6" id="campo-dia-semana" style="display: none;">
                                <label class="form-label">Dia da Semana</label>
                                <select name="dia_semana" class="form-select">
                                    <option value="">Selecione o dia</option>
                                    <option value="0" {{ old('dia_semana')==='0' ? 'selected' : '' }}>Domingo</option>
                                    <option value="1" {{ old('dia_semana')==='1' ? 'selected' : '' }}>Segunda-feira
                                    </option>
                                    <option value="2" {{ old('dia_semana')==='2' ? 'selected' : '' }}>Terça-feira
                                    </option>
                                    <option value="3" {{ old('dia_semana')==='3' ? 'selected' : '' }}>Quarta-feira
                                    </option>
                                    <option value="4" {{ old('dia_semana')==='4' ? 'selected' : '' }}>Quinta-feira
                                    </option>
                                    <option value="5" {{ old('dia_semana')==='5' ? 'selected' : '' }}>Sexta-feira
                                    </option>
                                    <option value="6" {{ old('dia_semana')==='6' ? 'selected' : '' }}>Sábado</option>
                                </select>
                            </div>

                            <!-- Horários -->
                            <div class="col-md-3" id="campo-horario-inicio" style="display: none;">
                                <label class="form-label">Horário Início</label>
                                <input type="time" name="horario_inicio" class="form-control"
                                    value="{{ old('horario_inicio') }}">
                            </div>

                            <div class="col-md-3" id="campo-horario-fim" style="display: none;">
                                <label class="form-label">Horário Fim</label>
                                <input type="time" name="horario_fim" class="form-control"
                                    value="{{ old('horario_fim') }}">
                            </div>

                            <!-- Referência ID (para categoria/produto) -->
                            <div class="col-md-6" id="campo-referencia" style="display: none;">
                                <label class="form-label">ID de Referência</label>
                                <input type="number" name="referencia_id" class="form-control"
                                    value="{{ old('referencia_id') }}">
                                <small class="text-muted">ID da categoria ou produto</small>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="ativo" value="1" id="ativo" {{
                                        old('ativo', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="ativo">
                                        Regra ativa
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('fidelidade.regras.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-2"></i>Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Salvar Regra
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    const tipoRegraSelect = document.querySelector('select[name="tipo_regra"]');
    const condicoesDiv = document.getElementById('condicoes-especificas');
    const campoDiaSemana = document.getElementById('campo-dia-semana');
    const campoHorarioInicio = document.getElementById('campo-horario-inicio');
    const campoHorarioFim = document.getElementById('campo-horario-fim');
    const campoReferencia = document.getElementById('campo-referencia');

    function toggleCampos() {
        const tipo = tipoRegraSelect.value;
        
        // Esconder todos primeiro
        condicoesDiv.style.display = 'none';
        campoDiaSemana.style.display = 'none';
        campoHorarioInicio.style.display = 'none';
        campoHorarioFim.style.display = 'none';
        campoReferencia.style.display = 'none';

        if (tipo) {
            condicoesDiv.style.display = 'block';
            
            switch(tipo) {
                case 'dia_semana':
                    campoDiaSemana.style.display = 'block';
                    break;
                case 'horario':
                    campoHorarioInicio.style.display = 'block';
                    campoHorarioFim.style.display = 'block';
                    break;
                case 'categoria':
                case 'produto':
                    campoReferencia.style.display = 'block';
                    break;
            }
        }
    }

    tipoRegraSelect.addEventListener('change', toggleCampos);
    toggleCampos(); // Executar na carga da página
});
</script>
@endsection