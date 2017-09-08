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
$baseNum = 1;

$mysqlConfig = [
    'dsn' => 'mysql:host=localhost;dbname=myweb',
    'name' => 'root',
    'password' => '123456',
];

$db = Mysql::getInstance();

while($baseNum <= 5) {
    $html = Http::get($baseUrl . $baseNum);

    $crawler = new Crawler($html);

    $data = [];
    $data['url'] = $baseUrl . $baseNum;
    $data['src'] = '';
    $data['width'] = 0;
    $data['height'] = 0;
    $data['tags'] = [];
    $data['class'] = '';
    $data['size'] = 0;
    $data['type'] = '';

    $widthHeight = $crawler->filter('body .showcase-resolution');
    $tags = $crawler->filter('body .tag-sfw .tagname');
    $class = $crawler->filter('body .purity.sfw');
    $size = $crawler->filter('body, dl.showcase-uploader')->parents()->filter('dd')->eq(2);
    $src = $crawler->filter('body, #wallpaper');
    if ($src->count()) {
        $data['src'] = $src->first()->attr('src');
        $data['src'] = trim(strrchr('.', $data['src']), '.');
    } else {
        $db->prepare('insert into wall_pic (`url`, `src`, `width`, `height`, `size`, `class`, `tags`, `type`) values(:url, :src, :width, :height, :size, :class, :tags, :type)')->bindArray($data)->execute();
        sleep(1);
        continue;
    }

    if ($widthHeight->count()) {
        $widthHeightInfo = explode('x', $widthHeight->first()->html());
        $data['width'] = trim($widthHeightInfo[0]);
        $data['height'] = trim($widthHeightInfo[1]);
    }
    $tagsArr = [];
    if ($tags->count()) {
        $tags->each(function($node){
            $tagsArr[] = trim($node->html());
        });
    }
    $data['tags'] = $tagsArr;

    if ($class->count()) {
        $data['class'] = trim($class->first()->html());
    }
    if ($size->count()) {
        $data['size'] = toBite($size->first()->html());
    }

    $baseNum++;
    sleep(1);
}
