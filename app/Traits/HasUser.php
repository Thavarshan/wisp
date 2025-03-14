<?php

namespace App\Traits;

use App\Models\User;

trait HasUser
{
    /**
     * Create a new user instance.
     */
    public function __construct(protected User $user) {}

    /**
     * Get the user instance.
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * Set the user instance.
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
