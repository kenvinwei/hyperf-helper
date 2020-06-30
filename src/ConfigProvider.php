<?php

declare(strict_types=1);

namespace Kenvinwei\HyperfHelper;

class ConfigProvider
{
	public function __invoke(): array
	{
		return [
			'dependencies' => [
			],
			'annotations'  => [
				'scan' => [
					'paths' => [
						__DIR__,
					],
				],
			],
			'commands'     => [
			],
			'publish'      => [
			],
		];
	}
}

