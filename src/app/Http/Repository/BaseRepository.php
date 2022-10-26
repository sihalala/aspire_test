<?php

namespace App\Http\Repository;

use Illuminate\Database\Eloquent\Model;

class BaseRepository implements IRepository
{
    public function __construct(private Model $model)
    {
    }

    public function table(): Model
    {
        return $this->model;
    }

    public function create(array $attributes): Model
    {
        return $this->model->create($attributes);
    }


    public function insert(array $records): bool
    {
        return $this->model->insert($records);
    }


    public function update(array $attributes): bool
    {
        return $this->model->update($attributes);
    }


    public function delete(): bool
    {
        return $this->model->delete();
    }


    public function find(int $id): ?Model
    {
        return $this->model->find($id);
    }
}
