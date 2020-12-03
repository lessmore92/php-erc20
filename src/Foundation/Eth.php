<?php
/**
 * User: Lessmore92
 * Date: 11/21/2020
 * Time: 2:19 AM
 */

namespace Lessmore92\Ethereum\Foundation;


class Eth extends EthBase
{
    public function getTransactionCount(string $address, string $blockParameter = 'latest')
    {
        return $this->call('getTransactionCount', [$address, $blockParameter])
                    ->toString()
            ;
    }

    public function gasPrice()
    {
        return $this->call('gasPrice')
                    ->toString()
            ;
    }

    public function sendRawTransaction(string $hash)
    {
        return (string)$this->call('sendRawTransaction', [$hash]);
    }
}
