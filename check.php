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

$mysqlConfig = [
    'dsn' => 'mysql:host=localhost;dbname=myweb',
    'name' => 'root',
    'password' => '123456',
];

$db = Mysql::instance($mysqlConfig);

$needUpdate = $db->query("select * from wall_pic where class='' and type = '' and id >1000 and id<=2000 ")->fetchAll();


foreach ($needUpdate as $pic){

    $html = Http::get($pic->url);

    $crawler = new Crawler($html);

    $data = [];
    $data['url'] = $pic->url;
    $data['src'] = '';
    $data['width'] = 0;
    $data['height'] = 0;
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
            $tags->each(function($node) use ($db, $pic){
                $existTag =  $db->query("select * from wall_tag where `name`='". trim($node->html()). "'")->fetchAll();
                if (!empty($existTag)) {
                    $tagId = $existTag[0]->id;
                } else {
                    $db->prepare('insert into wall_tag (`name`) values(:name)')->bindArray(['name' => trim($node->html())])->execute();
                    $tagId = $db->lastId();
                }

                $existPicTag = $db->query("select * from wall_pic_tag where pic_id ={$pic->id} and tag_id = {$tagId}")->fetchAll();
                if (empty($existPicTag)) {
                    $db->prepare('insert into wall_pic_tag (`pic_id`,`tag_id`) values(:pic_id, :tag_id)')->bindArray(['pic_id' => $pic->id, 'tag_id' => $tagId])->execute();
                }

            });
        }

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
        $data['id'] = $pic->id;
        $res = $db->prepare('update wall_pic set `url`=:url, `src`=:src, `width`=:width, `height`=:height, `size`=:size, `class`=:class, `type`=:type where id=:id')->bindArray($data)->execute();

        if (!$res) {
            $temp = [];
            $temp['pic_id'] = $pic->id;
            $temp['data'] = json_encode($data);
            $temp['create_time'] = date('Y-m-d H:i:s');
            $db->prepare('insert into wall_log (`pic_id`, `data`, `create_time`) values(:pic_id, :data, :create_time)')->bindArray($temp)->execute();
        }

        sleep(1);
    } catch(Exception $e) {

        $temp = [];
        $temp['pic_id'] = $pic->id;
        $temp['data'] = json_encode($data);
        $temp['create_time'] = date('Y-m-d H:i:s');
        $db->prepare('insert into wall_log (`pic_id`, `data`, `create_time`) values(:pic_id, :data, :create_time)')->bindArray($temp)->execute();

        sleep(1);
    }

}
