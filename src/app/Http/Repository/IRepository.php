<?php

namespace App\Http\Repository;

use Illuminate\Database\Eloquent\Model;

interface IRepository
{
    public function table(): Model;

    public function find(int $id): ?Model;

    public function create(array $attributes): Model;

    public function insert(array $records): bool;

    public function update(array $attributes): bool;

    public function delete(): bool;
}
