<?php
error_reporting(E_ALL);
require_once 'functions.php';
require_once 'Parser.php';

$url = 'https://annalshub.com/category/buisness/';
$pattern = '#<div class="td_module_1 td_module_wrap td-animation-stack">(.*?)<div class="meta-info">#s';
$parser = new Parser($url, $pattern);

$parser->getNeedData([
    ['src', '#<div class="td-module-thumb"><a href="(.*?)" rel="bookmark"#s'],
    ['imgSrc', '#data-img-url="(.*?)"  width#s'],
    ['title', '#class="td-image-wrap " title="(.*?)" ><img class="entry-thumb"#s'],
]);

$content = $parser->getTextContent('src', '#(<div class="td-post-content td-pb-padding-side">.*?)<footer>#s');
$parser->getImg("imgSrc", __DIR__ . "/img");

debug($content);

