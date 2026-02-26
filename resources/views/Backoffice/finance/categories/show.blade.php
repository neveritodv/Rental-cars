<?php $page = 'finance-category-details'; ?>
@extends('layout.mainlayout_admin')

@section('content')
<style>
    .info-label {
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 0.25rem;
    }
    .info-value {
        font-weight: 500;
        margin-bottom: 1rem;
    }
    .badge-income { background: #d4edda; color: #155724; }
    .badge-expense { background: #f8d7da; color: #721c24; }
    .badge-both { background: #cce5ff; color: #004085; }
</style>

<div class="page-wrapper">
    <div class="content me-0">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <a href="{{ route('backoffice.finance.categories.index') }}" class="d-inline-flex align-items-center fw-medium">
                        <i class="ti ti-arrow-left me-1"></i> Retour à la liste
                    </a>
                    <div>
                        {{-- Bouton Modifier - contrôlé par permission EDIT --}}
                        @can('transaction-categories.general.edit')
                            <a href="{{ route('backoffice.finance.categories.edit', $transactionCategory) }}" class="btn btn-primary">
                                <i class="ti ti-edit me-1"></i>Modifier
                            </a>
                        @endcan
                    </div>
                </div>

                <!-- Header Card -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-lg me-3" style="border-radius: 10px; background-color: #f0f3f8;">
                                    <span class="avatar-title fw-bold fs-24 text-primary">
                                        <i class="ti ti-category"></i>
                                    </span>
                                </span>
                                <div>
                                    <h4 class="mb-1">{{ $transactionCategory->name }}</h4>
                                    <p class="mb-0 text-muted">
                                        <i class="ti ti-calendar me-1"></i>
                                        Créée le {{ $transactionCategory->created_at->format('d/m/Y à H:i') }}
                                    </p>
                                </div>
                            </div>
                            <div>
                                <span class="badge {{ $transactionCategory->type_badge_class }} fs-6 p-2">
                                    {{ $transactionCategory->type_text }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body text-center">
                                <h3 class="mb-1">{{ $transactionCategory->transactions_count ?? 0 }}</h3>
                                <p class="text-muted mb-0">Transactions</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body text-center">
                                <h3 class="mb-1">{{ number_format($transactionCategory->total_amount ?? 0, 2, ',', ' ') }} MAD</h3>
                                <p class="text-muted mb-0">Montant total</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Transactions récentes</h5>
                        @can('financial-transactions.general.view')
                            <a href="{{ route('backoffice.finance.transactions.index', ['category_id' => $transactionCategory->id]) }}" class="btn btn-sm btn-primary">
                                Voir toutes
                            </a>
                        @endcan
                    </div>
                    <div class="card-body">
                        @if($transactionCategory->transactions && $transactionCategory->transactions->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Description</th>
                                            <th>Compte</th>
                                            <th>Montant</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($transactionCategory->transactions->take(5) as $transaction)
                                        <tr>
                                            <td>{{ $transaction->formatted_date }}</td>
                                            <td>
                                                @can('financial-transactions.general.view')
                                                    <a href="{{ route('backoffice.finance.transactions.show', $transaction) }}">
                                                        {{ $transaction->description ?? '—' }}
                                                    </a>
                                                @else
                                                    {{ $transaction->description ?? '—' }}
                                                @endcan
                                            </td>
                                            <td>
                                                @if($transaction->account)
                                                    @can('financial-accounts.general.view')
                                                        <a href="{{ route('backoffice.finance.accounts.show', $transaction->financial_account_id) }}">
                                                            {{ $transaction->account->name }}
                                                        </a>
                                                    @else
                                                        {{ $transaction->account->name }}
                                                    @endcan
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td>
                                                <span class="{{ $transaction->type === 'income' ? 'text-success' : 'text-danger' }}">
                                                    {{ $transaction->formatted_amount }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted text-center py-3">Aucune transaction pour cette catégorie</p>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@include('backoffice.finance.categories.partials._modal_delete')
@endsection