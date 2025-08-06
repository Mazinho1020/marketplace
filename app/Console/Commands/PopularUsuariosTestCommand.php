<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Comerciantes\Models\EmpresaUsuario;
use App\Comerciantes\Models\Empresa;
use Illuminate\Support\Str;

class PopularUsuariosTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'marketplace:popular-usuarios-test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Popular usuários de teste para desenvolvimento';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Populando usuários de teste...');

        // Buscar uma empresa para vincular os usuários
        $empresa = Empresa::first();

        if (!$empresa) {
            $this->error('Nenhuma empresa encontrada. Crie uma empresa primeiro.');
            return;
        }

        $this->info("Empresa selecionada: {$empresa->nome_fantasia} (ID: {$empresa->id})");

        // Criar usuários de teste
        $usuarios = [
            [
                'nome' => 'João Silva',
                'username' => 'joao.silva',
                'email' => 'joao.silva@exemplo.com',
                'cargo' => 'Gerente de Vendas',
                'perfil' => 'gerente',
                'permissoes' => ['produtos.view', 'produtos.create', 'vendas.view', 'relatorios.view']
            ],
            [
                'nome' => 'Maria Santos',
                'username' => 'maria.santos',
                'email' => 'maria.santos@exemplo.com',
                'cargo' => 'Assistente Administrativo',
                'perfil' => 'colaborador',
                'permissoes' => ['produtos.view', 'vendas.view']
            ],
            [
                'nome' => 'Carlos Oliveira',
                'username' => 'carlos.oliveira',
                'email' => 'carlos.oliveira@exemplo.com',
                'cargo' => 'Diretor de Operações',
                'perfil' => 'administrador',
                'permissoes' => ['produtos.view', 'produtos.create', 'produtos.edit', 'vendas.view', 'relatorios.view', 'configuracoes.edit', 'usuarios.manage']
            ],
            [
                'nome' => 'Ana Costa',
                'username' => 'ana.costa',
                'email' => 'ana.costa@exemplo.com',
                'cargo' => 'Vendedora',
                'perfil' => 'colaborador',
                'permissoes' => ['produtos.view', 'vendas.view']
            ]
        ];

        foreach ($usuarios as $dadosUsuario) {
            $this->info("Criando usuário: {$dadosUsuario['nome']}");

            // Verificar se o usuário já existe
            $usuarioExistente = EmpresaUsuario::where('email', $dadosUsuario['email'])->first();

            if ($usuarioExistente) {
                $this->warn("  - Usuário já existe: {$dadosUsuario['email']}");
                $usuario = $usuarioExistente;
            } else {
                // Criar o usuário
                $usuario = EmpresaUsuario::create([
                    'uuid' => Str::uuid(),
                    'nome' => $dadosUsuario['nome'],
                    'username' => $dadosUsuario['username'],
                    'email' => $dadosUsuario['email'],
                    'senha' => bcrypt('123456'), // Senha padrão para teste
                    'cargo' => $dadosUsuario['cargo'],
                    'status' => 'ativo',
                ]);

                $this->info("  - Usuário criado com sucesso");
            }

            // Verificar se já está vinculado à empresa
            $vinculoExistente = $empresa->usuariosVinculados()->where('user_id', $usuario->id)->exists();

            if ($vinculoExistente) {
                $this->warn("  - Usuário já está vinculado à empresa");
            } else {
                // Vincular à empresa
                $empresa->usuariosVinculados()->attach($usuario->id, [
                    'perfil' => $dadosUsuario['perfil'],
                    'permissoes' => json_encode($dadosUsuario['permissoes']),
                    'status' => 'ativo',
                    'data_vinculo' => now(),
                ]);

                $this->info("  - Usuário vinculado à empresa");
            }
        }

        $this->info('');
        $this->info('✅ Usuários de teste criados com sucesso!');
        $this->info('');
        $this->info('Usuários criados:');
        foreach ($usuarios as $usuario) {
            $this->line("  - {$usuario['nome']} ({$usuario['email']}) - Senha: 123456");
        }
        $this->info('');
        $this->info("Total de usuários vinculados à empresa: " . $empresa->usuariosVinculados()->count());
    }
}
