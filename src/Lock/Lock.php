<?php

declare(strict_types=1);

namespace Kenvinwei\HyperfHelper\Lock;


use Carbon\Carbon;
use Hyperf\Utils\InteractsWithTime;
use Hyperf\Utils\Str;

abstract class Lock
{
	use InteractsWithTime;
	/**
	 * The name of the lock.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * The number of seconds the lock should be maintained.
	 *
	 * @var int
	 */
	protected $seconds;

	/**
	 * The scope identifier of this lock.
	 *
	 * @var string
	 */
	protected $owner;

	/**
	 * Create a new lock instance.
	 *
	 * @param  string  $name
	 * @param  int  $seconds
	 * @param  string|null  $owner
	 * @return void
	 */
	public function __construct($name, $seconds, $owner = null)
	{
		if (is_null($owner)) {
			$owner = Str::random();
		}

		$this->name = $name;
		$this->owner = $owner;
		$this->seconds = $seconds;
	}

	/**
	 * Attempt to acquire the lock.
	 *
	 * @return bool
	 */
	abstract public function acquire();

	/**
	 * Release the lock.
	 *
	 * @return bool
	 */
	abstract public function release();

	/**
	 * Returns the owner value written into the driver for this lock.
	 *
	 * @return string
	 */
	abstract protected function getCurrentOwner();

	/**
	 * Attempt to acquire the lock.
	 *
	 * @param  callable|null  $callback
	 * @return mixed
	 */
	public function get($callback = null)
	{
		$result = $this->acquire();

		if ($result && is_callable($callback)) {
			try {
				return $callback();
			} finally {
				$this->release();
			}
		}

		return $result;
	}

	/**
	 * User: kenvinwei
	 * @param $seconds
	 * @param null $callback
	 * @return bool
	 * @throws \Exception
	 */
	public function block($seconds, $callback = null)
	{
		$starting = $this->currentTime();

		while (! $this->acquire()) {
			usleep(250 * 1000);

			if ($this->currentTime() - $seconds >= $starting) {
				throw new \Exception('Lock timeout!');
			}
		}

		if (is_callable($callback)) {
			try {
				return $callback();
			} finally {
				$this->release();
			}
		}

		return true;
	}

	/**
	 * Returns the current owner of the lock.
	 *
	 * @return string
	 */
	public function owner()
	{
		return $this->owner;
	}

	/**
	 * Determines whether this lock is allowed to release the lock in the driver.
	 *
	 * @return bool
	 */
	protected function isOwnedByCurrentProcess()
	{
		return $this->getCurrentOwner() === $this->owner;
	}
}