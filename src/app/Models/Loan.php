<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'amount_required',
        'loan_term',
        'status'
    ];

    public function loanRepayments(): HasMany
    {
        return $this->hasMany(LoanRepayment::class,'loan_id','id');
    }
}
