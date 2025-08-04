<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Empresa;
use Carbon\Carbon;

class EmpresaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $empresas = [
            [
                'nome_fantasia' => 'Tech Solutions',
                'razao_social' => 'Tech Solutions Tecnologia Ltda',
                'cnpj' => '12345678000195',
                'email' => 'contato@techsolutions.com.br',
                'telefone' => '11987654321',
                'endereco' => 'Av. Paulista, 1000, Conj. 101',
                'cep' => '01310100',
                'cidade' => 'São Paulo',
                'estado' => 'SP',
                'plano' => 'premium',
                'status' => 'ativo',
                'valor_mensalidade' => 299.90,
                'data_vencimento' => Carbon::now()->addMonth(),
                'observacoes' => 'Cliente premium com grande volume de transações'
            ],
            [
                'nome_fantasia' => 'ComércioMax',
                'razao_social' => 'ComércioMax Varejo e Atacado S.A.',
                'cnpj' => '98765432000176',
                'email' => 'admin@comerciomax.com.br',
                'telefone' => '21912345678',
                'endereco' => 'Rua das Flores, 500',
                'cep' => '20000000',
                'cidade' => 'Rio de Janeiro',
                'estado' => 'RJ',
                'plano' => 'pro',
                'status' => 'ativo',
                'valor_mensalidade' => 199.90,
                'data_vencimento' => Carbon::now()->addDays(15),
                'observacoes' => 'Empresa com bom histórico de pagamento'
            ],
            [
                'nome_fantasia' => 'StartUp Inovadora',
                'razao_social' => 'StartUp Inovadora ME',
                'cnpj' => '11122233000144',
                'email' => 'info@startup.com.br',
                'telefone' => '11999887766',
                'endereco' => 'Rua dos Empreendedores, 123',
                'cep' => '04567890',
                'cidade' => 'São Paulo',
                'estado' => 'SP',
                'plano' => 'basico',
                'status' => 'ativo',
                'valor_mensalidade' => 99.90,
                'data_vencimento' => Carbon::now()->addDays(30),
                'observacoes' => 'Startup em crescimento acelerado'
            ],
            [
                'nome_fantasia' => 'Empresa Bloqueada',
                'razao_social' => 'Empresa Problemas Ltda',
                'cnpj' => '55555555000155',
                'email' => 'contato@problemas.com.br',
                'telefone' => '11555444333',
                'endereco' => 'Rua da Inadimplência, 666',
                'cep' => '12345678',
                'cidade' => 'São Paulo',
                'estado' => 'SP',
                'plano' => 'basico',
                'status' => 'bloqueado',
                'valor_mensalidade' => 99.90,
                'data_vencimento' => Carbon::now()->subDays(30),
                'observacoes' => 'Empresa bloqueada por inadimplência'
            ],
            [
                'nome_fantasia' => 'Enterprise Corp',
                'razao_social' => 'Enterprise Corporation Brasil S.A.',
                'cnpj' => '33344455000166',
                'email' => 'corporate@enterprise.com.br',
                'telefone' => '11333222111',
                'endereco' => 'Av. Faria Lima, 2000, 10º andar',
                'cep' => '01451000',
                'cidade' => 'São Paulo',
                'estado' => 'SP',
                'plano' => 'enterprise',
                'status' => 'ativo',
                'valor_mensalidade' => 999.90,
                'data_vencimento' => Carbon::now()->addMonths(3),
                'observacoes' => 'Cliente corporativo de grande porte'
            ],
            [
                'nome_fantasia' => 'Comércio Suspenso',
                'razao_social' => 'Comércio Suspenso Eireli',
                'cnpj' => '77788899000177',
                'email' => 'suspenso@comercio.com.br',
                'telefone' => '21777666555',
                'endereco' => 'Rua da Suspensão, 999',
                'cep' => '22000000',
                'cidade' => 'Rio de Janeiro',
                'estado' => 'RJ',
                'plano' => 'pro',
                'status' => 'suspenso',
                'valor_mensalidade' => 199.90,
                'data_vencimento' => Carbon::now()->addDays(5),
                'observacoes' => 'Empresa suspensa temporariamente'
            ]
        ];

        foreach ($empresas as $empresa) {
            Empresa::create($empresa);
        }
    }
}
