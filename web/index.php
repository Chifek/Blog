<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
Request::enableHttpMethodParameterOverride();
use Dflydev\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
$app = new Silex\Application();
$app['debug'] = true;
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../views',
));

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver' => 'pdo_pgsql',
        'host' => 'localhost',
        'dbname' => 'silex_db',
        'user' => 'postgres',
        'password' => 'postgres',
        'charset' => 'utf8',
    ),
));

///-----> NEED TO END
$app->register(new DoctrineOrmServiceProvider, array(
    'orm.proxies_dir' => '/path/to/proxies',
    'orm.em.options' => array(
        'mappings' => array(
            // Using actual filesystem paths
            array(
                'type' => 'annotation',
                'namespace' => 'Foo\Entities',
                'path' => __DIR__.'/src/Foo/Entities',
            ),
            array(
                'type' => 'xml',
                'namespace' => 'Bat\Entities',
                'path' => __DIR__.'/src/Bat/Resources/mappings',
            ),
        ),
    ),
));

$app->get('/login', function () use ($app) {
    return $app['twig']->render('login.twig', array());
});

//ADD NEW POST
$app->post('/post', function (Request $request) use ($app) {

    $post = $request->request->get('post');
    $post['date'] = date('Y-m-d');

    $app['db']->insert('posts', $post);
    $postId = $app['db']->lastInsertId();

    return $app->redirect('/post/' . $postId);
})
    ->bind('add_new_post');

//DELETE POST
$app->delete('/post/{post}', function ($post) use ($app) {
    $app['db']->delete('posts', ['id' => $post]);

    return $app->redirect('/');
})
    ->bind('delete_post');

// ROUTE - 'ADD NEW POST'delete
$app->get('/add-new', function () use ($app) {
    return $app['twig']->render('add_new_post.twig', array());
})
    ->bind('new_post');

//SHOW ALL POST FROM DB
$app->get('/', function () use ($app) {
    $sql = "SELECT * FROM posts";
    $posts = $app['db']->fetchAll($sql);
    return $app['twig']->render('main.twig', array(
        'blogs' => $posts
    ));
})
    ->bind('blog_posts');

//SHOW ONE POST FROM DB
$app->get('/post/{post}', function ($post) use ($app) {
    $sql = "SELECT * FROM posts WHERE id = ?";
    $post = $app['db']->fetchAssoc($sql, array((int)$post));
    return $app['twig']->render('oneblog.twig', array(
        'blog' => $post
    ));
})
    ->bind('blog_post');
$app->run();