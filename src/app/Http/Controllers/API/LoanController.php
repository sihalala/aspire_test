<?php

namespace App\Http\Controllers\API;

use App\Http\DTO\CreateLoanDTO;
use App\Http\Requests\CreateLoanRequest;
use App\Http\Resources\LoanResource;
use App\Models\Loan;
use App\Http\Services\Loans\LoanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanController extends BaseController
{
    public function __construct(private LoanService $loanService)
    {

    }

    public function index(): JsonResponse
    {
        $loans = Loan::where('user_id', auth()->user()->id)->get();
        return $this->sendResponse(LoanResource::collection($loans), 'Loan retrieved successfully.');
    }

    public function store(CreateLoanRequest $request)
    {
        $user = $request->user();
        $loanDTO = new CreateLoanDTO($user->id, $request->amount_required, $request->loan_term);
        
        try {
            DB::beginTransaction();

            $loan = $this->loanService->createLoanAndRepayment($loanDTO);
            
            DB::commit();

            return $this->sendResponse(new LoanResource($loan), 'Loan has been created');

        } catch(\Exception $exp) {
            DB::rollBack(); 
            return $this->sendError($exp->getMessage(),[], 500);
        }
    }

    public function changeStatus(Request $request,Loan $loan)
    {
        $message = "Loan is already APPROVED";
        
        if($loan->status === 'PENDING'){
            $loan->update(['status'=>'APPROVED']);
            $message = 'Loan has been approved successfully.';
        }

        return $this->sendResponse(new LoanResource($loan), $message);
    }
}
