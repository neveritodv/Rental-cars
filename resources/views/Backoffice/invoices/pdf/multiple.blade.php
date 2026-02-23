<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Liste des factures</title>
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
        .badge-sent { background: #cce5ff; color: #004085; }
        .badge-paid { background: #d4edda; color: #155724; }
        .badge-partially-paid { background: #fff3cd; color: #856404; }
        .badge-cancelled { background: #f8d7da; color: #721c24; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h1>LISTE DES FACTURES</h1>
            <p>Généré le {{ $generated_at }} par {{ $generated_by }}</p>
        </div>
        @if(isset($logo) && $logo)
        <div class="logo-container">
            <img src="{{ $logo }}" alt="Logo">
        </div>
        @endif
    </div>

    <div class="summary">
        <p>Total: <strong>{{ $total_count }}</strong> facture(s) | Montant total: <strong>{{ number_format($total_amount, 2) }} MAD</strong></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>N° Facture</th>
                <th>Date</th>
                <th>Client</th>
                <th>Contrat</th>
                <th>Montant TTC</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $invoice)
            <tr>
                <td>{{ $invoice->invoice_number }}</td>
                <td>{{ $invoice->formatted_issue_date ?? $invoice->created_at->format('d/m/Y') }}</td>
                <td>
                    @if($invoice->client)
                        {{ $invoice->client->first_name ?? '' }} {{ $invoice->client->last_name ?? '' }}
                    @elseif($invoice->company_name)
                        {{ $invoice->company_name }}
                    @else
                        N/A
                    @endif
                </td>
                <td>{{ $invoice->rentalContract->contract_number ?? 'N/A' }}</td>
                <td class="text-right">{{ number_format($invoice->total_ttc ?? 0, 2, ',', ' ') }} MAD</td>
                <td>
                    <span class="badge badge-{{ $invoice->status }}">
                        {{ $invoice->status_text ?? $invoice->status }}
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