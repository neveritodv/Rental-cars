<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vehicle;

class VehiclePolicy
{
    public function view(User $user, Vehicle $vehicle): bool
    {
        return $user->agency_id === $vehicle->agency_id;
    }

    public function update(User $user, Vehicle $vehicle): bool
    {
        return $user->agency_id === $vehicle->agency_id;
    }

    public function delete(User $user, Vehicle $vehicle): bool
    {
        return $user->agency_id === $vehicle->agency_id;
    }

    public function create(User $user): bool
    {
        return true;
    }
}
