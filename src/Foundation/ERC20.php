<?php
/**
 * User: Lessmore92
 * Date: 11/21/2020
 * Time: 2:19 AM
 */

namespace Lessmore\Ethereum\Foundation;

use Web3\Contract;
use Web3\Providers\HttpProvider;
use Web3\RequestManagers\HttpRequestManager;
use Web3\Web3;

abstract class ERC20
{
    /**
     * @var Web3
     */
    private $web3;
    /**
     * @var string
     */
    private $contractAddress;
    /**
     * @var string
     */
    private $abi;
    /**
     * @var Contract
     */
    private $contract;
    /**
     * @var Eth
     */
    private $eth;

    /**
     * ERC20 constructor.
     * @param string $contractAddress
     * @param string $abi
     * @param string $ethClient
     * @param int $timeout
     */
    public function __construct(string $contractAddress, string $abi, string $ethClient, $timeout = 2)
    {
        $web3                  = new Web3(new HttpProvider(new HttpRequestManager($ethClient, $timeout)));
        $this->web3            = $web3;
        $this->abi             = $abi;
        $this->contractAddress = $contractAddress;
        $this->contract        = new Contract($web3->getProvider(), $abi);
        $this->eth             = new Eth($web3);
    }

    /**
     * @param string $method
     * @param array $arguments
     * @return array|null
     */
    public function call(string $method, array $arguments = []): array
    {
        $params = [$method];
        foreach ($arguments as $argument)
        {
            $params[] = $argument;
        }
        $result   = null;
        $params[] = function ($err, $response) use (&$result) {
            if ($err)
            {
                throw $err;
            }
            $result = $response;
        };
        call_user_func_array([$this->contract->at($this->contractAddress), 'call'], $params);

        return $result;
    }

    /**
     * @return Contract
     */
    public function getContract()
    {
        return $this->contract;
    }

    /**
     * @return Eth
     */
    public function getEth()
    {
        return $this->eth;
    }

    /**
     * @return Web3
     */
    public function getWeb3()
    {
        return $this->web3;
    }
}
