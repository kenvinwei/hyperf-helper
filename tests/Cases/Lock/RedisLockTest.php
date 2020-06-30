<?php

declare(strict_types=1);

namespace Kenvinwei\HyperfHelper\Test\Cases\Lock;

use Kenvinwei\HyperfHelper\Lock\RedisLock;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
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
            'owner' => time(),
        ]);
        for ($i = 0; $i < 5; ++$i) {
            co(function () use ($lockObj, &$result) {
                $res = $lockObj->get(function () {
                    return true;
                });
                array_push($result, (int) $res);
            });
        }

        sleep(1);

        $timesSumValue = array_sum($result) ?? 0;
        $this->assertSame($timesSumValue, 1);
    }
    
    /**
     * composer test --  --filter=testRunWithLock
     */
    public function testRunWithLock()
    {
        $result = [];
        $name = 'test_lock_run_with_lock';
        for ($i = 0; $i < 5; ++$i) {
            co(function () use ($name, &$result) {
                $res = runWithLock(function () use ($name, $result) {
                    return true;
                }, $name);
                array_push($result, $res);
            });
        }

        sleep(1);

        $timesSumValue = array_sum($result) ?? 0;
        $this->assertSame($timesSumValue, 1);
    }
}
