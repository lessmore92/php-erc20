<?php
/**
 * User: Lessmore92
 * Date: 11/21/2020
 * Time: 2:19 AM
 */

namespace Lessmore\Ethereum\Foundation;

use Lessmore\Ethereum\Utils\Number;


abstract class StandardERC20Token extends ERC20
{
    protected $contractAddress;
    protected $gasLimits        = [
        'approve'      => 50000,
        'transfer'     => 50000,
        'transferFrom' => 50000,
        'default'      => 50000,
    ];
    protected $gasPriceModifier = 1;

    public function __construct($ethClient, $timeout = 3)
    {
        $abi = file_get_contents(__DIR__ . '/../../resources/erc20.abi.json');
        parent::__construct($this->contractAddress, $abi, $ethClient, $timeout);
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
        /*
        $data = $this->buildTransferData($to, $amount);
        //$fromAddress = KeyPair::privateKeyToAddress($privateKey);
        $fromAddress = "0x417d3dac69a08982cc6905723a5e2cc6c5b01735";

        $nonce = $this->getEth()
                      ->getTransactionCount($fromAddress, 'pending')
        ;
        */

        $gasPrice = $this->getSafeGasPrice();

        $gasLimit = $this->getGasLimit('transfer');

    }

    public function buildTransferData(string $to, $amount)
    {
        return $this->getContract()
                    ->at($this->contractAddress)
                    ->getData('transfer', $to, $amount)
            ;
    }


    public function getGasLimit($action = '')
    {
        return isset($this->gasLimits[$action]) ? $this->gasLimits[$action] : $this->gasLimits['default'];
    }

    public function getSafeGasPrice()
    {
        $gasPrice = $this->getEth()
                         ->gasPrice()
        ;

        $modified = floatval(Number::fromWei($gasPrice, 'gwei')) + $this->gasPriceModifier;
        return Number::toWei($modified, 'gwei')
                     ->toString()
            ;
    }
}
