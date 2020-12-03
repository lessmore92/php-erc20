<?php
/**
 * User: Lessmore92
 * Date: 11/22/2020
 * Time: 5:26 AM
 */

namespace Lessmore92\Ethereum\Utils;


use Web3p\EthereumUtil\Util;

class KeyPair
{
    public static function privateKeyToAddress(string $privateKey): string
    {
        $util      = new Util();
        $publicKey = $util->privateKeyToPublicKey($privateKey);
        return $util->publicKeyToAddress($publicKey);
    }
}
