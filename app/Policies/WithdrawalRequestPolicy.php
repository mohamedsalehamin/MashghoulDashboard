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
        return $user->can('view_any_withdrawal::request');
    }

    public function view(User $user, WithdrawalRequest $withdrawalRequest): bool
    {
        return $user->can('view_withdrawal::request') || $user->id === $withdrawalRequest->user_id;
    }

    public function create(User $user): bool
    {
        return $user->can('create_withdrawal::request') && !$user->hasPendingWithdrawalRequest();
    }

    public function update(User $user, WithdrawalRequest $withdrawalRequest): bool
    {
        return $user->can('update_withdrawal::request');
    }

    public function delete(User $user, WithdrawalRequest $withdrawalRequest): bool
    {
        return $user->can('delete_withdrawal::request');
    }
} 