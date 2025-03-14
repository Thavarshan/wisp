<?php

namespace App\Events;

use App\Traits\HasUser;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserDeletionRequested
{
    use Dispatchable, HasUser, InteractsWithSockets, SerializesModels;
}
