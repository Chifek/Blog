<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

// web/index.php
require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();
$blogPosts = array(
    1 => array(
        'date'      => '2011-03-29',
        'author'    => 'Ruslan',
        'title'     => 'Using Silex',
        'body'      => '...',
    ),
    2 => array(
        'date' => '2017-01-19',
        'author' => 'Samat',
        'title' => 'Silex Test',
        'body' => 'It is working!',
    ),
);

$app->get('/blog', function () use ($blogPosts) {
    $output = '';
    foreach ($blogPosts as $post) {
        $output .= $post['title'];
        $output .= '<br />';
        $output .= $post['date'];
    }

    return $output;
});

$app->get('/blog/{id}', function (Silex\Application $app, $id) use ($blogPosts) {
    if (!isset($blogPosts[$id])) {
        $app->abort(404, "Post $id does not exist.");
    }

    $post = $blogPosts[$id];

    return  "<h1>{$post['title']}</h1>".
        "<p>{$post['author']}</p>";
        "<b>{$post['author']}</b>";
});

$app->post('/feedback', function (Request $request) {
    $message = $request->get('message');
    mail('feedback@yoursite.com', '[YourSite] Feedback', $message);

    return new Response('Thank you for your feedback!', 201);
});
$app->run();
