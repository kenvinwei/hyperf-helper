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
		$result = [];
		$lockObj = make(RedisLock::class, [
			'name' => 'test_lock_name',
			'seconds' => 60,
			'owner' => time()
		]);

		co(function() use ($lockObj, &$result) {
			$result1 = $lockObj->get(function(){
				echo 'test1';
				return true;
			});
			array_push($result, (int)$result1);
		});

		co(function() use ($lockObj, &$result) {
			$result2 = $lockObj->get(function(){
				echo 'test2';
				return true;
			});
			array_push($result, (int)$result2);
		});

		co(function() use ($lockObj, &$result) {
			$result3 = $lockObj->get(function(){
				echo 'test3';
				return true;
			});
			array_push($result, (int)$result3);
		});
		sleep(1);
		
		$timesSumValue = array_sum($result) ?? 0;
		$this->assertSame($timesSumValue, 1);
	}

}