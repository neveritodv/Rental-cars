<?php

namespace App\Traits;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

trait Notifiable
{
    /**
     * Create a notification for any CRUD action.
     */
    protected function createNotification($action, $module, $item = null, $customData = [])
    {
        $user = Auth::guard('backoffice')->user();
        
        if (!$user) {
            return null;
        }

        $title = $this->getNotificationTitle($action, $module);
        $message = $this->getNotificationMessage($action, $module, $item);
        $type = $this->getNotificationType($action);
        $link = $this->getNotificationLink($module, $item, $action);

        return Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'link' => $link,
            'icon' => $this->getNotificationIcon($type),
            'is_read' => false,
            'is_archived' => false,
            ...$customData
        ]);
    }

    /**
     * Get notification title based on action.
     */
    protected function getNotificationTitle($action, $module)
    {
        $titles = [
            'create' => 'Nouvel élément créé',
            'store' => 'Nouvel élément créé',
            'update' => 'Élément modifié',
            'destroy' => 'Élément supprimé',
            'delete' => 'Élément supprimé',
            'restore' => 'Élément restauré',
            'status' => 'Statut modifié',
        ];

        return $titles[$action] ?? 'Action effectuée';
    }

    /**
     * Get notification message based on action and module.
     */
protected function getNotificationMessage($action, $module, $item = null)
{
    $user = Auth::guard('backoffice')->user();
    $performerName = $user ? $user->name : 'Système';
    
    $moduleNames = [
        'vehicle' => 'véhicule',
        'control' => 'contrôle',
        'control-item' => 'élément de contrôle',
        'agency' => 'agence',
        'agent' => 'agent',
        'client' => 'client',
        'user' => 'utilisateur',
        'booking' => 'réservation',
        'rental-contract' => 'contrat de location',
        'contract-client' => 'client du contrat',
        'invoice' => 'facture',
        'invoice-item' => 'élément de facture',
        'payment' => 'paiement',
        'vignette' => 'vignette',
        'insurance' => 'assurance',
        'oil-change' => 'vidange',
        'technical-check' => 'contrôle technique',
        'financial-account' => 'compte financier',
        'transaction-category' => 'catégorie de transaction',
        'financial-transaction' => 'transaction financière',
        'vehicle-brand' => 'marque',
        'vehicle-model' => 'modèle',
    ];

    $moduleName = $moduleNames[$module] ?? $module;
    $itemIdentifier = $this->getItemIdentifier($item);

    $messages = [
        'create' => "{$performerName} a créé un nouveau {$moduleName}" . ($itemIdentifier ? " : {$itemIdentifier}" : ""),
        'store' => "{$performerName} a créé un nouveau {$moduleName}" . ($itemIdentifier ? " : {$itemIdentifier}" : ""),
        'update' => "{$performerName} a modifié le {$moduleName}" . ($itemIdentifier ? " : {$itemIdentifier}" : ""),
        'destroy' => "{$performerName} a supprimé le {$moduleName}" . ($itemIdentifier ? " : {$itemIdentifier}" : ""),
        'delete' => "{$performerName} a supprimé le {$moduleName}" . ($itemIdentifier ? " : {$itemIdentifier}" : ""),
        'restore' => "{$performerName} a restauré le {$moduleName}" . ($itemIdentifier ? " : {$itemIdentifier}" : ""),
        'status' => "{$performerName} a modifié le statut du {$moduleName}" . ($itemIdentifier ? " : {$itemIdentifier}" : ""),
    ];

    return $messages[$action] ?? "{$performerName} a effectué une action sur {$moduleName}";
}

    /**
     * Get item identifier (name, number, etc.)
     */
    protected function getItemIdentifier($item)
    {
        if (!$item) return null;

        $possibleFields = [
            'control_number', 'contract_number', 'registration_number', 
            'name', 'title', 'number', 'reference', 'id'
        ];

        foreach ($possibleFields as $field) {
            if (isset($item->$field)) {
                return $item->$field;
            }
        }

        return '#' . $item->id;
    }

    /**
     * Get notification type based on action.
     */
    protected function getNotificationType($action)
    {
        $types = [
            'create' => 'success',
            'store' => 'success',
            'update' => 'info',
            'destroy' => 'error',
            'delete' => 'error',
            'restore' => 'warning',
            'status' => 'warning',
        ];

        return $types[$action] ?? 'info';
    }

    /**
     * Get notification icon based on type.
     */
    protected function getNotificationIcon($type)
    {
        $icons = [
            'success' => 'ti ti-circle-check',
            'error' => 'ti ti-circle-x',
            'warning' => 'ti ti-alert-triangle',
            'info' => 'ti ti-info-circle',
        ];

        return $icons[$type] ?? 'ti ti-bell';
    }

    /**
     * Get notification link.
     */
    protected function getNotificationLink($module, $item, $action)
    {
        if (!$item) return null;

        $routes = [
            'vehicle' => 'backoffice.vehicles.show',
            'control' => 'backoffice.controls.show',
            'control-item' => 'backoffice.control-items.show',
            'agency' => 'backoffice.agencies.show',
            'agent' => 'backoffice.agents.show',
            'client' => 'backoffice.clients.show',
            'user' => 'backoffice.users.show',
            'booking' => 'backoffice.bookings.show',
            'rental-contract' => 'backoffice.rental-contracts.show',
            'contract-client' => 'backoffice.contract-clients.show',
            'invoice' => 'backoffice.invoices.show',
            'invoice-item' => 'backoffice.invoice-items.show',
            'payment' => 'backoffice.payments.show',
            'vignette' => 'backoffice.vehicles.vignettes.show',
            'insurance' => 'backoffice.vehicles.insurances.show',
            'oil-change' => 'backoffice.vehicles.oil-changes.show',
            'technical-check' => 'backoffice.vehicles.technical-checks.show',
            'financial-account' => 'backoffice.finance.accounts.show',
            'transaction-category' => 'backoffice.finance.categories.show',
            'financial-transaction' => 'backoffice.finance.transactions.show',
            'vehicle-brand' => 'backoffice.vehicle-brands.show',
            'vehicle-model' => 'backoffice.vehicle-models.show',
        ];

        $route = $routes[$module] ?? null;

        if ($route && $action !== 'destroy' && $action !== 'delete') {
            try {
                return route($route, $item);
            } catch (\Exception $e) {
                return null;
            }
        }

        return null;
    }
}