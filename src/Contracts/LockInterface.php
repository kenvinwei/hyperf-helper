<?php

declare(strict_types=1);

namespace Kenvinwei\HyperfHelper\Contracts;

interface LockInterface
{
    /**
     * Attempt to acquire the lock.
     *
     * @param  null|callable $callback
     * @return mixed
     */
    public function get($callback = null);

    /**
     * Attempt to acquire the lock for the given number of seconds.
     *
     * @param  int           $seconds
     * @param  null|callable $callback
     * @return bool
     */
    public function block($seconds, $callback = null);

    /**
     * Release the lock.
     */
    public function release();

    /**
     * Returns the current owner of the lock.
     *
     * @return string
     */
    public function owner();

    /**
     * Releases this lock in disregard of ownership.
     */
    public function forceRelease();
}
