<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-29
 * Time: 15:25
 */

namespace Swokit\Http\Server\Util;

use PhpComp\Http\Message\Response;
use PhpComp\Http\Message\ServerRequest;
use PhpComp\Http\Message\UploadedFile;
use PhpComp\Http\Message\Uri;
use Psr\Http\Message\ResponseInterface;
use Swoole\Http\Request as SwRequest;
use Swoole\Http\Response as SwResponse;

/**
 * Class Psr7Http
 * @package Swokit\Http\Server\Util
 */
class Psr7Http
{
    public const ATTRIBUTE_FD = '__fd';
    public const ATTRIBUTE_REQ = '__sw_req';
    public const ATTRIBUTE_RES = '__sw_res';

    /**
     * @param \Swoole\Http\Request $swReq
     * @param \Swoole\Http\Response $swRes
     * @return ServerRequest
     */
    public static function createServerRequest(SwRequest $swReq, SwResponse $swRes)
    {
        $uri = $swReq->server['request_uri'];
        $method = $swReq->server['request_method'];
        $psr7req = new ServerRequest($method, Uri::createFromString($uri));

        // add attribute data
        $psr7req->setAttribute(self::ATTRIBUTE_FD, $swReq->fd);
        $psr7req->setAttribute(self::ATTRIBUTE_REQ, $swReq);
        $psr7req->setAttribute(self::ATTRIBUTE_RES, $swRes);

        // GET data
        if (!empty($swReq->get)) {
            $psr7req->setQueryParams($swReq->get);
        }

        // POST data
        if (!empty($swReq->post)) {
            $psr7req->setParsedBody($swReq->post);
        }

        // Cookies data
        if (!empty($swReq->cookie)) {
            $psr7req->setCookies($swReq->cookie);
        }

        // FILES data
        if (!empty($swReq->files)) {
            $psr7req->setUploadedFiles(UploadedFile::parseUploadedFiles($swReq->files));
        }

        // SERVER data
        $serverData = \array_change_key_case($swReq->server, \CASE_UPPER);

        if ($swReq->header) { // headers
            $psr7req->setHeaders($swReq->header);

            // 将 HTTP 头信息赋值给 $serverData
            foreach ((array)$swReq->header as $key => $value) {
                $_key = 'HTTP_' . \strtoupper(\str_replace('-', '_', $key));
                $serverData[$_key] = $value;
            }
        }

        $psr7req->setServerParams($serverData);
        return $psr7req;
    }

    /**
     * @param null|array $headers
     * @return Response
     */
    public static function createResponse(array $headers = null)
    {
        // $headers = [
        //   'Content-Type' => 'text/html; charset=' . \Sws::get('config')->get('charset', 'UTF-8')
        //];

        return new Response(200, $headers);
    }

    /**
     * @param Response|ResponseInterface $psr7res
     * @param SwResponse $swResponse
     * @param bool $send
     * @return SwResponse|mixed
     */
    public static function respond(Response $psr7res, SwResponse $swResponse = null, bool $send = true)
    {
        $swResponse = $swResponse ?: new SwResponse();

        // set http status
        $swResponse->status($psr7res->getStatus());

        // set headers
        foreach ($psr7res->getHeadersObject()->getLines() as $name => $value) {
            $swResponse->header($name, $value);
        }

        // set cookies
        foreach ($psr7res->getCookies()->toHeaders() as $value) {
            $swResponse->header('Set-Cookie', $value);
        }

        // write content
        if ($body = (string)$psr7res->getBody()) {
            $swResponse->write($body);
        }

        // send response to client
        if ($send) {
            return $swResponse->end();
        }

        return $swResponse;
    }
}
