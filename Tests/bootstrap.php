<?php

use Matze\Core\Core;

include __DIR__ . '/../src/bootstrap.php';

global $dic;
$dic = Core::rebuildDIC();