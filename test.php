<?php

require 'vendor/autoload.php';
require 'Helper.php';

use Symfony\Component\DomCrawler\Crawler;
// ...
spl_autoload_register('_loader');

function _loader($className){
    require_once './class/' . ucfirst($className) . '.php';
}

$baseUrl = 'https://alpha.wallhaven.cc/wallpaper/';
$baseNum = 10; // 557905

$mysqlConfig = [
    'dsn' => 'mysql:host=localhost;dbname=myweb',
    'name' => 'root',
    'password' => '123456',
];

$db = Mysql::instance($mysqlConfig);
$html = Http::get($baseUrl . $baseNum);

$crawler = new Crawler($html);

$data = [];
$data['url'] = $baseUrl . $baseNum;
$data['src'] = '';
$data['width'] = 0;
$data['height'] = 0;
$data['tags'] = '';
$data['class'] = '';
$data['size'] = 0;
$data['type'] = '';

    $widthHeight = $crawler->filter('body .showcase-resolution');
    $tags = $crawler->filter('body #tags .tag .tagname');
    $class = $crawler->filter('body #wallpaper-purity-form');

    $size = $crawler->filter('body dl')->eq(0)->filter('dd')->eq(2);
    $src = $crawler->filter('body #wallpaper');
    if ($src->count()) {
        $data['src'] = $src->first()->attr('src');
        $data['type'] = trim(strrchr($data['src'], '.'), '.');
    } else {

        sleep(1);
    }

    if ($widthHeight->count()) {
        $widthHeightInfo = explode('x', $widthHeight->first()->html());
        $data['width'] = trim($widthHeightInfo[0]);
        $data['height'] = trim($widthHeightInfo[1]);
    }
    $tagsArr = [];
    if ($tags->count()) {
        $tags->each(function($node) use ($db, $baseNum){

        });
    }
    $data['tags'] = '';

    if ($class->count()) {
        $data['class'] = trim($class->first()->text());
    } else {
        $data['class'] = 'NSFW';
    }
    if ($size->count()) {
        $data['size'] = toBite($size->html());
    }


    var_dump($data);