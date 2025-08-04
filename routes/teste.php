<?php

use Illuminate\Support\Facades\Route;

// Rota de teste simples
Route::get('/teste-simples', function () {
    return 'Sistema funcionando! ✅';
});

// Rota de teste para verificar se o sistema está funcionando
Route::get('/test-system', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'Sistema funcionando',
        'timestamp' => now(),
        'server' => request()->server('SERVER_NAME'),
        'ip' => request()->ip()
    ]);
});

// Rota de teste para fidelidade sem middleware
Route::get('/test-fidelidade-simple', function () {
    try {
        $stats = [
            'total_clientes' => \Illuminate\Support\Facades\DB::table('funforcli')->where(function ($query) {
                $query->where('tipo', 'cliente')->orWhere('tipo', 'funcionario');
            })->count(),
            'tabelas_fidelidade' => \Illuminate\Support\Facades\DB::select("
                SELECT table_name 
                FROM information_schema.tables 
                WHERE table_schema = 'meufinanceiro' 
                AND table_name LIKE 'fidelidade_%'
                ORDER BY table_name
            ")
        ];

        return response()->json([
            'status' => 'ok',
            'message' => 'Teste fidelidade executado com sucesso',
            'data' => $stats
        ]);
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
});

// Rota de teste com middleware de auth
Route::get('/test-auth', function () {
    return response()->json([
        'status' => 'authenticated',
        'message' => 'Autenticação funcionando',
        'user' => session()->all()
    ]);
})->middleware('auth.simple:60');

// Rota de teste do dashboard de fidelidade SEM middleware
Route::get('/test-fidelidade-dashboard', function () {
    try {
        // Chamar o método dashboard do controller diretamente
        $controller = new \App\Http\Controllers\Admin\AdminFidelidadeController();
        return $controller->dashboard();
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
});

// Rota de teste com view simples
Route::get('/teste-view', function () {
    return view('teste-simples');
});
