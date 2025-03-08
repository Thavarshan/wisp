<?php

namespace App\Observers;

use App\Models\Secret;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class SecretObserver
{
    /**
     * Handle the Secret "creating" event.
     */
    public function creating(Secret $secret): void
    {
        $secret->uid = uniqid();

        if ($secret->password) {
            $secret->password = Hash::make($secret->password);
        }

        $secret->content = Crypt::encrypt($secret->content);
    }
}
