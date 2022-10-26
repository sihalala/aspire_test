<?php

namespace App\Http\Services\Users;

use App\Http\Repository\Users\UserRepository;
use App\Http\Services\Service;


class UserService extends Service
{
    public function __construct(private UserRepository $userRepository)
    {
        parent::__construct($this->userRepository);
    }

}