<?php $page = 'notifications-setting'; ?>
@extends('layout.mainlayout_admin')
@section('content')
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
                    <div class="card">
                        <div class="card-header">
                            <h5>Notification Settings</h5>
                        </div>
                        <div class="card-body">
                            <div class="security-content">
                                <h6 class="mb-3">Notifications</h6>
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="notification-settings">
                                            <h6 class="fs-14 fw-medium mb-1">Notify me about</h6>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="flexRadioDefault"
                                                        id="flexRadioDefault1">
                                                    <label class="form-check-label" for="flexRadioDefault1">
                                                        All New Messages
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="flexRadioDefault"
                                                        id="flexRadioDefault2">
                                                    <label class="form-check-label" for="flexRadioDefault2">
                                                        Mentions Only
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="flexRadioDefault"
                                                        id="flexRadioDefault3">
                                                    <label class="form-check-label" for="flexRadioDefault3">
                                                        Nothing
                                                    </label>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3">
                                            <div>
                                                <h6 class="fs-14 fw-medium mb-1">Notify me about</h6>
                                                <p class="fs-13">Enable desktop notifications to get instant updates on
                                                    bookings, payments, and tenant requests.</p>
                                            </div>
                                            <div class="d-flex justify-content-end">
                                                <div class="form-check form-check-md form-switch">
                                                    <input class="form-check-input form-label" type="checkbox"
                                                        role="switch" checked>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
                                                    <input class="form-check-input form-label" type="checkbox"
                                                        role="switch" checked>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <h6 class="mb-3">Notification Type</h6>
                                <div class="notification-type">
                                    <ul>
                                        <li>
                                            <div
                                                class="d-flex align-items-center justify-content-between flex-wrap row-gap-3">
                                                <div>
                                                    <h6 class="fs-14 fw-medium mb-1">Booking & Rental Updates</h6>
                                                    <p class="fs-13">Get immediate alerts for any changes to bookings or
                                                        rental details to ensure you’re always in the loop.</p>
                                                </div>
                                                <div class="d-flex justify-content-end">
                                                    <div class="form-check form-check-md form-switch">
                                                        <input class="form-check-input form-label" type="checkbox"
                                                            role="switch" checked>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div
                                                class="d-flex align-items-center justify-content-between flex-wrap row-gap-3">
                                                <div>
                                                    <h6 class="fs-14 fw-medium mb-1">Payment & Invoice Notifications</h6>
                                                    <p class="fs-13">Ensure you never miss important rental updates or car
                                                        status changes with the unread notification badge.</p>
                                                </div>
                                                <div class="d-flex justify-content-end">
                                                    <div class="form-check form-check-md form-switch">
                                                        <input class="form-check-input form-label" type="checkbox"
                                                            role="switch" checked>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div
                                                class="d-flex align-items-center justify-content-between flex-wrap row-gap-3">
                                                <div>
                                                    <h6 class="fs-14 fw-medium mb-1">User & Tenant Notifications</h6>
                                                    <p class="fs-13">Get immediate alerts for every payment received,
                                                        failed transactions, and new invoices for smooth financial
                                                        management.</p>
                                                </div>
                                                <div class="d-flex justify-content-end">
                                                    <div class="form-check form-check-md form-switch">
                                                        <input class="form-check-input form-label" type="checkbox"
                                                            role="switch" checked>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div
                                                class="d-flex align-items-center justify-content-between flex-wrap row-gap-3">
                                                <div>
                                                    <h6 class="fs-14 fw-medium mb-1">Vehicle Management</h6>
                                                    <p class="fs-13">Stay informed about vehicle availability and
                                                        maintenance status for a smooth fleet management experience.</p>
                                                </div>
                                                <div class="d-flex justify-content-end">
                                                    <div class="form-check form-check-md form-switch">
                                                        <input class="form-check-input form-label" type="checkbox"
                                                            role="switch" checked>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div
                                                class="d-flex align-items-center justify-content-between flex-wrap row-gap-3">
                                                <div>
                                                    <h6 class="fs-14 fw-medium mb-1">Discounts & Offers</h6>
                                                    <p class="fs-13">Receive real-time updates on all the latest deals and
                                                        special promotions, ensuring you're always informed</p>
                                                </div>
                                                <div class="d-flex justify-content-end">
                                                    <div class="form-check form-check-md form-switch">
                                                        <input class="form-check-input form-label" type="checkbox"
                                                            role="switch" checked>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
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
@endsection
