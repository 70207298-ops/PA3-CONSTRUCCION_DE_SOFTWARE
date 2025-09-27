<?php

if (!defined('DB_HOST'))    define('DB_HOST','127.0.0.1');
if (!defined('DB_NAME'))    define('DB_NAME','pet_happy_store');
if (!defined('DB_USER'))    define('DB_USER','root');
if (!defined('DB_PASS'))    define('DB_PASS','');
if (!defined('DB_CHARSET')) define('DB_CHARSET','utf8mb4');


error_reporting(E_ALL);
ini_set('display_errors','1');
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
