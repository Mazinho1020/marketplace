<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Route::get('/test-buscar-produto', function () {
    return view('test-buscar-produto');
});

Route::get('/debug-auth-kits', function (Request $request) {
    $output = "<h2>Debug de Autenticação - Kits</h2>";

    // 1. Verificar se está autenticado
    $output .= "<h3>1. Status de Autenticação:</h3>";
    $output .= "<p>Auth::check(): " . (Auth::check() ? 'SIM' : 'NÃO') . "</p>";
    $output .= "<p>Auth::guard('comerciante')->check(): " . (Auth::guard('comerciante')->check() ? 'SIM' : 'NÃO') . "</p>";

    if (Auth::guard('comerciante')->check()) {
        $user = Auth::guard('comerciante')->user();
        $output .= "<p>Usuário logado: {$user->nome} (ID: {$user->id})</p>";
        $output .= "<p>Empresa ID: {$user->empresa_id}</p>";
    }

    // 2. Verificar sessão
    $output .= "<h3>2. Dados da Sessão:</h3>";
    $output .= "<p>Session ID: " . session()->getId() . "</p>";
    $output .= "<p>CSRF Token: " . csrf_token() . "</p>";

    // 3. Testar requisição para buscar produtos
    $output .= "<h3>3. Teste da Requisição:</h3>";
    try {
        $request->merge(['term' => 'test']);

        if (Auth::guard('comerciante')->check()) {
            $user = Auth::guard('comerciante')->user();
            $produtos = \App\Models\Produto::where('empresa_id', $user->empresa_id)
                ->where('ativo', true)
                ->where('tipo', '!=', 'kit')
                ->where(function ($query) {
                    $query->where('nome', 'like', "%test%")
                        ->orWhere('sku', 'like', "%test%")
                        ->orWhere('codigo_sistema', 'like', "%test%");
                })
                ->select(['id', 'nome', 'sku', 'preco_venda', 'estoque_atual', 'controla_estoque', 'unidade_medida'])
                ->orderBy('nome')
                ->limit(20)
                ->get();

            $output .= "<p>✅ Consulta executada com sucesso!</p>";
            $output .= "<p>Produtos encontrados: " . $produtos->count() . "</p>";

            if ($produtos->count() > 0) {
                $output .= "<ul>";
                foreach ($produtos as $produto) {
                    $output .= "<li>{$produto->nome} (SKU: {$produto->sku})</li>";
                }
                $output .= "</ul>";
            }
        } else {
            $output .= "<p>❌ Usuário não está autenticado</p>";
        }
    } catch (\Exception $e) {
        $output .= "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
    }

    // 4. Verificar headers da requisição
    $output .= "<h3>4. Headers da Requisição:</h3>";
    $headers = $request->headers->all();
    $output .= "<pre>" . print_r($headers, true) . "</pre>";

    return $output;
});
