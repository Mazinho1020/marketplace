<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User\EmpresaUsuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

/**
 * Controller de Autenticação - Login
 * 
 * Segue padrões do PADRONIZACAO_COMPLETA.md:
 * - Multitenancy com empresa_id
 * - Campos de sincronização
 * - Log de segurança
 * - Validação robusta
 */
class LoginController extends Controller
{
    /**
     * Exibe formulário de login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Processa login do usuário
     */
    public function login(Request $request)
    {
        try {
            // Validação dos dados
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string|min:6',
                'empresa_id' => 'nullable|integer' // Removida validação exists por enquanto
            ]);

            $email = $request->email;
            $password = $request->password;
            $empresaId = $request->empresa_id;

            // Busca usuário
            $query = EmpresaUsuario::where('email', $email)
                ->where('status', EmpresaUsuario::STATUS_ATIVO);

            if ($empresaId) {
                $query->where('empresa_id', $empresaId);
            }

            $usuario = $query->first();

            if (!$usuario) {
                $this->logTentativaLogin($email, $empresaId, 'Usuario não encontrado', $request->ip());
                return back()->withErrors([
                    'email' => 'Credenciais inválidas.'
                ])->withInput($request->only('email'));
            }

            // Verifica se conta não está bloqueada
            if ($usuario->isContaBloqueada()) {
                $this->logTentativaLogin($email, $empresaId, 'Conta bloqueada', $request->ip());
                return back()->withErrors([
                    'email' => 'Conta temporariamente bloqueada. Tente novamente em alguns minutos.'
                ])->withInput($request->only('email'));
            }

            // Verifica senha - COM DEBUG TEMPORÁRIO
            Log::info('DEBUG: Verificação de senha', [
                'email' => $email,
                'password_input_length' => strlen($password),
                'password_hash_length' => strlen($usuario->senha),
                'password_hash_start' => substr($usuario->senha, 0, 20),
                'hash_check_result' => Hash::check($password, $usuario->senha)
            ]);

            if (!Hash::check($password, $usuario->senha)) {
                // TESTE ADICIONAL: Tentar criar um hash da senha e comparar
                $testHash = password_hash($password, PASSWORD_DEFAULT);
                $manualVerify = password_verify($password, $usuario->senha);

                Log::error('DEBUG: Falha na verificação de senha', [
                    'email' => $email,
                    'password_input' => '***' . substr($password, -3), // últimos 3 chars para debug
                    'stored_hash' => substr($usuario->senha, 0, 30) . '...',
                    'hash_check' => Hash::check($password, $usuario->senha),
                    'manual_verify' => $manualVerify,
                    'test_hash' => substr($testHash, 0, 30) . '...'
                ]);

                $usuario->incrementarTentativasLogin();
                $this->logTentativaLogin($email, $empresaId, 'Senha incorreta', $request->ip());

                return back()->withErrors([
                    'password' => 'DEBUG: Hash check falhou. Verifique logs para detalhes.'
                ])->withInput($request->only('email'));
            }

            // Login bem-sucedido
            $usuario->resetarTentativasLogin();
            $usuario->atualizarUltimoLogin();

            Auth::login($usuario, $request->boolean('remember'));

            // Log de sucesso
            Log::info('Login realizado com sucesso', [
                'usuario_id' => $usuario->id,
                'email' => $email,
                'empresa_id' => $usuario->empresa_id,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            // Regenera sessão por segurança
            $request->session()->regenerate();

            // Redireciona baseado no tipo de usuário
            return $this->redirecionarPorTipo($usuario);
        } catch (\Exception $e) {
            Log::error('Erro no processo de login', [
                'email' => $request->email ?? 'N/A',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // TEMPORÁRIO: Mostrar erro real para debug
            return back()->withErrors([
                'email' => 'ERRO DEBUG: ' . $e->getMessage() . ' (Linha: ' . $e->getLine() . ')'
            ]);
        }
    }

    /**
     * Logout do usuário
     */
    public function logout(Request $request)
    {
        $usuario = Auth::user();

        if ($usuario) {
            Log::info('Logout realizado', [
                'usuario_id' => $usuario->id,
                'email' => $usuario->email,
                'empresa_id' => $usuario->empresa_id,
                'ip' => $request->ip()
            ]);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('status', 'Logout realizado com sucesso.');
    }

    /**
     * Redireciona usuário baseado no seu tipo principal
     * Agora usa a nova arquitetura multiempresa com verificação de níveis
     */
    private function redirecionarPorTipo(EmpresaUsuario $usuario)
    {
        // Usar o novo método que implementa a lógica multiempresa
        $rota = $this->determinarRedirecionamento($usuario->id);

        // Armazenar informações do usuário na sessão
        $tipos = $this->obterTiposUsuario($usuario->id);
        $tipoPrincipal = $this->obterTipoPrincipal($usuario->id);
        $nivelMaximo = $this->obterMaiorNivelAcesso($usuario->id);

        Session::put('user_tipos', $tipos);
        Session::put('user_tipo_principal', $tipoPrincipal);
        Session::put('user_nivel_acesso', $nivelMaximo);
        Session::put('user_permissions', [
            'is_admin' => $this->isUsuarioAdmin($usuario->id),
            'is_gerente' => $this->isUsuarioGerente($usuario->id),
            'is_operacional' => $this->isUsuarioOperacional($usuario->id),
            'is_publico' => $this->isUsuarioPublico($usuario->id)
        ]);

        Log::info('Redirecionamento realizado com hierarquia', [
            'usuario_id' => $usuario->id,
            'tipos_count' => count($tipos),
            'tipo_principal' => $tipoPrincipal->codigo ?? 'none',
            'nivel_acesso' => $nivelMaximo,
            'rota_destino' => $rota
        ]);

        return redirect($rota)->with('success', 'Login realizado com sucesso!');
    }
    /**
     * Log de tentativas de login para auditoria
     */
    private function logTentativaLogin($email, $empresaId, $motivo, $ip)
    {
        // TODO: Criar tabela logs_login
        /*
        try {
            DB::table('logs_login')->insert([
                'email' => $email,
                'empresa_id' => $empresaId,
                'sucesso' => false,
                'motivo' => $motivo,
                'ip' => $ip,
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao registrar log de login', [
                'error' => $e->getMessage()
            ]);
        }
        */

        Log::warning('Tentativa de login falhada', [
            'email' => $email,
            'empresa_id' => $empresaId,
            'motivo' => $motivo,
            'ip' => $ip
        ]);
    }

    /**
     * Verifica se IP está na lista de bloqueados
     */
    private function isIpBloqueado($ip)
    {
        // Implementar lógica de IPs bloqueados se necessário
        return false;
    }

    /**
     * Verifica múltiplas tentativas por IP
     */
    private function verificarTentativasPorIp($ip)
    {
        $tentativas = DB::table('logs_login')
            ->where('ip', $ip)
            ->where('sucesso', false)
            ->where('created_at', '>=', now()->subMinutes(15))
            ->count();

        return $tentativas < 10; // Máximo 10 tentativas por IP em 15 minutos
    }

    /**
     * Verifica se um usuário tem determinado tipo
     * Baseado na arquitetura multiempresa explicada
     */
    private function usuarioTemTipo($usuarioId, $tipoCodigo)
    {
        $count = DB::table('empresa_usuario_tipo_rel as rel')
            ->join('empresa_usuario_tipos as t', 'rel.tipo_id', '=', 't.id')
            ->where('rel.usuario_id', $usuarioId)
            ->where('t.codigo', $tipoCodigo)
            ->whereNull('rel.deleted_at')
            ->count();

        return $count > 0;
    }

    /**
     * Obtém todos os tipos de um usuário
     * Retorna array com tipos ordenados por prioridade (principal primeiro)
     * Inclui nível de acesso para verificações hierárquicas
     */
    private function obterTiposUsuario($usuarioId)
    {
        return DB::table('empresa_usuario_tipo_rel as rel')
            ->join('empresa_usuario_tipos as t', 'rel.tipo_id', '=', 't.id')
            ->select('t.id', 't.codigo', 't.nome', 't.nivel_acesso', 'rel.is_primary')
            ->where('rel.usuario_id', $usuarioId)
            ->whereNull('rel.deleted_at')
            ->orderBy('rel.is_primary', 'desc')
            ->orderBy('t.nivel_acesso', 'desc')
            ->orderBy('t.codigo')
            ->get()
            ->toArray();
    }

    /**
     * Obtém o tipo principal do usuário
     * Fundamental para determinar interface padrão e permissões
     * Inclui nível de acesso para verificações
     */
    private function obterTipoPrincipal($usuarioId)
    {
        return DB::table('empresa_usuario_tipo_rel as rel')
            ->join('empresa_usuario_tipos as t', 'rel.tipo_id', '=', 't.id')
            ->select('t.id', 't.codigo', 't.nome', 't.nivel_acesso')
            ->where('rel.usuario_id', $usuarioId)
            ->where('rel.is_primary', 1)
            ->whereNull('rel.deleted_at')
            ->first();
    }

    /**
     * Adiciona um novo tipo a um usuário
     * Implementa a lógica de tipo principal único
     */
    private function adicionarTipoUsuario($usuarioId, $tipoId, $isPrimary = false)
    {
        try {
            DB::beginTransaction();

            // Se for marcado como primário, desmarcar outros primários
            if ($isPrimary) {
                DB::table('empresa_usuario_tipo_rel')
                    ->where('usuario_id', $usuarioId)
                    ->update(['is_primary' => 0]);
            }

            // Inserir ou atualizar relacionamento
            DB::table('empresa_usuario_tipo_rel')
                ->updateOrInsert(
                    ['usuario_id' => $usuarioId, 'tipo_id' => $tipoId],
                    [
                        'is_primary' => $isPrimary,
                        'sync_status' => 'pendente',
                        'sync_data' => now(),
                        'updated_at' => now()
                    ]
                );

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erro ao adicionar tipo ao usuário', [
                'usuario_id' => $usuarioId,
                'tipo_id' => $tipoId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Determina o redirecionamento baseado no tipo principal do usuário
     * Implementa a lógica multiempresa de redirecionamento inteligente
     */
    private function determinarRedirecionamento($usuarioId)
    {
        $tipoPrincipal = $this->obterTipoPrincipal($usuarioId);

        if (!$tipoPrincipal) {
            // Se não tem tipo principal, pegar o primeiro tipo disponível
            $tipos = $this->obterTiposUsuario($usuarioId);
            $tipoPrincipal = $tipos[0] ?? null;
        }

        if (!$tipoPrincipal) {
            Log::warning('Usuário sem tipos atribuídos', ['usuario_id' => $usuarioId]);
            return '/dashboard'; // Fallback padrão
        }

        // Mapeamento de tipos para rotas
        $redirecionamentos = [
            'admin' => '/admin/dashboard',
            'comerciante' => '/comerciante/dashboard',
            'cliente' => '/cliente/dashboard',
            'entregador' => '/entregador/dashboard',
            'user' => '/dashboard'
        ];

        $rota = $redirecionamentos[$tipoPrincipal->codigo] ?? '/dashboard';

        Log::info('Redirecionamento determinado', [
            'usuario_id' => $usuarioId,
            'tipo_principal' => $tipoPrincipal->codigo,
            'rota' => $rota
        ]);

        return $rota;
    }

    /**
     * Verifica se um usuário tem nível de acesso suficiente
     * Implementa a hierarquia de níveis de acesso (1-100)
     * Usa apenas a nova arquitetura de relacionamento
     */
    private function verificarNivelAcesso($usuarioId, $nivelMinimo)
    {
        // Verificar tipos através da tabela de relacionamento
        $nivelMaximo = DB::table('empresa_usuario_tipo_rel as r')
            ->join('empresa_usuario_tipos as t', 'r.tipo_id', '=', 't.id')
            ->where('r.usuario_id', $usuarioId)
            ->whereNull('r.deleted_at')
            ->max('t.nivel_acesso') ?: 0;

        Log::info('Verificação de nível de acesso', [
            'usuario_id' => $usuarioId,
            'nivel_requerido' => $nivelMinimo,
            'nivel_usuario' => $nivelMaximo,
            'acesso_autorizado' => $nivelMaximo >= $nivelMinimo
        ]);

        return $nivelMaximo >= $nivelMinimo;
    }

    /**
     * Obtém o maior nível de acesso do usuário
     * Útil para verificações condicionais
     */
    private function obterMaiorNivelAcesso($usuarioId)
    {
        // Verificar tipos através da tabela de relacionamento
        return DB::table('empresa_usuario_tipo_rel as r')
            ->join('empresa_usuario_tipos as t', 'r.tipo_id', '=', 't.id')
            ->where('r.usuario_id', $usuarioId)
            ->whereNull('r.deleted_at')
            ->max('t.nivel_acesso') ?: 0;
    }

    /**
     * Obtém todos os tipos disponíveis ordenados por hierarquia
     * Baseado na documentação da estrutura
     */
    private function obterTiposDisponiveis()
    {
        return DB::table('empresa_usuario_tipos')
            ->select('id', 'codigo', 'nome', 'descricao', 'nivel_acesso', 'status')
            ->whereNull('deleted_at')
            ->where('status', 'ativo')
            ->orderBy('nivel_acesso', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Verifica se usuário tem permissão de administração
     * Considera níveis 80+ como administrativos
     */
    private function isUsuarioAdmin($usuarioId)
    {
        return $this->verificarNivelAcesso($usuarioId, 80);
    }

    /**
     * Verifica se usuário tem permissão de gerência
     * Considera níveis 60+ como gerenciais
     */
    private function isUsuarioGerente($usuarioId)
    {
        return $this->verificarNivelAcesso($usuarioId, 60);
    }

    /**
     * Verifica se usuário é operacional
     * Considera níveis 30-59 como operacionais
     */
    private function isUsuarioOperacional($usuarioId)
    {
        $nivel = $this->obterMaiorNivelAcesso($usuarioId);
        return $nivel >= 30 && $nivel < 60;
    }

    /**
     * Verifica se usuário é cliente/público
     * Considera níveis 1-29 como público
     */
    private function isUsuarioPublico($usuarioId)
    {
        $nivel = $this->obterMaiorNivelAcesso($usuarioId);
        return $nivel > 0 && $nivel < 30;
    }
}
