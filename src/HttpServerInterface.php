<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2018/9/3
 * Time: 下午4:59
 */

namespace SwoKit\Http\Server;

use Swoole\Http\Request;
use Swoole\Http\Response;

/**
 * Interface HttpServerInterface
 * @package SwoKit\Http\Server
 */
interface HttpServerInterface
{
    public function handleRequest(Request $request, Response $response);
}
