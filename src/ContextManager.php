<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-10-11
 * Time: 11:03
 */

namespace Swokit\Http\Server;

use Swokit\Util\Coroutine;

/**
 * Class ContextManager
 * @package Swokit\Http\Server
 */
class ContextManager extends \Swokit\Context\ContextManager
{
    /**
     * @return int|string
     */
    protected function getDefaultId()
    {
        return Coroutine::tid();
    }
}
