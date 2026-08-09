<?php

namespace App\Modules\Leads\Policies;

use App\Models\User;
use App\Modules\Leads\Models\Lead;
use Illuminate\Contracts\Auth\Authenticatable;

class LeadPolicy
{
    public function viewAny(Authenticatable $user): bool
    {
        return $user->can('leads.view');
    }

    public function view(Authenticatable $user, Lead $lead): bool
    {
        return $this->owns($user, $lead) && $user->can('leads.view');
    }

    public function update(Authenticatable $user, Lead $lead): bool
    {
        return $this->owns($user, $lead) && $user->can('leads.manage');
    }

    public function delete(Authenticatable $user, Lead $lead): bool
    {
        return $this->owns($user, $lead) && $user->can('leads.manage');
    }

    protected function owns(Authenticatable $user, Lead $lead): bool
    {
        return $user instanceof User && $lead->user_id === $user->getKey();
    }
}
