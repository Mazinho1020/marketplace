<?php

return [
    App\Providers\DatabaseConfigServiceProvider::class, // PRIMEIRO para configurar banco
    App\Providers\AppServiceProvider::class,
    // App\Providers\ConfigServiceProvider::class, // TEMPORARIAMENTE DESABILITADO
    App\Providers\FidelidadeServiceProvider::class,
    App\Providers\EmpresaHelperServiceProvider::class,
];
