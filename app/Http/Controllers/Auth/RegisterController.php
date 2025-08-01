<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User\EmpresaUsuario;
use App\Models\User\EmpresaUsuarioTipo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;

/**
 * Controller de Registro de Usuários
 * 
 * Segue padrões do PADRONIZACAO_COMPLETA.md:
 * - Multitenancy com empresa_id
 * - Campos de sincronização
 * - Validação robusta
 * - Transações seguras
 */
class RegisterController extends Controller
{
    /**
     * Exibe formulário de registro
     */
    public function showRegistrationForm(Request $request)
    {
        // Buscar empresas ativas da tabela empresas
        $empresas = DB::table('empresas')->where('ativo', true)->get();
        $tiposUsuario = EmpresaUsuarioTipo::where('ativo', true)
            ->whereIn('codigo', ['cliente', 'comerciante'])
            ->get();

        return view('auth.register', [
            'empresas' => $empresas,
            'tiposUsuario' => $tiposUsuario
        ]);
    }

    /**
     * Processa registro de novo usuário
     */
    public function register(Request $request)
    {
        try {
            // Validação dos dados
            $validator = $this->validator($request->all());

            if ($validator->fails()) {
                return back()
                    ->withErrors($validator)
                    ->withInput($request->except('password', 'password_confirmation'));
            }

            DB::beginTransaction();

            // Cria o usuário
            $usuario = $this->create($request->all());

            // Associa tipo de usuário
            $this->associarTipoUsuario($usuario, $request->tipo_usuario);

            // Log de registro
            Log::info('Novo usuário registrado', [
                'usuario_id' => $usuario->id,
                'email' => $usuario->email,
                'nome' => $usuario->nome,
                'empresa_id' => $usuario->empresa_id,
                'tipo' => $request->tipo_usuario,
                'ip' => $request->ip()
            ]);

            DB::commit();

            // Dispara evento de registro
            event(new Registered($usuario));

            // Auto-login após registro (opcional)
            if ($request->auto_login !== false) {
                Auth::login($usuario);
                return redirect()->route('dashboard')->with('success', 'Conta criada com sucesso! Bem-vindo!');
            }

            return redirect()->route('login')->with('success', 'Conta criada com sucesso! Faça login para continuar.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro no registro de usuário', [
                'email' => $request->email ?? 'N/A',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors([
                'email' => 'Erro interno. Tente novamente.'
            ])->withInput($request->except('password', 'password_confirmation'));
        }
    }

    /**
     * Validador dos dados de registro
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'nome' => 'required|string|max:100|min:2',
            'email' => 'required|string|email|max:100|unique:empresa_usuarios,email',
            'telefone' => 'nullable|string|max:20',
            'cpf_cnpj' => 'nullable|string|max:20|unique:empresa_usuarios,cpf_cnpj',
            'password' => 'required|string|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/',
            'empresa_id' => 'required|exists:empresas,id',
            'tipo_usuario' => 'required|exists:empresa_usuario_tipos,codigo',
            'aceito_termos' => 'required|accepted',
            'aceito_privacidade' => 'required|accepted'
        ], [
            'nome.required' => 'O nome é obrigatório.',
            'nome.min' => 'O nome deve ter pelo menos 2 caracteres.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Digite um e-mail válido.',
            'email.unique' => 'Este e-mail já está cadastrado.',
            'cpf_cnpj.unique' => 'Este CPF/CNPJ já está cadastrado.',
            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
            'password.confirmed' => 'A confirmação da senha não confere.',
            'password.regex' => 'A senha deve conter pelo menos: 1 letra minúscula, 1 maiúscula, 1 número e 1 caractere especial.',
            'empresa_id.required' => 'Selecione uma empresa.',
            'empresa_id.exists' => 'Empresa selecionada é inválida.',
            'tipo_usuario.required' => 'Selecione o tipo de usuário.',
            'tipo_usuario.exists' => 'Tipo de usuário inválido.',
            'aceito_termos.accepted' => 'Você deve aceitar os termos de uso.',
            'aceito_privacidade.accepted' => 'Você deve aceitar a política de privacidade.'
        ]);
    }

    /**
     * Cria novo usuário
     */
    protected function create(array $data)
    {
        return EmpresaUsuario::create([
            'nome' => $data['nome'],
            'email' => strtolower($data['email']),
            'telefone' => $data['telefone'] ?? null,
            'cpf_cnpj' => $data['cpf_cnpj'] ?? null,
            'password' => Hash::make($data['password']),
            'empresa_id' => $data['empresa_id'],
            'ativo' => true,
            'email_verified_at' => null, // Verificar depois se necessário
            'tentativas_login' => 0,
            'ultimo_login' => null,

            // Campos de sincronização (padrão)
            'sync_status' => 'pending',
            'sync_at' => null,
            'sync_error' => null,
            'hash_sync' => null,

            // Auditoria
            'created_by' => null,
            'updated_by' => null
        ]);
    }

    /**
     * Associa tipo de usuário
     */
    protected function associarTipoUsuario(EmpresaUsuario $usuario, $codigoTipo)
    {
        $tipo = EmpresaUsuarioTipo::where('codigo', $codigoTipo)->first();

        if ($tipo) {
            $usuario->tipos()->attach($tipo->id, [
                'principal' => true,
                'ativo' => true,
                'data_inicio' => now(),
                'data_fim' => null,

                // Campos de sincronização
                'sync_status' => 'pending',
                'sync_at' => null,
                'sync_error' => null,
                'hash_sync' => null,

                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    /**
     * Verifica se CPF/CNPJ é válido
     */
    protected function validarCpfCnpj($cpfCnpj)
    {
        if (!$cpfCnpj) return true;

        $cpfCnpj = preg_replace('/[^0-9]/', '', $cpfCnpj);

        if (strlen($cpfCnpj) == 11) {
            return $this->validarCpf($cpfCnpj);
        } elseif (strlen($cpfCnpj) == 14) {
            return $this->validarCnpj($cpfCnpj);
        }

        return false;
    }

    /**
     * Valida CPF
     */
    protected function validarCpf($cpf)
    {
        if (strlen($cpf) != 11 || preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }

        return true;
    }

    /**
     * Valida CNPJ
     */
    protected function validarCnpj($cnpj)
    {
        if (strlen($cnpj) != 14) {
            return false;
        }

        // Elimina CNPJs inválidos conhecidos
        if (preg_match('/(\d)\1{13}/', $cnpj)) {
            return false;
        }

        // Valida DVs
        for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++) {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }

        $resto = $soma % 11;

        if ($cnpj[12] != ($resto < 2 ? 0 : 11 - $resto)) {
            return false;
        }

        for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++) {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }

        $resto = $soma % 11;

        return $cnpj[13] == ($resto < 2 ? 0 : 11 - $resto);
    }
}
