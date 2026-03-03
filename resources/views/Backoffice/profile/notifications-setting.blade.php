<?php $page = 'notifications-setting'; ?>
@extends('layout.mainlayout_admin')

@section('content')
<style>
    .notification-type ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .notification-type ul li {
        padding: 15px;
        border: 1px solid #e8e8e8;
        border-radius: 8px;
        margin-bottom: 15px;
        transition: all 0.3s ease;
    }
    .notification-type ul li:hover {
        background: #f8f9fa;
        border-color: #0d6efd;
    }
    .notification-preview-item {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .notification-preview-item:hover {
        background: #f8f9fa;
        transform: translateX(5px);
    }
    .notification-stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }
</style>

<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content me-4 pb-0">

        <!-- Breadcrumb -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Agency Settings</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('backoffice.dashboard') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('backoffice.agencies.index') }}">Agencies</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Notifications</li>
                    </ol>
                </nav>
            </div>
        </div>
        <!-- /Breadcrumb -->

        <div class="row">
            <div class="col-xl-3">
                <!-- inner sidebar -->
                @include('Backoffice.profile.partials._agency_settings_sidebar', [
                    'agency' => $agency,
                    'active' => 'notifications',
                ])
                <!-- /inner sidebar -->
            </div>
            <div class="col-xl-9">
                <!-- Notification Stats -->
                @php
                    $userId = Auth::guard('backoffice')->id();
                    $unreadCount = App\Models\Notification::where('user_id', $userId)->unread()->count();
                    $archivedCount = App\Models\Notification::where('user_id', $userId)->archived()->count();
                    $totalCount = App\Models\Notification::where('user_id', $userId)->active()->count();
                @endphp
                
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h6 class="text-white-50 mb-2">Total Notifications</h6>
                                        <h3 class="text-white mb-0">{{ $totalCount }}</h3>
                                    </div>
                                    <i class="ti ti-bell fs-40 opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h6 class="text-white-50 mb-2">Unread</h6>
                                        <h3 class="text-white mb-0">{{ $unreadCount }}</h3>
                                    </div>
                                    <i class="ti ti-mail fs-40 opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-secondary text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h6 class="text-white-50 mb-2">Archived</h6>
                                        <h3 class="text-white mb-0">{{ $archivedCount }}</h3>
                                    </div>
                                    <i class="ti ti-archive fs-40 opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <form action="{{ route('backoffice.agencies.settings.update', $agency) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    
                    <div class="card">
                        <div class="card-header">
                            <h5>Notification Settings</h5>
                        </div>
                        <div class="card-body">
                            <div class="security-content">
                                <!-- Email Frequency -->
                                <h6 class="mb-3">Email Notifications</h6>
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="notification-settings">
                                            <h6 class="fs-14 fw-medium mb-1">Notify me about</h6>
                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                @php
                                                    $settings = $agency->settings ?? [];
                                                    $emailFrequency = $settings['notifications']['email_frequency'] ?? 'all';
                                                @endphp
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="app[notifications][email_frequency]" 
                                                        id="emailAll" value="all"
                                                        {{ $emailFrequency === 'all' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="emailAll">
                                                        All New Messages
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="app[notifications][email_frequency]" 
                                                        id="emailMentions" value="mentions"
                                                        {{ $emailFrequency === 'mentions' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="emailMentions">
                                                        Mentions Only
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="app[notifications][email_frequency]" 
                                                        id="emailNothing" value="nothing"
                                                        {{ $emailFrequency === 'nothing' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="emailNothing">
                                                        Nothing
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Desktop Notifications -->
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3">
                                            <div>
                                                <h6 class="fs-14 fw-medium mb-1">Desktop Notifications</h6>
                                                <p class="fs-13">Enable desktop notifications to get instant updates on
                                                    bookings, payments, and rental requests.</p>
                                            </div>
                                            <div class="d-flex justify-content-end">
                                                <div class="form-check form-check-md form-switch">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="app[notifications][desktop_enabled]" role="switch" value="1"
                                                        {{ ($settings['notifications']['desktop_enabled'] ?? true) ? 'checked' : '' }}>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Unread Badge -->
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3">
                                            <div>
                                                <h6 class="fs-14 fw-medium mb-1">Unread Notification Badge</h6>
                                                <p class="fs-13">Ensure you never miss important rental updates or car
                                                    status changes with the unread notification badge.</p>
                                            </div>
                                            <div class="d-flex justify-content-end">
                                                <div class="form-check form-check-md form-switch">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="app[notifications][badge_enabled]" role="switch" value="1"
                                                        {{ ($settings['notifications']['badge_enabled'] ?? true) ? 'checked' : '' }}>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Notification Types -->
                                <h6 class="mb-3 mt-4">Notification Types</h6>
                                <div class="notification-type">
                                    <ul>
                                        @php
                                            $notificationTypes = [
                                                'bookings' => [
                                                    'title' => 'Booking & Rental Updates',
                                                    'description' => 'Get immediate alerts for any changes to bookings or rental details.',
                                                    'icon' => 'ti ti-calendar',
                                                    'color' => '#0d6efd'
                                                ],
                                                'payments' => [
                                                    'title' => 'Payment & Invoice Notifications',
                                                    'description' => 'Get alerts for payments received, failed transactions, and new invoices.',
                                                    'icon' => 'ti ti-currency-dollar',
                                                    'color' => '#198754'
                                                ],
                                                'vehicles' => [
                                                    'title' => 'Vehicle Management',
                                                    'description' => 'Stay informed about vehicle availability, maintenance status, and fleet updates.',
                                                    'icon' => 'ti ti-car',
                                                    'color' => '#ffc107'
                                                ],
                                                'users' => [
                                                    'title' => 'User & Client Notifications',
                                                    'description' => 'Get updates about user registrations, client activities, and account changes.',
                                                    'icon' => 'ti ti-users',
                                                    'color' => '#6f42c1'
                                                ],
                                                'contracts' => [
                                                    'title' => 'Contract Updates',
                                                    'description' => 'Notifications for contract creation, renewals, and expirations.',
                                                    'icon' => 'ti ti-file-text',
                                                    'color' => '#dc3545'
                                                ],
                                                'promotions' => [
                                                    'title' => 'Discounts & Offers',
                                                    'description' => 'Receive updates on all the latest deals and special promotions.',
                                                    'icon' => 'ti ti-tag',
                                                    'color' => '#fd7e14'
                                                ]
                                            ];
                                        @endphp

                                        @foreach($notificationTypes as $key => $type)
                                        <li>
                                            <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3">
                                                <div class="d-flex gap-3">
                                                    <span class="avatar avatar-md" style="background-color: {{ $type['color'] }}20;">
                                                        <i class="{{ $type['icon'] }}" style="color: {{ $type['color'] }};"></i>
                                                    </span>
                                                    <div>
                                                        <h6 class="fs-14 fw-medium mb-1">{{ $type['title'] }}</h6>
                                                        <p class="fs-13 text-muted mb-0">{{ $type['description'] }}</p>
                                                    </div>
                                                </div>
                                                <div class="d-flex justify-content-end">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="app[notifications][types][{{ $key }}]" 
                                                            role="switch" value="1"
                                                            {{ ($settings['notifications']['types'][$key] ?? true) ? 'checked' : '' }}>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>

                                <!-- Notification Channels -->
                                <h6 class="mb-3 mt-4">Notification Channels</h6>
                                <div class="row g-3">
                                    @php
                                        $channels = [
                                            'email' => ['label' => 'Email', 'icon' => 'ti ti-mail', 'color' => '#0d6efd', 'value' => auth()->user()->email],
                                            'sms' => ['label' => 'SMS', 'icon' => 'ti ti-device-mobile', 'color' => '#198754', 'value' => $agency->phone ?? 'Not set'],
                                            'push' => ['label' => 'Push Notifications', 'icon' => 'ti ti-bell-ringing', 'color' => '#ffc107', 'value' => 'Browser notifications'],
                                            'slack' => ['label' => 'Slack', 'icon' => 'ti ti-brand-slack', 'color' => '#6f42c1', 'value' => 'Connect Slack'],
                                        ];
                                    @endphp

                                    @foreach($channels as $key => $channel)
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div class="d-flex gap-3">
                                                        <span class="avatar avatar-sm" style="background-color: {{ $channel['color'] }}20;">
                                                            <i class="{{ $channel['icon'] }}" style="color: {{ $channel['color'] }};"></i>
                                                        </span>
                                                        <div>
                                                            <h6 class="mb-1">{{ $channel['label'] }}</h6>
                                                            <small class="text-muted">{{ $channel['value'] }}</small>
                                                        </div>
                                                    </div>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="app[notifications][channel][{{ $key }}]" 
                                                            role="switch" value="1"
                                                            {{ ($settings['notifications']['channel'][$key] ?? ($key === 'email' ? true : false)) ? 'checked' : '' }}>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex align-items-center justify-content-end">
                                <a href="{{ route('backoffice.agencies.index') }}" class="btn btn-light me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Recent Notifications Preview -->
                <div class="card mt-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">Recent Notifications</h5>
                        <a href="{{ route('backoffice.notifications.index') }}" class="btn btn-sm btn-primary">
                            View All <i class="ti ti-arrow-right ms-1"></i>
                        </a>
                    </div>
                    <div class="card-body">
                        @php
                            use App\Models\Notification as NotificationModel;
                            $recentNotifications = NotificationModel::where('user_id', $userId)
                                ->active()
                                ->orderBy('created_at', 'desc')
                                ->limit(5)
                                ->get();
                        @endphp

                        @if($recentNotifications->count() > 0)
                            <div class="notification-list">
                                @foreach($recentNotifications as $notification)
                                    <div class="notification-preview-item d-flex align-items-start p-3 border-bottom">
                                        <div class="flex-shrink-0 me-3">
                                            <span class="avatar avatar-md" style="background-color: {{ $notification->color }}20;">
                                                <i class="{{ $notification->icon }}" style="color: {{ $notification->color }};"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center justify-content-between mb-1">
                                                <h6 class="mb-0">{{ $notification->title }}</h6>
                                                <small class="text-muted">{{ $notification->time_ago }}</small>
                                            </div>
                                            <p class="mb-1 text-muted small">{{ $notification->message }}</p>
                                            <div class="d-flex align-items-center gap-2">
                                                <small class="text-muted">
                                                    <i class="ti ti-user me-1"></i>{{ $notification->performer_name }}
                                                </small>
                                                @if(!$notification->is_read)
                                                    <span class="badge bg-primary">New</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <!-- Quick Actions -->
                            <div class="d-flex align-items-center justify-content-between mt-3 pt-3 border-top">
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-primary" onclick="markAllAsRead()">
                                        <i class="ti ti-check me-1"></i>Mark All Read
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" onclick="archiveAllRead()">
                                        <i class="ti ti-archive me-1"></i>Archive Read
                                    </button>
                                </div>
                                <button class="btn btn-sm btn-outline-danger" onclick="clearAll()">
                                    <i class="ti ti-trash me-1"></i>Clear All
                                </button>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="ti ti-bell-off fs-48 text-gray-4 mb-3"></i>
                                <h6 class="mb-2">No Notifications</h6>
                                <p class="text-muted mb-0">You don't have any notifications yet</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="footer d-sm-flex align-items-center justify-content-between bg-white p-3">
        <p class="mb-0">
            <a href="javascript:void(0);">Privacy Policy</a>
            <a href="javascript:void(0);" class="ms-4">Terms of Use</a>
        </p>
        <p>&copy; 2025 Dreamsrent, Made with <span class="text-danger">❤</span> by <a href="javascript:void(0);"
                class="text-secondary">Dreams</a></p>
    </div>
</div>
<!-- /Page Wrapper -->

<script>
function markAllAsRead() {
    if (!confirm('Mark all notifications as read?')) return;
    
    fetch('/backoffice/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function archiveAllRead() {
    if (!confirm('Archive all read notifications?')) return;
    
    fetch('/backoffice/notifications/archive-all-read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function clearAll() {
    if (!confirm('Clear all notifications? This action cannot be undone.')) return;
    
    fetch('/backoffice/notifications/clear-all', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

// Optional: Real-time updates
setInterval(function() {
    fetch('/backoffice/notifications/unread-count')
        .then(response => response.json())
        .then(data => {
            // Update counts in real-time
            document.querySelectorAll('.notification-count').forEach(el => {
                el.textContent = data.unread;
            });
        });
}, 30000); // Update every 30 seconds
</script>
@endsection