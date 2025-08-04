<?php

namespace App\Services;

use App\Models\Notificacao\NotificacaoTemplate;
use App\Models\Notificacao\NotificacaoTipoEvento;
use App\Models\Notificacao\NotificacaoAplicacao;
use Illuminate\Support\Facades\Cache;

class NotificacaoTemplateService
{
    protected $empresaId;

    public function __construct($empresaId = null)
    {
        $this->empresaId = $empresaId ?? 1;
    }

    public function getTemplate(string $eventoSlug, string $appSlug): ?NotificacaoTemplate
    {
        $cacheKey = "template_{$this->empresaId}_{$eventoSlug}_{$appSlug}";

        return Cache::remember($cacheKey, 1800, function () use ($eventoSlug, $appSlug) {
            $tipoEvento = NotificacaoTipoEvento::where('codigo', $eventoSlug)
                ->where('empresa_id', $this->empresaId)
                ->where('ativo', true)
                ->first();

            if (!$tipoEvento) {
                return null;
            }

            $aplicacao = NotificacaoAplicacao::where('codigo', $appSlug)
                ->where('empresa_id', $this->empresaId)
                ->where('ativo', true)
                ->first();

            if (!$aplicacao) {
                return null;
            }

            return NotificacaoTemplate::where('tipo_evento_id', $tipoEvento->id)
                ->where('aplicacao_id', $aplicacao->id)
                ->where('empresa_id', $this->empresaId)
                ->where('ativo', true)
                ->orderBy('padrao', 'desc')
                ->orderBy('percentual_ab_test', 'desc')
                ->first();
        });
    }

    public function processTemplate(NotificacaoTemplate $template, array $dados): array
    {
        $processed = $template->processarVariaveis($dados);

        // Incrementa contador de uso
        $template->incrementarUso();

        return $processed;
    }

    public function getTemplateWithABTest(string $eventoSlug, string $appSlug, array $userContext = []): ?NotificacaoTemplate
    {
        $templates = $this->getActiveTemplates($eventoSlug, $appSlug);

        if ($templates->isEmpty()) {
            return null;
        }

        // Se só tem um template, retorna ele
        if ($templates->count() === 1) {
            return $templates->first();
        }

        // A/B Testing: escolhe baseado no percentual
        $random = rand(1, 100);
        $accumulated = 0;

        foreach ($templates as $template) {
            $accumulated += $template->percentual_ab_test;
            if ($random <= $accumulated) {
                return $template;
            }
        }

        // Fallback: retorna o primeiro
        return $templates->first();
    }

    protected function getActiveTemplates(string $eventoSlug, string $appSlug)
    {
        $tipoEvento = NotificacaoTipoEvento::where('codigo', $eventoSlug)
            ->where('empresa_id', $this->empresaId)
            ->where('ativo', true)
            ->first();

        if (!$tipoEvento) {
            return collect([]);
        }

        $aplicacao = NotificacaoAplicacao::where('codigo', $appSlug)
            ->where('empresa_id', $this->empresaId)
            ->where('ativo', true)
            ->first();

        if (!$aplicacao) {
            return collect([]);
        }

        return NotificacaoTemplate::where('tipo_evento_id', $tipoEvento->id)
            ->where('aplicacao_id', $aplicacao->id)
            ->where('empresa_id', $this->empresaId)
            ->where('ativo', true)
            ->orderBy('percentual_ab_test', 'desc')
            ->get();
    }

    public function createTemplate(array $data): NotificacaoTemplate
    {
        $template = NotificacaoTemplate::create(array_merge($data, [
            'empresa_id' => $this->empresaId
        ]));

        // Limpa cache
        $this->clearTemplateCache();

        return $template;
    }

    public function updateTemplate(NotificacaoTemplate $template, array $data): NotificacaoTemplate
    {
        $template->update($data);

        // Limpa cache
        $this->clearTemplateCache();

        return $template;
    }

    protected function clearTemplateCache()
    {
        // Remove cache de templates desta empresa
        Cache::tags(['templates', "empresa_{$this->empresaId}"])->flush();
    }
}
