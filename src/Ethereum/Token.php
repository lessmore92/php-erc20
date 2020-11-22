<?php
/**
 * User: Lessmore92
 * Date: 11/21/2020
 * Time: 2:19 AM
 */

namespace Lessmore\Ethereum;

use Lessmore\Utils\Number;


class Token extends StandardERC20Token
{
    protected $contractAddress;

    public function __construct($contractAddress, $ethClient, $timeout = 3)
    {
        $this->contractAddress = $contractAddress;
        parent::__construct($ethClient, $timeout);
    }

    public function name(): string
    {
        return $this->call('name')[0];
    }

    public function symbol(): string
    {
        return $this->call('symbol')[0];
    }

    public function decimals(): int
    {
        return intval($this->call('decimals')[0]->toString());
    }

    /**
     * @param string $address
     * @return string
     */
    public function balanceOf(string $address)
    {
        return Number::scaleDown($this->call('balanceOf', [$address])['balance']->toString(), $this->decimals());
    }

    public function transfer(string $to, $amount, string $privateKey)
    {
        $data = $this->buildTransferData($to, $amount);
    }

    public function buildTransferData(string $to, $amount)
    {
        return $this->getContract()
                    ->at($this->contractAddress)
                    ->getData('transfer', $to, $amount)
            ;
    }

    function privateKeyToAddress(string $privateKey): string
    {
        $util      = new Util();
        $publicKey = $util->privateKeyToPublicKey($privateKey);
        $address   = $util->publicKeyToAddress($publicKey);

        return $address;
    }
}
