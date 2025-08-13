<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class ImageController extends Controller
{
    /**
     * Serve imagens com fallback automático para placeholder
     */
    public function serve(Request $request, $filename)
    {
        $path = 'produtos/' . $filename;

        // Verifica se a imagem existe no storage
        if (Storage::disk('public')->exists($path)) {
            $file = Storage::disk('public')->get($path);
            $fullPath = Storage::disk('public')->path($path);
            $type = File::mimeType($fullPath);

            return response($file, 200)->header('Content-Type', $type);
        }

        // Se não existe, retorna o placeholder SVG
        $placeholderPath = public_path('images/placeholder.svg');

        if (File::exists($placeholderPath)) {
            $placeholder = File::get($placeholderPath);
            return response($placeholder, 200)->header('Content-Type', 'image/svg+xml');
        }

        // Fallback final: retorna 404
        abort(404, 'Imagem não encontrada');
    }

    /**
     * Gera placeholder dinâmico com texto personalizado
     */
    public function placeholder(Request $request, $width = 300, $height = 300, $text = 'Sem Imagem')
    {
        $svg = $this->generatePlaceholderSVG($width, $height, $text);

        return response($svg, 200)->header('Content-Type', 'image/svg+xml');
    }

    private function generatePlaceholderSVG($width, $height, $text)
    {
        return sprintf(
            '<svg width="%d" height="%d" xmlns="http://www.w3.org/2000/svg">
                <rect width="%d" height="%d" fill="#f8f9fa" stroke="#dee2e6" stroke-width="2"/>
                <text x="%d" y="%d" font-family="Arial, sans-serif" font-size="16" text-anchor="middle" fill="#6c757d">%s</text>
                <text x="%d" y="%d" font-family="Arial, sans-serif" font-size="12" text-anchor="middle" fill="#6c757d">%d × %d</text>
            </svg>',
            $width,
            $height,
            $width,
            $height,
            $width / 2,
            $height / 2 - 10,
            htmlspecialchars($text),
            $width / 2,
            $height / 2 + 15,
            $width,
            $height
        );
    }
}
