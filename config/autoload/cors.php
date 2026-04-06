<?php

declare(strict_types=1);
return [
	'origin' => ['*'],
	'allowed_methods' => ['*'],
	'allowed_headers' => ['*'],
	'exposed_headers' => ['*'],
	'max_age' => 3600,
	// 如果你前端带 cookie/Authorization 设为 true
	'supports_credentials' => false,
];
