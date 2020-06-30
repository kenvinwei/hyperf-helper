<?php

declare(strict_types=1);

namespace Kenvinwei\HyperfHelper\Lock;

use Hyperf\Redis\Redis;

class RedisLock extends Lock
{

	/**
	 * @var Redis 
	 */
    protected $redis;

	/**
	 * RedisLock constructor.
	 * @param Redis $redis
	 * @param $name
	 * @param $seconds
	 * @param null $owner
	 */
    public function __construct(Redis $redis, $name, $seconds, $owner = null)
    {
        parent::__construct($name, $seconds, $owner);

        $this->redis = $redis;
    }

    /**
     * Attempt to acquire the lock.
     *
     * @return bool
     */
    public function acquire()
    {
        if ($this->seconds > 0) {
            return $this->redis->set($this->name, $this->owner, ['nx', 'ex' => $this->seconds]);
        }
        return $this->redis->setnx($this->name, $this->owner);
    }

    /**
     * Release the lock.
     *
     * @return bool
     */
    public function release()
    {
    	$name = config('redis.prefix', '') . $this->name;
    	
        $luaScript = <<<'LUA'
if redis.call("get",KEYS[1]) == ARGV[1] then
    return redis.call("del",KEYS[1])
else
    return 0
end
LUA;
        return $this->redis->eval($luaScript, [$name, $this->owner], 1);
    }

    /**
     * Releases this lock in disregard of ownership.
     */
    public function forceRelease()
    {
        $this->redis->del($this->name);
    }

    /**
     * Returns the owner value written into the driver for this lock.
     *
     * @return string
     */
    protected function getCurrentOwner()
    {
        return $this->redis->get($this->name);
    }
}
