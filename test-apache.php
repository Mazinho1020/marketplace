<?php
echo "<h1>Teste Apache/Laravel</h1>";
echo "<p>PHP funcionando: ✅</p>";
echo "<p>Versão PHP: " . phpversion() . "</p>";
echo "<p>Diretório: " . __DIR__ . "</p>";

echo "<h2>Módulos Apache Carregados:</h2>";
if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    echo "<ul>";
    foreach ($modules as $module) {
        if (strpos($module, 'rewrite') !== false) {
            echo "<li><strong>$module</strong> ✅</li>";
        } else {
            echo "<li>$module</li>";
        }
    }
    echo "</ul>";
} else {
    echo "<p>Função apache_get_modules() não disponível</p>";
}

echo "<h2>Links de Teste:</h2>";
echo "<ul>";
echo "<li><a href='/marketplace/'>Root</a></li>";
echo "<li><a href='/marketplace/public/'>Public</a></li>";
echo "<li><a href='/marketplace/public/admin/config'>Admin Config (via public)</a></li>";
echo "<li><a href='/marketplace/admin/config'>Admin Config (via htaccess)</a></li>";
echo "<li><a href='http://localhost:8000/admin/config'>Admin Config (via Laravel serve)</a></li>";
echo "</ul>";
