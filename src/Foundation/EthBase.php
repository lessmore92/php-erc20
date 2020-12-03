<?php
/**
 * User: Lessmore92
 * Date: 11/21/2020
 * Time: 2:19 AM
 */

namespace Lessmore92\Ethereum\Foundation;

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

    private $batchCalls = [];

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

    public function addCall(string $id, string $method, array $arguments = [])
    {
        $this->batchCalls[$id] = ['method' => $method, 'params' => $arguments];
        return $this;
    }


    public function batchCall()
    {
        $this->eth->batch(true);
        $callResultMap = [];
        $counter       = 0;
        foreach ($this->batchCalls as $key => $call)
        {
            $callResultMap[$counter] = $key;
            call_user_func_array([$this->eth, $call['method']], $call['params']);
            $counter++;
        }

        $result = null;
        $this->eth->getProvider()
                  ->execute(function ($err, $responses) use (&$result, $callResultMap) {
                      if ($err)
                      {
                          throw $err;
                      }

                      foreach ($responses as $key => $response)
                      {
                          $result[$callResultMap[$key]] = $response;
                      }
                  })
        ;

        return $result;
    }

}
