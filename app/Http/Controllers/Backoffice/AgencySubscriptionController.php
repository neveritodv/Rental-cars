<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backoffice\AgencySubscription\AgencySubscriptionStoreRequest;
use App\Http\Requests\Backoffice\AgencySubscription\AgencySubscriptionUpdateRequest;
use App\Models\Agency;
use App\Models\AgencySubscription;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AgencySubscriptionController extends Controller
{
    use AuthorizesRequests;

    public function index(): View
    {
        //$this->authorize('viewAny', AgencySubscription::class);

        $query = AgencySubscription::query()->with('agency');

        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */
        if (request()->filled('search')) {
            $query->where(function ($q) {
                $q->where('plan_name', 'like', '%' . request('search') . '%')
                  ->orWhereHas('agency', function ($sub) {
                      $sub->where('name', 'like', '%' . request('search') . '%');
                  });
            });
        }

        /*
        |--------------------------------------------------------------------------
        | STATUS FILTER
        |--------------------------------------------------------------------------
        */
        if (request()->filled('status')) {

            switch (request('status')) {

                case 'active':
                    $query->where('is_active', true)
                          ->where(function ($q) {
                              $q->whereNull('ends_at')
                                ->orWhere('ends_at', '>=', now());
                          });
                    break;

                case 'inactive':
                    $query->where('is_active', false);
                    break;

                case 'expired':
                    $query->whereNotNull('ends_at')
                          ->where('ends_at', '<', now());
                    break;

                case 'trial':
                    $query->whereNotNull('trial_ends_at')
                          ->where('trial_ends_at', '>=', now());
                    break;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | PROVIDER FILTER
        |--------------------------------------------------------------------------
        */
        if (request()->filled('provider') && request('provider') !== 'all') {
            $query->where('provider', request('provider'));
        }

        /*
        |--------------------------------------------------------------------------
        | SORT
        |--------------------------------------------------------------------------
        */
        if (request('sort') === 'az') {
            $query->orderBy('plan_name', 'asc');
        } elseif (request('sort') === 'za') {
            $query->orderBy('plan_name', 'desc');
        } else {
            $query->latest();
        }

        $subscriptions = $query->paginate(15)->withQueryString();

        $agencies = Agency::orderBy('name')->get(['id', 'name']);

        return view('backoffice.agency-subscriptions.index', compact('subscriptions', 'agencies'));
    }

    public function create(): View
    {
        $this->authorize('create', AgencySubscription::class);

        $agencies = Agency::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('backoffice.agency-subscriptions.create', compact('agencies'));
    }

    public function store(AgencySubscriptionStoreRequest $request): RedirectResponse
    {
        $this->authorize('create', AgencySubscription::class);

        $subscription = AgencySubscription::create($request->validated());
        $subscription->load('agency');

        $agencyName = $subscription->agency?->name ?? 'Agence';
        
        // FIXED: Use correct module name 'agency-subscription' and the actual subscription object
        $this->createNotification('store', 'agency-subscription', $subscription);

        return redirect()
            ->route('backoffice.agency-subscriptions.index')
            ->with('toast', [
                'title'   => 'Créé',
                'message' => "L’abonnement de « {$agencyName} » a été créé avec succès.",
                'dot'     => '#198754',
                'delay'   => 3500,
                'time'    => 'now',
            ]);
    }

    /**
 * Show the current agency's subscription
 */
public function mySubscription(): View
{
    $user = auth()->user();
    
    // Get the agency of the logged-in user
    $agency = $user->agency;
    
    if (!$agency) {
        abort(404, 'Agence non trouvée');
    }
    
    // Get the subscription for this agency (latest one)
    $subscription = AgencySubscription::where('agency_id', $agency->id)
        ->with('agency')
        ->latest()
        ->first();
    
    // Return the view
    return view('Backoffice.profile.my-subscription', [
        'subscription' => $subscription,
        'agency' => $agency
    ]);
}

    public function show(AgencySubscription $agencySubscription): View
    {
        $this->authorize('view', $agencySubscription);

        $agencySubscription->load('agency');

        return view('backoffice.agency-subscriptions.show', [
            'subscription' => $agencySubscription,
        ]);
    }

    public function edit(AgencySubscription $agencySubscription): View
    {
        $this->authorize('update', $agencySubscription);

        $agencySubscription->load('agency');

        $agencies = Agency::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('backoffice.agency-subscriptions.edit', [
            'subscription' => $agencySubscription,
            'agencies'     => $agencies,
        ]);
    }

    public function update(AgencySubscriptionUpdateRequest $request, AgencySubscription $agencySubscription): RedirectResponse
    {
        $this->authorize('update', $agencySubscription);

        $agencySubscription->update($request->validated());
        $agencySubscription->load('agency');

        $agencyName = $agencySubscription->agency?->name ?? 'Agence';
        
        // ADDED: Create notification for update
        $this->createNotification('update', 'agency-subscription', $agencySubscription);

        return redirect()
            ->route('backoffice.agency-subscriptions.index')
            ->with('toast', [
                'title'   => 'Modifié',
                'message' => "L’abonnement de « {$agencyName} » a été modifié avec succès.",
                'dot'     => '#0d6efd',
                'delay'   => 3500,
                'time'    => 'now',
            ]);
    }

    public function destroy(AgencySubscription $agencySubscription): RedirectResponse
    {
        $this->authorize('delete', $agencySubscription);
 $item->delete();
        $agencySubscription->load('agency');
        $agencyName = $agencySubscription->agency?->name ?? 'Agence';
        
        // Store subscription data for notification before delete
        $subscriptionData = clone $agencySubscription;
        $agencySubscription->delete();
        
        // ADDED: Create notification for delete
        $this->createNotification('destroy', 'agency-subscription', $subscriptionData);

        return redirect()
            ->route('backoffice.agency-subscriptions.index')
            ->with('toast', [
                'title'   => 'Supprimé',
                'message' => "L’abonnement de « {$agencyName} » a été supprimé avec succès.",
                'dot'     => '#dc3545',
                'delay'   => 3500,
                'time'    => 'now',
            ]);
    }
}