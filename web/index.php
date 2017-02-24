<?php
require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

//Add new post
$app->post('/add_new_post', function (){

    $sql = "INSERT INTO posts (text, date, title, author) VALUES ('Text', '2017-02-24', 'Title', 'Author')";
    return new Response();
})
    ->bind('add_new_post');

//Go to 'Add new post'
$app->get('/add-new', function () use($app){
    return $app['twig']->render('add_new_post.twig', array(
    ));
})
    ->bind('new_post');

//Show all posts from DB
$app->get('/', function () use ($app) {
    $sql = "SELECT * FROM posts";
    $posts = $app['db']->fetchAll($sql);
    return $app['twig']->render('main.twig', array(
        'blogs' => $posts
    ));
})
    ->bind('blog_posts')
;

//Show one blog from  DB
$app->get('/post/{post}', function ($post) use ($app) {
    $sql = "SELECT * FROM posts WHERE id = ?";
    $post = $app['db']->fetchAssoc($sql, array((int) $post));
    return $app['twig']->render('oneblog.twig', array(
        'blogs' => $post
    ));
})
    ->bind('blog_post')
;
$app->run();