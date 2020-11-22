<?php
/**
 * User: Lessmore92
 * Date: 11/21/2020
 * Time: 2:19 AM
 */

namespace Lessmore\Ethereum\Foundation;

use Lessmore\Ethereum\Foundation\Transaction\TransactionBuilder;
use Lessmore\Ethereum\Utils\Number;


abstract class StandardERC20Token extends ERC20
{
    protected $contractAddress;
    protected $decimals;
    protected $gasLimits = [
        'approve'      => 50000,
        'transfer'     => 50000,
        'transferFrom' => 50000,
        'default'      => 50000,
    ];

    public function __construct($ethClient, $timeout = 3)
    {
        $abi = file_get_contents(__DIR__ . '/../resources/erc20.abi.json');
        parent::__construct($this->contractAddress, $abi, $ethClient, $timeout);
    }

    protected $gasPriceModifier = 0;

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
        if ($this->decimals)
        {
            return $this->decimals;
        }
        return $this->decimals = intval($this->call('decimals')[0]->toString());
    }

    /**
     * @param string $address
     * @return string
     */
    public function balanceOf(string $address)
    {
        return Number::toDecimalValue($this->call('balanceOf', [$address])['balance']->toString(), $this->decimals());
    }

    /**
     * @param string $from
     * @param string $to
     * @param float $amount
     * @return Transaction\Transaction
     */
    public function transfer(string $from, string $to, float $amount)
    {
        $amount   = Number::fromDecimalValue($amount, $this->decimals());
        $data     = $this->buildTransferData($to, $amount);
        $nonce    = Number::toHex($this->getEth()
                                       ->getTransactionCount($from));
        $gasLimit = $this->getGasLimit('transfer');
        $gasPrice = $this->getSafeGasPrice();

        return (new TransactionBuilder())
            ->setEth($this->getEth())
            ->to($this->contractAddress)
            ->nonce($nonce)
            ->gasPrice($gasPrice)
            ->gasLimit($gasLimit)
            ->data($data)
            ->amount(0)
            ->build()
            ;

    }

    public function buildTransferData(string $to, $amount)
    {
        return $this->getContract()
                    ->at($this->contractAddress)
                    ->getData('transfer', $to, $amount)
            ;
    }

    public function approve(string $ownerAddress, string $spenderAddress, string $amount)
    {
        $amount   = Number::fromDecimalValue($amount, $this->decimals());
        $data     = $this->buildApproveData($spenderAddress, $amount);
        $nonce    = Number::toHex($this->getEth()
                                       ->getTransactionCount($ownerAddress));
        $gasLimit = $this->getGasLimit('approve');
        $gasPrice = $this->getSafeGasPrice();

        return (new TransactionBuilder())
            ->setEth($this->getEth())
            ->to($this->contractAddress)
            ->nonce($nonce)
            ->gasPrice($gasPrice)
            ->gasLimit($gasLimit)
            ->data($data)
            ->amount(0)
            ->build()
            ;
    }

    public function buildApproveData(string $to, $amount)
    {
        return $this->getContract()
                    ->at($this->contractAddress)
                    ->getData('approve', $to, $amount)
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
