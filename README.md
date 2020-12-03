# Simple Ethereum ERC20 token php library

This library provides simple way to interact with Ethereum ERC20 token.  
By default, supports all ERC20 Standard functions (like balanceOf, transfer, transferFrom, approve, allowance, decimal, name, ...) also can be extends to support other contracts as well.

## Installation
`composer require ...`

## Usage
There are two ways to use:
#### 1- Make a new class for your token and specified their functions
#### 2- Use general class with all standard functions

See below to find out more


## 1-Make a new class for your token
Simply create a new class inherits from `\Lessmore\Ethereum\Foundation\StandardERC20Token`

in below sample we create a new class for Tether (USDT)
```
class USDT extends \Lessmore\Ethereum\Token  
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

#### 2- Use general class

```
$token = new \Lessmore\Ethereum\Token("0xc6fDe3FD2Cc2b173aEC24cc3f267cb3Cd78a26B7", "https://mainnet.infura.io/v3/API_KEY");
var_dump($token->name());
```

## Ethereum RPC Client
For connect to Ethereum blockchain you need an Ethereum node; [Infura](https://infura.io/) is a simple and fast solution, however you can launch you [Geth](https://geth.ethereum.org/) node

 

