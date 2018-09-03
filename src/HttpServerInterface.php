<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2018/9/3
 * Time: 下午4:59
 */

namespace SwooleKit\Http\Server;

use Swoole\Http\Request;
use Swoole\Http\Response;

/**
 * Interface HttpServerInterface
 * @package SwooleKit\Http\Server
 */
interface HttpServerInterface
{
    public function handleRequest(Request $request, Response $response);
}
