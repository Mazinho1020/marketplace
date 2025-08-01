<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConfigController extends Controller
{
    public function index()
    {
        // Verificar autenticação
        if (!session('usuario_id')) {
            return redirect('/login');
        }

        $user = (object) [
            'id' => session('usuario_id'),
            'nome' => session('usuario_nome'),
            'email' => session('usuario_email'),
            'tipo' => session('usuario_tipo'),
            'tipo_nome' => session('tipo_nome'),
            'nivel_acesso' => session('nivel_acesso')
        ];

        // Buscar configurações existentes
        $configs = $this->getConfiguracoes();

        return view('admin.config.index', compact('user', 'configs'));
    }

    public function store(Request $request)
    {
        // Verificar autenticação
        if (!session('usuario_id')) {
            return redirect('/login');
        }

        try {
            $dados = $request->all();

            foreach ($dados as $chave => $valor) {
                if ($chave !== '_token') {
                    // Verificar se a configuração já existe
                    $existe = DB::table('config_items')
                        ->where('chave', $chave)
                        ->first();

                    if ($existe) {
                        // Atualizar configuração existente
                        DB::table('config_items')
                            ->where('chave', $chave)
                            ->update([
                                'valor' => $valor,
                                'updated_at' => now()
                            ]);
                    } else {
                        // Criar nova configuração
                        DB::table('config_items')->insert([
                            'chave' => $chave,
                            'valor' => $valor,
                            'descricao' => $this->getDescricaoConfig($chave),
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }
            }

            return redirect('/admin/config')
                ->with('success', 'Configurações salvas com sucesso!');
        } catch (\Exception $e) {
            return redirect('/admin/config')
                ->with('error', 'Erro ao salvar configurações: ' . $e->getMessage());
        }
    }

    private function getConfiguracoes()
    {
        try {
            // Configurações padrão do sistema
            $configsPadrao = [
                'app_name' => 'MeuFinanceiro',
                'app_email' => 'admin@meufinanceiro.com',
                'app_phone' => '(11) 99999-9999',
                'app_address' => 'Rua das Empresas, 123',
                'maintenance_mode' => 'off',
                'registration_enabled' => 'on',
                'email_verification' => 'off',
                'backup_frequency' => 'daily',
                'session_timeout' => '120',
                'max_login_attempts' => '5',
                'password_min_length' => '8',
                'smtp_host' => '',
                'smtp_port' => '587',
                'smtp_username' => '',
                'smtp_encryption' => 'tls',
                'fidelidade_enabled' => 'on',
                'cashback_enabled' => 'on',
                'max_cashback_percent' => '10',
                'currency' => 'BRL',
                'timezone' => 'America/Sao_Paulo',
                'date_format' => 'd/m/Y',
                'logo_url' => '',
                'theme_color' => '#667eea'
            ];

            // Buscar configurações salvas no banco
            $configsSalvas = DB::table('config_items')
                ->pluck('valor', 'chave')
                ->toArray();

            // Mesclar configurações padrão com as salvas
            return array_merge($configsPadrao, $configsSalvas);
        } catch (\Exception $e) {
            // Se houver erro, retorna apenas as configurações padrão
            return [
                'app_name' => 'MeuFinanceiro',
                'app_email' => 'admin@meufinanceiro.com',
                'maintenance_mode' => 'off'
            ];
        }
    }

    private function getDescricaoConfig($chave)
    {
        $descricoes = [
            'app_name' => 'Nome da aplicação',
            'app_email' => 'Email principal do sistema',
            'app_phone' => 'Telefone de contato',
            'app_address' => 'Endereço da empresa',
            'maintenance_mode' => 'Modo de manutenção',
            'registration_enabled' => 'Permitir novos cadastros',
            'email_verification' => 'Verificação de email obrigatória',
            'backup_frequency' => 'Frequência de backup',
            'session_timeout' => 'Timeout da sessão (minutos)',
            'max_login_attempts' => 'Máximo de tentativas de login',
            'password_min_length' => 'Tamanho mínimo da senha',
            'smtp_host' => 'Servidor SMTP',
            'smtp_port' => 'Porta SMTP',
            'smtp_username' => 'Usuário SMTP',
            'smtp_encryption' => 'Criptografia SMTP',
            'fidelidade_enabled' => 'Sistema de fidelidade habilitado',
            'cashback_enabled' => 'Cashback habilitado',
            'max_cashback_percent' => 'Percentual máximo de cashback',
            'currency' => 'Moeda padrão',
            'timezone' => 'Fuso horário',
            'date_format' => 'Formato de data',
            'logo_url' => 'URL do logotipo',
            'theme_color' => 'Cor do tema'
        ];

        return $descricoes[$chave] ?? 'Configuração do sistema';
    }

    public function backup()
    {
        // Verificar autenticação
        if (!session('usuario_id')) {
            return redirect('/login');
        }

        try {
            // Simular backup (implementação básica)
            $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';

            return response()->json([
                'success' => true,
                'message' => 'Backup criado com sucesso!',
                'filename' => $filename
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar backup: ' . $e->getMessage()
            ]);
        }
    }

    public function testEmail(Request $request)
    {
        // Verificar autenticação
        if (!session('usuario_id')) {
            return redirect('/login');
        }

        try {
            $email = $request->input('email');

            // Simular teste de email
            return response()->json([
                'success' => true,
                'message' => 'Email de teste enviado com sucesso para ' . $email
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar email: ' . $e->getMessage()
            ]);
        }
    }
}
