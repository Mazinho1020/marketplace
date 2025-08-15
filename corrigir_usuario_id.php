<?php

echo "🔍 Verificando problema do usuario_id null...\n";
echo "===============================================\n\n";

// Executar comando para verificar usuários
echo "👥 Verificando usuários no sistema...\n";
$cmd1 = 'php artisan tinker --execute="echo \'Total usuários: \' . \App\Models\User::count();"';
$output1 = shell_exec($cmd1 . ' 2>&1');
echo $output1 . "\n";

// Se não há usuários, criar um
echo "👤 Criando usuário de teste se necessário...\n";
$cmd2 = 'php artisan tinker --execute="if(\App\Models\User::count() == 0) { $u = \App\Models\User::create([\'name\' => \'Admin\', \'email\' => \'admin@test.com\', \'password\' => bcrypt(\'123456\')]); echo \'Usuário criado: \' . $u->id; } else { echo \'Usuário já existe: \' . \App\Models\User::first()->id; }"';
$output2 = shell_exec($cmd2 . ' 2>&1');
echo $output2 . "\n";

// Obter ID do primeiro usuário
echo "🔑 Obtendo ID do primeiro usuário...\n";
$cmd3 = 'php artisan tinker --execute="echo \App\Models\User::first()->id;"';
$userId = trim(shell_exec($cmd3 . ' 2>&1'));
echo "ID do usuário: $userId\n\n";

// Agora vou corrigir o RecebimentoController para garantir que o usuario_id seja sempre definido
echo "🔧 Corrigindo RecebimentoController...\n";
