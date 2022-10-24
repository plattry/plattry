<?php

declare(strict_types = 1);

namespace App\Http\Logic;

use App\Facade\Config;
use App\Facade\Log;

/**
 * A index logic instance.
 */
class IndexLogic
{
    /**
     * Say hello.
     * @return string
     */
    public function hello(): string
    {
        return "hello plattry!";
    }
}
