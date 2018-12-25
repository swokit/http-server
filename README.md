# http server 

## Quick Start

```php
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Swokit\Http\Serer\HttpServer;
use Swokit\Http\Serer\Util\ClosureWrapper;
use PhpComp\Http\Message\HttpFactory;

$http = new HttpServer([
    // 
]);

$http->setRequestHandler(ClosureWrapper::create(function($r ServerRequestInterface): ResponseInterface {
    $psr7res = HttpFactory::createResponse();
    $psr7res->write('hello');
    
    return $psr7res;
}));

$http->listen();
```

## license

MIT
