<?php

declare(strict_types=1);
use Hyperf\Utils\ApplicationContext;

if (! function_exists('getuuid')) {
    /**
     * Notes: make uuid
     * User: Weihz
     * @return mixed
     */
    function getuuid()
    {
        $container = ApplicationContext::getContainer();
        $generator = $container->get(\Hyperf\Snowflake\IdGeneratorInterface::class);

        return $generator->generate();
    }
}

if (! function_exists('runWithLock')) {
    /**
     * Notes: run closure with lock
     * User: Weihz
     * @param  Closure $callback
     * @param  string  $lockName
     * @param  int     $lockValue
     * @param  int     $lockSec
     * @return mixed
     */
    function runWithLock(Closure $callback, $lockName = '', $lockValue = null, $lockSec = 60)
    {
        $lockValue = $lockValue ?? getuuid();
        $lock = make(\Kenvinwei\HyperfHelper\Lock\RedisLock::class, ['name' => $lockName, 'seconds' => $lockSec, 'owner' => $lockValue]);
        return $lock->get($callback);
    }
}
