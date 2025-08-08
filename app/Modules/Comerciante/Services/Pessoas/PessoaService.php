<?php

namespace App\Modules\Comerciante\Services\Pessoas;

use App\Modules\Comerciante\Models\Pessoas\Pessoa;
use App\Modules\Comerciante\Models\Pessoas\PessoaEndereco;
use App\Modules\Comerciante\Models\Pessoas\PessoaContaBancaria;
use App\Modules\Comerciante\Models\Pessoas\PessoaDocumento;
use App\Modules\Comerciante\Config\PessoasConfig;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PessoaService
{
    protected $config;

    public function __construct(PessoasConfig $config)
    {
        $this->config = $config;
    }

    /**
     * Cria nova pessoa
     */
    public function criar(array $dados, $tipo = 'cliente')
    {
        return DB::transaction(function () use ($dados, $tipo) {
            // Valida dados
            $this->validarDados($dados, $tipo);

            // Aplica configurações padrão
            $dados = $this->aplicarPadroes($dados, $tipo);

            // Gera código se não fornecido
            if (empty($dados['codigo'])) {
                $dados['codigo'] = $this->gerarCodigo($tipo);
            }

            // Cria pessoa
            $pessoa = Pessoa::create($dados);

            // Cria endereço se fornecido
            if (!empty($dados['endereco'])) {
                $this->criarEndereco($pessoa, $dados['endereco'], true);
            }

            // Cria conta bancária se fornecida
            if (!empty($dados['conta_bancaria'])) {
                $this->criarContaBancaria($pessoa, $dados['conta_bancaria'], true);
            }

            return $pessoa->fresh();
        });
    }

    /**
     * Atualiza pessoa
     */
    public function atualizar(Pessoa $pessoa, array $dados)
    {
        return DB::transaction(function () use ($pessoa, $dados) {
            // Valida dados
            $this->validarDados($dados, null, $pessoa->id);

            // Atualiza dados principais
            $pessoa->update($dados);

            // Atualiza endereço principal se fornecido
            if (!empty($dados['endereco'])) {
                $enderecoPrincipal = $pessoa->enderecoPrincipal;
                if ($enderecoPrincipal) {
                    $enderecoPrincipal->update($dados['endereco']);
                } else {
                    $this->criarEndereco($pessoa, $dados['endereco'], true);
                }
            }

            // Atualiza conta bancária principal se fornecida
            if (!empty($dados['conta_bancaria'])) {
                $contaPrincipal = $pessoa->contaBancariaPrincipal;
                if ($contaPrincipal) {
                    $contaPrincipal->update($dados['conta_bancaria']);
                } else {
                    $this->criarContaBancaria($pessoa, $dados['conta_bancaria'], true);
                }
            }

            return $pessoa->fresh();
        });
    }

    /**
     * Remove pessoa (soft delete)
     */
    public function remover(Pessoa $pessoa)
    {
        return DB::transaction(function () use ($pessoa) {
            // Verifica se pode ser removida
            $this->validarRemocao($pessoa);

            // Inativa relacionamentos
            $pessoa->enderecos()->update(['ativo' => false]);
            $pessoa->contasBancarias()->update(['ativo' => false]);
            $pessoa->documentos()->update(['ativo' => false]);

            // Remove pessoa
            $pessoa->delete();

            return true;
        });
    }

    /**
     * Cria endereço para pessoa
     */
    public function criarEndereco(Pessoa $pessoa, array $dadosEndereco, $principal = false)
    {
        $dadosEndereco['pessoa_id'] = $pessoa->id;
        $dadosEndereco['empresa_id'] = $pessoa->empresa_id;
        $dadosEndereco['principal'] = $principal;

        // Se é principal, remove flag dos outros
        if ($principal) {
            PessoaEndereco::where('pessoa_id', $pessoa->id)
                ->update(['principal' => false]);
        }

        $endereco = PessoaEndereco::create($dadosEndereco);

        // Atualiza referência na pessoa
        if ($principal) {
            $pessoa->update(['endereco_principal_id' => $endereco->id]);
        }

        return $endereco;
    }

    /**
     * Cria conta bancária para pessoa
     */
    public function criarContaBancaria(Pessoa $pessoa, array $dadosConta, $principal = false)
    {
        $dadosConta['pessoa_id'] = $pessoa->id;
        $dadosConta['empresa_id'] = $pessoa->empresa_id;
        $dadosConta['principal'] = $principal;

        // Se é principal, remove flag das outras
        if ($principal) {
            PessoaContaBancaria::where('pessoa_id', $pessoa->id)
                ->update(['principal' => false]);
        }

        $conta = PessoaContaBancaria::create($dadosConta);

        // Atualiza referência na pessoa
        if ($principal) {
            $pessoa->update(['conta_bancaria_principal_id' => $conta->id]);
        }

        return $conta;
    }

    /**
     * Upload de documento
     */
    public function uploadDocumento(Pessoa $pessoa, $arquivo, array $dadosDocumento)
    {
        return DB::transaction(function () use ($pessoa, $arquivo, $dadosDocumento) {
            // Valida arquivo
            $this->validarArquivo($arquivo);

            // Define caminho
            $path = config('comerciante.uploads.documentos_path', 'comerciante/documentos');
            $filename = $this->gerarNomeArquivo($arquivo, $pessoa, $dadosDocumento['tipo']);

            // Faz upload
            $url = Storage::disk('public')->putFileAs($path, $arquivo, $filename);

            // Cria registro do documento
            $dadosDocumento = array_merge($dadosDocumento, [
                'pessoa_id' => $pessoa->id,
                'empresa_id' => $pessoa->empresa_id,
                'arquivo_nome' => $filename,
                'arquivo_url' => Storage::url($url),
                'arquivo_tamanho' => $arquivo->getSize(),
                'arquivo_tipo' => $arquivo->getMimeType()
            ]);

            return PessoaDocumento::create($dadosDocumento);
        });
    }

    /**
     * Busca pessoas com filtros
     */
    public function buscar(array $filtros = [], $empresaId = null)
    {
        $query = Pessoa::query();

        if ($empresaId) {
            $query->empresa($empresaId);
        }

        // Filtros
        if (!empty($filtros['nome'])) {
            $query->where(function ($q) use ($filtros) {
                $q->where('nome', 'LIKE', "%{$filtros['nome']}%")
                    ->orWhere('nome_completo', 'LIKE', "%{$filtros['nome']}%")
                    ->orWhere('nome_social', 'LIKE', "%{$filtros['nome']}%");
            });
        }

        if (!empty($filtros['cpf_cnpj'])) {
            $cpfCnpj = preg_replace('/\D/', '', $filtros['cpf_cnpj']);
            $query->where('cpf_cnpj', 'LIKE', "%{$cpfCnpj}%");
        }

        if (!empty($filtros['email'])) {
            $query->where(function ($q) use ($filtros) {
                $q->where('email', 'LIKE', "%{$filtros['email']}%")
                    ->orWhere('email_secundario', 'LIKE', "%{$filtros['email']}%");
            });
        }

        if (!empty($filtros['telefone'])) {
            $telefone = preg_replace('/\D/', '', $filtros['telefone']);
            $query->where(function ($q) use ($telefone) {
                $q->where('telefone', 'LIKE', "%{$telefone}%")
                    ->orWhere('whatsapp', 'LIKE', "%{$telefone}%");
            });
        }

        if (!empty($filtros['tipo'])) {
            $query->tipo($filtros['tipo']);
        }

        if (!empty($filtros['status'])) {
            $query->where('status', $filtros['status']);
        }

        if (!empty($filtros['departamento_id'])) {
            $query->where('departamento_id', $filtros['departamento_id']);
        }

        if (!empty($filtros['cargo_id'])) {
            $query->where('cargo_id', $filtros['cargo_id']);
        }

        // Ordenação
        $orderBy = $filtros['order_by'] ?? 'nome';
        $orderDirection = $filtros['order_direction'] ?? 'asc';
        $query->orderBy($orderBy, $orderDirection);

        return $query;
    }

    /**
     * Valida dados da pessoa
     */
    protected function validarDados(array $dados, $tipo = null, $pessoaId = null)
    {
        $errors = [];

        // Validações obrigatórias baseadas na configuração
        if ($this->config->cpfObrigatorio() && empty($dados['cpf_cnpj'])) {
            $errors[] = 'CPF/CNPJ é obrigatório';
        }

        if ($this->config->emailObrigatorio() && empty($dados['email'])) {
            $errors[] = 'Email é obrigatório';
        }

        if ($this->config->telefoneObrigatorio() && empty($dados['telefone'])) {
            $errors[] = 'Telefone é obrigatório';
        }

        // Validação de CPF/CNPJ único
        if (!empty($dados['cpf_cnpj'])) {
            $query = Pessoa::where('cpf_cnpj', $dados['cpf_cnpj']);
            if ($pessoaId) {
                $query->where('id', '!=', $pessoaId);
            }
            if ($query->exists()) {
                $errors[] = 'CPF/CNPJ já cadastrado';
            }
        }

        // Validação de email único
        if (!empty($dados['email'])) {
            $query = Pessoa::where('email', $dados['email']);
            if ($pessoaId) {
                $query->where('id', '!=', $pessoaId);
            }
            if ($query->exists()) {
                $errors[] = 'Email já cadastrado';
            }
        }

        if (!empty($errors)) {
            throw new \Exception(implode(', ', $errors));
        }
    }

    /**
     * Aplica configurações padrão
     */
    protected function aplicarPadroes(array $dados, $tipo)
    {
        if ($tipo === 'cliente') {
            $dados['limite_credito'] = $dados['limite_credito'] ?? $this->config->limiteCreditoPadrao();
            $dados['limite_fiado'] = $dados['limite_fiado'] ?? $this->config->limiteFiadoPadrao();
            $dados['prazo_pagamento_padrao'] = $dados['prazo_pagamento_padrao'] ?? $this->config->prazoPagamentoCliente();
        }

        return $dados;
    }

    /**
     * Gera código único para pessoa
     */
    protected function gerarCodigo($tipo)
    {
        $prefixo = strtoupper(substr($tipo, 0, 2));
        $numero = Pessoa::where('codigo', 'LIKE', "{$prefixo}%")->count() + 1;

        return $prefixo . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Valida se pessoa pode ser removida
     */
    protected function validarRemocao(Pessoa $pessoa)
    {
        // Verificar se tem vendas, compras, etc.
        // Implementar validações específicas
    }

    /**
     * Valida arquivo de upload
     */
    protected function validarArquivo($arquivo)
    {
        $maxSize = config('comerciante.uploads.max_size', 10485760);
        $allowedTypes = config('comerciante.uploads.allowed_types', []);

        if ($arquivo->getSize() > $maxSize) {
            throw new \Exception('Arquivo muito grande');
        }

        $extension = strtolower($arquivo->getClientOriginalExtension());
        if (!in_array($extension, $allowedTypes)) {
            throw new \Exception('Tipo de arquivo não permitido');
        }
    }

    /**
     * Gera nome único para arquivo
     */
    protected function gerarNomeArquivo($arquivo, Pessoa $pessoa, $tipo)
    {
        $extension = $arquivo->getClientOriginalExtension();
        $timestamp = now()->format('Y-m-d_H-i-s');
        $hash = Str::random(8);

        return "pessoa_{$pessoa->id}_{$tipo}_{$timestamp}_{$hash}.{$extension}";
    }
}
