<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=marketplace', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Conectado ao banco marketplace com sucesso!" . PHP_EOL;

    // Executar SQL dos grupos de configuração
    $sql1 = file_get_contents('c:\\Users\\leoma\\Downloads\\database_migrations_comerciante_config_001_setup_comerciante_config_groups.sql');
    if ($sql1) {
        $pdo->exec($sql1);
        echo "Grupos de configuração criados com sucesso!" . PHP_EOL;
    }

    // Executar SQL das definições de configuração  
    $sql2 = file_get_contents('c:\\Users\\leoma\\Downloads\\database_migrations_comerciante_config_002_setup_comerciante_config_definitions.sql');
    if ($sql2) {
        $pdo->exec($sql2);
        echo "Definições de configuração criadas com sucesso!" . PHP_EOL;
    }

    // Executar SQL dos departamentos
    $sql3 = file_get_contents('c:\\Users\\leoma\\Downloads\\database_migrations_comerciante_pessoas_003_create_pessoas_departamentos.sql');
    if ($sql3) {
        $pdo->exec($sql3);
        echo "Tabela pessoas_departamentos criada com sucesso!" . PHP_EOL;
    }

    // Executar SQL dos cargos
    $sql4 = file_get_contents('c:\\Users\\leoma\\Downloads\\database_migrations_comerciante_pessoas_004_create_pessoas_cargos.sql');
    if ($sql4) {
        $pdo->exec($sql4);
        echo "Tabela pessoas_cargos criada com sucesso!" . PHP_EOL;
    }

    // Executar SQL da tabela principal pessoas
    $sql5 = file_get_contents('c:\\Users\\leoma\\Downloads\\database_migrations_comerciante_pessoas_005_create_pessoas_main.sql');
    if ($sql5) {
        $pdo->exec($sql5);
        echo "Tabela pessoas criada com sucesso!" . PHP_EOL;
    }

    // Executar SQL dos endereços
    $sql6 = file_get_contents('c:\\Users\\leoma\\Downloads\\database_migrations_comerciante_pessoas_006_create_pessoas_enderecos.sql');
    if ($sql6) {
        $pdo->exec($sql6);
        echo "Tabela pessoas_enderecos criada com sucesso!" . PHP_EOL;
    }

    // Executar SQL das contas bancárias
    $sql7 = file_get_contents('c:\\Users\\leoma\\Downloads\\database_migrations_comerciante_pessoas_007_create_pessoas_contas_bancarias.sql');
    if ($sql7) {
        $pdo->exec($sql7);
        echo "Tabela pessoas_contas_bancarias criada com sucesso!" . PHP_EOL;
    }

    // Executar SQL dos documentos
    $sql8 = file_get_contents('c:\\Users\\leoma\\Downloads\\database_migrations_comerciante_pessoas_008_create_pessoas_documentos.sql');
    if ($sql8) {
        $pdo->exec($sql8);
        echo "Tabela pessoas_documentos criada com sucesso!" . PHP_EOL;
    }

    // Executar SQL dos dependentes
    $sql9 = file_get_contents('c:\\Users\\leoma\\Downloads\\database_migrations_comerciante_pessoas_009_create_pessoas_dependentes.sql');
    if ($sql9) {
        $pdo->exec($sql9);
        echo "Tabela pessoas_dependentes criada com sucesso!" . PHP_EOL;
    }

    // Executar SQL do histórico de cargos
    $sql10 = file_get_contents('c:\\Users\\leoma\\Downloads\\database_migrations_comerciante_pessoas_010_create_pessoas_historico_cargos.sql');
    if ($sql10) {
        $pdo->exec($sql10);
        echo "Tabela pessoas_historico_cargos criada com sucesso!" . PHP_EOL;
    }

    echo PHP_EOL . "Todas as tabelas foram criadas com sucesso!" . PHP_EOL;
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage() . PHP_EOL;
} catch (Exception $e) {
    echo "Erro geral: " . $e->getMessage() . PHP_EOL;
}
