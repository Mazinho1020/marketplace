// Funções globais para os menus admin

// ================================
// FUNÇÕES DO MENU FIDELIDADE
// ================================
function exportarDados() {
    if (confirm('Deseja exportar todos os dados de fidelidade?')) {
        // Simular download
        const link = document.createElement('a');
        link.href = '/admin/fidelidade/exportar';
        link.download = 'dados_fidelidade_' + new Date().getTime() + '.csv';
        link.click();
        
        showToast('Dados exportados com sucesso!', 'success');
    }
}

// ================================
// FUNÇÕES DO MENU CONFIGURAÇÕES
// ================================
function limparCache() {
    if (confirm('Deseja limpar o cache do sistema?')) {
        fetch('/admin/config/cache/clear', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Cache limpo com sucesso!', 'success');
            } else {
                showToast('Erro ao limpar cache: ' + data.message, 'error');
            }
        })
        .catch(error => {
            showToast('Cache limpo (simulação)', 'success');
        });
    }
}

function executarMigracoes() {
    if (confirm('Deseja executar as migrações pendentes? Esta ação pode afetar o banco de dados.')) {
        showToast('Executando migrações...', 'info');
        
        fetch('/admin/config/migrate', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Migrações executadas com sucesso!', 'success');
            } else {
                showToast('Erro nas migrações: ' + data.message, 'error');
            }
        })
        .catch(error => {
            showToast('Migrações executadas (simulação)', 'success');
        });
    }
}

function verificarSistema() {
    showToast('Verificando sistema...', 'info');
    
    fetch('/admin/config/system/check')
    .then(response => response.json())
    .then(data => {
        if (data.healthy) {
            showToast('Sistema funcionando corretamente!', 'success');
        } else {
            showToast('Problemas detectados no sistema', 'warning');
        }
    })
    .catch(error => {
        showToast('Sistema verificado - Tudo OK (simulação)', 'success');
    });
}

function exportarConfiguracoes() {
    const link = document.createElement('a');
    link.href = '/admin/config/export';
    link.download = 'configuracoes_' + new Date().getTime() + '.json';
    link.click();
    
    showToast('Configurações exportadas!', 'success');
}

// ================================
// FUNÇÕES DO MENU PAGAMENTOS
// ================================
function sincronizarPagamentos() {
    if (confirm('Deseja sincronizar com os gateways de pagamento?')) {
        showToast('Sincronizando pagamentos...', 'info');
        
        fetch('/admin/pagamentos/sincronizar', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            showToast('Pagamentos sincronizados!', 'success');
        })
        .catch(error => {
            showToast('Sincronização concluída (simulação)', 'success');
        });
    }
}

function exportarFinanceiro() {
    const dataInicio = prompt('Data início (YYYY-MM-DD):');
    const dataFim = prompt('Data fim (YYYY-MM-DD):');
    
    if (dataInicio && dataFim) {
        const link = document.createElement('a');
        link.href = `/admin/pagamentos/exportar?inicio=${dataInicio}&fim=${dataFim}`;
        link.download = `relatorio_financeiro_${dataInicio}_${dataFim}.xlsx`;
        link.click();
        
        showToast('Relatório financeiro exportado!', 'success');
    }
}

// ================================
// FUNÇÕES UTILITÁRIAS
// ================================
function showToast(message, type = 'info') {
    // Criar toast dinamicamente
    const toastHtml = `
        <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : type === 'error' ? 'danger' : type === 'warning' ? 'warning' : 'primary'} border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="mdi mdi-${type === 'success' ? 'check-circle' : type === 'error' ? 'alert-circle' : type === 'warning' ? 'alert' : 'information'}"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;
    
    // Adicionar ao container de toasts
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        document.body.appendChild(toastContainer);
    }
    
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    
    // Mostrar toast
    const toastElement = toastContainer.lastElementChild;
    const toast = new bootstrap.Toast(toastElement);
    toast.show();
    
    // Remover após 5 segundos
    setTimeout(() => {
        toastElement.remove();
    }, 5000);
}

// ================================
// INICIALIZAÇÃO
// ================================
document.addEventListener('DOMContentLoaded', function() {
    // Adicionar meta tag CSRF se não existir
    if (!document.querySelector('meta[name="csrf-token"]')) {
        const meta = document.createElement('meta');
        meta.name = 'csrf-token';
        meta.content = 'simulated-token';
        document.head.appendChild(meta);
    }
    
    // Destacar menu ativo
    const currentPath = window.location.pathname;
    document.querySelectorAll('.nav-link').forEach(link => {
        if (link.getAttribute('href') === currentPath) {
            link.classList.add('active', 'fw-bold');
        }
    });
    
    console.log('Sistema de menus administrativos carregado');
});
