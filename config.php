<?php

define('SITE_NAME', 'Slim Boilerplate');


//DB parameters have to be specified here AND in /migrations/.dbup/properties.ini

if ($_SERVER['HTTP_HOST'] == 'server.deployment.com') {
	define('DB_TYPE', 'mysql');
	define('DB_HOST', 'localhost');
	define('DB_BASE', 'test');
	define('DB_USER', 'root');
	define('DB_PASSWORD', '');
} else {
	define('DB_TYPE', 'mysql');
	define('DB_HOST', 'localhost');
	define('DB_BASE', 'test');
	define('DB_USER', 'root');
	define('DB_PASSWORD', '');
}
