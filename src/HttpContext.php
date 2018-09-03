<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-31
 * Time: 15:16
 */

namespace SwooleKit\Http\Server;

use Inhere\Http\Request;
use Inhere\Http\Response;
use Swoole\Http\Request as SwRequest;
use Swoole\Http\Response as SwResponse;

use SwooleKit\Context\AbstractContext;
use SwooleKit\Http\Server\Util\Psr7Http;
use SwooleKit\Util\Coroutine;

/**
 * Class HttpContext
 * @package SwooleKit\Http\Server;
 */
class HttpContext extends AbstractContext
{
    /** @var array  */
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
    public function setRequestResponse(SwRequest $swRequest, SwResponse $swResponse)
    {
        $this->request = Psr7Http::createServerRequest($swRequest);
        $this->response = Psr7Http::createResponse();

        $this->swRequest = $swRequest;
        $this->swResponse = $swResponse;
    }

    protected function init()
    {
    }

    /**
     * destroy
     */
    public function destroy()
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
    public function setRequest(Request $request)
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
    public function setResponse(Response $response)
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
    public function setSwRequest(SwRequest $swRequest)
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
    public function setSwResponse(SwResponse $swResponse)
    {
        $this->swResponse = $swResponse;
    }
}
