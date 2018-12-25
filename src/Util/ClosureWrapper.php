<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2018-12-26
 * Time: 00:13
 */

namespace Swokit\Http\Server\Util;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class ClosureWrapper
 * @package Swokit\Http\Server\Util
 */
class ClosureWrapper implements RequestHandlerInterface
{
    /**
     * function(ServerRequestInterface $request): ResponseInterface {}
     * @var \Closure
     */
    private $closure;

    /**
     * @param \Closure $closureHandler
     * @return RequestHandlerInterface
     */
    public static function create(\Closure $closureHandler): RequestHandlerInterface
    {
        $self = new self();
        $self->closure = $closureHandler;
        return $self;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return ($this->closure)($request);
    }
}
