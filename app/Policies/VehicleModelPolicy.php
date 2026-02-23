<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VehicleModel;
use Illuminate\Auth\Access\Response;

class VehicleModelPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Allow if user is authenticated
        return $user !== null;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, VehicleModel $vehicleModel): bool
    {
        // User can only view models from their agency
        return $user->agency_id === $vehicleModel->agency_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Any authenticated user can create models for their agency
        return $user !== null;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, VehicleModel $vehicleModel): bool
    {
        // User can only update models from their agency
        return $user->agency_id === $vehicleModel->agency_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, VehicleModel $vehicleModel): bool
    {
        // User can only delete models from their agency
        return $user->agency_id === $vehicleModel->agency_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, VehicleModel $vehicleModel): bool
    {
        // User can only restore models from their agency
        return $user->agency_id === $vehicleModel->agency_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, VehicleModel $vehicleModel): bool
    {
        // User can only force delete models from their agency
        return $user->agency_id === $vehicleModel->agency_id;
    }
}