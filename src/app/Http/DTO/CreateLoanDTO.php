<?php

namespace App\Http\DTO;

class CreateLoanDTO
{
    public function __construct(private int $userId, private float $amountRequired, private int $loanTerm)
    {
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * @return float
     */
    public function getAmountRequired(): float
    {
        return $this->amountRequired;
    }

    /**
     * @param float $amountRequired
     */
    public function setAmountRequired(float $amountRequired): void
    {
        $this->amountRequired = $amountRequired;
    }

    /**
     * @return int
     */
    public function getLoanTerm(): int
    {
        return $this->loanTerm;
    }

    /**
     * @param int $loanTerm
     */
    public function setLoanTerm(int $loanTerm): void
    {
        $this->loanTerm = $loanTerm;
    }


}