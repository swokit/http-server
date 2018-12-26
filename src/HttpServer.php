<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-02-24
 * Time: 16:04
 */

namespace Swokit\Http\Server;

use Inhere\Console\Utils\Show;
use Inhere\Library\Traits\OptionsTrait;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LogLevel;
use Swokit\Http\Server\Util\AssetProcessor;
use Swokit\Http\Server\Util\Psr7Http;
use Swokit\Server\Server;
use Swoole\Http\Request;
use Swoole\Http\Response;

// use Swokit\Server\Traits\HttpServerTrait;

/*

http config:

```
'main_server' => [
    'host' => '0.0.0.0',
    'port' => '9662',

    // enable https(SSL)
    // 使用SSL必须在编译swoole时加入--enable-openssl选项 并且要在'swoole'中的配置相关信息(@see AServerManager::defaultConfig())
    'type' => 'http', // 'http' 'https'

    // 运行模式
    // SWOOLE_PROCESS 业务代码在Worker进程中执行 SWOOLE_BASE 业务代码在Reactor进程中直接执行
    'mode' => 'process', // 'process' 'base'

    'event_handler' => \Swokit\Server\handlers\HttpServerHandler::class,
    'event_list' => [ '' ]
],
'options' => [

]
```
*/

/**
 * Class HttpServerHandler
 * @package Swokit\Server\handlers
 */
class HttpServer extends Server
{
    use OptionsTrait;

    /**
     * handle static file access.
     * @var AssetProcessor
     */
    protected $staticAccessHandler;

    /**
     * @var RequestHandlerInterface
     */
    protected $requestHandler;

    /**
     * @var array
     */
    protected $options = [
        'ignoreFavicon' => false,

        // @link https://wiki.swoole.com/wiki/page/410.html
        'openGzip' => true,
        'gzipLevel' => 1, // allow 1 - 9

        'enableStatic' => false,
        'staticSettings' => [
            'basePath' => '',
            'ext' => [],
            'dirMap' => [
                // 'url_match' => 'assets dir',
                '/assets' => 'web/assets',
                '/uploads' => 'web/uploads'
            ]
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public function __construct(array $config = [], array $options = [])
    {
        parent::__construct($config);

        if ($options1 = $this->config('options')) {
            $this->setOptions($options1);
        }

        if ($options) {
            $this->setOptions($options);
        }
    }

    /**
     * @param string $host
     * @param int $port
     */
    public function listen(string $host = 'localhost', int $port = 9501)
    {
        $this->setServerSettings([
            'host' => $host,
            'port' => $port,
        ]);

        $this->start();
    }

    /**
     * {@inheritDoc}
     */
    protected function beforeServerStart()
    {
        if ($this->getOption('enableStatic')) {
            $opts = $this->getOption('staticSettings');
            $this->staticAccessHandler = new AssetProcessor($opts);
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return bool
     */
    public function beforeRequest(Request $request, Response $response): bool
    {
    }

    /**
     * 处理http请求
     * @param  Request $request
     * @param  Response $response
     * @return bool|mixed
     */
    public function onRequest(Request $request, Response $response)
    {
        $uri = $request->server['request_uri'];
        $startTime = microtime(true);
        $request->server['request_memory'] = memory_get_usage(true);

        // test: `curl 127.0.0.1:9501/ping`
        if ($uri === '/ping') {
            return $response->end('+PONG' . PHP_EOL);
        }

        if (\strtolower($uri) === '/favicon.ico' && $this->getOption('ignoreFavicon')) {
            return $response->end('+ICON');
        }

        $reqTime = $request->server['request_time_float'];
        $this->log("request start, current time={$startTime}, request time={$reqTime}", [
            'uri' => $uri,
        ]);

        // handle the static resource request
        if ($stHandler = $this->staticAccessHandler) {
            if ($stHandler->handle($request, $response, $uri)) {
                $this->log("Access asset: $uri");
                return true;
            }

            if ($error = $stHandler->getError()) {
                $this->log($error, [], LogLevel::ERROR);
            }
        }

        // handle the Dynamic Request
        $psr7Req = Psr7Http::createServerRequest($request, $response);
        $psr7Res = ($this->requestHandler)($psr7Req);

        // respond to client
        Psr7Http::respond($psr7Res, $response);

        // after
        $endTime = microtime(true);
        $this->log(sprintf(
            'request ended, start time=%s, current time=%s, runtime=%s ms',
            $startTime, $endTime, round(($endTime - $startTime) * 1000, 4)
        ), [
            'uri' => $uri,
        ]);

        $this->afterRequest($request, $response);
        return true;
    }

    /**
     * @param Request $request
     * @param Response $response
     */
    protected function afterRequest(Request $request, Response $response)
    {
    }

    /**
     * @param Response $response
     * @param $url
     * @param int $mode
     * @return mixed
     */
    public function redirect($response, $url, $mode = 302)
    {
        $response->status($mode);
        $response->header('Location', $url);

        return $response->end();
    }

    /**
     * @param Request $req
     * @return bool
     */
    public function isAjax(Request $req): bool
    {
        if (isset($req->header['x-requested-with'])) {
            return $req->header['x-requested-with'] === 'XMLHttpRequest';
        }

        return false;
    }

    public function info()
    {
        parent::info();

        Show::title('some options for the http server');
        Show::mList($this->options);
    }

    /**
     * @return RequestHandlerInterface
     */
    public function getRequestHandler(): RequestHandlerInterface
    {
        return $this->requestHandler;
    }

    /**
     * @param RequestHandlerInterface $requestHandler
     */
    public function setRequestHandler(RequestHandlerInterface $requestHandler): void
    {
        $this->requestHandler = $requestHandler;
    }

}
