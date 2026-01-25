<?php

namespace App\Policies;

use App\Models\User;
use App\UsersModule\Models\WithdrawalRequest;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class WithdrawalRequestPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:WithdrawalRequest');
    }

    public function view(User $user, WithdrawalRequest $withdrawalRequest): bool
    {
        return $user->can('View:WithdrawalRequest') || $user->id === $withdrawalRequest->user_id;
    }

    public function create(User $user): bool
    {
        return $user->can('Create:WithdrawalRequest') && !$user->hasPendingWithdrawalRequest();
    }

    public function update(User $user, WithdrawalRequest $withdrawalRequest): bool
    {
        return $user->can('Update:WithdrawalRequest');
    }

    public function delete(User $user, WithdrawalRequest $withdrawalRequest): bool
    {
        return $user->can('Delete:WithdrawalRequest');
    }
} 