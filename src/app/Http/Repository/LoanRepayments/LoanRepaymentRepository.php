<?php

namespace App\Http\Repository\LoanRepayments;

use App\Models\LoanRepayment;
use App\Http\Repository\BaseRepository;

class LoanRepaymentRepository extends BaseRepository
{

    public function __construct(private LoanRepayment $model)
    {
        parent::__construct($model);
    }
}
