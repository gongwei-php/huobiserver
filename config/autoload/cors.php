<?php declare(strict_types=1); 
return [ 
    	'origin' => [ 
		'http://localhost:2888', 
		'http://45.93.29.17:2888',
        	// 生产可写具体域名，开发用 * 
		'*',
    	], 
	'allowed_methods' => ['*'], 
	'allowed_headers' => ['*'], 
	'exposed_headers' => ['*'], 
	'max_age' => 3600,
    	// 如果你前端带 cookie/Authorization 设为 true
    	'supports_credentials' => false,
];
