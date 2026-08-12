<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UserDeletionService
{
    public function permanentlyDelete(User $user): void
    {
        DB::transaction(function () use ($user): void {
            $user->tokens()->delete();
            $user->deviceTokens()->delete();
            $user->pushSubscriptions()->delete();
            $user->syncPermissions([]);
            $user->syncRoles([]);

            $this->deleteTableRowsIfExists('sessions', 'user_id', $user->id);
            $this->deleteTableRowsIfExists('password_reset_tokens', 'email', $user->email);

            $user->forceDelete();
        });
    }

    public function permanentlyDeleteTrashedByEmail(?string $email): void
    {
        $email = trim((string) $email);

        if ($email === '') {
            return;
        }

        User::onlyTrashed()
            ->where('email', $email)
            ->get()
            ->each(fn (User $user) => $this->permanentlyDelete($user));
    }

    protected function deleteTableRowsIfExists(string $table, string $column, mixed $value): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        DB::table($table)->where($column, $value)->delete();
    }
}
