<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

$access_action = ['Home'];
if (!in_array($action, $access_action)) return;