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

$http->setRequestHandler(ClosureWrapper::create(function(ServerRequestInterface $r): ResponseInterface {
    $psr7res = HttpFactory::createResponse();
    $psr7res->write('hello. URI: '. (string)$r->getUri());
    
    return $psr7res;
}));

$http->run();
```

## license

MIT
