
<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

if (isset($get['token'])){
    $activeToken = $get['token'];
    $length = 86;

    if (is_string($activeToken) && strlen($activeToken) === $length){
        
    }
}