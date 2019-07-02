<?php

require '../vendor/autoload.php';

use \Spencer\Application;
use \Spencer\BootLoader;

$app = Application::instance();

BootLoader::bootstrap();