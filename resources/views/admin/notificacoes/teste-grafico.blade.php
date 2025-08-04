@extends('layouts.admin')

@section('title', 'Teste de Gráfico')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Teste de Gráfico</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Gráfico de Teste</h4>
                    <div id="loading">Carregando...</div>
                    <canvas id="teste-grafico" height="400" style="display: none;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Página de teste carregada');
    
    function iniciarTeste() {
        console.log('Chart.js disponível:', typeof Chart !== 'undefined');
        
        if (typeof Chart === 'undefined') {
            console.log('Aguardando Chart.js...');
            setTimeout(iniciarTeste, 100);
            return;
        }
        
        const canvas = document.getElementById('teste-grafico');
        const loading = document.getElementById('loading');
        
        console.log('Canvas encontrado:', !!canvas);
        
        if (!canvas) {
            console.error('Canvas não encontrado!');
            return;
        }
        
        try {
            console.log('Criando gráfico de teste...');
            
            const chart = new Chart(canvas, {
                type: 'bar',
                data: {
                    labels: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio'],
                    datasets: [{
                        label: 'Vendas',
                        data: [12, 19, 3, 5, 2],
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
            
            console.log('Gráfico criado com sucesso!');
            loading.style.display = 'none';
            canvas.style.display = 'block';
            
        } catch (error) {
            console.error('Erro ao criar gráfico:', error);
            loading.textContent = 'Erro ao carregar gráfico: ' + error.message;
        }
    }
    
    iniciarTeste();
});
</script>
@endpush
