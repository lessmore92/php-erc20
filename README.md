# Simple Ethereum ERC20 token php library

This library provides simple way to interact with Ethereum ERC20 token.  
By default, supports all ERC20 Standard functions (like balanceOf, transfer, transferFrom, approve, allowance, decimal, name, ...) also can be extends to support other contracts as well.

## Installation
`composer require lessmore92/php-erc20`

## Usage
There are two ways to use:
#### 1- Make a new class for your token and specified their functions
#### 2- Use general class with all standard functions

See below to find out more


### 1-Make a new class for your token
Simply create a new class inherits from `\Lessmore92\Ethereum\Foundation\StandardERC20Token`

in below sample we create a new class for Tether (USDT)
```
class USDT extends \Lessmore92\Ethereum\Foundation\StandardERC20Token 
{
    protected $contractAddress = "0xdac17f958d2ee523a2206206994597c13d831ec7";  
}
```
Then for use create new instantiate from your class and

```
$tether = new USDT("https://mainnet.infura.io/v3/API_KEY");
var_dump($tether->name());
var_dump($tether->decimals());
```

### 2- Use general class

```
$token = new \Lessmore92\Ethereum\Token("0xdac17f958d2ee523a2206206994597c13d831ec7", "https://mainnet.infura.io/v3/API_KEY");
var_dump($token->name());
```


### Connection Timeout

Connection timeout can be set by last parameter of token class

```
$timeout  = 3; //secs
$tether = new USDT("https://mainnet.infura.io/v3/API_KEY",$timeout);
```
OR
```
$timeout  = 3; //secs
$tether = new \Lessmore92\Ethereum\Token("0xdac17f958d2ee523a2206206994597c13d831ec7", "https://mainnet.infura.io/v3/API_KEY", $timeout);
```

## Ethereum RPC Client
For connect to Ethereum blockchain you need an Ethereum node; [Infura](https://infura.io/) is a simple and fast solution, however you can launch you [Geth](https://geth.ethereum.org/) node


## ERC20 Token `transferFrom`
ERC20 transaction fee needs to be paid in `ETH`. In some situation your app needs to pay this fee behalf of user.  
Suppose, user A have a key pair (private, public) and all their transaction is limited to usdt. User A needs to send 10 usdt, but he/she haven't ETH to pay transaction fee.  
In these cases your app should pay fee behalf of users.      
`transferFrom` is a good solution in these cases.


## `transferFrom` Flow:
1.First, Using `approve` method to grant permission to a delegator.  
2.Then, Using `transferFrom` method to make transaction behalf of user. 

*In Action*
```
$owner_private = '0xcf29c83a88e23d0b9e676beca426490bf79aca71e9d24f79a99d30c48292e1e3';
$owner_address = '0xA7e5F270c27E9d33911EE7D50D8E814f793d2760';

$myapp_private = '0xa6b6be193bfeac6160178ee6e1435609ae566a9054715e0802e4c3b39bb94e83';
$myapp_address = '0x8dC9b3c20795815aa063FEdBE8E564566CEc1893';

$to_address = '0x245013F05DdA116142Ca8db205ec4F8C780E3DcB';

//by this method we allow $myapp_address to send upto 99999 token behalf of $owner_address
$approve_tx    = $token->approve($owner_address, $myapp_address, 99999);
$approve_tx_id = $approve_tx->sign($owner_private)
                            ->send()
;


//the magic is here, $myapp_address send 10 tokens behalf of user and $myapp_address pay transaction fee
$transfer_tx    = $token->transferFrom($myapp_address, $owner_address, $to_address, 10);
$transfer_tx_id = $transfer_tx->sign($myapp_private)
                              ->send()
;

```

### `allowance` to check how much transferFrom remain

`$remain = $token->allowance($owner_address, $myapp_address);`

*Notices:*  
`approve` method not need to be used on every transaction.  
To revoke `transferFrom` permission call `$token->approve($owner_address, $myapp_address, 0)` by amount `0`.  
