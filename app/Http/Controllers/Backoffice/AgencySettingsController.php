<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backoffice\Agency\UpdateAgencySettingsRequest;
use App\Models\Agency;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class AgencySettingsController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display the settings form for the given agency.
     */
    public function edit(Agency $agency): View
    {
        $this->authorize('update', $agency);

        return view('backoffice.agencies.settings.edit', compact('agency'));
    }

    /**
     * Display the notifications settings form.
     */
    public function notifications(Agency $agency): View
    {
        $this->authorize('update', $agency);

        return view('Backoffice.profile.notifications-setting', compact('agency'));
    }

    /**
     * Display the invoice template settings form.
     */
    public function invoiceTemplate(Agency $agency): View
    {
        $this->authorize('update', $agency);

        return view('Backoffice.profile.invoice-template', compact('agency'));
    }

    /**
     * Display the company (system) settings form.
     */
    public function company(Agency $agency): View
    {
        $this->authorize('update', $agency);

        return view('Backoffice.profile.company-setting', compact('agency'));
    }

    /**
     * Display the signatures (branding) settings form.
     */
    public function signatures(Agency $agency): View
    {
        $this->authorize('update', $agency);

        return view('Backoffice.profile.signatures-setting', compact('agency'));
    }

    /**
     * Update company settings via POST
     */
    public function updateCompany(Request $request, Agency $agency): RedirectResponse
    {
        $this->authorize('update', $agency);

        // Manually validate the request data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'legal_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'default_currency' => 'nullable|string|max:10',
            'description' => 'nullable|string',
            'tp_number' => 'nullable|string|max:50',
            'rc_number' => 'nullable|string|max:50',
            'if_number' => 'nullable|string|max:50',
            'ice_number' => 'nullable|string|max:50',
            'vat_number' => 'nullable|string|max:50',
            'creation_date' => 'nullable|date',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'signature' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Extract media files from validated data
        $logo = $request->file('logo');
        $signature = $request->file('signature');

        // Remove media from validated array to avoid storing in settings JSON
        unset($validated['logo'], $validated['signature']);

        // Merge settings (deep merge to avoid overwriting all settings)
        $currentSettings = $agency->settings ?? [];
        $newSettings = array_replace_recursive($currentSettings, $validated);

        // Update settings
        $agency->update(['settings' => $newSettings]);

        // Handle media uploads
        if ($logo) {
            $agency->clearMediaCollection('logo');
            $agency->addMedia($logo)->toMediaCollection('logo');
        }

        if ($signature) {
            $agency->clearMediaCollection('signature');
            $agency->addMedia($signature)->toMediaCollection('signature');
        }

        // Create notification for update
        $this->createNotification('update', 'agency_settings', $agency);

        return redirect()
            ->route('backoffice.agencies.settings.company', $agency)
            ->with('toast', [
                'title'   => 'Updated',
                'message' => "Les paramètres de l'agence « {$agency->name} » ont été mis à jour avec succès.",
                'dot'     => '#0d6efd', // blue
                'delay'   => 3500,
                'time'    => 'now',
            ]);
    }

    /**
     * Update the agency settings.
     */
    public function update(UpdateAgencySettingsRequest $request, Agency $agency): RedirectResponse
    {
        $this->authorize('update', $agency);

        $validated = $request->validated();

        // Extract media files from validated data
        $logo = $validated['logo'] ?? null;
        $signature = $validated['signature'] ?? null;
        $stamp = $validated['stamp'] ?? null;

        // Remove media from validated array to avoid storing in settings JSON
        unset($validated['logo'], $validated['signature'], $validated['stamp']);

        // Merge settings (deep merge to avoid overwriting all settings)
        $currentSettings = $agency->settings ?? [];
        $newSettings = array_replace_recursive($currentSettings, $validated);

        // Update settings
        $agency->update(['settings' => $newSettings]);

        // Handle media uploads
        if ($logo) {
            $agency->clearMediaCollection('logo');
            $agency->addMediaFromRequest('logo')
                ->toMediaCollection('logo');
        }

        if ($signature) {
            $agency->clearMediaCollection('signature');
            $agency->addMediaFromRequest('signature')
                ->toMediaCollection('signature');
        }

        if ($stamp) {
            $agency->clearMediaCollection('stamp');
            $agency->addMediaFromRequest('stamp')
                ->toMediaCollection('stamp');
        }

        // Create notification for update
        $this->createNotification('update', 'agency_settings', $agency);

        return redirect()
            ->route('backoffice.agencies.settings.edit', $agency)
            ->with('toast', [
                'title'   => 'Updated',
                'message' => "Les paramètres de l'agence « {$agency->name} » ont été mis à jour avec succès.",
                'dot'     => '#0d6efd', // blue
                'delay'   => 3500,
                'time'    => 'now',
            ]);
    }

    /**
     * Create a notification for agency settings actions
     */
/**
 * Create a notification for agency settings actions
 */
protected function createNotification($action, $module, $item = null, $customData = []): void
{
    try {
        // Check if the NotificationController exists and method is available
        if (class_exists('App\Http\Controllers\Backoffice\NotificationController')) {
            $notificationController = app('App\Http\Controllers\Backoffice\NotificationController');
            
            if (method_exists($notificationController, 'createFromAction')) {
                // Pass the agency model as the item
                $notificationController->createFromAction($action, $module, $item, $customData);
            }
        }
    } catch (\Exception $e) {
        // Silently fail - notification is not critical
        \Log::warning('Could not create notification: ' . $e->getMessage());
    }
}  


/**
 * Delete logo
 */
public function deleteLogo(Request $request)
{
    $user = Auth::user();
    $agency = $user->agency;
    
    if ($agency->hasMedia('logo')) {
        $agency->clearMediaCollection('logo');
        return response()->json(['success' => true, 'message' => 'Logo supprimé avec succès.']);
    }
    
    return response()->json(['success' => false, 'message' => 'Aucun logo à supprimer.'], 404);
}

/**
 * Delete signature
 */
public function deleteSignature(Request $request)
{
    $user = Auth::user();
    $agency = $user->agency;
    
    if ($agency->hasMedia('signature')) {
        $agency->clearMediaCollection('signature');
        return response()->json(['success' => true, 'message' => 'Signature supprimée avec succès.']);
    }
    
    return response()->json(['success' => false, 'message' => 'Aucune signature à supprimer.'], 404);
}


}