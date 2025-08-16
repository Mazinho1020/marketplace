<?php

if (!function_exists('empresaAtual')) {
    function empresaAtual()
    {
        if (auth()->check() && auth()->user()) {
            return auth()->user()->empresa_id;
        }
        return null;
    }
}