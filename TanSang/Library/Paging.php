<?php
namespace Library;
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

class Paging{

    static function rangePage($current, $range, $total){
        $diff = ($range - ($range % 2)) / 2;

        $min = $current - $diff;
        if ($min < 1) $min = 1;

        $max = $min + $range;
        if ($max > $total){
            $max = $total;
            $min = $max - $range;
            if ($min < 1) $min = 1;
        }

        return [$min, $max];
    }

    static function current(array $get, $name){
        $page = 1;
        if (isset($get[$name])){
            $gpage = $get[$name];
            if (is_numeric($gpage) && $gpage > 0) $page = $gpage;
        }
        return $page;
    }

    static function getStart($limit, $current){
        return ($current - 1) * $limit;
    }

    static function getPageNext($page, $total, $class, $url){
        if ($page < $total) $class = '';
        else $url = '#';
        return [$url, $class];
    }

    static function getPagePrev($page, $class, $url){
        if ($page > 1) $class = '';
        else $url = '#';
        return [$url, $class];
    }

}