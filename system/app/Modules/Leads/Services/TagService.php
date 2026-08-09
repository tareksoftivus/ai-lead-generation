<?php

namespace App\Modules\Leads\Services;

use App\Models\User;
use App\Modules\Leads\Models\Tag;

class TagService
{
    /**
     * Find an existing tag for this user by case-insensitive name match, or
     * create a new one preserving the caller's casing. Prevents "Hot" and
     * "hot" from becoming two separate tags for the same user.
     */
    public function findOrCreate(User $user, string $name): Tag
    {
        $name = trim($name);

        $existing = Tag::query()
            ->forUser($user->id)
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
            ->first();

        if ($existing) {
            return $existing;
        }

        return Tag::query()->create([
            'user_id' => $user->id,
            'name' => $name,
        ]);
    }
}
