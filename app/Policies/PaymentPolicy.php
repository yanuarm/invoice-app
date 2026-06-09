<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Payment $payment): bool
    {
        return $payment->invoice->created_by === $user->id || $payment->created_by === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function delete(User $user, Payment $payment): bool
    {
        return $payment->created_by === $user->id;
    }
}
