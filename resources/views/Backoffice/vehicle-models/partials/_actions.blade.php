<div class="dropdown">
    <button
        class="btn btn-icon btn-sm btn-light"
        type="button"
        data-bs-toggle="dropdown"
        data-bs-boundary="viewport"
        data-bs-display="static"
        aria-expanded="false">
        <i class="ti ti-dots-vertical"></i>
    </button>

    <ul class="dropdown-menu dropdown-menu-end p-2" style="min-width: 180px;">
        <li>
<a class="dropdown-item rounded-1 d-flex align-items-center edit-model-btn"
   href="javascript:void(0);"
   data-id="{{ $model->id }}"
   data-name="{{ $model->name }}"
   data-brand-id="{{ $model->vehicle_brand_id }}"
   data-status="{{ $model->status }}">
    <i class="ti ti-edit me-2"></i> Modifier
</a>

        </li>

        <li><hr class="dropdown-divider my-1"></li>

        <li>
            <a class="dropdown-item rounded-1 d-flex align-items-center text-danger"
               href="javascript:void(0);"
               data-bs-toggle="modal"
               data-bs-target="#delete_model"
               data-delete-action="{{ route('backoffice.vehicle-models.destroy', $model) }}">
                <i class="ti ti-trash me-2"></i> Supprimer
            </a>
        </li>
    </ul>
</div>
