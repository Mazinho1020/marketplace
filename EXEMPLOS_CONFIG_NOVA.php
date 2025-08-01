<?php

// Exemplos de uso do novo sistema de configuração

// =====================================
// 1. Usando o Helper (forma mais simples)
// =====================================

// Obter uma configuração
$appName = config_get('sistema.nome_aplicacao', 'MeuFinanceiro');

// Definir uma configuração  
config_set('fidelidade.pontos_por_real', 10);

// Obter configurações específicas de módulos
$configsFidelidade = config_fidelidade(); // Todas as configs do fidelidade
$pontosMinimos = config_fidelidade('pontos_minimos_resgate', 100); // Config específica

$configsEmail = config_email(); // Todas as configs de email
$smtpHost = config_email('smtp_host', 'localhost');

$configsApi = config_api(); // Todas as configs de API
$apiKey = config_api('chave_publica');

// =====================================
// 2. Usando o ConfigService diretamente
// =====================================

use App\Services\ConfigService;

// Criar instância com contexto específico
$configService = new ConfigService(
    $empresaId = 1,    // ID da empresa
    $siteId = null,    // ID do site (opcional)
    $ambienteId = 1,   // ID do ambiente (1=prod, 2=dev)
    $usuarioId = auth()->user()->id ?? null
);

// Obter configuração
$valor = $configService->get('fidelidade.percentual_desconto', 5);

// Definir configuração
$configService->set('fidelidade.limite_pontos_diario', 1000);

// Obter todas as configurações de um grupo
$configsGrupo = $configService->getByGroup('FIDELIDADE');

// =====================================
// 3. Usando o ConfigHelper com contexto
// =====================================

use App\Helpers\ConfigHelper;

// Definir contexto específico
ConfigHelper::context($empresaId = 1, $siteId = 2, $ambienteId = 1);

// Agora todas as chamadas usarão esse contexto
$valor = ConfigHelper::get('sistema.versao');
ConfigHelper::set('api.timeout', 30);

// =====================================
// 4. Exemplos de configurações por contexto
// =====================================

// Configuração geral (sem site/ambiente específico)
$configService = new ConfigService(1); // Apenas empresa
$configService->set('sistema.nome', 'MeuFinanceiro');

// Configuração específica para um site
$configService = new ConfigService(1, 2); // Empresa e Site
$configService->set('sistema.logo', '/images/logo-site2.png');

// Configuração específica para ambiente de desenvolvimento
$configService = new ConfigService(1, null, 2); // Empresa e Ambiente DEV  
$configService->set('api.debug', true);

// Configuração específica para site e ambiente
$configService = new ConfigService(1, 2, 1); // Empresa, Site e Ambiente PROD
$configService->set('api.base_url', 'https://api.site2.com');

// =====================================
// 5. Exemplos de uso em Controllers
// =====================================

class ExemploController extends Controller
{
    public function configurarFidelidade(Request $request)
    {
        // Usando helper global
        $pontosAtivos = config_fidelidade('sistema_ativo', false);

        if (!$pontosAtivos) {
            return response()->json(['error' => 'Sistema de fidelidade desativado']);
        }

        // Configurar novos valores
        config_set('fidelidade.pontos_por_compra', $request->pontos);
        config_set('fidelidade.valor_ponto', $request->valor);

        return response()->json(['success' => true]);
    }

    public function enviarEmail($destinatario, $assunto, $mensagem)
    {
        // Obter configurações de email
        $configs = config_email();

        Mail::to($destinatario)->send(new MeuEmail([
            'smtp_host' => $configs['smtp_host'],
            'smtp_port' => $configs['smtp_port'],
            'smtp_user' => $configs['smtp_usuario'],
            'smtp_pass' => $configs['smtp_senha'],
            'assunto' => $assunto,
            'mensagem' => $mensagem
        ]));
    }
}

// =====================================
// 6. Exemplos de uso em Views Blade
// =====================================

/*
// No arquivo .blade.php:

@if(config_fidelidade('sistema_ativo'))
    <div class="fidelidade-box">
        <h3>Sistema de Fidelidade</h3>
        <p>Você ganha {{ config_fidelidade('pontos_por_real', 1) }} pontos a cada R$ 1,00 gasto</p>
        <p>Valor do ponto: R$ {{ config_fidelidade('valor_ponto', '0.01') }}</p>
    </div>
@endif

<h1>{{ config_get('sistema.nome_aplicacao', 'MeuFinanceiro') }}</h1>

<img src="{{ config_get('sistema.logo', '/images/logo-default.png') }}" alt="Logo">
*/

// =====================================
// 7. Tipos de configuração suportados
// =====================================

// String
config_set('sistema.nome', 'MeuFinanceiro');

// Integer
config_set('fidelidade.pontos_minimos', 100);

// Float  
config_set('fidelidade.valor_ponto', 0.01);

// Boolean
config_set('fidelidade.sistema_ativo', true);

// Array/JSON
config_set('email.destinatarios_copia', ['admin@site.com', 'financeiro@site.com']);

// Date
config_set('sistema.data_instalacao', '2024-01-15');

// URL
config_set('api.base_url', 'https://api.meusite.com');

// Email
config_set('sistema.email_suporte', 'suporte@meusite.com');

// =====================================
// 8. Limpeza de cache
// =====================================

// Limpar todo o cache de configurações
ConfigHelper::clearCache();

// =====================================
// 9. Estrutura hierárquica de valores
// =====================================

/*
O sistema busca valores na seguinte ordem de prioridade:

1. Valor específico para Site + Ambiente (ex: Site "Loja Online" + Ambiente "Produção")
2. Valor específico para Site (ex: Site "Loja Online")  
3. Valor específico para Ambiente (ex: Ambiente "Produção")
4. Valor geral da empresa
5. Valor padrão da definição
6. Valor padrão fornecido na chamada da função

Exemplo:
- Empresa 1, Site NULL, Ambiente NULL: "MeuFinanceiro" (valor geral)
- Empresa 1, Site 2, Ambiente NULL: "Loja Online" (valor específico do site)
- Empresa 1, Site 2, Ambiente 1: "Loja Online - Produção" (valor específico site+ambiente)
*/

// =====================================
// 10. Validação automática por tipo
// =====================================

try {
    // Estas chamadas farão validação automática
    config_set('sistema.porta', 'abc'); // Erro: deve ser integer
    config_set('sistema.email', 'email-inválido'); // Erro: deve ser email válido
    config_set('sistema.url', 'url-inválida'); // Erro: deve ser URL válida
} catch (Exception $e) {
    echo "Erro de validação: " . $e->getMessage();
}
