<?php

declare(strict_types=1);


use Hyperf\Cache\Driver\RedisDriver;
use Hyperf\Codec\Packer\PhpSerializerPacker;

return [
	'default' => [
		'driver' => RedisDriver::class,
		'host'       => '127.0.0.1',
		'auth'   => 'Ab111222..',
		'port'       => 6379,
		'db'         => 0,
		'packer' => PhpSerializerPacker::class,
		'prefix' => 'Huobi:',
	],
	'jwt' => [
		'driver' => RedisDriver::class,
		'connection' => 'jwt',
		'packer' => PhpSerializerPacker::class,
		'prefix' => 'jwt:',
	],
];
