<?php
require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;

$blogPosts = array(
    1=>array(
        'date' => '02.02.2017',
        'author' => 'J.Samat',
        'title' => 'One more time.',
        'body' => 'Appium aims to automate any mobile app from any language and any test framework, with full access to',
    ),
);
$app->get('/', function () use($blogPosts){
   $output = '';
   foreach ($blogPosts as $post){
       $output .= 'Title: ' .$post['title'];
       $output .= '<br />';
       $output .= 'Author: ' .$post['author'];
       $output .= '<br />';
       $output .= 'Text: ' .$post['body'];
       $output .= '<br />';
   }
   return $output;
});
$app->run();