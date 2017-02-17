<?php
require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver'    => 'pdo_pgsql',
        'host'      => 'localhost',
        'dbname'    => 'silex_db',
        'user'      => 'postgres',
        'password'  => 'postgres',
        'charset'   => 'utf8',
    ),
));

$app->get('/login', function () use ($app){
    return $app['twig']->render('login.twig', array(
    ));
});

//Show one blog from $blogPosts
//$app->get('/post/{post}', function ($post) use($app){
//    return $app['twig']->render('oneblog.twig', array(
//        'blogs' => $post
//    ));
//})
//    ->bind('blog_post')
//    ->convert('post', $blogProvider)
//;

//Show all blogs from $blogPosts
//$app->get('/', function () use ($app, $blogPosts){
//    return $app['twig']->render('main.twig', array(
//        'blogs' => $blogPosts
//    ));
//});

//Usage DB
//$app->get('/blog/{id}', function ($id) use ($app) {
//    $sql = "SELECT * FROM posts WHERE id = ?";
//    $post = $app['db']->fetchAssoc($sql, array((int) $id));
//
//    return  "<h1>{$post['title']}</h1>".
//        "<p>{$post['text']}</p>";
//});

//Show all posts from DB
$app->get('/', function () use ($app) {
    $sql = "SELECT * FROM posts";
    $posts = $app['db']->fetchAll($sql);
    return $app['twig']->render('main.twig', array(
        'blogs' => $posts
    ));
});

//Show one blog from  DB
$app->get('/{post}', function () use ($app) {
    $sql = "SELECT * FROM posts";
    $post = $app['db']->fetchAssoc($sql);
//    var_dump($post);
//    die();
    return $app['twig']->render('oneblog.twig', array(
        'blogs' => $post
    ));
})
    ->bind('blog_post')
;

$app->run();