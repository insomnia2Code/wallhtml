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
$baseNum = 3269; // 557905

$mysqlConfig = [
    'dsn' => 'mysql:host=localhost;dbname=myweb',
    'name' => 'root',
    'password' => 'root',
];

$db = Mysql::instance($mysqlConfig);

while($baseNum <= 20000) {
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

    try{

        $widthHeight = $crawler->filter('body .showcase-resolution');
        $tags = $crawler->filter('body #tags .tag .tagname');
        $class = $crawler->filter('body #wallpaper-purity-form');

        $size = $crawler->filter('body dl')->eq(0)->filter('dd')->eq(2);
        $src = $crawler->filter('body #wallpaper');
        if ($src->count()) {
            $data['src'] = $src->first()->attr('src');
            $data['type'] = trim(strrchr($data['src'], '.'), '.');
        } else {
            $db->prepare('insert into wall_pic (`url`, `src`, `width`, `height`, `size`, `class`, `tags`, `type`) values(:url, :src, :width, :height, :size, :class, :tags, :type)')->bindArray($data)->execute();
            $baseNum++;
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
            $tags->each(function($node) use ($db, $baseNum){
                $existTag =  $db->query("select * from wall_tag where `name`='". trim($node->html()). "'")->fetchAll();
                if (!empty($existTag)) {
                    $tagId = $existTag[0]->id;
                } else {
                    $db->prepare('insert into wall_tag (`name`) values(:name)')->bindArray(['name' => trim($node->html())])->execute();
                    $tagId = $db->lastId();
                }

                $db->prepare('insert into wall_pic_tag (`pic_id`,`tag_id`) values(:pic_id, :tag_id)')->bindArray(['pic_id' => $baseNum, 'tag_id' => $tagId])->execute();
            });
        }
        $data['tags'] = '';

        if ($class->count()) {
            if ($class->first()->filter('input[checked]')->attr('value')) {
                $data['class'] = trim($class->first()->filter('input[checked]')->attr('value'));
            } else {
                $data['class'] = trim($class->first()->text());
            }

        } else {
            $data['class'] = 'NSFW';
        }
        if ($size->count()) {
            $data['size'] = toBite($size->html());
        }

        $res = $db->prepare('insert into wall_pic (`url`, `src`, `width`, `height`, `size`, `class`, `tags`, `type`) values(:url, :src, :width, :height, :size, :class, :tags, :type)')->bindArray($data)->execute();

        if (!$res) {
            $temp = [];
            $temp['pic_id'] = $baseNum;
            $temp['data'] = json_encode($data);
            $temp['create_time'] = date('Y-m-d H:i:s');
            $db->prepare('insert into wall_log (`pic_id`, `data`, `create_time`) values(:pic_id, :data, :create_time)')->bindArray($temp)->execute();
        }

        $baseNum++;
        sleep(1);
    } catch(Exception $e) {
        $res = $db->prepare('insert into wall_pic (`url`, `src`, `width`, `height`, `size`, `class`, `tags`, `type`) values(:url, :src, :width, :height, :size, :class, :tags, :type)')->bindArray($data)->execute();

        if (!$res) {
            $temp = [];
            $temp['pic_id'] = $baseNum;
            $temp['data'] = json_encode($data);
            $temp['create_time'] = date('Y-m-d H:i:s');
            $db->prepare('insert into wall_log (`pic_id`, `data`, `create_time`) values(:pic_id, :data, :create_time)')->bindArray($temp)->execute();
        }
        $baseNum++;
        sleep(1);
    }

}
