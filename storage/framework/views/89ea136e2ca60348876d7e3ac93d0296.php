<!-- Dropdown - Notificações -->
<li class="nav-item dropdown no-arrow mx-1">
    <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-bell fa-fw"></i>
        <!-- Contador Badge -->
        <span class="badge badge-danger badge-counter" id="notificacao-counter" style="display: none;">3+</span>
    </a>
    <!-- Dropdown - Notificações -->
    <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
        aria-labelledby="alertsDropdown">
        <h6 class="dropdown-header">
            Central de Notificações
        </h6>
        <div id="notificacoes-lista">
            <!-- As notificações serão carregadas via JavaScript -->
            <div class="text-center py-3">
                <i class="fas fa-spinner fa-spin text-muted"></i>
                <small class="text-muted d-block">Carregando...</small>
            </div>
        </div>
        <a class="dropdown-item text-center small text-gray-500" href="<?php echo e(route('comerciantes.notificacoes.index')); ?>">
            Ver Todas as Notificações
        </a>
    </div>
</li>

<?php $__env->startPush('scripts'); ?>
<script>
    let notificacoesInterval;

    document.addEventListener('DOMContentLoaded', function() {
        carregarNotificacoesHeader();

        // Atualizar a cada 30 segundos
        notificacoesInterval = setInterval(carregarNotificacoesHeader, 30000);
    });

    function carregarNotificacoesHeader() {
        // Verificar se estamos em uma página autenticada
        if (!document.querySelector('meta[name="csrf-token"]')) {
            console.log('CSRF token não encontrado, pulando carregamento de notificações');
            return;
        }

        // Fazer requisição para o backend
        const baseUrl = '<?php echo e(url("/")); ?>';
        fetch(`${baseUrl}/comerciantes/notificacoes/header`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                credentials: 'same-origin'
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    atualizarHeaderNotificacoes(data.notificacoes, data.total_nao_lidas);
                } else {
                    console.warn('Falha ao carregar notificações:', data.message || 'Erro desconhecido');
                }
            })
            .catch(error => {
                console.error('Erro ao carregar notificações:', error);
                // Não mostrar erro para o usuário, apenas log
            });
    }

    function atualizarHeaderNotificacoes(notificacoes, totalNaoLidas) {
        const counter = document.getElementById('notificacao-counter');
        const lista = document.getElementById('notificacoes-lista');

        // Atualizar contador
        if (totalNaoLidas > 0) {
            counter.textContent = totalNaoLidas > 9 ? '9+' : totalNaoLidas;
            counter.style.display = 'inline';
        } else {
            counter.style.display = 'none';
        }

        // Atualizar lista
        if (notificacoes.length > 0) {
            lista.innerHTML = '';
            notificacoes.forEach(function(notificacao) {
                const item = document.createElement('a');
                item.className = `dropdown-item d-flex align-items-center ${!notificacao.lida ? 'bg-light' : ''}`;
                item.href = notificacao.url || '#';
                item.onclick = function(e) {
                    if (!notificacao.lida) {
                        marcarNotificacaoLida(notificacao.id);
                    }
                };

                item.innerHTML = `
                <div class="mr-3">
                    <div class="icon-circle ${notificacao.cor.replace('text-', 'bg-')}">
                        <i class="${notificacao.icone} text-white"></i>
                    </div>
                </div>
                <div>
                    <div class="small text-gray-500">${notificacao.tempo}</div>
                    <span class="${!notificacao.lida ? 'font-weight-bold' : ''}">${notificacao.titulo}</span>
                    <div class="small text-gray-500">${notificacao.mensagem.substring(0, 50)}...</div>
                </div>
            `;

                lista.appendChild(item);
            });
        } else {
            lista.innerHTML = `
            <div class="text-center py-3">
                <i class="fas fa-bell-slash text-muted"></i>
                <small class="text-muted d-block">Nenhuma notificação</small>
            </div>
        `;
        }
    }

    function marcarNotificacaoLida(id) {
        fetch(`/comerciantes/notificacoes/${id}/marcar-lida`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Recarregar notificações após marcar como lida
                    setTimeout(carregarNotificacoesHeader, 500);
                }
            })
            .catch(error => {
                console.error('Erro ao marcar como lida:', error);
            });
    }
</script>
<?php $__env->stopPush(); ?><?php /**PATH C:\xampp\htdocs\marketplace\resources\views/comerciantes/partials/header-notifications.blade.php ENDPATH**/ ?>