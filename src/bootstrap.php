<?php

use BrainExe\Core\Core;

define('ROOT', realpath(__DIR__ . '/..') . '/');

include ROOT . '/vendor/autoload.php';

return (new Core())->boot();
