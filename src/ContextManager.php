<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-10-11
 * Time: 11:03
 */

namespace SwooleLib\Http;

use Sws\Coroutine\Coroutine;

/**
 * Class ContextManager
 * @package SwooleLib\Http
 */
class ContextManager extends \SwooleLib\Context\ContextManager
{
    /**
     * @return int|string
     */
    protected function getDefaultId()
    {
        return Coroutine::tid();
    }
}
