<?php

namespace App\Http\Services\LoanRepayments;

use App\Http\Repository\LoanRepayments\LoanRepaymentRepository;
use App\Http\Services\Service;
use App\Models\Loan;
use App\Models\LoanRepayment;


class RepaymentService extends Service
{
    public function __construct(private LoanRepaymentRepository $repaymentRepository) {
        parent::__construct($this->repaymentRepository);
    }

    public function doRepayment(LoanRepayment $loanRepayment, Loan $loan, float $amount, bool $lastRepayment)
    {
        $loanRepayment->update(['paid_amount' => $amount, 'status' => 'PAID']);
        if ($lastRepayment) {
            $loan->update(['status' => 'PAID']);
        }
    }

}