<!DOCTYPE html>
<html>
<head>
    <title>Teste do Sistema de Configuração</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: #28a745; background: #d4edda; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .error { color: #dc3545; background: #f8d7da; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .warning { color: #856404; background: #fff3cd; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .info { color: #0c5460; background: #d1ecf1; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .config-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin: 20px 0; }
        .config-card { background: #f8f9fa; padding: 15px; border-radius: 6px; border-left: 4px solid #007bff; }
        .config-card h3 { margin: 0 0 10px 0; color: #495057; }
        .config-value { font-weight: bold; color: #007bff; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .badge { display: inline-block; padding: 0.25em 0.4em; font-size: 75%; font-weight: 700; line-height: 1; text-align: center; white-space: nowrap; vertical-align: baseline; border-radius: 0.25rem; }
        .badge-success { color: #fff; background-color: #28a745; }
        .badge-warning { color: #212529; background-color: #ffc107; }
        .badge-danger { color: #fff; background-color: #dc3545; }
        .badge-info { color: #fff; background-color: #17a2b8; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Teste do Sistema de Configuração Multi-Empresa</h1>
        
        <?php
        try {
            // Carregar o autoloader do Laravel se disponível
            if (file_exists('vendor/autoload.php')) {
                require_once 'vendor/autoload.php';
                
                // Carregar o app Laravel
                $app = require_once 'bootstrap/app.php';
                $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
                
                echo '<div class="success">✅ Laravel carregado com sucesso</div>';
                
                // Testar ConfigManager
                $configManager = \App\Services\Config\ConfigManager::getInstance();
                
                echo '<div class="success">✅ ConfigManager instanciado</div>';
                
        ?>
        
        <div class="config-grid">
            <div class="config-card">
                <h3>📊 Informações Gerais</h3>
                <p><strong>Empresa ID:</strong> <span class="config-value"><?= $configManager->getCurrentEmpresaId() ?></span></p>
                <p><strong>Modo:</strong> 
                    <?php if ($configManager->isOnlineMode()): ?>
                        <span class="badge badge-warning">Online</span>
                    <?php else: ?>
                        <span class="badge badge-success">Offline</span>
                    <?php endif; ?>
                </p>
                
                <?php 
                $environment = $configManager->getCurrentEnvironment();
                if ($environment):
                ?>
                <p><strong>Ambiente:</strong> <span class="config-value"><?= $environment['nome'] ?> (<?= $environment['codigo'] ?>)</span></p>
                <p><strong>Produção:</strong> 
                    <?php if ($environment['is_producao']): ?>
                        <span class="badge badge-danger">Sim</span>
                    <?php else: ?>
                        <span class="badge badge-info">Não</span>
                    <?php endif; ?>
                </p>
                <?php endif; ?>
            </div>
            
            <div class="config-card">
                <h3>⚙️ Configurações Laravel</h3>
                <p><strong>App Name:</strong> <span class="config-value"><?= $configManager->get('app.name') ?></span></p>
                <p><strong>App Env:</strong> <span class="config-value"><?= $configManager->get('app.env') ?></span></p>
                <p><strong>App Debug:</strong> 
                    <?php if ($configManager->get('app.debug')): ?>
                        <span class="badge badge-warning">Ativo</span>
                    <?php else: ?>
                        <span class="badge badge-success">Inativo</span>
                    <?php endif; ?>
                </p>
                <p><strong>Database:</strong> <span class="config-value"><?= $configManager->get('database.default') ?></span></p>
            </div>
            
            <div class="config-card">
                <h3>🏢 Configurações Personalizadas</h3>
                <?php 
                $customConfigs = $configManager->get('values', []);
                if (!empty($customConfigs)): 
                ?>
                    <?php foreach (array_slice($customConfigs, 0, 5) as $key => $value): ?>
                        <p><strong><?= htmlspecialchars($key) ?>:</strong> 
                           <span class="config-value"><?= htmlspecialchars(is_array($value) ? json_encode($value) : $value) ?></span>
                        </p>
                    <?php endforeach; ?>
                    
                    <?php if (count($customConfigs) > 5): ?>
                        <p><em>... e mais <?= count($customConfigs) - 5 ?> configurações</em></p>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="warning">Nenhuma configuração personalizada encontrada</p>
                <?php endif; ?>
            </div>
        </div>
        
        <?php
        // Teste de carregamento do banco
        echo '<h2>🗄️ Teste de Carregamento do Banco</h2>';
        
        $loadResult = $configManager->loadFromDatabase();
        
        if ($loadResult) {
            echo '<div class="success">✅ Configurações carregadas do banco com sucesso</div>';
        } else {
            if ($configManager->isOnlineMode()) {
                echo '<div class="warning">⚠️ Carregamento do banco foi pulado (modo online detectado)</div>';
            } else {
                echo '<div class="error">❌ Erro ao carregar configurações do banco</div>';
            }
        }
        ?>
        
        <h2>📋 Todas as Configurações Carregadas</h2>
        
        <?php 
        $allConfigs = $configManager->get();
        $customValues = $allConfigs['values'] ?? [];
        
        if (!empty($customValues)): 
        ?>
        <table>
            <thead>
                <tr>
                    <th>Chave</th>
                    <th>Valor</th>
                    <th>Tipo</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($customValues as $key => $value): ?>
                <tr>
                    <td><?= htmlspecialchars($key) ?></td>
                    <td>
                        <?php 
                        if (is_array($value) || is_object($value)) {
                            echo '<code>' . htmlspecialchars(json_encode($value, JSON_PRETTY_PRINT)) . '</code>';
                        } else {
                            echo htmlspecialchars($value);
                        }
                        ?>
                    </td>
                    <td>
                        <span class="badge badge-info"><?= gettype($value) ?></span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <div class="warning">⚠️ Nenhuma configuração personalizada carregada</div>
        <?php endif; ?>
        
        <h2>🔄 Teste de Definição de Configuração</h2>
        
        <?php
        // Teste definir uma configuração
        $testKey = 'teste.timestamp';
        $testValue = date('Y-m-d H:i:s');
        
        $configManager->set($testKey, $testValue);
        $retrievedValue = $configManager->get($testKey);
        
        if ($retrievedValue === $testValue) {
            echo '<div class="success">✅ Teste de definição/recuperação bem-sucedido</div>';
            echo "<p><strong>Chave:</strong> {$testKey}</p>";
            echo "<p><strong>Valor definido:</strong> {$testValue}</p>";
            echo "<p><strong>Valor recuperado:</strong> {$retrievedValue}</p>";
        } else {
            echo '<div class="error">❌ Erro no teste de definição/recuperação</div>';
        }
        ?>
        
        <?php
            } catch (Exception $e) {
                echo '<div class="error">❌ Erro ao carregar Laravel: ' . htmlspecialchars($e->getMessage()) . '</div>';
                echo '<div class="info">Verifique se as migrações foram executadas e o banco está disponível.</div>';
            }
        ?>
        
        <div style="margin-top: 40px; padding: 20px; background: #e9ecef; border-radius: 6px;">
            <h3>📚 Próximos Passos</h3>
            <ol>
                <li>✅ Sistema de configuração inicializado</li>
                <li>🔄 Executar migrações: <code>php artisan migrate</code></li>
                <li>🗄️ Restaurar backup do banco</li>
                <li>⚙️ Popular dados de configuração: <code>php init_config_system.php</code></li>
                <li>🧪 Testar login do sistema</li>
                <li>🎯 Criar interface de administração de configurações</li>
            </ol>
        </div>
    </div>
</body>
</html>
