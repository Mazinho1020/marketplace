<?php
// Interface Web para Gerenciar Ambiente de Banco de Dados

// Verificar se é uma submissão
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    try {
        require_once __DIR__ . '/../app/Services/Database/ConnectionManagerSimples.php';
        $manager = \App\Services\Database\ConnectionManagerSimples::getInstance();

        if ($acao === 'alterar_ambiente') {
            $novoAmbiente = $_POST['ambiente'] ?? '';

            // Validar ambiente
            if (!in_array($novoAmbiente, ['desenvolvimento', 'homologacao', 'producao'])) {
                throw new Exception("Ambiente inválido: {$novoAmbiente}");
            }

            // Testar conexão antes de alterar
            $teste = $manager->testarConexao($novoAmbiente);
            if (!$teste['sucesso']) {
                throw new Exception("Erro ao testar conexão: {$teste['erro']}");
            }

            // Alterar ambiente
            if ($manager->alternarAmbiente($novoAmbiente)) {
                $mensagem = "✅ Ambiente alterado para " . strtoupper($novoAmbiente) . " com sucesso!";
                $tipo = "sucesso";
            } else {
                throw new Exception("Falha ao alterar ambiente");
            }
        } elseif ($acao === 'testar_conexao') {
            $ambienteTeste = $_POST['ambiente_teste'] ?? '';

            if (!in_array($ambienteTeste, ['desenvolvimento', 'homologacao', 'producao'])) {
                throw new Exception("Ambiente inválido para teste: {$ambienteTeste}");
            }

            $teste = $manager->testarConexao($ambienteTeste);

            if ($teste['sucesso']) {
                $mensagem = "✅ Conexão com " . strtoupper($ambienteTeste) . " testada com sucesso!";
                $tipo = "sucesso";
            } else {
                $mensagem = "❌ Erro ao conectar com " . strtoupper($ambienteTeste) . ": {$teste['erro']}";
                $tipo = "erro";
            }
        } elseif ($acao === 'toggle_deteccao') {
            $configPath = __DIR__ . '/../config/database_simples.php';
            $config = require $configPath;

            $config['deteccao_auto'] = !$config['deteccao_auto'];

            $content = "<?php\n// Configuração Simplificada de Banco de Dados\n";
            $content .= "// Última atualização: " . date('Y-m-d H:i:s') . "\n\n";
            $content .= "return " . var_export($config, true) . ";\n";

            if (file_put_contents($configPath, $content)) {
                $status = $config['deteccao_auto'] ? 'ATIVADA' : 'DESATIVADA';
                $mensagem = "✅ Detecção automática {$status}!";
                $tipo = "sucesso";
            } else {
                throw new Exception("Erro ao atualizar configuração");
            }
        }
    } catch (Exception $e) {
        $mensagem = "❌ Erro: " . $e->getMessage();
        $tipo = "erro";
    }

    // Recarregar a página para mostrar as mudanças
    header("Location: " . $_SERVER['PHP_SELF'] . "?msg=" . urlencode($mensagem) . "&tipo=" . $tipo);
    exit;
}

// Carregar informações atuais
try {
    require_once __DIR__ . '/../app/Services/Database/ConnectionManagerSimples.php';
    $manager = \App\Services\Database\ConnectionManagerSimples::getInstance();
    $info = $manager->getInfoSistema();
    $conexaoAtual = $info['conexao_atual'];

    // Testar todas as conexões
    $testesConexao = [];
    foreach (['desenvolvimento', 'homologacao', 'producao'] as $amb) {
        $testesConexao[$amb] = $manager->testarConexao($amb);
    }
} catch (Exception $e) {
    $erro = $e->getMessage();
}

// Verificar mensagens
$mensagem = $_GET['msg'] ?? '';
$tipoMensagem = $_GET['tipo'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Ambiente - Sistema Simplificado</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: #f5f5f5;
        }

        .container {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .card {
            border: 1px solid #e1e5e9;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            background: #fafbfc;
        }

        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .btn-dev {
            background: #28a745;
            color: white;
        }

        .btn-homolog {
            background: #ffc107;
            color: #212529;
        }

        .btn-prod {
            background: #dc3545;
            color: white;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-info {
            background: #17a2b8;
            color: white;
        }

        .alert {
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table th,
        table td {
            border: 1px solid #dee2e6;
            padding: 12px;
            text-align: left;
        }

        table th {
            background: #e9ecef;
            font-weight: 600;
        }

        .env-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .env-desenvolvimento {
            background: #d4edda;
            color: #155724;
        }

        .env-homologacao {
            background: #fff3cd;
            color: #856404;
        }

        .env-producao {
            background: #f8d7da;
            color: #721c24;
        }

        .status-icon {
            font-size: 18px;
            margin-right: 8px;
        }

        .status-ok {
            color: #28a745;
        }

        .status-erro {
            color: #dc3545;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .info-item {
            background: white;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #e1e5e9;
        }

        .info-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 5px;
        }

        .info-value {
            color: #212529;
            font-family: 'Courier New', monospace;
        }

        .historico {
            max-height: 200px;
            overflow-y: auto;
            font-size: 13px;
        }

        .historico-item {
            padding: 8px;
            border-bottom: 1px solid #e9ecef;
        }

        .historico-item:last-child {
            border-bottom: none;
        }

        h1 {
            color: #212529;
            margin-bottom: 10px;
        }

        h2 {
            color: #495057;
            margin-bottom: 15px;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 8px;
        }

        .loading {
            display: none;
        }

        .loading.show {
            display: inline-block;
        }

        @media (max-width: 768px) {
            .btn-group {
                flex-direction: column;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>🔧 Gerenciador de Ambiente - Sistema Simplificado</h1>
        <p style="color: #6c757d; margin-bottom: 30px;">Gerencie facilmente os ambientes de banco de dados do seu sistema</p>

        <?php if ($mensagem): ?>
            <div class="alert alert-<?php echo $tipoMensagem === 'sucesso' ? 'success' : 'danger'; ?>">
                <?php echo htmlspecialchars($mensagem); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($erro)): ?>
            <div class="alert alert-danger">
                ❌ Erro ao carregar sistema: <?php echo htmlspecialchars($erro); ?>
            </div>
        <?php else: ?>

            <div class="card">
                <h2>📊 Status Atual</h2>

                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Ambiente Ativo</div>
                        <div class="info-value">
                            <span class="env-badge env-<?php echo $info['ambiente_atual']; ?>">
                                <?php echo strtoupper($info['ambiente_atual']); ?>
                            </span>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Detecção Automática</div>
                        <div class="info-value">
                            <?php echo $info['deteccao_auto'] ? '✅ ATIVADA' : '❌ DESATIVADA'; ?>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Hostname</div>
                        <div class="info-value"><?php echo htmlspecialchars($info['hostname']); ?></div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Diretório</div>
                        <div class="info-value"><?php echo htmlspecialchars(basename($info['working_dir'])); ?></div>
                    </div>
                </div>

                <h3 style="margin-top: 25px; margin-bottom: 15px;">🔗 Conexão Atual</h3>
                <table>
                    <tr>
                        <th>Host</th>
                        <td><?php echo htmlspecialchars($conexaoAtual['host'] . ':' . $conexaoAtual['porta']); ?></td>
                    </tr>
                    <tr>
                        <th>Banco de Dados</th>
                        <td><?php echo htmlspecialchars($conexaoAtual['banco']); ?></td>
                    </tr>
                    <tr>
                        <th>Usuário</th>
                        <td><?php echo htmlspecialchars($conexaoAtual['usuario']); ?></td>
                    </tr>
                    <tr>
                        <th>Charset</th>
                        <td><?php echo htmlspecialchars($conexaoAtual['charset']); ?></td>
                    </tr>
                </table>
            </div>

            <div class="card">
                <h2>🎯 Alterar Ambiente</h2>
                <p style="color: #6c757d; margin-bottom: 20px;">Selecione o ambiente desejado para alterar a conexão de banco de dados</p>

                <form method="post" class="btn-group">
                    <input type="hidden" name="acao" value="alterar_ambiente">

                    <button type="submit" name="ambiente" value="desenvolvimento"
                        class="btn btn-dev"
                        <?php echo $info['ambiente_atual'] === 'desenvolvimento' ? 'disabled title="Ambiente já ativo"' : ''; ?>>
                        💻 DESENVOLVIMENTO
                    </button>

                    <button type="submit" name="ambiente" value="homologacao"
                        class="btn btn-homolog"
                        <?php
                        $homologHabilitado = !isset($info['config']['conexoes']['homologacao']['habilitado']) || $info['config']['conexoes']['homologacao']['habilitado'] !== false;
                        if ($info['ambiente_atual'] === 'homologacao') {
                            echo 'disabled title="Ambiente já ativo"';
                        } elseif (!$homologHabilitado) {
                            echo 'disabled title="Ambiente desabilitado - configure antes de usar"';
                        }
                        ?>>
                        🧪 HOMOLOGAÇÃO <?php echo !$homologHabilitado ? '(Desabilitado)' : ''; ?>
                    </button>

                    <button type="submit" name="ambiente" value="producao"
                        class="btn btn-prod"
                        <?php echo $info['ambiente_atual'] === 'producao' ? 'disabled title="Ambiente já ativo"' : ''; ?>>
                        🏭 PRODUÇÃO
                    </button>
                </form>
            </div>

            <div class="card">
                <h2>🧪 Teste de Conexões</h2>
                <p style="color: #6c757d; margin-bottom: 20px;">Verificar a conectividade com todos os ambientes configurados</p>

                <table>
                    <thead>
                        <tr>
                            <th>Ambiente</th>
                            <th>Status</th>
                            <th>Host / Banco</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($testesConexao as $ambiente => $teste): ?>
                            <tr>
                                <td>
                                    <span class="env-badge env-<?php echo $ambiente; ?>">
                                        <?php echo strtoupper($ambiente); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($teste['sucesso']): ?>
                                        <span class="status-icon status-ok">✅</span> Conectado
                                    <?php elseif (isset($teste['desabilitado']) && $teste['desabilitado']): ?>
                                        <span class="status-icon" style="color: #ffc107;">⚠️</span> Desabilitado
                                    <?php else: ?>
                                        <span class="status-icon status-erro">❌</span> Erro
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($teste['sucesso']): ?>
                                        <?php echo htmlspecialchars($teste['host'] . ' / ' . $teste['banco']); ?>
                                    <?php elseif (isset($teste['desabilitado']) && $teste['desabilitado']): ?>
                                        <small style="color: #856404;">Ambiente não configurado para uso</small>
                                    <?php else: ?>
                                        <small style="color: #dc3545;"><?php echo htmlspecialchars($teste['erro']); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <form method="post" style="display: inline;">
                                        <input type="hidden" name="acao" value="testar_conexao">
                                        <input type="hidden" name="ambiente_teste" value="<?php echo $ambiente; ?>">
                                        <button type="submit" class="btn btn-secondary" style="font-size: 12px; padding: 6px 12px;">
                                            🔄 Testar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="card">
                <h2>⚙️ Configurações</h2>

                <div style="margin-bottom: 20px;">
                    <form method="post" style="display: inline;">
                        <input type="hidden" name="acao" value="toggle_deteccao">
                        <button type="submit" class="btn btn-info">
                            <?php echo $info['deteccao_auto'] ? '🔒 Desativar' : '🔓 Ativar'; ?> Detecção Automática
                        </button>
                    </form>

                    <small style="display: block; margin-top: 10px; color: #6c757d;">
                        A detecção automática escolhe o ambiente baseado no hostname e diretório do sistema.
                    </small>
                </div>

                <?php if (!empty($info['historico_mudancas'])): ?>
                    <h3 style="margin-top: 25px; margin-bottom: 15px;">📝 Histórico de Mudanças</h3>
                    <div class="historico">
                        <?php foreach (array_reverse($info['historico_mudancas']) as $mudanca): ?>
                            <div class="historico-item">
                                <strong><?php echo $mudanca['data_mudanca']; ?></strong> -
                                <span class="env-badge env-<?php echo $mudanca['ambiente_anterior']; ?>" style="font-size: 10px; padding: 2px 6px;">
                                    <?php echo strtoupper($mudanca['ambiente_anterior']); ?>
                                </span>
                                →
                                <span class="env-badge env-<?php echo $mudanca['ambiente_novo']; ?>" style="font-size: 10px; padding: 2px 6px;">
                                    <?php echo strtoupper($mudanca['ambiente_novo']); ?>
                                </span>
                                <small style="color: #6c757d;">(<?php echo $mudanca['ip']; ?>)</small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="card">
                <h2>ℹ️ Instruções</h2>
                <ul style="color: #495057; line-height: 1.6;">
                    <li><strong>Alteração de Ambiente:</strong> Use os botões acima para trocar entre desenvolvimento, homologação e produção</li>
                    <li><strong>Teste de Conexão:</strong> Sempre teste a conectividade antes de alterar para um novo ambiente</li>
                    <li><strong>Detecção Automática:</strong> Quando ativada, o sistema detecta automaticamente o ambiente baseado no servidor</li>
                    <li><strong>Após Alterações:</strong> Reinicie a aplicação web e limpe caches para aplicar as mudanças</li>
                    <li><strong>Segurança:</strong> Em produção, restrinja o acesso a esta interface apenas para administradores</li>
                </ul>
            </div>

        <?php endif; ?>
    </div>

    <script>
        // Auto-refresh da página após mudanças para mostrar status atualizado
        if (window.location.search.includes('msg=')) {
            setTimeout(() => {
                window.history.replaceState({}, document.title, window.location.pathname);
            }, 3000);
        }

        // Confirmação para alterações em produção
        document.querySelectorAll('button[name="ambiente"][value="producao"]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                if (!confirm('⚠️ Você está alterando para o ambiente de PRODUÇÃO!\n\nTem certeza que deseja continuar?')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>

</html>