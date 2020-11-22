<?php
/**
 * User: Lessmore92
 * Date: 11/21/2020
 * Time: 5:20 AM
 */

namespace Lessmore\Ethereum\Utils;


use phpseclib\Math\BigInteger;
use Web3\Utils;

class Number
{
    public static function scaleDown($number, $scale)
    {
        list($decimal, $precious) = (new BigInteger($number))->divide(self::bigIntegerPow(10, $scale));
        return $decimal->toString() . '.' . $precious->toString();
    }

    public static function bigIntegerPow($base, $power)
    {
        $number = new BigInteger($base);
        for ($i = 1; $i < $power; $i++)
        {
            $number = $number->multiply(new BigInteger($base));
        }
        return $number;
    }

    public static function fromWei($number, $unit)
    {
        list($decimal, $precious) = Utils::fromWei($number, $unit);
        return $decimal->toString() . '.' . $precious->toString();
    }

    public static function toWei($number, $unit)
    {
        return Utils::toWei($number, $unit);
    }
}
