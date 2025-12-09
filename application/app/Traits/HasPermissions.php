<?php

namespace App\Traits;

trait HasPermissions
{
    // For single permission
    public function hasPermission($slug)
    {
        if ($this->role_id == 0) return true;

        return optional($this->role)->permissions->contains('slug', $slug);
    }

    // For multiple permissions
    public function hasAnyPermission(array $slugs)
    {
        if ($this->role_id == 0) return true;

        return optional($this->role)->permissions->pluck('slug')->intersect($slugs)->isNotEmpty();
    }
}

