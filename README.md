# jttp
>just a simple http request tool



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