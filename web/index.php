<?php
require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));

$blogPosts = array(
    1=>array(
        'date' => '02.02.2017',
        'author' => 'J.Samat',
        'title' => 'One more time.',
        'body' => 'Appium aims to automate any mobile app from any language and any test framework, with full access to',
    ),
    2=>array(
        'date' => '02.02.2016',
        'author' => 'I.Ruslan',
        'title' => 'Хоп хей нананей!',
        'body' => 'Appwdeghfekjhgkjfedhgfkjdhgkjfheg, with full access to',
    ),
    3=>array(
        'date' => '02.03.2017',
        'author' => 'M.Nursultan',
        'title' => 'One day Odin raz.',
        'body' => 'Mobile app from any language and any test framework, with full access to',
    ),
);

//show all articles
$app->get('/', function () use($blogPosts){
   $output = '';
   foreach ($blogPosts as $post){
       $output .= '<br />';
       $output .= '<a href="#">' .$post['title'] .'</a>';
       $output .= '<br />';
   }
   return $output;
});

// show one article
$app->get('/{id}', function (Silex\Application $app, $id) use ($blogPosts){
    if(!isset($blogPosts[$id])){
        $app->abort(404, "Post does not exist");
    }
    $post = $blogPosts[$id];
    return "<h1>{$post['title']}</h1>" . "<p>{$post['body']}</p>";
});

//$app->get('/hello/{name}', function ($name) use($app){
//   return $app['twig']->render('hello.twig', array(
//       'name' => $name
//   ));
//});
$app->run();