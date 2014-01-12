<?php
date_default_timezone_set('UTC');

chdir(__DIR__);
include('vendor'.DIRECTORY_SEPARATOR.'autoload_register.php');

$bot = new \Crawler\Bot();
$bot->init();