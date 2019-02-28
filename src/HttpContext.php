<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-31
 * Time: 15:16
 */

namespace Swokit\Http\Server;

use PhpComp\Http\Message\Request;
use PhpComp\Http\Message\Response;
use Swokit\Context\AbstractContext;
use Swokit\Http\Server\Util\Psr7Http;
use Swokit\Util\Coroutine;
use Swoole\Http\Request as SwRequest;
use Swoole\Http\Response as SwResponse;

/**
 * Class HttpContext
 * @package Swokit\Http\Server;
 */
class HttpContext extends AbstractContext
{
    /** @var array */
    private static $dataTypes = [
        'html' => 'text/html',
        'txt' => 'text/plain',
        'js' => 'application/javascript',
        'css' => 'text/css',
        'json' => 'application/json',
        'xml' => 'text/xml',
        'rdf' => 'application/rdf+xml',
        'atom' => 'application/atom+xml',
        'rss' => 'application/rss+xml',
        'form' => 'application/x-www-form-urlencoded'
    ];

    /**
     * @var Request
     */
    public $request;

    /**
     * @var Response
     */
    public $response;

    /**
     * @var SwRequest
     */
    public $swRequest;

    /**
     * @var SwResponse
     */
    public $swResponse;

    /**
     * @param SwRequest $swRequest
     * @param SwResponse $swResponse
     * @return static
     */
    public static function make(SwRequest $swRequest, SwResponse $swResponse)
    {
        $self = new static();
        $self->setRequestResponse($swRequest, $swResponse);

        return $self;
    }

    /**
     * object constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $id = Coroutine::id();

        $this->setId($id);
        $this->setKey(static::genKey($id));

        // \Sws::getCtxManager()->add($this);
    }

    /**
     * @param SwRequest $swRequest
     * @param SwResponse $swResponse
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function setRequestResponse(SwRequest $swRequest, SwResponse $swResponse): void
    {
        $this->request = Psr7Http::createServerRequest($swRequest, $swResponse);
        $this->response = Psr7Http::createResponse();

        $this->swRequest = $swRequest;
        $this->swResponse = $swResponse;
    }

    protected function init(): void
    {
    }

    /**
     * destroy
     */
    public function destroy(): void
    {
        // \Sws::getCtxManager()->del($this->getId());
        parent::destroy();

        $this->request = $this->response = $this->swRequest = $this->swResponse = null;
    }

    /**
     * @return array
     */
    public static function getDataTypes(): array
    {
        return self::$dataTypes;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @param Request $request
     */
    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }

    /**
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * @param Response $response
     */
    public function setResponse(Response $response): void
    {
        $this->response = $response;
    }

    /**
     * @return SwRequest
     */
    public function getSwRequest(): SwRequest
    {
        return $this->swRequest;
    }

    /**
     * @param SwRequest $swRequest
     */
    public function setSwRequest(SwRequest $swRequest): void
    {
        $this->swRequest = $swRequest;
    }

    /**
     * @return SwResponse
     */
    public function getSwResponse(): SwResponse
    {
        return $this->swResponse;
    }

    /**
     * @param SwResponse $swResponse
     */
    public function setSwResponse(SwResponse $swResponse): void
    {
        $this->swResponse = $swResponse;
    }
}
