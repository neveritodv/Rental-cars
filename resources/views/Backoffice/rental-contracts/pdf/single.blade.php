<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Contrat #{{ $contract->contract_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
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
            font-size: 22px;
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
            border: 1px solid #dee2e6;
        }
        .section {
            margin-bottom: 15px;
            border: 1px solid #dee2e6;
        }
        .section-title {
            background: #0d6efd;
            color: white;
            padding: 6px 10px;
            margin: 0;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .section-content {
            padding: 10px;
            background: white;
        }
        .info-row {
            display: flex;
            margin-bottom: 5px;
            padding-bottom: 3px;
            border-bottom: 1px dotted #eee;
        }
        .info-label {
            width: 140px;
            font-weight: bold;
            color: #495057;
        }
        .info-value {
            flex: 1;
        }
        .two-column {
            display: flex;
            gap: 15px;
        }
        .column {
            flex: 1;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        table th {
            background: #f0f0f0;
            border: 1px solid #999;
            padding: 6px;
            text-align: left;
            font-weight: bold;
        }
        table td {
            border: 1px solid #999;
            padding: 5px;
        }
        .total-table {
            width: 300px;
            margin-left: auto;
            border: 1px solid #999;
            background: #f9f9f9;
        }
        .total-table td {
            border: none;
            padding: 5px 8px;
        }
        .total-table .total-row {
            font-weight: bold;
            border-top: 1px solid #000;
            background: #e9ecef;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 8px;
            color: #777;
            border-top: 1px solid #dee2e6;
            padding-top: 8px;
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
        }
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 5px;
            padding-top: 5px;
            font-size: 9px;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9px;
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
            <h1>CONTRAT DE LOCATION</h1>
            <h3>N° {{ $contract->contract_number }}</h3>
        </div>
        @if(isset($logo) && $logo)
        <div class="logo-container">
            <img src="{{ $logo }}" alt="Logo">
        </div>
        @endif
    </div>
    
    <div class="agency-box">
        <strong>{{ $agency->name ?? 'Agence' }}</strong><br>
        @if($agency->address ?? false) {{ $agency->address }}<br> @endif
        @if($agency->phone ?? false) Tél: {{ $agency->phone }} | @endif
        @if($agency->email ?? false) Email: {{ $agency->email }} @endif
    </div>
    
    <!-- Status Badge -->
    <div style="text-align: right; margin-bottom: 10px;">
        <span class="badge badge-{{ $contract->status }}">
            Statut: {{ strtoupper($contract->status_text ?? $contract->status) }}
        </span>
    </div>
    
    <!-- Client and Vehicle in two columns -->
    <div class="two-column">
        <!-- Client Information -->
        <div class="column">
            <div class="section">
                <div class="section-title">CLIENT</div>
                <div class="section-content">
                    <div class="info-row">
                        <div class="info-label">Nom:</div>
                        <div class="info-value"><strong>{{ $client->first_name ?? '' }} {{ $client->last_name ?? '' }}</strong></div>
                    </div>
                    @if($client->email ?? false)
                    <div class="info-row">
                        <div class="info-label">Email:</div>
                        <div class="info-value">{{ $client->email }}</div>
                    </div>
                    @endif
                    @if($client->phone ?? false)
                    <div class="info-row">
                        <div class="info-label">Téléphone:</div>
                        <div class="info-value">{{ $client->phone }}</div>
                    </div>
                    @endif
                    @if($client->cin_number ?? false)
                    <div class="info-row">
                        <div class="info-label">CIN:</div>
                        <div class="info-value">{{ $client->cin_number }}</div>
                    </div>
                    @endif
                    @if($client->driving_license_number ?? false)
                    <div class="info-row">
                        <div class="info-label">Permis:</div>
                        <div class="info-value">{{ $client->driving_license_number }}</div>
                    </div>
                    @endif
                    @if(isset($secondaryClient) && $secondaryClient)
                    <div class="info-row">
                        <div class="info-label">Co-conducteur:</div>
                        <div class="info-value">{{ $secondaryClient->first_name ?? '' }} {{ $secondaryClient->last_name ?? '' }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Vehicle Information -->
        <div class="column">
            <div class="section">
                <div class="section-title">VÉHICULE</div>
                <div class="section-content">
                    @if(isset($vehicle) && $vehicle)
                        <div class="info-row">
                            <div class="info-label">Véhicule:</div>
                            <div class="info-value">
                                @php
                                    $brandName = $vehicle->model->brand->name ?? '';
                                    $modelName = $vehicle->model->name ?? '';
                                    echo trim($brandName . ' ' . $modelName) ?: 'Véhicule';
                                @endphp
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Immatriculation:</div>
                            <div class="info-value">{{ $vehicle->registration_number ?? 'N/A' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Année/Couleur:</div>
                            <div class="info-value">{{ $vehicle->year ?? '' }} {{ $vehicle->color ? '('.$vehicle->color.')' : '' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Kilométrage:</div>
                            <div class="info-value">{{ number_format($vehicle->current_mileage ?? 0, 0, ',', ' ') }} km</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Tarif/jour:</div>
                            <div class="info-value">{{ number_format($vehicle->daily_rate ?? 0, 2, ',', ' ') }} MAD</div>
                        </div>
                    @else
                        <div class="info-value">Aucun véhicule associé</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Rental Details -->
    <div class="section">
        <div class="section-title">DÉTAILS DE LA LOCATION</div>
        <div class="section-content">
            <div class="two-column">
                <div class="column">
                    <div class="info-row">
                        <div class="info-label">Date de début:</div>
                        <div class="info-value">{{ \Carbon\Carbon::parse($contract->start_date)->format('d/m/Y') }} à {{ $contract->start_time ?? '00:00' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Date de fin:</div>
                        <div class="info-value">{{ \Carbon\Carbon::parse($contract->end_date)->format('d/m/Y') }} à {{ $contract->end_time ?? '00:00' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Durée:</div>
                        <div class="info-value">{{ $contract->planned_days ?? $contract->duration_days ?? 'N/A' }} jour(s)</div>
                    </div>
                </div>
                <div class="column">
                    <div class="info-row">
                        <div class="info-label">Lieu départ:</div>
                        <div class="info-value">{{ $contract->pickup_location ?? 'Agence' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Lieu retour:</div>
                        <div class="info-value">{{ $contract->dropoff_location ?? 'Agence' }}</div>
                    </div>
                    @if($contract->observations)
                    <div class="info-row">
                        <div class="info-label">Observations:</div>
                        <div class="info-value">{{ $contract->observations }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Financial Details -->
    <div class="section">
        <div class="section-title">DÉTAILS FINANCIERS</div>
        <div class="section-content">
            <table class="total-table">
                <tr>
                    <td>Tarif journalier:</td>
                    <td class="text-right">{{ number_format($contract->daily_rate ?? 0, 2, ',', ' ') }} MAD</td>
                </tr>
                <tr>
                    <td>Nombre de jours:</td>
                    <td class="text-right">{{ $contract->planned_days ?? $contract->duration_days ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Sous-total:</td>
                    <td class="text-right">{{ number_format(($contract->daily_rate ?? 0) * ($contract->planned_days ?? 0), 2, ',', ' ') }} MAD</td>
                </tr>
                @if(($contract->discount_amount ?? 0) > 0)
                <tr>
                    <td>Remise:</td>
                    <td class="text-right">- {{ number_format($contract->discount_amount, 2, ',', ' ') }} MAD</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td><strong>TOTAL TTC:</strong></td>
                    <td class="text-right"><strong>{{ number_format($contract->total_amount ?? 0, 2, ',', ' ') }} MAD</strong></td>
                </tr>
                @if(($contract->deposit_amount ?? 0) > 0)
                <tr>
                    <td>Caution:</td>
                    <td class="text-right">{{ number_format($contract->deposit_amount, 2, ',', ' ') }} MAD</td>
                </tr>
                @endif
            </table>
        </div>
    </div>
    
    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line">Signature du client</div>
            <div style="font-size: 8px; margin-top: 5px;">{{ $client->first_name ?? '' }} {{ $client->last_name ?? '' }}</div>
        </div>
        <div class="signature-box">
            @if(isset($signature) && $signature)
            <img src="{{ $signature }}" alt="Signature" class="signature-image">
            @endif
            <div class="signature-line">Signature de l'agence</div>
            <div style="font-size: 8px; margin-top: 5px;">{{ $agency->name ?? 'Agence' }}</div>
        </div>
    </div>
    
    <!-- Footer -->
    <div class="footer">
        <p>Document généré le {{ $generated_at }} par {{ $generated_by }}</p>
        <p>Contrat valable sous réserve des conditions générales de location</p>
    </div>
</body>
</html>