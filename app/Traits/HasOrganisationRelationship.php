<?php

namespace App\Traits;

use App\Models\Organisation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasOrganisationRelationship
{
    /**
     * Boot the trait.
     */
    protected static function bootHasOrganisationRelationship(): void
    {
        static::creating(function (Model $model) {
            if (! auth()->check()) {
                return;
            }

            if (! is_null($model->organisation_id)) {
                return;
            }

            $model->organisation_id = auth()->user()->organisation_id;
        });
    }

    /**
     * Get the organisation that the user belongs to.
     */
    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }
}
