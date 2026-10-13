<?php

namespace App\Events;

use App\Models\Paper;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaperApproved
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Paper $paper,
        public User $actor
    ) {}
}
