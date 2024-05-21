<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function create(array $data): User
    {
        return User::create($data);
    }

    public function find(int $id): ?User
    {
        return User::find($id);
    }

    public function update(int $id, array $data): bool
    {
        $user = $this->find($id);

        if ($user) {
            return $user->update($data);
        }

        return false;
    }

    public function delete(int $id): ?User
    {
        $user = $this->find($id);

        if ($user) {
            $user->delete();
            return $user;
        }

        return null;
    }
}
