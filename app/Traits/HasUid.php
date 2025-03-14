<?php

namespace App\Traits;

use App\Enums\App;
use App\Support\HashId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

trait HasUid
{
    /**
     * Boot all of the bootable traits on the model.
     */
    public static function bootHasUid(): void
    {
        static::created(function (Model $model) {
            $model->generateHashId();
        });
    }

    /**
     * Generate a new and unique code
     */
    public function generateHashId(): void
    {
        $code = HashId::encode($this->id);
        $uid = ! blank($this->getPrefix())
            ? sprintf('%s-%s', $this->getPrefix(), $code)
            : $code;

        $this->forceFill(compact('uid'))->saveQuietly();
    }

    /**
     * Get the entity's unique identifier prefix.
     */
    public function getPrefix(): string
    {
        if (! isset($this->hashIdPrefix)) {
            return App::ABBREVIATION->value;
        }

        return $this->hashIdPrefix;
    }

    /**
     * Get the entity's unique identifier.
     */
    public function getUid(): ?string
    {
        return $this->getAttribute('uid');
    }

    /**
     * Get the entity's unique identifier.
     */
    public function revealHashedId(): string
    {
        $hash = Arr::last(explode('-', $this->getUid()));

        return HashId::decode($hash)[0];
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'uid';
    }

    /**
     * Find a model by its unique identifier.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public static function findByUid(string $uid): static
    {
        return static::where('uid', $uid)->firstOrFail();
    }
}
