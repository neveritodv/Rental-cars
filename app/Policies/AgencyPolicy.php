<?php

namespace App\Policies;

use App\Models\Agency;
use App\Models\User;

class AgencyPolicy
{
    /**
     * Super Admin only (guard backoffice)
     */
    private function isSuperAdmin(?User $user): bool
    {
        return $user && $user->hasRole('super-admin', 'backoffice');
    }

    /**
     * Agency Admin - user belongs to this agency
     */
    private function isAgencyAdmin(User $user, Agency $agency): bool
    {
        return $user && 
               $user->agency_id === $agency->id && 
               $user->hasRole('agency-admin', 'backoffice');
    }

    /**
     * Agency Manager - user belongs to this agency
     */
    private function isAgencyManager(User $user, Agency $agency): bool
    {
        return $user && 
               $user->agency_id === $agency->id && 
               $user->hasRole('agency-manager', 'backoffice');
    }

    /**
     * Agency Staff - user belongs to this agency
     */
    private function isAgencyStaff(User $user, Agency $agency): bool
    {
        return $user && 
               $user->agency_id === $agency->id && 
               $user->hasRole('agency-staff', 'backoffice');
    }

    public function viewAny(User $user): bool
    {
        // Only super admin can view all agencies
        return $this->isSuperAdmin($user);
    }

    public function view(User $user, Agency $agency): bool
    {
        // Super admin can view any agency
        if ($this->isSuperAdmin($user)) {
            return true;
        }
        
        // Agency users can view their own agency
        return $user->agency_id === $agency->id && 
               ($user->hasRole('agency-admin', 'backoffice') || 
                $user->hasRole('agency-manager', 'backoffice') || 
                $user->hasRole('agency-staff', 'backoffice'));
    }

    public function create(User $user): bool
    {
        // Only super admin can create agencies
        return $this->isSuperAdmin($user);
    }

    public function update(User $user, Agency $agency): bool
    {
        // Super admin can update any agency
        if ($this->isSuperAdmin($user)) {
            return true;
        }
        
        // Agency admin can update their own agency
        return $this->isAgencyAdmin($user, $agency);
    }

    public function delete(User $user, Agency $agency): bool
    {
        // Only super admin can delete agencies
        return $this->isSuperAdmin($user);
    }

    public function restore(User $user, Agency $agency): bool
    {
        // Only super admin can restore agencies
        return $this->isSuperAdmin($user);
    }

    public function forceDelete(User $user, Agency $agency): bool
    {
        // Only super admin can force delete agencies
        return $this->isSuperAdmin($user);
    }
}