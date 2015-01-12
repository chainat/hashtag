<?php namespace Chainat\Hashtag;
ini_set('display_errors', true);
error_reporting(E_ALL);

include '../src/collectors/CollectorInterface.php';
include '../src/collectors/Instagram.php';

$className = "\Chainat\Hashtag\Instagram";
$a = new $className();
print_r($a);