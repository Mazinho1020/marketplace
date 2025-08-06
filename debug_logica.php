<?php

echo "DEBUG - ACESSO DIRETO\n";
echo "===================\n\n";

echo "Empresa: 1\n";
echo "URL: http://localhost:8000/comerciantes/empresas/1/horarios\n\n";

// Simular o que deveria acontecer
echo "O que DEVERIA acontecer:\n";
echo "1. Middleware auth.comerciante verifica autenticação\n";
echo "2. Se não logado -> redireciona para /comerciantes/login\n";
echo "3. Se logado -> controller HorarioFuncionamentoController@index\n";
echo "4. Controller verifica permissão da empresa\n";
echo "5. Se sem permissão -> retorna 403\n";
echo "6. Se com permissão -> mostra a página\n\n";

echo "Diagnóstico possível do problema:\n";
echo "- O usuário NÃO está logado\n";
echo "- O middleware redireciona para login\n";
echo "- Mas algo está causando um loop\n\n";

echo "SOLUÇÃO: Testar diretamente o comportamento sem usar o browser\n";
echo "Vou tentar criar um usuário de teste e fazer login programaticamente\n\n";

echo "=== FIM DEBUG ===\n";
