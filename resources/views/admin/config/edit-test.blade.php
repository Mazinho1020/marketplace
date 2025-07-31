@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1>Test Edit Page</h1>
    
    @if(isset($config))
        <p>Config exists: {{ $config->id }}</p>
        <p>Config chave: {{ $config->chave }}</p>
        <p>Config nome: {{ $config->nome }}</p>
    @else
        <p style="color: red;">Config variable is NOT defined!</p>
    @endif
    
    <p>Variables passed:</p>
    <ul>
        <li>config: {{ isset($config) ? 'YES' : 'NO' }}</li>
        <li>groups: {{ isset($groups) ? 'YES' : 'NO' }}</li>
        <li>sites: {{ isset($sites) ? 'YES' : 'NO' }}</li>
        <li>ambientes: {{ isset($ambientes) ? 'YES' : 'NO' }}</li>
        <li>valor: {{ isset($valor) ? 'YES' : 'NO' }}</li>
    </ul>
</div>
@endsection
