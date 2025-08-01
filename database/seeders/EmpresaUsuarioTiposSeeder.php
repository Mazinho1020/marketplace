<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmpresaUsuarioTiposSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipos = [
            [
                'codigo' => 'admin',
                'nome' => 'Administrador',
                'descricao' => 'Acesso completo ao sistema',
                'nivel_acesso' => 100,
                'is_active' => true,
                'sync_status' => 'synced',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo' => 'comerciante',
                'nome' => 'Comerciante',
                'descricao' => 'Acesso ao painel de comerciante',
                'nivel_acesso' => 50,
                'is_active' => true,
                'sync_status' => 'synced',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo' => 'cliente',
                'nome' => 'Cliente',
                'descricao' => 'Acesso à área de cliente',
                'nivel_acesso' => 10,
                'is_active' => true,
                'sync_status' => 'synced',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo' => 'entregador',
                'nome' => 'Entregador',
                'descricao' => 'Acesso ao app de entregadores',
                'nivel_acesso' => 20,
                'is_active' => true,
                'sync_status' => 'synced',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($tipos as $tipo) {
            DB::table('empresa_usuario_tipos')->updateOrInsert(
                ['codigo' => $tipo['codigo']],
                $tipo
            );
        }

        $this->command->info('Tipos de usuário criados com sucesso!');
    }
}
