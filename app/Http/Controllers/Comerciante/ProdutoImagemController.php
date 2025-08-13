<?php

namespace App\Http\Controllers\Comerciante;

use App\Http\Controllers\Controller;
use App\Models\Produto;
use App\Models\ProdutoImagem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProdutoImagemController extends Controller
{
    public function __construct()
    {
        // Middleware será aplicado nas rotas
    }

    /**
     * Exibir galeria de imagens do produto
     */
    public function index($produtoId)
    {
        $empresaId = Auth::guard('comerciante')->user()->empresa_id;

        $produto = Produto::where('id', $produtoId)
            ->where('empresa_id', $empresaId)
            ->with(['imagens' => function ($query) {
                $query->orderBy('ordem')->orderBy('created_at');
            }])
            ->firstOrFail();

        return view('comerciantes.produtos.imagens.index', compact('produto'));
    }

    /**
     * Upload de múltiplas imagens
     */
    public function upload(Request $request, $produtoId)
    {
        $request->validate([
            'imagens' => 'required|array|min:1',
            'imagens.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
            'tipo' => 'required|in:principal,galeria,miniatura,zoom'
        ]);

        $empresaId = Auth::guard('comerciante')->user()->empresa_id;

        $produto = Produto::where('id', $produtoId)
            ->where('empresa_id', $empresaId)
            ->firstOrFail();

        $uploadedImages = [];
        $ordem = ProdutoImagem::where('produto_id', $produtoId)->max('ordem') ?? 0;

        foreach ($request->file('imagens') as $arquivo) {
            $ordem++;

            // Gerar nome único para o arquivo
            $nomeArquivo = time() . '_' . $ordem . '_' . Str::random(10) . '.' . $arquivo->getClientOriginalExtension();

            // Fazer upload para storage/produtos
            $caminhoArquivo = $arquivo->storeAs('produtos', $nomeArquivo, 'public');

            // Obter dimensões da imagem
            $dimensoes = getimagesize($arquivo->getPathname());
            $dimensoesTexto = $dimensoes ? $dimensoes[0] . 'x' . $dimensoes[1] : null;

            // Criar registro na base de dados
            $imagem = ProdutoImagem::create([
                'empresa_id' => $empresaId,
                'produto_id' => $produtoId,
                'tipo' => $request->tipo,
                'arquivo' => $nomeArquivo,
                'titulo' => $request->input('titulo') ?? $produto->nome,
                'alt_text' => $request->input('alt_text') ?? $produto->nome,
                'ordem' => $ordem,
                'tamanho_arquivo' => $arquivo->getSize(),
                'dimensoes' => $dimensoesTexto,
                'ativo' => true
            ]);

            $uploadedImages[] = $imagem;
        }

        return redirect()->back()->with('success', count($uploadedImages) . ' imagem(ns) enviada(s) com sucesso!');
    }

    /**
     * Atualizar dados da imagem
     */
    public function update(Request $request, $produtoId, $imagemId)
    {
        $request->validate([
            'titulo' => 'nullable|string|max:255',
            'alt_text' => 'nullable|string|max:255',
            'tipo' => 'required|in:principal,galeria,miniatura,zoom',
            'ordem' => 'nullable|integer|min:0'
        ]);

        $empresaId = Auth::guard('comerciante')->user()->empresa_id;

        $imagem = ProdutoImagem::whereHas('produto', function ($query) use ($empresaId) {
            $query->where('empresa_id', $empresaId);
        })
            ->where('produto_id', $produtoId)
            ->where('id', $imagemId)
            ->firstOrFail();

        $imagem->update($request->only(['titulo', 'alt_text', 'tipo', 'ordem']));

        return redirect()->back()->with('success', 'Imagem atualizada com sucesso!');
    }

    /**
     * Remover imagem
     */
    public function destroy($produtoId, $imagemId)
    {
        $empresaId = Auth::guard('comerciante')->user()->empresa_id;

        $imagem = ProdutoImagem::whereHas('produto', function ($query) use ($empresaId) {
            $query->where('empresa_id', $empresaId);
        })
            ->where('produto_id', $produtoId)
            ->where('id', $imagemId)
            ->firstOrFail();

        // Remover arquivo físico
        if (Storage::disk('public')->exists('produtos/' . $imagem->arquivo)) {
            Storage::disk('public')->delete('produtos/' . $imagem->arquivo);
        }

        // Remover registro da base de dados
        $imagem->delete();

        return redirect()->back()->with('success', 'Imagem removida com sucesso!');
    }

    /**
     * Definir como imagem principal
     */
    public function setPrincipal($produtoId, $imagemId)
    {
        $empresaId = Auth::guard('comerciante')->user()->empresa_id;

        // Verificar se a imagem pertence ao produto e empresa
        $imagem = ProdutoImagem::whereHas('produto', function ($query) use ($empresaId) {
            $query->where('empresa_id', $empresaId);
        })
            ->where('produto_id', $produtoId)
            ->where('id', $imagemId)
            ->firstOrFail();

        // Remover tipo principal de outras imagens do produto
        ProdutoImagem::where('produto_id', $produtoId)
            ->where('tipo', 'principal')
            ->update(['tipo' => 'galeria']);

        // Definir esta como principal
        $imagem->update(['tipo' => 'principal']);

        return redirect()->back()->with('success', 'Imagem definida como principal!');
    }

    /**
     * Reordenar imagens via AJAX
     */
    public function reordenar(Request $request, $produtoId)
    {
        $request->validate([
            'imagens' => 'required|array',
            'imagens.*' => 'required|integer|exists:produto_imagens,id'
        ]);

        $empresaId = Auth::guard('comerciante')->user()->empresa_id;

        // Verificar se todas as imagens pertencem ao produto e empresa
        $imagens = ProdutoImagem::whereHas('produto', function ($query) use ($empresaId) {
            $query->where('empresa_id', $empresaId);
        })
            ->where('produto_id', $produtoId)
            ->whereIn('id', $request->imagens)
            ->get();

        if ($imagens->count() !== count($request->imagens)) {
            return response()->json(['error' => 'Algumas imagens não foram encontradas'], 400);
        }

        // Atualizar ordem
        foreach ($request->imagens as $ordem => $imagemId) {
            ProdutoImagem::where('id', $imagemId)->update(['ordem' => $ordem + 1]);
        }

        return response()->json(['success' => true]);
    }
}
