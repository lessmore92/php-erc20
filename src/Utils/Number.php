<?php
/**
 * User: Lessmore92
 * Date: 11/21/2020
 * Time: 5:20 AM
 */

namespace Lessmore92\Ethereum\Utils;


use phpseclib\Math\BigInteger;
use Web3\Utils;

class Number
{
    public static function scaleDown(string $number, int $decimals)
    {
        return bcdiv($number, bcpow("10", strval($decimals)), $decimals);
    }

    public static function scaleUp(string $number, int $decimals): string
    {
        return bcmul($number, bcpow("10", strval($decimals)));
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

    public static function toHex($number)
    {
        return Utils::toHex($number);
    }
}
