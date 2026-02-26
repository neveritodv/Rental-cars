<div class="table-responsive">
    <table class="table align-middle">
        <thead class="thead-light">
            <tr>
                {{-- Case à cocher - visible seulement si permission DELETE --}}
                @can('bookings.general.delete')
                <th width="50" class="text-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="select-all">
                    </div>
                </th>
                @endcan
                <th>ID</th>
                <th>Client</th>
                <th>Véhicule</th>
                <th>Dates</th>
                <th>Montant</th>
                <th>Source</th>
                <th>Statut</th>
                {{-- Colonne Actions - visible seulement si au moins une permission d'action --}}
                @canany(['bookings.general.view', 'bookings.general.edit', 'bookings.general.delete'])
                <th width="80">Actions</th>
                @endcanany
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $booking)
            <tr>
                {{-- Case à cocher - visible seulement si permission DELETE --}}
                @can('bookings.general.delete')
                <td class="text-center">
                    <div class="form-check">
                        <input class="form-check-input booking-checkbox" type="checkbox" value="{{ $booking->id }}">
                    </div>
                </td>
                @endcan
                
                <td>#{{ $booking->id }}</td>
                
                <td>
                    @if($booking->client)
                        {{-- Lien vers show client - visible seulement si permission VIEW sur clients --}}
                        @can('clients.general.view')
                            <a href="{{ route('backoffice.clients.show', $booking->client_id) }}" class="fw-medium">
                                {{ $booking->client->first_name }} {{ $booking->client->last_name }}
                            </a>
                        @else
                            <span class="fw-medium">{{ $booking->client->first_name }} {{ $booking->client->last_name }}</span>
                        @endcan
                        <br><small>{{ $booking->client->phone ?? '' }}</small>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
                
                <td>
                    @if($booking->vehicle)
                        {{-- Lien vers show véhicule - visible seulement si permission VIEW sur véhicules --}}
                        @can('vehicles.general.view')
                            <a href="{{ route('backoffice.vehicles.show', $booking->vehicle_id) }}" class="fw-medium">
                                {{ $booking->vehicle->registration_number }}
                            </a>
                        @else
                            <span class="fw-medium">{{ $booking->vehicle->registration_number }}</span>
                        @endcan
                        <br><small>{{ $booking->vehicle->model->name ?? '' }}</small>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
                
                <td>
                    <div class="booking-info">
                        <span><i class="ti ti-calendar-check me-1"></i>{{ $booking->formatted_start_date }}</span>
                        <br>
                        <span><i class="ti ti-calendar-x me-1"></i>{{ $booking->formatted_end_date }}</span>
                        <br>
                        <small>{{ $booking->booked_days }} jour(s)</small>
                    </div>
                </td>
                
                <td>
                    <span class="amount-badge">{{ $booking->formatted_estimated_total }}</span>
                </td>
                
                <td>
                    <span class="badge bg-light text-dark">
                        <i class="{{ $booking->source_icon }} me-1"></i>
                        {{ $booking->source_text }}
                    </span>
                </td>
                
                <td>
                    <span class="badge {{ $booking->status_badge_class }}">
                        {{ $booking->status_text }}
                    </span>
                </td>
                
                {{-- Actions - visible seulement si au moins une permission d'action --}}
                @canany(['bookings.general.view', 'bookings.general.edit', 'bookings.general.delete'])
                <td class="text-center">
                    @include('backoffice.bookings.partials._actions', ['booking' => $booking])
                </td>
                @endcanany
            </tr>
            @empty
            <tr>
                @can('bookings.general.delete')
                <td></td>
                @endcan
                <td colspan="{{ (auth()->user()->can('bookings.general.delete') ? 8 : 7) }}" class="text-center py-5">
                    <div class="text-center">
                        <i class="ti ti-calendar-stats fs-48 text-gray-4 mb-3"></i>
                        <h5 class="mb-2">Aucune réservation trouvée</h5>
                        @can('bookings.general.create')
                            <p class="text-muted mb-3">Commencez par créer une nouvelle réservation</p>
                            <a href="{{ route('backoffice.bookings.create') }}" class="btn btn-primary">
                                <i class="ti ti-plus me-2"></i>Nouvelle réservation
                            </a>
                        @endcan
                    </div>
                </td>
                @canany(['bookings.general.view', 'bookings.general.edit', 'bookings.general.delete'])
                <td></td>
                @endcanany
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Script pour "Select All" - seulement si permission DELETE --}}
@can('bookings.general.delete')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('select-all');
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                document.querySelectorAll('.booking-checkbox').forEach(cb => {
                    cb.checked = selectAll.checked;
                });
            });
        }
    });
</script>
@endcan