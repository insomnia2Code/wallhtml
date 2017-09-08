<?php

require 'vendor/autoload.php';
require 'Helper.php';

use Symfony\Component\DomCrawler\Crawler;
// ...
spl_autoload_register('_loader');

function _loader($className){
    require_once './class/' . ucfirst($className) . '.php';
}


$db = Mysql::instance();

$data = [];
$data['url'] = 'test';
$data['src'] = '';
$data['width'] = 0;
$data['height'] = 0;
$data['tags'] = [];
$data['class'] = '';
$data['size'] = 0;
$data['type'] = '';
$data['tags'] = 0;
$db->prepare('insert into wall_pic (`url`, `src`, `width`, `height`, `size`, `class`, `tags`, `type`) values(:url, :src, :width, :height, :size, :class, :tags, :type)')->bindArray($data)->execute();