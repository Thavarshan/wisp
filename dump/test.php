<?php
// the FireDragon species implements the Reptile interface. When a ReptileEgg hatches, a new reptile will be created of the same species that laid the egg. An exception is thrown if a ReptileEgg tries to hatch more than once.

interface Reptile
{
    public function layEgg(): ReptileEgg;
}

class FireDragon implements Reptile
{
    public function layEgg(): ReptileEgg
    {
        return new ReptileEgg(self::class);
    }
}

class ReptileEgg
{
    protected $reptileType;
    protected $hatched = false;

    public function __construct(string $reptileType)
    {
        $this->reptileType = $reptileType;
    }

    public function hatch(): Reptile
    {
        if ($this->hatched) {
            throw new Exception("This egg has already hatched.");
        }
        $this->hatched = true;
        return new $this->reptileType();
    }
}
