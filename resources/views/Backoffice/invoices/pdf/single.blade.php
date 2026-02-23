<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Facture #{{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
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
            font-size: 24px;
        }
        .header h3 {
            margin: 5px 0 0;
            color: #666;
            font-weight: normal;
        }
        .logo-container {
            max-width: 150px;
            max-height: 80px;
        }
        .logo-container img {
            max-width: 100%;
            max-height: 80px;
            object-fit: contain;
        }
        .agency-box {
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }
        .section {
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            overflow: hidden;
        }
        .section-title {
            background: #0d6efd;
            color: white;
            padding: 8px 15px;
            margin: 0;
            font-size: 14px;
            font-weight: bold;
        }
        .section-content {
            padding: 15px;
            background: white;
        }
        .info-row {
            display: flex;
            margin-bottom: 8px;
            border-bottom: 1px dotted #eee;
            padding-bottom: 5px;
        }
        .info-label {
            width: 150px;
            font-weight: bold;
            color: #495057;
        }
        .info-value {
            flex: 1;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table td, table th {
            padding: 8px;
            border: 1px solid #dee2e6;
        }
        table th {
            background: #f8f9fa;
            font-weight: bold;
        }
        .total-table {
            margin-top: 15px;
        }
        .total-table td {
            border: none;
            padding: 5px 8px;
        }
        .total-table .total-row {
            font-weight: bold;
            font-size: 14px;
            background: #e9ecef;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
        }
        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .signature-box {
            width: 200px;
            text-align: center;
        }
        .signature-image {
            max-width: 150px;
            max-height: 60px;
            margin-bottom: 5px;
            display: inline-block;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 5px;
            padding-top: 5px;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }
        .badge-paid { background: #d4edda; color: #155724; }
        .badge-sent { background: #cce5ff; color: #004085; }
        .badge-draft { background: #e2e3e5; color: #383d41; }
        .badge-partially-paid { background: #fff3cd; color: #856404; }
        .badge-cancelled { background: #f8d7da; color: #721c24; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h1>FACTURE</h1>
            <h3>N° {{ $invoice->invoice_number }}</h3>
        </div>
        @if(isset($logo) && $logo)
        <div class="logo-container">
            <img src="{{ $logo }}" alt="Logo">
        </div>
        @endif
    </div>
    
    <div class="agency-box">
        <strong>{{ $invoice->agency->name ?? ($agency->name ?? 'Agence') }}</strong><br>
        @if($invoice->agency->address ?? $agency->address ?? false) 
            {{ $invoice->agency->address ?? $agency->address ?? '' }}<br> 
        @endif
        @if($invoice->agency->phone ?? $agency->phone ?? false) 
            Tél: {{ $invoice->agency->phone ?? $agency->phone ?? '' }}<br> 
        @endif
        @if($invoice->agency->email ?? $agency->email ?? false) 
            Email: {{ $invoice->agency->email ?? $agency->email ?? '' }} 
        @endif
    </div>
    
    <!-- Status -->
    <div style="text-align: right; margin-bottom: 10px;">
        <span class="badge badge-{{ $invoice->status }}">
            Statut: {{ $invoice->status_text ?? $invoice->status }}
        </span>
    </div>
    
    <!-- Client Information -->
    <div class="section">
        <div class="section-title">INFORMATIONS CLIENT</div>
        <div class="section-content">
            @if($invoice->client)
                <div class="info-row">
                    <div class="info-label">Nom complet:</div>
                    <div class="info-value">{{ $invoice->client->first_name ?? '' }} {{ $invoice->client->last_name ?? '' }}</div>
                </div>
                @if($invoice->client->email ?? false)
                <div class="info-row">
                    <div class="info-label">Email:</div>
                    <div class="info-value">{{ $invoice->client->email }}</div>
                </div>
                @endif
                @if($invoice->client->phone ?? false)
                <div class="info-row">
                    <div class="info-label">Téléphone:</div>
                    <div class="info-value">{{ $invoice->client->phone }}</div>
                </div>
                @endif
                @if($invoice->client->address ?? false)
                <div class="info-row">
                    <div class="info-label">Adresse:</div>
                    <div class="info-value">{{ $invoice->client->address }}</div>
                </div>
                @endif
            @elseif($invoice->company_name)
                <div class="info-row">
                    <div class="info-label">Société:</div>
                    <div class="info-value">{{ $invoice->company_name }}</div>
                </div>
            @else
                <div class="info-row">
                    <div class="info-value">Client non spécifié</div>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Invoice Details -->
    <div class="section">
        <div class="section-title">DÉTAILS DE LA FACTURE</div>
        <div class="section-content">
            <div class="info-row">
                <div class="info-label">Date d'émission:</div>
                <div class="info-value">{{ $invoice->formatted_issue_date ?? $invoice->created_at->format('d/m/Y') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Date d'échéance:</div>
                <div class="info-value">{{ $invoice->formatted_due_date ?? 'N/A' }}</div>
            </div>
            @if($invoice->rentalContract)
            <div class="info-row">
                <div class="info-label">Contrat associé:</div>
                <div class="info-value">{{ $invoice->rentalContract->contract_number }}</div>
            </div>
            @endif
        </div>
    </div>
    
    <!-- Items Table -->
    <div class="section">
        <div class="section-title">DÉTAIL DES PRESTATIONS</div>
        <div class="section-content">
            <table>
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Quantité</th>
                        <th>Prix unitaire</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @if($invoice->items && count($invoice->items) > 0)
                        @foreach($invoice->items as $item)
                        <tr>
                            <td>{{ $item->description ?? 'Description' }}</td>
                            <td class="text-center">{{ $item->quantity ?? 1 }}</td>
                            <td class="text-right">{{ number_format($item->unit_price ?? 0, 2, ',', ' ') }} MAD</td>
                            <td class="text-right">{{ number_format($item->total ?? 0, 2, ',', ' ') }} MAD</td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="text-center">Aucun détail disponible</td>
                        </tr>
                    @endif
                </tbody>
            </table>
            
            <!-- Totals -->
            <table class="total-table" style="width: 300px; margin-left: auto;">
                <tr>
                    <td>Sous-total:</td>
                    <td class="text-right">{{ number_format($invoice->subtotal ?? 0, 2, ',', ' ') }} MAD</td>
                </tr>
                @if(($invoice->discount ?? 0) > 0)
                <tr>
                    <td>Remise:</td>
                    <td class="text-right">- {{ number_format($invoice->discount, 2, ',', ' ') }} MAD</td>
                </tr>
                @endif
                <tr>
                    <td>TVA ({{ $invoice->tax_rate ?? 20 }}%):</td>
                    <td class="text-right">{{ number_format($invoice->tax_amount ?? 0, 2, ',', ' ') }} MAD</td>
                </tr>
                <tr class="total-row">
                    <td><strong>TOTAL TTC:</strong></td>
                    <td class="text-right"><strong>{{ number_format($invoice->total_ttc ?? 0, 2, ',', ' ') }} MAD</strong></td>
                </tr>
                @if($invoice->status == 'paid')
                <tr>
                    <td colspan="2" style="color: green; text-align: right;">✓ Payée</td>
                </tr>
                @elseif($invoice->status == 'partially_paid')
                <tr>
                    <td colspan="2" style="color: orange; text-align: right;">Partiellement payée ({{ number_format($invoice->paid_amount ?? 0, 2, ',', ' ') }} MAD)</td>
                </tr>
                @endif
            </table>
        </div>
    </div>
    
    @if($invoice->notes)
    <!-- Notes -->
    <div class="section">
        <div class="section-title">NOTES</div>
        <div class="section-content">
            <p>{{ $invoice->notes }}</p>
        </div>
    </div>
    @endif
    
    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line">Signature du client</div>
        </div>
        <div class="signature-box">
            @if(isset($signature) && $signature)
                <img src="{{ $signature }}" alt="Signature" class="signature-image">
            @endif
            <div class="signature-line">Signature de l'agence</div>
        </div>
    </div>
    
    <!-- Footer -->
    <div class="footer">
        <p>Document généré le {{ $generated_at }} par {{ $generated_by }}</p>
        <p>{{ config('app.name') }} - Tous droits réservés</p>
    </div>
</body>
</html>