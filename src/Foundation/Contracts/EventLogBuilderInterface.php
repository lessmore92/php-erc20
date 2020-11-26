<?php
/**
 * User: Lessmore92
 * Date: 11/26/2020
 * Time: 5:03 AM
 */

namespace Lessmore\Ethereum\Foundation\Contracts;


use Lessmore\Ethereum\Foundation\ERC20;

interface EventLogBuilderInterface
{
    public function build(\stdClass $log): array;

    public function setContract(ERC20 $contract);
}
