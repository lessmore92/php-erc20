<?php
/**
 * User: Lessmore92
 * Date: 11/21/2020
 * Time: 2:19 AM
 */

namespace Lessmore\Ethereum\Foundation;

use Web3\Eth as BaseEth;
use Web3\Web3;

abstract class EthBase
{
    /**
     * @var Web3
     */
    private $web3;
    /**
     * @var BaseEth
     */
    private $eth;

    /**
     * ERC20 constructor.
     * @param Web3 $web3
     */
    public function __construct(Web3 $web3)
    {
        $this->web3 = $web3;
        $this->eth  = new BaseEth($this->web3->getProvider());
    }

    /**
     * @param string $method
     * @param array $arguments
     * @return array|null
     */
    public function call(string $method, array $arguments = [])
    {
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
        call_user_func_array([$this->eth, $method], $params);

        return $result;
    }
}
