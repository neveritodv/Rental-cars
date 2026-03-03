
@php
    $agencyId = auth()->guard('backoffice')->user()->agency_id;
    $startDate = request('start_date', now()->startOfMonth());
    $endDate = request('end_date', now());
    
    $totalClients = App\Models\Client::where('agency_id', $agencyId)->count();
    $newClients = App\Models\Client::where('agency_id', $agencyId)
        ->whereBetween('created_at', [$startDate, $endDate])
        ->count();
    
    $repeatClients = App\Models\Booking::where('agency_id', $agencyId)
        ->select('client_id')
        ->groupBy('client_id')
        ->havingRaw('COUNT(*) > 1')
        ->get()
        ->count();
    
    $topClients = App\Models\Booking::where('agency_id', $agencyId)
        ->with('client')
        ->selectRaw('client_id, count(*) as bookings')
        ->groupBy('client_id')
        ->orderBy('bookings', 'desc')
        ->limit(5)
        ->get();
@endphp

<div class="table-responsive">
    <table class="table table-hover">
        <tr>
            <th>Total clients</th>
            <td class="text-end fw-bold">{{ $totalClients }}</td>
        </tr>
        <tr>
            <th>Nouveaux clients</th>
            <td class="text-end fw-bold">{{ $newClients }}</td>
        </tr>
        <tr>
            <th>Clients fidèles (2+ locations)</th>
            <td class="text-end fw-bold">{{ $repeatClients }}</td>
        </tr>
    </table>
    
    <h6 class="mt-3 mb-2">Top clients</h6>
    <table class="table table-sm">
        @foreach($topClients as $item)
        <tr>
            <td>{{ $item->client->first_name ?? '' }} {{ $item->client->last_name ?? '' }}</td>
            <td class="text-end">{{ $item->bookings }} réservations</td>
        </tr>
        @endforeach
    </table>
</div>