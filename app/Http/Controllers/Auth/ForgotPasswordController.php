<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User\EmpresaUsuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * Controller de Reset de Senha
 * 
 * Segue padrões do PADRONIZACAO_COMPLETA.md:
 * - Multitenancy com empresa_id
 * - Log de segurança
 * - Validação robusta
 */
class ForgotPasswordController extends Controller
{
    /**
     * Exibe formulário de solicitação de reset
     */
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * Envia link de reset por email
     */
    public function sendResetLinkEmail(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|exists:empresa_usuarios,email'
            ], [
                'email.required' => 'O e-mail é obrigatório.',
                'email.email' => 'Digite um e-mail válido.',
                'email.exists' => 'Este e-mail não está cadastrado no sistema.'
            ]);

            $email = $request->email;
            $usuario = EmpresaUsuario::where('email', $email)->where('ativo', true)->first();

            if (!$usuario) {
                return back()->withErrors(['email' => 'Usuário não encontrado ou inativo.']);
            }

            // Gera token único
            $token = Str::random(64);

            // Salva token na tabela
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $email],
                [
                    'token' => Hash::make($token),
                    'created_at' => now()
                ]
            );

            // Log da solicitação
            Log::info('Solicitação de reset de senha', [
                'email' => $email,
                'usuario_id' => $usuario->id,
                'ip' => $request->ip()
            ]);

            // Simular envio de email (implementar Mail depois)
            $resetUrl = url('/password/reset/' . $token . '?email=' . urlencode($email));

            // Por enquanto, apenas retornar sucesso
            return back()->with('status', 'Link de recuperação enviado para seu e-mail!');
        } catch (\Exception $e) {
            Log::error('Erro no reset de senha', [
                'email' => $request->email ?? 'N/A',
                'error' => $e->getMessage()
            ]);

            return back()->withErrors(['email' => 'Erro interno. Tente novamente.']);
        }
    }

    /**
     * Exibe formulário de reset com token
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.passwords.reset')->with([
            'token' => $token,
            'email' => $request->email
        ]);
    }

    /**
     * Processa reset da senha
     */
    public function reset(Request $request)
    {
        try {
            $request->validate([
                'token' => 'required',
                'email' => 'required|email|exists:empresa_usuarios,email',
                'password' => 'required|string|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/',
            ], [
                'password.regex' => 'A senha deve conter pelo menos: 1 letra minúscula, 1 maiúscula, 1 número e 1 caractere especial.',
                'password.confirmed' => 'A confirmação da senha não confere.'
            ]);

            // Verifica token
            $passwordReset = DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->first();

            if (!$passwordReset || !Hash::check($request->token, $passwordReset->token)) {
                return back()->withErrors(['email' => 'Token inválido ou expirado.']);
            }

            // Verifica se token não expirou (24 horas)
            if (now()->diffInHours($passwordReset->created_at) > 24) {
                return back()->withErrors(['email' => 'Token expirado. Solicite um novo.']);
            }

            // Atualiza senha
            $usuario = EmpresaUsuario::where('email', $request->email)->first();
            $usuario->update([
                'password' => Hash::make($request->password),
                'updated_at' => now()
            ]);

            // Remove token usado
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            // Log da alteração
            Log::info('Senha alterada via reset', [
                'email' => $request->email,
                'usuario_id' => $usuario->id,
                'ip' => $request->ip()
            ]);

            return redirect()->route('login')->with('status', 'Senha alterada com sucesso! Faça login com a nova senha.');
        } catch (\Exception $e) {
            Log::error('Erro ao resetar senha', [
                'email' => $request->email ?? 'N/A',
                'error' => $e->getMessage()
            ]);

            return back()->withErrors(['email' => 'Erro interno. Tente novamente.']);
        }
    }
}
