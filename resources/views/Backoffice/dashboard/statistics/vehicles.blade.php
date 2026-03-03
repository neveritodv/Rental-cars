@php
    $agencyId = auth()->guard('backoffice')->user()->agency_id;
    
    $totalVehicles = App\Models\Vehicle::where('agency_id', $agencyId)->count();
    $avgDailyRate = App\Models\Vehicle::where('agency_id', $agencyId)->avg('daily_rate');
    $totalMileage = App\Models\Vehicle::where('agency_id', $agencyId)->sum('current_mileage');
    
    $topModels = App\Models\Vehicle::where('agency_id', $agencyId)
        ->with('model')
        ->selectRaw('vehicle_model_id, count(*) as count')
        ->groupBy('vehicle_model_id')
        ->orderBy('count', 'desc')
        ->limit(5)
        ->get();
@endphp

<div class="table-responsive">
    <table class="table table-hover">
        <tr>
            <th>Total véhicules</th>
            <td class="text-end fw-bold">{{ $totalVehicles }}</td>
        </tr>
        <tr>
            <th>Tarif journalier moyen</th>
            <td class="text-end fw-bold">{{ number_format($avgDailyRate ?? 0, 2, ',', ' ') }} MAD</td>
        </tr>
        <tr>
            <th>Kilométrage total</th>
            <td class="text-end fw-bold">{{ number_format($totalMileage, 0, ',', ' ') }} km</td>
        </tr>
    </table>
    
    <h6 class="mt-3 mb-2">Modèles les plus populaires</h6>
    <table class="table table-sm">
        @foreach($topModels as $item)
        <tr>
            <td>{{ $item->model->name ?? 'N/A' }}</td>
            <td class="text-end">{{ $item->count }}</td>
        </tr>
        @endforeach
    </table>
</div>