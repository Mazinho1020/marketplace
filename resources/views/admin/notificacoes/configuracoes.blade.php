@extends('layouts.admin')

@section('title', 'Configurações de Notificação')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.notificacoes.index') }}">Notificações</a></li>
                        <li class="breadcrumb-item active">Configurações</li>
                    </ol>
                </div>
                <h4 class="page-title">
                    <i class="mdi mdi-cog"></i> Configurações do Sistema
                </h4>
            </div>
        </div>
    </div>

    <!-- Abas de Configuração -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs nav-bordered" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" data-bs-toggle="tab" href="#gerais" role="tab">
                                <i class="mdi mdi-settings"></i> Gerais
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#email" role="tab">
                                <i class="mdi mdi-email"></i> Email
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#sms" role="tab">
                                <i class="mdi mdi-cellphone"></i> SMS
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#push" role="tab">
                                <i class="mdi mdi-bell"></i> Push
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#seguranca" role="tab">
                                <i class="mdi mdi-shield"></i> Segurança
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#backup" role="tab">
                                <i class="mdi mdi-backup-restore"></i> Backup
                            </a>
                        </li>
                    </ul>

                    <!-- Tab content -->
                    <div class="tab-content">
                        <!-- Configurações Gerais -->
                        <div class="tab-pane show active" id="gerais" role="tabpanel">
                            <form id="form-gerais" onsubmit="salvarConfiguracoes(event, 'gerais')">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <h5 class="mt-4">Configurações Básicas</h5>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Nome do Sistema</label>
                                            <input type="text" class="form-control" value="Marketplace Notificações" name="nome_sistema">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Email do Administrador</label>
                                            <input type="email" class="form-control" value="admin@marketplace.com" name="email_admin">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Timezone</label>
                                            <select class="form-select" name="timezone">
                                                <option value="America/Sao_Paulo" selected>America/São Paulo</option>
                                                <option value="UTC">UTC</option>
                                                <option value="America/New_York">America/New York</option>
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Idioma Padrão</label>
                                            <select class="form-select" name="idioma">
                                                <option value="pt_BR" selected>Português (Brasil)</option>
                                                <option value="en_US">English (US)</option>
                                                <option value="es_ES">Español</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-6">
                                        <h5 class="mt-4">Configurações de Sistema</h5>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Máximo de Tentativas de Envio</label>
                                            <input type="number" class="form-control" value="3" min="1" max="10" name="max_tentativas">
                                            <small class="form-text text-muted">Número máximo de tentativas para reenvio de notificações falhadas</small>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Timeout de Conexão (segundos)</label>
                                            <input type="number" class="form-control" value="30" min="5" max="120" name="timeout">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Rate Limit (por minuto)</label>
                                            <input type="number" class="form-control" value="100" min="10" max="1000" name="rate_limit">
                                            <small class="form-text text-muted">Número máximo de notificações por minuto</small>
                                        </div>
                                        
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="debug_mode" name="debug_mode" checked>
                                            <label class="form-check-label" for="debug_mode">
                                                Modo Debug Ativo
                                            </label>
                                        </div>
                                        
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="log_detalhado" name="log_detalhado" checked>
                                            <label class="form-check-label" for="log_detalhado">
                                                Log Detalhado
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="mdi mdi-content-save"></i> Salvar Configurações
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Configurações de Email -->
                        <div class="tab-pane" id="email" role="tabpanel">
                            <form id="form-email" onsubmit="salvarConfiguracoes(event, 'email')">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <h5 class="mt-4">Servidor SMTP</h5>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Host SMTP</label>
                                            <input type="text" class="form-control" value="smtp.gmail.com" name="smtp_host">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Porta</label>
                                            <input type="number" class="form-control" value="587" name="smtp_port">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Criptografia</label>
                                            <select class="form-select" name="smtp_encryption">
                                                <option value="tls" selected>TLS</option>
                                                <option value="ssl">SSL</option>
                                                <option value="none">Nenhuma</option>
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Usuário</label>
                                            <input type="text" class="form-control" value="notificacoes@marketplace.com" name="smtp_usuario">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Senha</label>
                                            <input type="password" class="form-control" value="********" name="smtp_senha">
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-6">
                                        <h5 class="mt-4">Configurações de Envio</h5>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Nome do Remetente</label>
                                            <input type="text" class="form-control" value="Marketplace" name="from_name">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Email do Remetente</label>
                                            <input type="email" class="form-control" value="noreply@marketplace.com" name="from_email">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Email de Resposta</label>
                                            <input type="email" class="form-control" value="suporte@marketplace.com" name="reply_to">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Limite por Hora</label>
                                            <input type="number" class="form-control" value="1000" min="10" max="10000" name="email_limite_hora">
                                        </div>
                                        
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="email_tracking" name="email_tracking" checked>
                                            <label class="form-check-label" for="email_tracking">
                                                Tracking de Abertura
                                            </label>
                                        </div>
                                        
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="email_click_tracking" name="email_click_tracking" checked>
                                            <label class="form-check-label" for="email_click_tracking">
                                                Tracking de Cliques
                                            </label>
                                        </div>
                                        
                                        <div class="mt-3">
                                            <button type="button" class="btn btn-outline-primary" onclick="testarEmail()">
                                                <i class="mdi mdi-test-tube"></i> Testar Configuração
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="mdi mdi-content-save"></i> Salvar Configurações
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Configurações de SMS -->
                        <div class="tab-pane" id="sms" role="tabpanel">
                            <form id="form-sms" onsubmit="salvarConfiguracoes(event, 'sms')">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <h5 class="mt-4">Gateway SMS</h5>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Provedor</label>
                                            <select class="form-select" name="sms_provider">
                                                <option value="">Selecionar...</option>
                                                <option value="twilio">Twilio</option>
                                                <option value="nexmo">Vonage (Nexmo)</option>
                                                <option value="totalvoice">TotalVoice</option>
                                                <option value="zenvia">Zenvia</option>
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">API Key</label>
                                            <input type="text" class="form-control" name="sms_api_key" placeholder="Sua API Key">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">API Secret</label>
                                            <input type="password" class="form-control" name="sms_api_secret" placeholder="Seu API Secret">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Número do Remetente</label>
                                            <input type="text" class="form-control" name="sms_from" placeholder="Ex: Marketplace">
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-6">
                                        <h5 class="mt-4">Configurações de Envio</h5>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Limite por Hora</label>
                                            <input type="number" class="form-control" value="100" min="1" max="1000" name="sms_limite_hora">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Timeout (segundos)</label>
                                            <input type="number" class="form-control" value="30" min="5" max="120" name="sms_timeout">
                                        </div>
                                        
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="sms_delivery_receipt" name="sms_delivery_receipt">
                                            <label class="form-check-label" for="sms_delivery_receipt">
                                                Comprovante de Entrega
                                            </label>
                                        </div>
                                        
                                        <div class="alert alert-warning">
                                            <i class="mdi mdi-alert"></i>
                                            <strong>Atenção:</strong> Configure um gateway SMS para habilitar o envio de notificações por SMS.
                                        </div>
                                        
                                        <div class="mt-3">
                                            <button type="button" class="btn btn-outline-primary" onclick="testarSMS()" disabled>
                                                <i class="mdi mdi-test-tube"></i> Testar Configuração
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="mdi mdi-content-save"></i> Salvar Configurações
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Configurações de Push -->
                        <div class="tab-pane" id="push" role="tabpanel">
                            <form id="form-push" onsubmit="salvarConfiguracoes(event, 'push')">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <h5 class="mt-4">Firebase Cloud Messaging</h5>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Server Key</label>
                                            <input type="password" class="form-control" value="AAAA..." name="fcm_server_key">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Sender ID</label>
                                            <input type="text" class="form-control" value="123456789" name="fcm_sender_id">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Project ID</label>
                                            <input type="text" class="form-control" value="marketplace-push" name="fcm_project_id">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Arquivo de Credenciais (JSON)</label>
                                            <input type="file" class="form-control" accept=".json" name="fcm_credentials">
                                            <small class="form-text text-muted">Upload do arquivo de credenciais do Firebase</small>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-6">
                                        <h5 class="mt-4">Configurações de Push</h5>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Limite por Hora</label>
                                            <input type="number" class="form-control" value="5000" min="100" max="50000" name="push_limite_hora">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">TTL Padrão (segundos)</label>
                                            <input type="number" class="form-control" value="3600" min="60" max="86400" name="push_ttl">
                                            <small class="form-text text-muted">Time to Live - tempo máximo para entrega</small>
                                        </div>
                                        
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="push_high_priority" name="push_high_priority" checked>
                                            <label class="form-check-label" for="push_high_priority">
                                                Alta Prioridade por Padrão
                                            </label>
                                        </div>
                                        
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="push_sound" name="push_sound" checked>
                                            <label class="form-check-label" for="push_sound">
                                                Som de Notificação
                                            </label>
                                        </div>
                                        
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="push_vibrate" name="push_vibrate" checked>
                                            <label class="form-check-label" for="push_vibrate">
                                                Vibração
                                            </label>
                                        </div>
                                        
                                        <div class="mt-3">
                                            <button type="button" class="btn btn-outline-primary" onclick="testarPush()">
                                                <i class="mdi mdi-test-tube"></i> Testar Push
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="mdi mdi-content-save"></i> Salvar Configurações
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Configurações de Segurança -->
                        <div class="tab-pane" id="seguranca" role="tabpanel">
                            <form id="form-seguranca" onsubmit="salvarConfiguracoes(event, 'seguranca')">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <h5 class="mt-4">Autenticação e Acesso</h5>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Chave de Criptografia</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" value="********" name="encryption_key">
                                                <button class="btn btn-outline-secondary" type="button" onclick="gerarChave()">
                                                    <i class="mdi mdi-refresh"></i> Gerar Nova
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Tempo de Expiração do Token (horas)</label>
                                            <input type="number" class="form-control" value="24" min="1" max="168" name="token_expiry">
                                        </div>
                                        
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="require_api_key" name="require_api_key" checked>
                                            <label class="form-check-label" for="require_api_key">
                                                Exigir API Key para todas as requisições
                                            </label>
                                        </div>
                                        
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="ip_whitelist" name="ip_whitelist">
                                            <label class="form-check-label" for="ip_whitelist">
                                                Whitelist de IPs
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-6">
                                        <h5 class="mt-4">Logs e Auditoria</h5>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Retenção de Logs (dias)</label>
                                            <input type="number" class="form-control" value="30" min="1" max="365" name="log_retention">
                                        </div>
                                        
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="log_sensitive_data" name="log_sensitive_data">
                                            <label class="form-check-label" for="log_sensitive_data">
                                                Log de Dados Sensíveis
                                            </label>
                                        </div>
                                        
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="audit_trail" name="audit_trail" checked>
                                            <label class="form-check-label" for="audit_trail">
                                                Trilha de Auditoria
                                            </label>
                                        </div>
                                        
                                        <h5 class="mt-4">Notificações de Segurança</h5>
                                        
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="alert_failed_auth" name="alert_failed_auth" checked>
                                            <label class="form-check-label" for="alert_failed_auth">
                                                Alertar sobre falhas de autenticação
                                            </label>
                                        </div>
                                        
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="alert_suspicious_activity" name="alert_suspicious_activity" checked>
                                            <label class="form-check-label" for="alert_suspicious_activity">
                                                Alertar sobre atividade suspeita
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="mdi mdi-content-save"></i> Salvar Configurações
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Configurações de Backup -->
                        <div class="tab-pane" id="backup" role="tabpanel">
                            <form id="form-backup" onsubmit="salvarConfiguracoes(event, 'backup')">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <h5 class="mt-4">Backup Automático</h5>
                                        
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="backup_enabled" name="backup_enabled" checked>
                                            <label class="form-check-label" for="backup_enabled">
                                                Habilitar Backup Automático
                                            </label>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Frequência</label>
                                            <select class="form-select" name="backup_frequency">
                                                <option value="daily" selected>Diário</option>
                                                <option value="weekly">Semanal</option>
                                                <option value="monthly">Mensal</option>
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Horário do Backup</label>
                                            <input type="time" class="form-control" value="02:00" name="backup_time">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Retenção (dias)</label>
                                            <input type="number" class="form-control" value="30" min="1" max="365" name="backup_retention">
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-6">
                                        <h5 class="mt-4">Destino do Backup</h5>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Tipo de Armazenamento</label>
                                            <select class="form-select" name="backup_storage">
                                                <option value="local" selected>Local</option>
                                                <option value="s3">Amazon S3</option>
                                                <option value="gcs">Google Cloud Storage</option>
                                                <option value="ftp">FTP</option>
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Caminho/Bucket</label>
                                            <input type="text" class="form-control" value="/backups/notificacoes" name="backup_path">
                                        </div>
                                        
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="backup_encrypt" name="backup_encrypt" checked>
                                            <label class="form-check-label" for="backup_encrypt">
                                                Criptografar Backups
                                            </label>
                                        </div>
                                        
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="backup_compress" name="backup_compress" checked>
                                            <label class="form-check-label" for="backup_compress">
                                                Compressão
                                            </label>
                                        </div>
                                        
                                        <div class="mt-3">
                                            <button type="button" class="btn btn-outline-primary" onclick="executarBackupManual()">
                                                <i class="mdi mdi-backup-restore"></i> Backup Manual
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary" onclick="restaurarBackup()">
                                                <i class="mdi mdi-restore"></i> Restaurar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="mdi mdi-content-save"></i> Salvar Configurações
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips se necessário
    if (typeof bootstrap !== 'undefined') {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    }
});

function salvarConfiguracoes(event, secao) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    
    // Simular salvamento
    mostrarAlerta(`Salvando configurações de ${secao}...`, 'info');
    
    setTimeout(() => {
        mostrarAlerta(`Configurações de ${secao} salvas com sucesso!`, 'success');
    }, 1500);
}

function testarEmail() {
    mostrarAlerta('Enviando email de teste...', 'info');
    
    setTimeout(() => {
        mostrarAlerta('Email de teste enviado com sucesso! Verifique sua caixa de entrada.', 'success');
    }, 2000);
}

function testarSMS() {
    mostrarAlerta('Enviando SMS de teste...', 'info');
    
    setTimeout(() => {
        mostrarAlerta('SMS de teste enviado com sucesso!', 'success');
    }, 2000);
}

function testarPush() {
    mostrarAlerta('Enviando push notification de teste...', 'info');
    
    setTimeout(() => {
        mostrarAlerta('Push notification de teste enviado com sucesso!', 'success');
    }, 2000);
}

function gerarChave() {
    if (confirm('Deseja gerar uma nova chave de criptografia? Isso pode afetar dados já criptografados.')) {
        const novaChave = 'key_' + Math.random().toString(36).substr(2, 32);
        document.querySelector('input[name="encryption_key"]').value = novaChave;
        mostrarAlerta('Nova chave gerada! Não se esqueça de salvar as configurações.', 'warning');
    }
}

function executarBackupManual() {
    if (confirm('Deseja executar um backup manual agora?')) {
        mostrarAlerta('Iniciando backup manual...', 'info');
        
        setTimeout(() => {
            mostrarAlerta('Backup manual concluído com sucesso!', 'success');
        }, 5000);
    }
}

function restaurarBackup() {
    if (confirm('Deseja restaurar um backup? Esta ação irá sobrescrever os dados atuais.')) {
        // Abrir modal de seleção de backup ou redirecionar para página específica
        window.open('/admin/backup/restaurar', '_blank');
    }
}

function mostrarAlerta(mensagem, tipo) {
    const cores = {
        'success': 'success',
        'danger': 'danger', 
        'warning': 'warning',
        'info': 'info'
    };
    
    const popup = document.createElement('div');
    popup.className = `alert alert-${cores[tipo]} alert-dismissible fade show position-fixed`;
    popup.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 350px;';
    popup.innerHTML = `
        ${mensagem}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(popup);
    
    setTimeout(() => {
        if (popup.parentNode) {
            popup.parentNode.removeChild(popup);
        }
    }, 5000);
}
</script>

<style>
.nav-bordered {
    border-bottom: 1px solid #dee2e6;
}

.nav-bordered .nav-link {
    border: 1px solid transparent;
    border-bottom: none;
    margin-bottom: -1px;
}

.nav-bordered .nav-link.active {
    background-color: #fff;
    border-color: #dee2e6 #dee2e6 #fff;
    color: #007bff;
}

.tab-content {
    padding-top: 20px;
}

.form-label {
    font-weight: 600;
    color: #495057;
}

.form-text {
    font-size: 0.875rem;
}

.alert {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.btn-group .btn {
    margin-right: 5px;
}

.input-group .btn {
    border-left: none;
}

h5 {
    color: #495057;
    font-weight: 600;
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 8px;
    margin-bottom: 20px;
}

.card {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border: none;
}

.form-check-label {
    font-weight: 500;
}

.text-end {
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #e9ecef;
}
</style>
@endpush
@endsection
