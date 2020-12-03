<?php
/**
 * User: Lessmore92
 * Date: 11/23/2020
 * Time: 2:09 AM
 */

namespace Lessmore92\Ethereum\Foundation\Transaction;

use kornrunner\Ethereum\Transaction as BaseTransaction;
use Lessmore92\Ethereum\Foundation\Eth;

class Transaction
{
    private $transaction;
    private $eth;

    public function __construct(BaseTransaction $transaction, Eth $eth = null)
    {
        $this->transaction = $transaction;
        $this->eth         = $eth;
    }

    public function sign($privateKey)
    {
        $privateKey = str_replace('0x', '', $privateKey);
        return new SignedTransaction('0x' . $this->transaction->getRaw($privateKey), $this->eth);
    }
}
