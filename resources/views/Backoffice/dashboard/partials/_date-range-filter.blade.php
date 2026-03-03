<div class="card mb-3">
    <div class="card-body py-2">
        <div class="row align-items-center">
            <div class="col-md-2">
                <h6 class="mb-0 fw-semibold">Période:</h6>
            </div>
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="ti ti-calendar"></i>
                    </span>
                    <input type="date" class="form-control border-start-0" id="start_date" name="start_date" value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}">
                </div>
            </div>
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="ti ti-calendar"></i>
                    </span>
                    <input type="date" class="form-control border-start-0" id="end_date" name="end_date" value="{{ request('end_date', now()->format('Y-m-d')) }}">
                </div>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100" id="applyFilter">
                    <i class="ti ti-filter me-1"></i>Appliquer
                </button>
            </div>
            <div class="col-md-2">
                <select class="form-select" id="quickRange" onchange="quickRangeChange(this.value)">
                    <option value="">Sélectionner</option>
                    <option value="today">Aujourd'hui</option>
                    <option value="yesterday">Hier</option>
                    <option value="week">Cette semaine</option>
                    <option value="month">Ce mois</option>
                    <option value="quarter">Ce trimestre</option>
                    <option value="year">Cette année</option>
                </select>
            </div>
        </div>
    </div>
</div>

<script>
function quickRangeChange(range) {
    const today = new Date();
    let startDate = new Date();
    let endDate = new Date();
    
    switch(range) {
        case 'today':
            startDate = today;
            endDate = today;
            break;
        case 'yesterday':
            startDate = new Date(today.setDate(today.getDate() - 1));
            endDate = new Date(today.setDate(today.getDate()));
            break;
        case 'week':
            startDate = new Date(today.setDate(today.getDate() - today.getDay()));
            endDate = new Date(today.setDate(today.getDate() + 6));
            break;
        case 'month':
            startDate = new Date(today.getFullYear(), today.getMonth(), 1);
            endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            break;
        case 'quarter':
            const quarter = Math.floor(today.getMonth() / 3);
            startDate = new Date(today.getFullYear(), quarter * 3, 1);
            endDate = new Date(today.getFullYear(), (quarter + 1) * 3, 0);
            break;
        case 'year':
            startDate = new Date(today.getFullYear(), 0, 1);
            endDate = new Date(today.getFullYear(), 11, 31);
            break;
    }
    
    document.getElementById('start_date').value = formatDate(startDate);
    document.getElementById('end_date').value = formatDate(endDate);
}

function formatDate(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}
</script>