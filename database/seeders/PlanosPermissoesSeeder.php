<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AfiPlanPlanos;

class PlanosPermissoesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Plano Básico
        AfiPlanPlanos::updateOrCreate(
            ['codigo' => 'basico'],
            [
                'empresa_id' => 1, // Global
                'nome' => 'Plano Básico',
                'descricao' => 'Para pequenas empresas iniciantes',
                'preco_mensal' => 29.90,
                'preco_anual' => 299.00,
                'dias_trial' => 7,
                'recursos' => [
                    // Funcionalidades básicas (todas liberadas)
                    'basic_reports' => true,
                    'user_management' => true,
                    'company_management' => true,
                    'brand_management' => true,

                    // Funcionalidades premium (bloqueadas)
                    'advanced_reports' => false,
                    'unlimited_users' => false,
                    'api_access' => false,
                    'custom_branding' => false,
                    'priority_support' => false
                ],
                'limites' => [
                    'max_users' => 3,
                    'max_companies' => 1,
                    'max_brands' => 2,
                    'storage_gb' => 1,
                    'api_calls_month' => 0
                ],
                'ativo' => true
            ]
        );

        // Plano Profissional
        AfiPlanPlanos::updateOrCreate(
            ['codigo' => 'profissional'],
            [
                'empresa_id' => 1, // Global
                'nome' => 'Plano Profissional',
                'descricao' => 'Para empresas em crescimento',
                'preco_mensal' => 79.90,
                'preco_anual' => 799.00,
                'dias_trial' => 14,
                'recursos' => [
                    // Funcionalidades básicas
                    'basic_reports' => true,
                    'user_management' => true,
                    'company_management' => true,
                    'brand_management' => true,

                    // Funcionalidades profissionais
                    'advanced_reports' => true,
                    'unlimited_users' => false,
                    'api_access' => true,
                    'custom_branding' => true,
                    'priority_support' => true,
                    'advanced_integrations' => true,
                    'bulk_operations' => true,

                    // Funcionalidades enterprise (bloqueadas)
                    'custom_fields' => false,
                    'audit_logs' => false,
                    'white_label' => false
                ],
                'limites' => [
                    'max_users' => 10,
                    'max_companies' => 5,
                    'max_brands' => 10,
                    'storage_gb' => 10,
                    'api_calls_month' => 10000
                ],
                'ativo' => true
            ]
        );

        // Plano Enterprise
        AfiPlanPlanos::updateOrCreate(
            ['codigo' => 'enterprise'],
            [
                'empresa_id' => 1, // Global
                'nome' => 'Plano Enterprise',
                'descricao' => 'Para grandes empresas e corporações',
                'preco_mensal' => 199.90,
                'preco_anual' => 1999.00,
                'dias_trial' => 30,
                'recursos' => [
                    // Todas as funcionalidades liberadas
                    'basic_reports' => true,
                    'user_management' => true,
                    'company_management' => true,
                    'brand_management' => true,
                    'advanced_reports' => true,
                    'unlimited_users' => true,
                    'api_access' => true,
                    'custom_branding' => true,
                    'priority_support' => true,
                    'advanced_integrations' => true,
                    'bulk_operations' => true,
                    'custom_fields' => true,
                    'audit_logs' => true,
                    'white_label' => true,
                    'multi_company' => true,
                    'advanced_permissions' => true
                ],
                'limites' => [
                    'max_users' => -1, // Ilimitado
                    'max_companies' => -1, // Ilimitado
                    'max_brands' => -1, // Ilimitado
                    'storage_gb' => 100,
                    'api_calls_month' => 100000
                ],
                'ativo' => true
            ]
        );

        // Plano Free (para testes)
        AfiPlanPlanos::updateOrCreate(
            ['codigo' => 'free'],
            [
                'empresa_id' => 1, // Global
                'nome' => 'Plano Gratuito',
                'descricao' => 'Para testar o sistema',
                'preco_mensal' => 0.00,
                'preco_anual' => 0.00,
                'dias_trial' => 0,
                'recursos' => [
                    // Apenas funcionalidades básicas limitadas
                    'basic_reports' => true,
                    'user_management' => true,
                    'company_management' => true,
                    'brand_management' => false,

                    // Todas as outras bloqueadas
                    'advanced_reports' => false,
                    'unlimited_users' => false,
                    'api_access' => false,
                    'custom_branding' => false,
                    'priority_support' => false
                ],
                'limites' => [
                    'max_users' => 1,
                    'max_companies' => 1,
                    'max_brands' => 0,
                    'storage_gb' => 0.1,
                    'api_calls_month' => 0
                ],
                'ativo' => true
            ]
        );
    }
}
