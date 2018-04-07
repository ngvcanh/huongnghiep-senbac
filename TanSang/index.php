<?php
define('K_DS', DIRECTORY_SEPARATOR);
define('K_ROOT', __DIR__ . K_DS);

include 'Config.php';
include 'Autoload.php';

$controller = \Application\Controller::init();
$controller->run();