<?php

declare(strict_types=1);

namespace Kenvinwei\HyperfHelper\Test\Cases\Lock;

use Kenvinwei\HyperfHelper\Lock\RedisLock;
use PHPUnit\Framework\TestCase;

class RedisLockTest extends TestCase
{
	/**
     * composer test --  --filter=testLockGet
     */
	public function testLockGet()
	{
		$lockObj = make(RedisLock::class, [
			'name' => 'test_lock_name',
			'seconds' => 60,
			'owner' => time()
		]);

		$test1 = co(function() use ($lockObj) {
			return $lockObj->get(function(){
				echo 'test1';
				return true;
			});
		});

		$test2 = co(function() use ($lockObj) {
			return $lockObj->get(function(){
				echo 'test2';
				return true;
			});
		});

		$test3 = co(function() use ($lockObj) {
			$lockObj->get(function(){
				echo 'test3';
				return true;
			});
		});
		
		var_dump([$test1, $test2, $test3]);
	}

}