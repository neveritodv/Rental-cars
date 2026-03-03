
<div class="card mb-3">
    <div class="card-body">
        <div class="row g-2">
            <div class="col-auto">
                <a href="{{ route('backoffice.bookings.create') }}" class="btn btn-outline-primary">
                    <i class="ti ti-plus me-1"></i>Nouvelle réservation
                </a>
            </div>
            <div class="col-auto">
                <a href="{{ route('backoffice.vehicles.create') }}" class="btn btn-outline-success">
                    <i class="ti ti-car me-1"></i>Ajouter véhicule
                </a>
            </div>
            <div class="col-auto">
                <a href="{{ route('backoffice.clients.create') }}" class="btn btn-outline-info">
                    <i class="ti ti-user-plus me-1"></i>Nouveau client
                </a>
            </div>
            <div class="col-auto">
                <a href="{{ route('backoffice.invoices.create') }}" class="btn btn-outline-warning">
                    <i class="ti ti-file-invoice me-1"></i>Nouvelle facture
                </a>
            </div>
            <div class="col-auto">
                <a href="{{ route('backoffice.payments.create') }}" class="btn btn-outline-secondary">
                    <i class="ti ti-credit-card me-1"></i>Enregistrer paiement
                </a>
            </div>
        </div>
    </div>
</div>