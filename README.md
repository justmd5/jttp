<h1 align="center">JTTP</h1>

<p align="center">just a simple http request tool</p>

<p align="center">
<a href="https://styleci.io/repos/138467318"><img src="https://styleci.io/repos/138467318/shield?branch=master" alt="styleci"></a>
<a href="https://packagist.org/packages/justmd5/jttp"><img src="https://img.shields.io/packagist/php-v/justmd5/jttp.svg" alt="PHP from Packagist"></a>
<a href="https://packagist.org/packages/justmd5/jttp"><img src="https://poser.pugx.org/justmd5/jttp/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/justmd5/jttp"><img src="https://img.shields.io/github/stars/justmd5/jttp.svg?style=social&label=Stars" alt="GitHub stars"></a>
<a href="https://packagist.org/packages/justmd5/jttp"><img src="https://poser.pugx.org/justmd5/jttp/v/unstable.svg" alt="Latest Unstable Version"></a>
<a href="https://packagist.org/packages/justmd5/jttp"><img src="https://img.shields.io/github/license/justmd5/jttp.svg" alt="License"></a>
</p>

### Requirement
1. PHP >= 7.0
2. **[Composer](https://getcomposer.org/)**
3. ext-curl 拓展
4. ext-json 拓展


## Installing

```shell
$ composer require justmd5/jttp -vvv
```

## Usage
```
<?php

use Justmd5\Jttp\Jttp;

require  __DIR__.'/vendor/autoload.php';
print_r(Jttp::request('get','https://httpbin.org/ip'));
//{"origin": "1.2.3.4"}

```

## License

MIT