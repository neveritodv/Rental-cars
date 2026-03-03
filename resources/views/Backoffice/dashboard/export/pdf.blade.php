
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport - Dreamsrent</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th { background: #0d6efd; color: white; padding: 10px; text-align: left; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { margin: 0; color: #333; }
        .header p { color: #666; margin: 5px 0 0; }
        .summary { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .footer { margin-top: 30px; text-align: center; color: #666; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Dreamsrent</h1>
        <p>Rapport {{ ucfirst(request('type', 'général')) }}</p>
        <p>Période: {{ request('start_date', now()->startOfMonth()->format('d/m/Y')) }} - {{ request('end_date', now()->format('d/m/Y')) }}</p>
    </div>
    
    <div class="summary">
        <h3>Résumé</h3>
        @php
            $agencyId = auth()->guard('backoffice')->user()->agency_id;
            $startDate = request('start_date', now()->startOfMonth());
            $endDate = request('end_date', now());
            
            $totalBookings = App\Models\Booking::where('agency_id', $agencyId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
            $totalRevenue = App\Models\Payment::where('agency_id', $agencyId)
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->sum('amount');
        @endphp
        <p>Total réservations: {{ $totalBookings }}</p>
        <p>Revenu total: {{ number_format($totalRevenue, 2, ',', ' ') }} MAD</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Description</th>
                <th>Montant</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data ?? [] as $item)
            <tr>
                <td>{{ $item['date'] ?? '' }}</td>
                <td>{{ $item['type'] ?? '' }}</td>
                <td>{{ $item['description'] ?? '' }}</td>
                <td>{{ isset($item['amount']) ? number_format($item['amount'], 2, ',', ' ') . ' MAD' : '' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        <p>Généré le {{ now()->format('d/m/Y à H:i') }}</p>
        <p>Dreamsrent - Tous droits réservés</p>
    </div>
</body>
</html>