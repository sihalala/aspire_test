<?php

namespace App\Http\Services\Loans;

use App\Http\DTO\CreateLoanDTO;
use App\Models\Loan;
use App\Http\Repository\Loans\LoanRepository;
use App\Http\Services\Service;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;


class LoanService extends Service
{
    public function __construct(private LoanRepository $loanRepository)
    {
        parent::__construct($this->loanRepository);
    }

    public function createLoanAndRepayment(CreateLoanDTO $loanDTO): Loan
    {
        $loan = $this->create([
            'user_id' => $loanDTO->getUserId(),
            'amount_required' => $loanDTO->getAmountRequired(),
            'loan_term' => $loanDTO->getLoanTerm(),
        ]);

        $repaymentDate = Carbon::now();
        $loanRepayments = [];
        $repaymentAmounts = round($loanDTO->getAmountRequired()/$loanDTO->getLoanTerm(),2);

        for($i = 1; $i <= $loanDTO->getLoanTerm(); $i++){
            $repaymentDate = $repaymentDate->addDays(7);
            $loanRepayments[] =  [
                'loan_id' => $loan->id,
                'amount' => $repaymentAmounts,
                'payment_date' => $repaymentDate->format('Y-m-d')
            ];
        }

        $loan->loanRepayments()->createMany($loanRepayments);

        return $loan;
    }
}