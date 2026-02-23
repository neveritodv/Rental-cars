<?php

namespace App\Policies;

use App\Models\User;
use App\Models\RentalContract;
use Illuminate\Auth\Access\HandlesAuthorization;

class RentalContractPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, RentalContract $rentalContract): bool
    {
        return $user->agency_id === $rentalContract->agency_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, RentalContract $rentalContract): bool
    {
        return $user->agency_id === $rentalContract->agency_id;
    }

    public function delete(User $user, RentalContract $rentalContract): bool
    {
        return $user->agency_id === $rentalContract->agency_id;
    }
}