<?php

namespace App\Http\Repository\Loans;

use App\Models\Loan;
use App\Http\Repository\BaseRepository;

class LoanRepository extends BaseRepository
{

    public function __construct(private Loan $model)
    {
        parent::__construct($model);
    }
}
