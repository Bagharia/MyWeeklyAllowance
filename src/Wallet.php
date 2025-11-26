<?php

namespace Alexis\MyWeeklyAllowance;

class Wallet
{
    private float $balance;

    public function __construct()
    {
        $this->balance = 0;
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function addMoney(float $amount): void
    {
        $this->balance += $amount;
    }

    public function removeMoney(float $amount): void
    {
        if ($amount > $this->balance) {
            throw new \Exception("Insufficient funds");
        }
        $this->balance -= $amount;
    }

    private float $allowance = 0;

    public function setAllowance(float $amount): void
    {
        $this->allowance = $amount;
    }

    public function processAllowance(): void
    {
        $this->balance += $this->allowance;
    }
}
