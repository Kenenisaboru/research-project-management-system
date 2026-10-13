<?php

namespace App\Events;

use App\Models\Evaluation;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EvaluationSubmitted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Evaluation $evaluation,
        public User $actor,
        public float $score,
        public string $decision
    ) {}
}
