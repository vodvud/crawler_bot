<?php
chdir(__DIR__);
include('vendor'.DIRECTORY_SEPARATOR.'autoload_register.php');

$bot = new \Crawler\Bot();
$bot->init();