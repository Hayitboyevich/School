<?php

namespace App\Services;

class RoleService
{
    public function delete(...$roles)
    {
        foreach ($roles as $role) {
            if ($role->users->count() > 0) {
                return false;
            }
        }

        foreach ($roles as $role) {
            $role->delete();
        }

        return true;
    }
}
