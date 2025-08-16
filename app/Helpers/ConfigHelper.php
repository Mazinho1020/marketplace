<?php

if (!function_exists('config_app')) {
    function config_app($key, $default = null)
    {
        return config("app.{$key}", $default);
    }
}

if (!function_exists('config_database')) {
    function config_database($key, $default = null)
    {
        return config("database.{$key}", $default);
    }
}

if (!function_exists('config_cache')) {
    function config_cache($key, $default = null)
    {
        return config("cache.{$key}", $default);
    }
}