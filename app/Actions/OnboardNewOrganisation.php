<?php

namespace App\Actions;

use App\Enums\Role as RoleType;
use App\Models\Organisation;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class OnboardNewOrganisation
{
    /**
     * Onboard a new organisation.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function onboard(array $data): array
    {
        try {
            DB::beginTransaction();

            $organisation = Organisation::create($data['organisation']);
            $user = User::make($data['owner']);
            $user->organisation()->associate($organisation);

            $user->save();

            collect(RoleType::defaults(asValues: true))
                ->each(function ($role) use ($organisation, $user) {
                    $role = Role::make(['name' => $role]);
                    $role->organisation()->associate($organisation);

                    $role->save();

                    if ($role->namedAs(RoleType::ADMIN)) {
                        $user->assignRole($role);
                    }
                });

            return [
                'message' => 'Organisation created.',
                'status' => Response::HTTP_CREATED,
            ];
        } catch (Throwable $th) {
            DB::rollBack();

            return [
                'message' => $th->getMessage(),
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ];
        } finally {
            DB::commit();
        }
    }
}
