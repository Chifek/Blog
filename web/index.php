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


$blogPosts = array(
    1=>array(
        'id' => '1',
        'date' => '02.02.2017',
        'author' => 'J.Samat',
        'title' => 'One more time.',
        'body' => 'Appium aims to automate any mobile app from any language and any test framework, with full access to',
    ),
    2=>array(
        'id' => '2',
        'date' => '02.02.2016',
        'author' => 'I.Ruslan',
        'title' => 'Хоп хей нананей!',
        'body' => 'Appwdeghfekjhgkjfedhgfkjdhgkjfheg, with full access to',
    ),
    3=>array(
        'id' => '3',
        'date' => '02.03.2017',
        'author' => 'M.Kashkaldakov',
        'title' => 'One day Odin raz.',
        'body' => 'Mobile app from any language and any test framework, with full access to',
    ),
    4=>array(
        'id' => '4',
        'date' => '02.03.2017',
        'author' => 'M.Nursultan',
        'title' => 'One day Odin raz.',
        'body' => 'Mobile app from any language and any test framework, with full access to',
    ),
);

$blogProvider = function ($id) use ($app, $blogPosts) {
    if(!isset($blogPosts[$id])){
        $app->abort(404, "Нет такого ID - вращайте барабан!");
    }
    return $blogPosts[$id];
};

////show all articles
//$app->get('/', function () use($blogPosts){
//   $output = '';
//   foreach ($blogPosts as $post){
//       $output .= '<br />';
//       $output .= '<a href="#">' .$post['title'] .'</a>';
//       $output .= '<br />';
//   }
//   return $output;
//});

//// show one article
//$app->get('/{id}', function (Silex\Application $app, $id) use ($blogPosts){
//    if(!isset($blogPosts[$id])){
//        $app->abort(404, "Post does not exist");
//    }
//    $post = $blogPosts[$id];
//    return "<h1>{$post['title']}</h1>" . "<p>{$post['body']}</p>";
//});

//$app->get('/hello/{name}', function ($name) use($app){
//   return $app['twig']->render('main.twig', array(
//       'name' => $name
//   ));
//});

//$app->get('/', function () use ($app, $blogPosts) {
//    return $app['twig']->render('main.twig', array(
//       'name' => 'Ruslan',
//        'blogs' => $blogPosts
//   ));
//});

//Show all blogs
$app->get('/', function () use ($app, $blogPosts){
   return $app['twig']->render('main.twig', array(
      'blogs' => $blogPosts
   ));
});

//Show one blog
$app->get('/post/{post}', function ($post) use($app){
    return $app['twig']->render('oneblog.twig', array(
        'blogs' => $post
    ));
})
    ->bind('blog_post')
    ->convert('post', $blogProvider)
;

$app->get('/login', function () use ($app){
    return $app['twig']->render('login.twig', array(
    ));
});

//Usage DB
$app->get('/blog/{id}', function ($id) use ($app) {
    $sql = "SELECT * FROM posts WHERE id = ?";
    $post = $app['db']->fetchAssoc($sql, array((int) $id));

    return  "<h1>{$post['title']}</h1>".
        "<p>{$post['text']}</p>";
});

$app->run();