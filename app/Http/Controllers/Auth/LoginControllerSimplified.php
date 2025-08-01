<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * LoginController Simplificado
 * Sistema de autenticação administrativa com tipos de usuário
 */
class LoginControllerSimplified extends Controller
{
    /**
     * Exibir formulário de login
     */
    public function showLoginForm()
    {
        // Se já estiver logado, redirecionar para admin
        if ($this->isAuthenticated()) {
            return redirect('/admin/dashboard');
        }

        return view('auth.login-simplified');
    }

    /**
     * Processar autenticação
     */
    public function login(Request $request)
    {
        // Verificar se é POST
        if ($request->method() !== 'POST') {
            return back()->withErrors(['error' => 'Método não permitido.']);
        }

        // Validar dados básicos
        $request->validate([
            'email' => 'required|email',
            'senha' => 'required|string|min:1'
        ], [
            'email.required' => 'Email é obrigatório.',
            'email.email' => 'Email inválido.',
            'senha.required' => 'Senha é obrigatória.'
        ]);

        $email = $request->input('email');
        $senha = $request->input('senha');
        $lembrar = $request->has('lembrar');

        try {
            // 1. Verificar bloqueio por tentativas excessivas
            $maxAttempts = 5;
            $lockoutMinutes = 30;

            $failedAttempts = $this->getFailedLoginAttempts($email, $lockoutMinutes);

            if ($failedAttempts >= $maxAttempts) {
                $this->recordLoginAttempt($email, false, $request);
                return back()->withErrors(['error' => "Conta temporariamente bloqueada devido a múltiplas tentativas. Tente novamente em {$lockoutMinutes} minutos."]);
            }

            // 2. Buscar usuário (sem tipo por enquanto - tabela não existe)
            $usuario = DB::table('empresa_usuarios')
                ->select('*')
                ->where('email', $email)
                ->whereNull('deleted_at')
                ->first();

            if (!$usuario) {
                $this->recordLoginAttempt($email, false, $request);
                return back()->withErrors(['error' => 'Credenciais inválidas. Tente novamente.']);
            }

            // 3. Verificar status do usuário
            if ($usuario->status !== 'ativo') {
                return back()->withErrors(['error' => 'Sua conta está inativa. Entre em contato com o administrador.']);
            }

            // 4. Verificar senha
            if (!Hash::check($senha, $usuario->senha)) {
                $this->recordLoginAttempt($email, false, $request);
                return back()->withErrors(['error' => 'Credenciais inválidas. Tente novamente.']);
            }

            // 5. Login bem-sucedido
            $this->recordLoginAttempt($email, true, $request);
            $this->clearLoginAttempts($email);
            $this->updateLastLogin($usuario->id);

            // 6. Criar sessão (usando campos que existem)
            $request->session()->regenerate();

            Session::put([
                'usuario_id' => $usuario->id,
                'usuario_nome' => $usuario->nome,
                'usuario_email' => $usuario->email,
                'empresa_id' => $usuario->empresa_id,
                'usuario_tipo_id' => $usuario->perfil_id ?? null, // Usar perfil_id existente
                'usuario_tipo' => 'admin', // Valor padrão
                'tipo_nome' => 'Administrador', // Valor padrão
                'nivel_acesso' => 100, // Valor padrão
                'login_time' => time(),
                'last_activity' => time()
            ]);

            // 7. Configurar "Lembrar de mim" se solicitado
            if ($lembrar) {
                $this->setRememberMeCookie($usuario->id);
            }

            // 8. Registrar atividade
            $this->logActivity($usuario->id, 'LOGIN', 'Login realizado com sucesso', $request);

            // 9. Redirecionar
            $redirectUrl = Session::pull('redirect_after_login', '/admin/dashboard');
            return redirect($redirectUrl)->with('success', 'Login realizado com sucesso!');
        } catch (Exception $e) {
            Log::error("Erro no login: " . $e->getMessage());
            return back()->withErrors(['error' => 'Erro interno do servidor. Tente novamente.']);
        }
    }

    /**
     * Processar logout
     */
    public function logout(Request $request)
    {
        // Registrar atividade de logout
        if (Session::has('usuario_id')) {
            $this->logActivity(Session::get('usuario_id'), 'LOGOUT', 'Logout realizado', $request);
        }

        // Remover cookie de "Lembrar de mim"
        if ($request->hasCookie('remember_token')) {
            return redirect('/login')
                ->withCookie(cookie()->forget('remember_token'))
                ->with('success', 'Logout realizado com sucesso.');
        }

        // Limpar sessão
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Logout realizado com sucesso.');
    }

    /**
     * Exibir formulário de recuperação de senha
     */
    public function showForgotForm()
    {
        return view('auth.forgot-simplified');
    }

    /**
     * Processar solicitação de recuperação
     */
    public function resetPasswordRequest(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $email = $request->input('email');

        try {
            $usuario = DB::table('empresa_usuarios')
                ->where('email', $email)
                ->where('status', 'ativo')
                ->whereNull('deleted_at')
                ->first();

            if (!$usuario) {
                // Por segurança, não revelar se o email existe
                return back()->with('success', 'Se o email estiver cadastrado, você receberá instruções para redefinir sua senha.');
            }

            // Gerar token de redefinição
            $token = bin2hex(random_bytes(32));
            $expiration = now()->addHour(); // 1 hora

            DB::table('empresa_usuarios_password_resets')->insert([
                'email' => $email,
                'token' => Hash::make($token),
                'created_at' => now(),
                'expires_at' => $expiration,
                'used' => false
            ]);

            // Para demonstração, vamos mostrar o link (em produção seria enviado por email)
            $resetUrl = url("/login/reset-password?email=" . urlencode($email) . "&token=" . $token);

            return back()->with('success', "Link de redefinição: {$resetUrl}");
        } catch (Exception $e) {
            \Log::error("Erro na recuperação de senha: " . $e->getMessage());
            return back()->withErrors(['error' => 'Erro ao processar solicitação. Tente novamente.']);
        }
    }

    /**
     * Verificar se está autenticado
     */
    protected function isAuthenticated()
    {
        if (!Session::has('usuario_id')) {
            return false;
        }

        // Verificar timeout de sessão (30 minutos)
        if (Session::has('last_activity')) {
            $timeout = 30 * 60; // 30 minutos
            if (time() - Session::get('last_activity') > $timeout) {
                Session::flush();
                return false;
            }
            Session::put('last_activity', time());
        }

        return true;
    }

    /**
     * Registrar tentativa de login
     */
    protected function recordLoginAttempt($email, $success, Request $request)
    {
        try {
            DB::table('empresa_usuarios_login_attempts')->insert([
                'email' => $email,
                'success' => $success,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now()
            ]);
        } catch (Exception $e) {
            \Log::error("Erro ao registrar tentativa de login: " . $e->getMessage());
        }
    }

    /**
     * Obter tentativas de login falhadas
     */
    protected function getFailedLoginAttempts($email, $minutes = 30)
    {
        try {
            return DB::table('empresa_usuarios_login_attempts')
                ->where('email', $email)
                ->where('success', false)
                ->where('created_at', '>', now()->subMinutes($minutes))
                ->count();
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Limpar tentativas de login
     */
    protected function clearLoginAttempts($email)
    {
        try {
            DB::table('empresa_usuarios_login_attempts')
                ->where('email', $email)
                ->delete();
        } catch (Exception $e) {
            \Log::error("Erro ao limpar tentativas de login: " . $e->getMessage());
        }
    }

    /**
     * Atualizar último login
     */
    protected function updateLastLogin($userId)
    {
        try {
            DB::table('empresa_usuarios')
                ->where('id', $userId)
                ->update(['last_login' => now()]);
        } catch (Exception $e) {
            \Log::error("Erro ao atualizar último login: " . $e->getMessage());
        }
    }

    /**
     * Registrar atividade
     */
    protected function logActivity($userId, $action, $description, Request $request)
    {
        try {
            DB::table('empresa_usuarios_activity_log')->insert([
                'user_id' => $userId,
                'action' => $action,
                'description' => $description,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now()
            ]);
        } catch (Exception $e) {
            \Log::error("Erro ao registrar atividade: " . $e->getMessage());
        }
    }

    /**
     * Configurar cookie "Lembrar de mim"
     */
    protected function setRememberMeCookie($userId)
    {
        try {
            $token = bin2hex(random_bytes(32));
            $expiration = now()->addDays(30); // 30 dias

            // Salvar token no banco (em produção, usar hash)
            DB::table('empresa_usuarios_remember_tokens')->insert([
                'user_id' => $userId,
                'token' => Hash::make($token),
                'expires_at' => $expiration,
                'created_at' => now()
            ]);

            // Definir cookie
            cookie('remember_token', $userId . ':' . $token, 30 * 24 * 60); // 30 dias

        } catch (Exception $e) {
            \Log::error("Erro ao configurar cookie remember me: " . $e->getMessage());
        }
    }
}
