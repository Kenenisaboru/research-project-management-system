<?php

namespace App\Events;

use App\Models\BudgetRequest;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BudgetDisbursed
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public BudgetRequest $budgetRequest,
        public User $actor,
        public float $amount,
        public string $paymentMethod
    ) {}
}
