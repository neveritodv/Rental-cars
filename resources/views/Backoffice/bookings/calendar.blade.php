@extends('layout.mainlayout_admin')
@section('content')
    <div class="page-wrapper">
        <div class="content">

            <!-- Page Header -->
            <div class="page-header">
                <div class="add-item d-flex" style="justify-content: space-between; align-items: center;">
                    <div>
                        <h3>Booking Calendar</h3>
                        <ul class="breadcrumb">
                            <li><a href="{{ route('backoffice.dashboard') }}">Dashboard</a></li>
                            <li class="active">Booking Calendar</li>
                        </ul>
                    </div>
                    <a href="{{ route('backoffice.bookings.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus"></i> New Booking
                    </a>
                </div>
            </div>
            <!-- /Page Header -->

            <!-- Calendar Section -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Bookings Calendar</h5>
                        </div>
                        <div class="card-body">
                            <div id="calendar" style="background: white; padding: 20px; border-radius: 5px;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bookings List -->
            <div class="row mt-4">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">All Bookings</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th>Client</th>
                                            <th>Vehicle</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Pickup Location</th>
                                            <th>Dropoff Location</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($bookings as $booking)
                                            <tr>
                                                <td>
                                                    <strong>
                                                        {{ $booking->client->first_name ?? 'N/A' }}
                                                        {{ $booking->client->last_name ?? '' }}
                                                    </strong>
                                                </td>
                                                <td>{{ $booking->vehicle->registration_number ?? 'N/A' }}</td>
                                                <td>{{ $booking->start_date->format('M d, Y') }}</td>
                                                <td>{{ $booking->end_date->format('M d, Y') }}</td>
                                                <td>{{ $booking->pickup_location }}</td>
                                                <td>{{ $booking->dropoff_location }}</td>
                                                <td>
                                                    <span
                                                        class="badge badge-{{ $booking->status === 'confirmed' ? 'success' : ($booking->status === 'pending' ? 'warning' : 'danger') }}">
                                                        {{ ucfirst($booking->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('backoffice.bookings.show', $booking) }}"
                                                        class="btn btn-sm btn-info">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('backoffice.bookings.edit', $booking) }}"
                                                        class="btn btn-sm btn-warning">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-muted py-4">
                                                    No bookings found. <a
                                                        href="{{ route('backoffice.bookings.create') }}">Create one</a>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- FullCalendar Library -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            var events = [
                @foreach ($bookings as $booking)
                    {
                        title: '{{ $booking->client->first_name ?? 'Booking' }} - {{ $booking->vehicle->registration_number ?? 'Vehicle' }}',
                        start: '{{ $booking->start_date->format('Y-m-d') }}',
                        end: '{{ $booking->end_date->format('Y-m-d\TH:i:s') }}',
                        backgroundColor: '{{ $booking->status === 'confirmed' ? '#28a745' : ($booking->status === 'pending' ? '#ffc107' : '#dc3545') }}',
                        borderColor: '{{ $booking->status === 'confirmed' ? '#28a745' : ($booking->status === 'pending' ? '#ffc107' : '#dc3545') }}',
                        extendedProps: {
                            bookingId: {{ $booking->id }},
                            client: '{{ $booking->client->first_name ?? '' }} {{ $booking->client->last_name ?? '' }}',
                            vehicle: '{{ $booking->vehicle->registration_number ?? '' }}',
                            pickupLocation: '{{ $booking->pickup_location }}',
                            dropoffLocation: '{{ $booking->dropoff_location }}',
                            status: '{{ $booking->status }}'
                        }
                    },
                @endforeach
            ];

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                height: 'auto',
                events: events,
                eventClick: function(info) {
                    var event = info.event;
                    var props = event.extendedProps;
                    alert(
                        'Booking Details:\n\n' +
                        'Client: ' + props.client + '\n' +
                        'Vehicle: ' + props.vehicle + '\n' +
                        'Pickup: ' + props.pickupLocation + '\n' +
                        'Dropoff: ' + props.dropoffLocation + '\n' +
                        'Status: ' + props.status
                    );
                },
                datesSet: function(info) {
                    console.log('Calendar dates changed', info.start, info.end);
                }
            });

            calendar.render();
        });
    </script>

    <style>
        .badge-success {
            background-color: #28a745;
            color: white;
        }

        .badge-warning {
            background-color: #ffc107;
            color: black;
        }

        .badge-danger {
            background-color: #dc3545;
            color: white;
        }

        .badge {
            padding: 0.5rem 0.75rem;
            border-radius: 0.25rem;
            font-size: 0.875rem;
        }
    </style>
@endsection
