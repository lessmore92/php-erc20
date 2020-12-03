<?php
/**
 * User: Lessmore92
 * Date: 11/21/2020
 * Time: 5:20 AM
 */

namespace Lessmore92\Ethereum\Utils;


class Address
{
    public static function toTopic(string $address)
    {
        return '0x' . str_pad(str_ireplace('0x', '', $address), 64, "0", STR_PAD_LEFT);
    }
}
