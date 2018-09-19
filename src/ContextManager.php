<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-10-11
 * Time: 11:03
 */

namespace SwoKit\Http\Server;

use SwoKit\Util\Coroutine;

/**
 * Class ContextManager
 * @package SwoKit\Http\Server
 */
class ContextManager extends \SwoKit\Context\ContextManager
{
    /**
     * @return int|string
     */
    protected function getDefaultId()
    {
        return Coroutine::tid();
    }
}
