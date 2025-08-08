<?php

namespace App\Modules\Comerciante\Config;

use App\Modules\Comerciante\Config\ConfigManager;

class PessoasConfig
{
    protected $config;

    public function __construct(ConfigManager $config)
    {
        $this->config = $config;
    }

    // Configurações de Validação
    public function cpfObrigatorio()
    {
        return $this->config->get('pessoas_cpf_obrigatorio', true);
    }

    public function emailObrigatorio()
    {
        return $this->config->get('pessoas_email_obrigatorio', false);
    }

    public function telefoneObrigatorio()
    {
        return $this->config->get('pessoas_telefone_obrigatorio', true);
    }

    public function enderecoObrigatorio()
    {
        return $this->config->get('pessoas_endereco_obrigatorio', false);
    }

    // Configurações de Padrões
    public function limiteCreditoPadrao()
    {
        return $this->config->get('pessoas_limite_credito_padrao', 500.00);
    }

    public function limiteFiadoPadrao()
    {
        return $this->config->get('pessoas_fiado_limite_padrao', 100.00);
    }

    public function prazoPagamentoCliente()
    {
        return $this->config->get('pessoas_prazo_pagamento_cliente', 30);
    }

    // Configurações de RH
    public function diaFechamentoFolha()
    {
        return $this->config->get('rh_dia_fechamento_folha', 25);
    }

    public function diaPagamentoFolha()
    {
        return $this->config->get('rh_dia_pagamento_folha', 5);
    }

    public function salarioMinimoReferencia()
    {
        return $this->config->get('rh_salario_minimo_referencia', 1412.00);
    }

    public function gerarContaPagarFolha()
    {
        return $this->config->get('rh_gerar_conta_pagar_folha', true);
    }

    // Configurações de Benefícios
    public function valeTransportePercentual()
    {
        return $this->config->get('rh_vale_transporte_percentual', 6.00);
    }

    public function valeAlimentacaoValor()
    {
        return $this->config->get('rh_vale_alimentacao_valor', 25.00);
    }

    // Configurações de PDV
    public function clienteObrigatorioPdv()
    {
        return $this->config->get('vendas_pdv_cliente_obrigatorio', false);
    }

    public function vendedorObrigatorio()
    {
        return $this->config->get('vendas_pdv_vendedor_obrigatorio', true);
    }

    public function gerarContaReceber()
    {
        return $this->config->get('vendas_gerar_conta_receber', true);
    }

    // Configurações de Cliente
    public function verificarLimiteCredito()
    {
        return $this->config->get('vendas_verificar_limite_credito', true);
    }

    public function bloquearClienteInadimplente()
    {
        return $this->config->get('vendas_cliente_inadimplente_bloquear', true);
    }

    // Métodos para obter configurações por grupo
    public function getValidacaoConfigs()
    {
        return $this->config->getGroup('pessoas_validacao');
    }

    public function getPadroesConfigs()
    {
        return $this->config->getGroup('pessoas_defaults');
    }

    public function getRhConfigs()
    {
        return [
            'folha' => $this->config->getGroup('rh_folha'),
            'beneficios' => $this->config->getGroup('rh_beneficios')
        ];
    }

    public function getVendasConfigs()
    {
        return [
            'pdv' => $this->config->getGroup('vendas_pdv'),
            'cliente' => $this->config->getGroup('vendas_cliente')
        ];
    }

    public function getFinanceiroConfigs()
    {
        return [
            'prazos' => $this->config->getGroup('financeiro_prazos'),
            'juros' => $this->config->getGroup('financeiro_juros')
        ];
    }

    // Métodos para atualizar configurações
    public function setCpfObrigatorio($valor)
    {
        return $this->config->set('pessoas_cpf_obrigatorio', $valor);
    }

    public function setEmailObrigatorio($valor)
    {
        return $this->config->set('pessoas_email_obrigatorio', $valor);
    }

    public function setLimiteCreditoPadrao($valor)
    {
        return $this->config->set('pessoas_limite_credito_padrao', $valor);
    }

    public function setSalarioMinimoReferencia($valor)
    {
        return $this->config->set('rh_salario_minimo_referencia', $valor);
    }

    // Validações específicas
    public function validarDadosPessoa(array $dados)
    {
        $errors = [];

        if ($this->cpfObrigatorio() && empty($dados['cpf_cnpj'])) {
            $errors[] = 'CPF/CNPJ é obrigatório';
        }

        if ($this->emailObrigatorio() && empty($dados['email'])) {
            $errors[] = 'Email é obrigatório';
        }

        if ($this->telefoneObrigatorio() && empty($dados['telefone'])) {
            $errors[] = 'Telefone é obrigatório';
        }

        if ($this->enderecoObrigatorio() && empty($dados['endereco'])) {
            $errors[] = 'Endereço é obrigatório';
        }

        return $errors;
    }

    // Aplicar padrões em nova pessoa
    public function aplicarPadroes(array &$dados, $tipo = 'cliente')
    {
        if ($tipo === 'cliente') {
            $dados['limite_credito'] = $dados['limite_credito'] ?? $this->limiteCreditoPadrao();
            $dados['limite_fiado'] = $dados['limite_fiado'] ?? $this->limiteFiadoPadrao();
            $dados['prazo_pagamento_padrao'] = $dados['prazo_pagamento_padrao'] ?? $this->prazoPagamentoCliente();
        }

        return $dados;
    }
}
