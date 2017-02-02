<?php
require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../app/Application.php';

$app = new Silex\Application();

$app['debug'] = true;

$app->register(new \Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../app/Resources/views'
));

$app->get('/hello/{name}', function($name) use($app) {
    return $app->render('hello.html.twig', array(
        'name' => $name
    ));
});

$app->get('/', function() use($app){
    return 'Hello!';
});
$app->run();