<?php

namespace Alexis\MyWeeklyAllowance\Tests;

use PHPUnit\Framework\TestCase;
use Alexis\MyWeeklyAllowance\Wallet;

class WalletTest extends TestCase
{
    public function test_initial_balance_is_zero()
    {
        $wallet = new Wallet();
        $this->assertEquals(0, $wallet->getBalance());
    }

    public function test_add_money()
    {
        $wallet = new Wallet();
        $wallet->addMoney(50);
        $this->assertEquals(50, $wallet->getBalance());
        
        $wallet->addMoney(30);
        $this->assertEquals(80, $wallet->getBalance());
    }

    public function test_remove_money()
    {
        $wallet = new Wallet();
        $wallet->addMoney(100);
        $wallet->removeMoney(40);
        $this->assertEquals(60, $wallet->getBalance());
    }

    public function test_cannot_remove_more_than_balance()
    {
        $this->expectException(\Exception::class);
        $wallet = new Wallet();
        $wallet->addMoney(50);
        $wallet->removeMoney(100);
    }

    public function test_weekly_allowance()
    {
        $wallet = new Wallet();
        $wallet->setAllowance(20);
        $wallet->processAllowance();
        
        $this->assertEquals(20, $wallet->getBalance());
        
        $wallet->processAllowance();
        $this->assertEquals(40, $wallet->getBalance());
    }
}
