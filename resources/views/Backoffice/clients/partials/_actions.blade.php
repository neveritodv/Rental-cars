<head>
    <style>
        <style>
/* ===== FIX 3-DOT DROPDOWN MENU ===== */

/* Fix dropdown container */
.dropdown {
    position: static !important;
}

/* Fix dropdown menu positioning */
.dropdown-menu {
    position: absolute !important;
    inset: auto 0 auto auto !important;
    transform: translate(0, 40px) !important;
    z-index: 9999 !important;
    min-width: 200px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    border: 1px solid rgba(0,0,0,0.05);
}

/* Fix table cell position */
td.position-static {
    position: relative !important;
}

/* Fix dropdown button */
.btn-icon {
    padding: 6px 10px;
    border-radius: 8px;
    background: transparent;
    border: none;
}

.btn-icon:hover {
    background: #f1f5f9;
}

.btn-icon i {
    font-size: 18px;
    color: #64748b;
}

/* Fix dropdown items */
.dropdown-item {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    padding: 8px 16px !important;
    font-size: 14px !important;
    border-radius: 6px !important;
    transition: all 0.2s ease;
}

.dropdown-item i {
    font-size: 18px;
    width: 20px;
    text-align: center;
}

.dropdown-item:hover {
    background: #f8fafc !important;
}

.dropdown-item.text-danger:hover {
    background: #fff1f0 !important;
    color: #dc3545 !important;
}

/* Fix dropdown divider */
.dropdown-divider {
    margin: 8px 0 !important;
    border-top: 1px solid #e9ecef;
}

/* Fix table overflow */
.table-responsive {
    overflow-x: visible !important;
    overflow-y: visible !important;
}

/* Fix datatable wrapper */
.dataTables_wrapper {
    overflow: visible !important;
}

/* Ensure dropdown shows above everything */
.modal,
.dropdown,
.dropdown-menu {
    z-index: 9999 !important;
}

/* Fix for Bootstrap dropdown container */
.custom-datatable-filter {
    overflow: visible !important;
    position: relative;
}

/* Fix table cell */
.table td:last-child {
    position: relative;
    overflow: visible;
    text-align: right;
}

/* Fix for mobile */
@media (max-width: 768px) {
    .dropdown-menu {
        position: fixed !important;
        top: auto !important;
        bottom: 20px !important;
        left: 20px !important;
        right: 20px !important;
        width: auto !important;
        transform: none !important;
    }
    
    .dropdown-item {
        padding: 12px 16px !important;
    }
}
</style>
    </style>
</head>
<div class="dropdown">
    <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="ti ti-dots-vertical"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu-end p-2">
        <li>
            <a class="dropdown-item rounded-1" href="{{ route('backoffice.clients.show', $client) }}">
                <i class="ti ti-eye me-2"></i>
                Voir détails
            </a>
        </li>
        <li>
            <a class="dropdown-item rounded-1" href="{{ route('backoffice.clients.edit', $client) }}">
                <i class="ti ti-edit me-2"></i>
                Modifier
            </a>
        </li>
        <li>
            <hr class="dropdown-divider">
        </li>
        <li>
            <a class="dropdown-item rounded-1 text-danger" 
               href="#"
               data-bs-toggle="modal" 
               data-bs-target="#delete_client"
               data-delete-action="{{ route('backoffice.clients.destroy', $client->id) }}"
               data-client-name="{{ $client->full_name }}">
                <i class="ti ti-trash me-2"></i>
                Supprimer
            </a>
        </li>
    </ul>
</div>