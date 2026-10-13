<?php

namespace App\Events;

use App\Models\ProjectExtension;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ExtensionApproved
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public ProjectExtension $extension,
        public User $actor
    ) {}
}
