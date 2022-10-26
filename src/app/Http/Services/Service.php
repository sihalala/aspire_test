<?php

namespace App\Http\Services;

use App\Http\Repository\IRepository;
use Illuminate\Database\Eloquent\Model;


class Service
{
    public function __construct(private IRepository $repository)
    {

    }


    public function getById(int $id): ?Model
    {
        return $this->repository->find($id);
    }


    public function create(array $attributes): Model
    {
        return $this->repository->create($attributes);
    }


    public function update(array $attributes): bool
    {
        return $this->repository->update($attributes);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }
}