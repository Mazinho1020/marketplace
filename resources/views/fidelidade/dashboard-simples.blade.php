<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Fidelidade - Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .card {
            border: 1px solid #ddd;
            padding: 20px;
            margin: 10px;
            border-radius: 5px;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
    </style>
</head>

<body>
    <h1>🎯 Dashboard - Programa de Fidelidade</h1>

    <div class="stats">
        <div class="card">
            <h3>Total de Carteiras</h3>
            <h2>{{ number_format($stats['total_carteiras']) }}</h2>
        </div>

        <div class="card">
            <h3>Carteiras Ativas</h3>
            <h2>{{ number_format($stats['carteiras_ativas']) }}</h2>
        </div>

        <div class="card">
            <h3>Transações do Mês</h3>
            <h2>{{ number_format($stats['transacoes_mes']) }}</h2>
        </div>

        <div class="card">
            <h3>Cashback do Mês</h3>
            <h2>R$ {{ number_format($stats['cashback_mes'], 2, ',', '.') }}</h2>
        </div>
    </div>

    <p><small>Dashboard simplificado - funcionando! ✅</small></p>
</body>

</html>