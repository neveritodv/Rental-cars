<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <a href="{{ route('backoffice.dashboard') }}" class="logo logo-normal">
            <img src="{{ URL::asset('admin_assets/img/logo.svg') }}" alt="Logo">
        </a>
        <a href="{{ route('backoffice.dashboard') }}" class="logo-small">
            <img src="{{ URL::asset('admin_assets/img/logo-small.svg') }}" alt="Logo">
        </a>
        <a href="{{ route('backoffice.dashboard') }}" class="dark-logo">
            <img src="{{ URL::asset('admin_assets/img/logo-white.svg') }}" alt="Logo">
        </a>
    </div>

    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <!-- MAIN SECTION -->
                <li class="menu-title"><span>Main</span></li>
                <li>
                    <ul>
                        @role('super-admin|admin|manager')
                            <li class="{{ request()->routeIs('backoffice.dashboard') ? 'active' : '' }}">
                                <a href="{{ route('backoffice.dashboard') }}">
                                    <i class="ti ti-layout-dashboard"></i>
                                    <span>Dashboard</span>
                                </a>
                            </li>
                        @endrole
                    </ul>
                </li>

                <!-- AGENCIES SECTION -->
                @role('super-admin|admin')
                    <li class="menu-title"><span>AGENCIES</span></li>
                    <li>
                        <ul>
                            <li class="{{ request()->routeIs('backoffice.agencies.*') ? 'active' : '' }}">
                                <a href="{{ route('backoffice.agencies.index') }}">
                                    <i class="ti ti-building"></i>
                                    <span>Agencies</span>
                                </a>
                            </li>
                            <li class="{{ request()->routeIs('backoffice.agency-subscriptions.*') ? 'active' : '' }}">
                                <a href="{{ route('backoffice.agency-subscriptions.index') }}">
                                    <i class="ti ti-credit-card"></i>
                                    <span>Subscriptions</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endrole

                <!-- ACCÈS SECTION -->
                @role('super-admin|admin')
                    <li class="menu-title"><span>ACCÈS</span></li>
                    <li>
                        <ul>
                            <li class="{{ request()->routeIs('backoffice.roles-permissions.*') ? 'active' : '' }}">
                                <a href="{{ route('backoffice.roles-permissions.roles') }}">
                                    <i class="ti ti-shield"></i>
                                    <span>Rôles & Permissions</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endrole

                <!-- EMPLOYEE SECTION -->
                @role('super-admin|admin|manager')
                    <li class="menu-title"><span>EMPLOYEE</span></li>
                    <li>
                        <ul>
                            <li class="{{ request()->routeIs('backoffice.agents.*') ? 'active' : '' }}">
                                <a href="{{ route('backoffice.agents.index') }}">
                                    <i class="ti ti-users"></i>
                                    <span>Agents</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endrole

                <!-- CLIENTS SECTION -->
                @role('super-admin|admin|manager')
                    <li class="menu-title"><span>CLIENTS</span></li>
                    <li>
                        <ul>
                            <li class="{{ request()->routeIs('backoffice.clients.*') ? 'active' : '' }}">
                                <a href="{{ route('backoffice.clients.index') }}">
                                    <i class="ti ti-user-circle"></i>
                                    <span>Clients</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endrole

                <!-- MANAGEMENT SECTION -->
                @role('super-admin|admin|manager')
                    <li class="menu-title"><span>MANAGEMENT</span></li>
                    <li>
                        <ul>
                            <li class="{{ request()->routeIs('backoffice.users.*') ? 'active' : '' }}">
                                <a href="{{ route('backoffice.users.index') }}">
                                    <i class="ti ti-users"></i>
                                    <span>Users</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endrole

                <!-- ==================== VÉHICULE SUIVI SECTION ==================== -->
                @role('super-admin|admin|manager')
                    <li class="menu-title"><span>VÉHICULE SUIVI</span></li>
                    <li>
                        <ul>
                            <li
                                class="{{ request()->routeIs('backoffice.vehicles.vignettes.index') && request('vehicle') == 'all' ? 'active' : '' }}">
                                <a href="{{ route('backoffice.vehicles.vignettes.index', ['vehicle' => 'all']) }}">
                                    <i class="ti ti-ticket"></i>
                                    <span>Vignettes</span>
                                </a>
                            </li>
                            <li
                                class="{{ request()->routeIs('backoffice.vehicles.insurances.index') && request('vehicle') == 'all' ? 'active' : '' }}">
                                <a href="{{ route('backoffice.vehicles.insurances.index', ['vehicle' => 'all']) }}">
                                    <i class="ti ti-shield"></i>
                                    <span>Assurances</span>
                                </a>
                            </li>
                            <li
                                class="{{ request()->routeIs('backoffice.vehicles.oil-changes.index') && request('vehicle') == 'all' ? 'active' : '' }}">
                                <a href="{{ route('backoffice.vehicles.oil-changes.index', ['vehicle' => 'all']) }}">
                                    <i class="ti ti-droplet"></i>
                                    <span>Vidanges</span>
                                </a>
                            </li>
                            <li
                                class="{{ request()->routeIs('backoffice.vehicles.technical-checks.index') && request('vehicle') == 'all' ? 'active' : '' }}">
                                <a href="{{ route('backoffice.vehicles.technical-checks.index', ['vehicle' => 'all']) }}">
                                    <i class="ti ti-clipboard-check"></i>
                                    <span>Contrôle technique</span>
                                </a>
                            </li>

                            <!-- Contrôles Section with Submenu -->
                            <li class="submenu">
                                <a href="javascript:void(0);"
                                    class="{{ request()->routeIs('backoffice.controls.*') || request()->routeIs('backoffice.control-items.*') ? 'active subdrop' : '' }}">
                                    <i class="ti ti-clipboard-list"></i>
                                    <span>Contrôles</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li>
                                        <a href="{{ route('backoffice.controls.index') }}"
                                            class="{{ request()->routeIs('backoffice.controls.*') && !request()->routeIs('backoffice.control-items.*') ? 'active' : '' }}">
                                            <i class="ti ti-list me-1"></i>
                                            Tous les contrôles
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('backoffice.control-items.index') }}"
                                            class="{{ request()->routeIs('backoffice.control-items.*') ? 'active' : '' }}">
                                            <i class="ti ti-checklist me-1"></i>
                                            Tous les éléments
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                @endrole

                <!-- ==================== CONTRATS SECTION ==================== -->
                @role('super-admin|admin|manager')
                    <li class="menu-title"><span>CONTRATS</span></li>
                    <li>
                        <ul>
                            <!-- Contrats - List all contracts -->
                            <li class="{{ request()->routeIs('backoffice.rental-contracts.index') ? 'active' : '' }}">
                                <a href="{{ route('backoffice.rental-contracts.index') }}">
                                    <i class="ti ti-file-text"></i>
                                    <span>Contrats</span>
                                </a>
                            </li>

                            <!-- Clients du contrat - Manage relationships -->
                            <li class="{{ request()->routeIs('backoffice.contract-clients.*') ? 'active' : '' }}">
                                <a href="{{ route('backoffice.contract-clients.index') }}">
                                    <i class="ti ti-users"></i>
                                    <span>Clients du contrat</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endrole

                <!-- ==================== VÉHICULES SECTION ==================== -->
                @role('super-admin|admin|manager')
                    <li class="menu-title"><span>VÉHICULES</span></li>
                    <li>
                        <ul>
                            <!-- Cars - Liste des véhicules -->
                            <li
                                class="{{ request()->routeIs('backoffice.vehicles.index') && !request()->routeIs('backoffice.vehicles.vignettes.*', 'backoffice.vehicles.insurances.*', 'backoffice.vehicles.oil-changes.*', 'backoffice.vehicle-credits.*') ? 'active' : '' }}">
                                <a href="{{ route('backoffice.vehicles.index') }}">
                                    <i class="ti ti-car"></i>
                                    <span>Cars</span>
                                </a>
                            </li>

                            <!-- ✅ CREDITS - Nouvel onglet pour les crédits véhicules -->
                            <li class="{{ request()->routeIs('backoffice.vehicle-credits.*') ? 'active' : '' }}">
                                <a href="{{ route('backoffice.vehicle-credits.index') }}">
                                    <i class="ti ti-credit-card"></i>
                                    <span>Crédits</span>
                                    @php
                                        $user = auth()->user();
                                        $agency = $user->agency;
                                        $latePayments = 0;
                                        if (class_exists('App\Models\VehicleCredit')) {
                                            $latePayments = \App\Models\CreditPayment::whereHas('credit', function($q) use ($agency) {
                                                $q->where('agency_id', $agency->id)->where('status', 'active');
                                            })
                                            ->where('status', 'pending')
                                            ->where('due_date', '<', now())
                                            ->count();
                                        }
                                    @endphp
                                    @if($latePayments > 0)
                                        <span class="badge bg-danger float-end">{{ $latePayments }}</span>
                                    @endif
                                </a>
                            </li>

                            <!-- Car Attributes with Submenu -->
                            <li class="submenu">
                                <a href="javascript:void(0);"
                                    class="{{ request()->routeIs('backoffice.vehicle-brands.*', 'backoffice.vehicle-models.*') ? 'active subdrop' : '' }}">
                                    <i class="ti ti-device-camera-phone"></i>
                                    <span>Car Attributes</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li>
                                        <a href="{{ route('backoffice.vehicle-brands.index') }}"
                                            class="{{ request()->routeIs('backoffice.vehicle-brands.*') ? 'active' : '' }}">
                                            Brands
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('backoffice.vehicle-models.index') }}"
                                            class="{{ request()->routeIs('backoffice.vehicle-models.*') ? 'active' : '' }}">
                                            Models
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                @endrole

                <!-- ==================== RÉSERVATIONS SECTION ==================== -->
                @role('super-admin|admin|manager')
                    <li class="menu-title"><span>RÉSERVATIONS</span></li>
                    <li>
                        <ul>
                            <li class="{{ request()->routeIs('backoffice.bookings.index') ? 'active' : '' }}">
                                <a href="{{ route('backoffice.bookings.index') }}">
                                    <i class="ti ti-calendar-stats"></i>
                                    <span>Réservations</span>
                                </a>
                            </li>
                            <li class="{{ request()->routeIs('backoffice.bookings.calendar') ? 'active' : '' }}">
                                <a href="{{ route('backoffice.bookings.calendar') }}">
                                    <i class="ti ti-calendar"></i>
                                    <span>Booking Calendar</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endrole

                <!-- ==================== FINANCE SECTION ==================== -->
                @role('super-admin|admin|manager')
                    <li class="menu-title"><span>FINANCE</span></li>
                    <li>
                        <ul>
                            <li class="submenu">
                                <a href="javascript:void(0);"
                                    class="{{ request()->routeIs('backoffice.finance.accounts.*') || request()->routeIs('backoffice.finance.categories.*') || request()->routeIs('backoffice.finance.transactions.*') ? 'active subdrop' : '' }}">
                                    <i class="ti ti-coin"></i>
                                    <span>Gestion financière</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li>
                                        <a href="{{ route('backoffice.finance.accounts.index') }}"
                                            class="{{ request()->routeIs('backoffice.finance.accounts.*') ? 'active' : '' }}">
                                            <i class="ti ti-building-bank me-1"></i>
                                            Comptes
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('backoffice.finance.categories.index') }}"
                                            class="{{ request()->routeIs('backoffice.finance.categories.*') ? 'active' : '' }}">
                                            <i class="ti ti-category me-1"></i>
                                            Catégories
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('backoffice.finance.transactions.index') }}"
                                            class="{{ request()->routeIs('backoffice.finance.transactions.*') ? 'active' : '' }}">
                                            <i class="ti ti-transfer me-1"></i>
                                            Transactions
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                @endrole

                <!-- ==================== FACTURATION SECTION ==================== -->
                @role('super-admin|admin|manager')
                    <li class="menu-title"><span>FACTURATION</span></li>
                    <li>
                        <ul>
                            <!-- Factures -->
                            <li class="{{ request()->routeIs('backoffice.invoices.*') ? 'active' : '' }}">
                                <a href="{{ route('backoffice.invoices.index') }}">
                                    <i class="ti ti-file-invoice"></i>
                                    <span>Factures</span>
                                </a>
                            </li>

                            <!-- Items de facture -->
                            <li class="{{ request()->routeIs('backoffice.invoice-items.*') ? 'active' : '' }}">
                                <a href="{{ route('backoffice.invoice-items.index') }}">
                                    <i class="ti ti-file-description"></i>
                                    <span>Items de facture</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endrole

                <!-- ==================== PAIEMENTS SECTION ==================== -->
                @role('super-admin|admin|manager')
                    <li class="menu-title"><span>PAIEMENTS</span></li>
                    <li>
                        <ul>
                            <li class="{{ request()->routeIs('backoffice.payments.*') ? 'active' : '' }}">
                                <a href="{{ route('backoffice.payments.index') }}">
                                    <i class="ti ti-currency-dollar"></i>
                                    <span>Paiements</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endrole
            </ul>
        </div>
    </div>
</div>
<!-- /Sidebar -->