<?php

namespace App\Comerciantes\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Comerciantes\Models\EmpresaUsuario;

/**
 * Controller responsável pelo login dos comerciantes
 * Usa a tabela empresa_usuarios existente
 */
class LoginController extends Controller
{
    /**
     * Mostra o formulário de login
     */
    public function showLoginForm()
    {
        return view('comerciantes.auth.login-simples');
    }

    /**
     * Processa o login do comerciante
     */
    public function login(Request $request)
    {
        // Validação dos dados de entrada
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'O campo email é obrigatório.',
            'email.email' => 'Digite um email válido.',
            'password.required' => 'O campo senha é obrigatório.',
            'password.min' => 'A senha deve ter pelo menos 6 caracteres.',
        ]);

        // Busca o usuário pelo email na tabela empresa_usuarios
        $user = EmpresaUsuario::where('email', $request->email)
            ->where('status', 'ativo')
            ->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => 'Usuário não encontrado ou inativo.',
            ]);
        }

        // Verifica a senha usando password_verify porque o campo é 'senha' (não 'password')
        if (!password_verify($request->password, $user->senha)) {
            // Incrementa tentativas de login falhadas
            $user->increment('failed_login_attempts');

            throw ValidationException::withMessages([
                'email' => 'Credenciais inválidas.',
            ]);
        }

        // Verifica se a conta não está bloqueada
        if ($user->locked_until && $user->locked_until->isFuture()) {
            throw ValidationException::withMessages([
                'email' => 'Conta temporariamente bloqueada. Tente novamente mais tarde.',
            ]);
        }

        // Faz o login manualmente usando o guard comerciante
        Auth::guard('comerciante')->login($user, $request->boolean('remember'));

        // Atualiza dados do último login e reseta tentativas falhadas
        $user->update([
            'last_login' => now(),
            'failed_login_attempts' => 0,
            'locked_until' => null
        ]);

        // Regenera a sessão por segurança
        $request->session()->regenerate();

        return redirect()->intended(route('comerciantes.dashboard'))
            ->with('success', 'Login realizado com sucesso!');
    }

    /**
     * Faz logout do comerciante
     */
    public function logout(Request $request)
    {
        Auth::guard('comerciante')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('comerciantes.login')
            ->with('success', 'Logout realizado com sucesso!');
    }
}
