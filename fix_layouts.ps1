# Script para corrigir layouts dos arquivos blade
$files = Get-ChildItem -Path "resources\views\comerciantes" -Recurse -Filter "*.blade.php" | Where-Object { (Get-Content $_.FullName -Raw) -match "@extends\('comerciantes\." }

foreach ($file in $files) {
    Write-Host "Corrigindo: $($file.FullName)"
    
    # Ler o conteúdo do arquivo
    $content = Get-Content $file.FullName -Raw
    
    # Substituir os layouts antigos
    $content = $content -replace "@extends\('comerciantes\.layout'\)", "@extends('layouts.comerciante')"
    $content = $content -replace "@extends\('comerciantes\.layouts\.app'\)", "@extends('layouts.comerciante')"
    
    # Escrever o conteúdo de volta
    $content | Set-Content $file.FullName -NoNewline
}

Write-Host "Correção concluída!"
