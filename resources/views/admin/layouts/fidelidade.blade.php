<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Fidelidade') - Admin</title>
    <link rel="stylesheet" href="/Theme1/css/bootstrap.min.css">
    <link rel="stylesheet" href="/Theme1/css/icons.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            border-left: 4px solid #667eea;
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .stats-card.success {
            border-left-color: #28a745;
        }
        .stats-card.warning {
            border-left-color: #ffc107;
        }
        .stats-card.danger {
            border-left-color: #dc3545;
        }
        .stats-card.info {
            border-left-color: #17a2b8;
        }
        .table-container {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-top: 2rem;
        }
        .btn-action {
            border: none;
            background: none;
            font-size: 1.2rem;
            margin: 0 0.2rem;
            transition: all 0.3s ease;
        }
        .btn-action:hover {
            transform: scale(1.2);
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .nivel-badge {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        .nivel-badge.bronze {
            background-color: #cd7f32;
            color: white;
        }
        .nivel-badge.prata {
            background-color: #c0c0c0;
            color: #333;
        }
        .nivel-badge.ouro {
            background-color: #ffd700;
            color: #333;
        }
        .badge-status {
            font-size: 0.75rem;
        }
        .valor-positivo {
            color: #28a745;
            font-weight: bold;
        }
        .valor-negativo {
            color: #dc3545;
            font-weight: bold;
        }
        .pagination-info {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 10px;
            margin-top: 1rem;
        }
        .cupom-card {
            background: white;
            border-radius: 10px;
            padding: 1.2rem;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            border-left: 4px solid #28a745;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }
        .cupom-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        .cupom-card.expirado {
            border-left-color: #dc3545;
            opacity: 0.8;
        }
        .cupom-card.usado {
            border-left-color: #6c757d;
            background-color: #f8f9fa;
        }
        .coupon-code {
            font-family: 'Courier New', monospace;
            font-size: 1.1rem;
            color: #28a745;
            font-weight: bold;
        }
        .transacao-card {
            background: white;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border-left: 4px solid #17a2b8;
        }
        .transacao-card.credito {
            border-left-color: #28a745;
        }
        .transacao-card.debito {
            border-left-color: #dc3545;
        }
        .form-container {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
        }
        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 1.5rem;
        }
        .metric-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .config-section {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            border: 1px solid #e9ecef;
        }
        .config-section h5 {
            color: #495057;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
        }
        
        /* CSS adicional específico da página */
        @yield('styles')
    </style>
</head>
<body>
    {{-- Include do Menu Principal --}}
    @include('admin.partials.menuConfig')
    
    {{-- Include do Menu Secundário Fidelidade --}}
    @include('admin.partials.menuFidelidade')

    {{-- Conteúdo Principal --}}
    <div class="container-fluid mt-4">
        @yield('content')
    </div>

    {{-- Scripts Base --}}
    <script src="/Theme1/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/js/admin-menus.js"></script>
    
    <!-- Chart.js (Latest stable version) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.0/dist/chart.umd.min.js"></script>
    
    {{-- Funções JavaScript Base --}}
    <script>
        // Toast notification system
        function showToast(message, type = 'info') {
            const toastContainer = document.getElementById('toast-container') || createToastContainer();
            const toast = createToast(message, type);
            toastContainer.appendChild(toast);
            
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();
            
            setTimeout(() => {
                toast.remove();
            }, 5000);
        }
        
        function createToastContainer() {
            const container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
            return container;
        }
        
        function createToast(message, type) {
            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-white bg-${type} border-0`;
            toast.setAttribute('role', 'alert');
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;
            return toast;
        }

        // Confirmação de ações
        function confirmarAcao(mensagem, callback) {
            if (confirm(mensagem)) {
                callback();
            }
        }

        // Formatação de valores
        function formatarMoeda(valor) {
            return 'R$ ' + parseFloat(valor).toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        // Loading state para botões
        function setLoadingButton(buttonElement, loading = true) {
            if (loading) {
                buttonElement.disabled = true;
                buttonElement.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Carregando...';
            } else {
                buttonElement.disabled = false;
                buttonElement.innerHTML = buttonElement.getAttribute('data-original-text') || 'Salvar';
            }
        }
    </script>
    
    {{-- Scripts específicos da página --}}
    @yield('scripts')
</body>
</html>
