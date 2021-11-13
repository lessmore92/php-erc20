<?php
/**
 * User: Lessmore92
 * Date: 11/21/2020
 * Time: 2:19 AM
 */

namespace Lessmore92\Ethereum\Foundation;

use Lessmore92\Ethereum\Foundation\Contracts\EventLogBuilderInterface;
use Lessmore92\Ethereum\Foundation\Transaction\TransactionBuilder;
use Lessmore92\Ethereum\Utils\Address;
use Lessmore92\Ethereum\Utils\Number;


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
        return Number::scaleDown($this->call('balanceOf', [$address])['balance']->toString(), $this->decimals());
    }

    /**
     * @param string $from
     * @param string $to
     * @param float $amount
     * @return Transaction\Transaction
     */
    public function transfer(string $from, string $to, float $amount, string $gasLimit = 'default', string $gasPrice = 'default')
    {
        $amount = Number::scaleUp($amount, $this->decimals());
        $data   = $this->buildTransferData($to, $amount);
        $nonce  = Number::toHex($this->getEth()
                                     ->getTransactionCount($from, 'pending'));
        if (strtolower($gasLimit) === 'default')
        {
            $gasLimit = $this->getGasLimit('transfer');
        }
        if (strtolower($gasPrice) === 'default')
        {
            $gasPrice = $this->getSafeGasPrice();
        }

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

    public function approve(string $ownerAddress, string $spenderAddress, string $amount, string $gasLimit = 'default', string $gasPrice = 'default')
    {
        $amount = Number::scaleUp($amount, $this->decimals());
        $data   = $this->buildApproveData($spenderAddress, $amount);
        $nonce  = Number::toHex($this->getEth()
                                     ->getTransactionCount($ownerAddress, 'pending'));
        if (strtolower($gasLimit) === 'default')
        {
            $gasLimit = $this->getGasLimit('approve');
        }
        if (strtolower($gasPrice) === 'default')
        {
            $gasPrice = $this->getSafeGasPrice();
        }

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

    public function allowance(string $ownerAddress, string $spenderAddress)
    {
        return Number::scaleDown($this->call('allowance', [$ownerAddress, $spenderAddress])[0]->toString(), $this->decimals());
    }

    /**
     * @param string $spender
     * @param string $from
     * @param string $to
     * @param float $amount
     * @return Transaction\Transaction
     */
    public function transferFrom(string $spender, string $from, string $to, float $amount, string $gasLimit = 'default', string $gasPrice = 'default')
    {
        $amount = Number::scaleUp($amount, $this->decimals());
        $data   = $this->buildTransferFromData($from, $to, $amount);
        $nonce  = Number::toHex($this->getEth()
                                     ->getTransactionCount($spender, 'pending'));
        if (strtolower($gasLimit) === 'default')
        {
            $gasLimit = $this->getGasLimit('transferFrom');
        }
        if (strtolower($gasPrice) === 'default')
        {
            $gasPrice = $this->getSafeGasPrice();
        }

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

    public function buildTransferFromData(string $from, string $to, $amount)
    {
        return $this->getContract()
                    ->at($this->contractAddress)
                    ->getData('transferFrom', $from, $to, $amount)
            ;
    }

    public function getEventLogFormatter(): EventLogBuilderInterface
    {
        $builder = new EventLogBuilder();
        $builder->setContract($this);
        return $builder;
    }

    public function logs(string $address, $fromBlock = '0x0', $toBlock = 'latest')
    {
        $topic_address = Address::toTopic($address);

        $logs = $this->getEth()
                     ->addCall('sent', 'getLogs', [['address' => $this->contractAddress, 'topics' => [null, $topic_address], 'fromBlock' => $fromBlock, 'toBlock' => $toBlock]])
                     ->addCall('receive', 'getLogs', [['address' => $this->contractAddress, 'topics' => [null, null, $topic_address], 'fromBlock' => $fromBlock, 'toBlock' => $toBlock]])
                     ->batchCall()
        ;
        $txs  = [];
        foreach (call_user_func_array('array_merge', $logs) as $log)
        {
            $txs[] = $this->getEventLogFormatter()
                          ->build($log)
            ;
        }

        return $txs;
    }

    public function transactions(string $address, $fromBlock = '0x0', $toBlock = 'latest')
    {
        return $this->logs($address, $fromBlock, $toBlock);
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
