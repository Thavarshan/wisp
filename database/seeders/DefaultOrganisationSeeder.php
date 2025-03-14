<?php

namespace Database\Seeders;

use App\Enums\DefaultData;
use App\Models\Organisation;
use Illuminate\Database\Seeder;

class DefaultOrganisationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Organisation::create([
            'name' => DefaultData::ORGANISATION->value,
            'email' => DefaultData::EMAIL->value,
            'website' => DefaultData::WEBSITE->value,
        ]);
    }
}
