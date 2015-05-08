<?php

use BrainExe\Core\Core;

define('ROOT', realpath(__DIR__ . '/..') . '/');

include ROOT . '/vendor/autoload.php';

$core = new Core();
return $core->boot();
