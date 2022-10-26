<?php

namespace App\Http\Repository\Users;

use App\Models\User;
use App\Http\Repository\BaseRepository;

class UserRepository extends BaseRepository
{
    public function __construct(private User $model)
    {
        parent::__construct($model);
    }
}
