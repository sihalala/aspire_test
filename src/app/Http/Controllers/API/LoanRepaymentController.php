<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\DoRepaymentRequest;
use App\Http\Resources\LoanResource;
use App\Http\Services\LoanRepayments\RepaymentService;
use App\Http\Services\Loans\LoanService;
use Illuminate\Support\Facades\DB;


class LoanRepaymentController extends BaseController
{
    public function __construct(private LoanService $loanService, private RepaymentService $repaymentService)
    {

    }

    public function doRepayment(DoRepaymentRequest $request, int $loan)
    {
        $loan = $this->loanService->getById($loan);
        if (!$loan) {
            return $this->sendError('Your loan is not found', [], 404);
        }

        $repaymentToBePaid = $loan->loanRepayments->filter(function ($item) {
            return $item->status === 'PENDING';
        })->first();

        if (!$repaymentToBePaid) {
            return $this->sendError('Your loan is fully paid', [], 422);
        }
        $lastRepayment = false;
        if ($loan->loanRepayments->filter(function ($item) {
                return $item->status === 'PENDING';
            })->count() === 1) {
            $lastRepayment = true;
        }

        if ($request->amount < $repaymentToBePaid->amount) {
            return $this->sendError('Your amount is not enough', [], 422);
        }
        try {
            DB::beginTransaction();

            $this->repaymentService->doRepayment($repaymentToBePaid, $loan, $request->amount, $lastRepayment);

            DB::commit();

            return $this->sendResponse(new LoanResource($loan), 'Repayment has been done');
        } catch(\Exception $exp) {
            DB::rollBack();
            return $this->sendError($exp->getMessage(),[], 500);
        }
    }
}
