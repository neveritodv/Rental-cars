<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Liste des contrats</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #333;
            margin: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #0d6efd;
            margin: 0;
            font-size: 20px;
        }
        .header p {
            margin: 5px 0 0;
            color: #666;
        }
        .logo-container {
            max-width: 120px;
            max-height: 60px;
        }
        .logo-container img {
            max-width: 100%;
            max-height: 60px;
            object-fit: contain;
        }
        .summary {
            margin-bottom: 20px;
            padding: 10px;
            background: #f0f0f0;
            border: 1px solid #ddd;
            text-align: center;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table th {
            background: #0d6efd;
            color: white;
            font-weight: bold;
            padding: 8px;
            text-align: left;
            font-size: 9px;
        }
        table td {
            padding: 6px 8px;
            border-bottom: 1px solid #dee2e6;
        }
        table tr:nth-child(even) {
            background: #f8f9fa;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 8px;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
        }
        .badge-draft { background: #e2e3e5; color: #383d41; }
        .badge-pending { background: #fff3cd; color: #856404; }
        .badge-accepted { background: #cce5ff; color: #004085; }
        .badge-in_progress { background: #d1ecf1; color: #0c5460; }
        .badge-completed { background: #d4edda; color: #155724; }
        .badge-cancelled { background: #f8d7da; color: #721c24; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h1>LISTE DES CONTRATS DE LOCATION</h1>
            <p>Généré le {{ $generated_at }} par {{ $generated_by }}</p>
        </div>
        @if(isset($logo) && $logo)
        <div class="logo-container">
            <img src="{{ $logo }}" alt="Logo">
        </div>
        @endif
    </div>

    <div class="summary">
        <p>Total: <strong>{{ $total_count }}</strong> contrat(s)</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>N° Contrat</th>
                <th>Client</th>
                <th>Véhicule</th>
                <th>Dates</th>
                <th>Montant</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($contracts as $contract)
            <tr>
                <td>{{ $contract->contract_number }}</td>
                <td>
                    {{ $contract->primaryClient->first_name ?? '' }} {{ $contract->primaryClient->last_name ?? '' }}
                </td>
                <td>{{ $contract->vehicle->registration_number ?? 'N/A' }}</td>
                <td>
                    {{ \Carbon\Carbon::parse($contract->start_date)->format('d/m/Y') }}<br>
                    → {{ \Carbon\Carbon::parse($contract->end_date)->format('d/m/Y') }}
                </td>
                <td class="text-right">{{ number_format($contract->total_amount, 2, ',', ' ') }} MAD</td>
                <td>
                    <span class="badge badge-{{ $contract->status }}">
                        {{ $contract->status_text }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Document généré par {{ config('app.name') }} - Tous droits réservés</p>
    </div>
</body>
</html>