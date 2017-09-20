<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

Request::enableHttpMethodParameterOverride();
use Dflydev\Provider\DoctrineOrm\DoctrineOrmServiceProvider;


$app = new Silex\Application();
$app['debug'] = true;
$app->register(new Silex\Provider\SessionServiceProvider());
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
                'path' => __DIR__ . '/src/Foo/Entities',
            ),
            array(
                'type' => 'xml',
                'namespace' => 'Bat\Entities',
                'path' => __DIR__ . '/src/Bat/Resources/mappings',
            ),
        ),
    ),
));

//ADD NEW POST
$app->post('/post', function (Request $request) use ($app) {
    $session = $request->getSession();
    $userName = $session->get('user'){'username'};

    if ($userName === null) {
        return new RedirectResponse('/login');
    }
    $post = $request->request->get('post');

    $post['date'] = date('Y-m-d');
    $post['author'] = $userName;
    $app['db']->insert('posts', $post);
    $postId = $app['db']->lastInsertId();

    return $app->redirect('/post/' . $postId);
})
    ->bind('add_new_post');

//DELETE POST
$app->delete('/post/{post}', function (Request $request, $post) use ($app) {
    $session = $request->getSession();
    $userName = $session->get('user'){'username'};

    if ($userName === null) {
        return new RedirectResponse('/login');
    }
    $app['db']->delete('posts', ['id' => $post]);

    return $app->redirect('/');
})
    ->bind('delete_post');

// ROUTE - 'ADD NEW POST'delete
$app->get('/add-new', function (Request $request) use ($app) {
    $session = $request->getSession();
    $userName = $session->get('user'){'username'};

    if ($userName === null) {
        return new RedirectResponse('/login');
    }
    return $app['twig']->render('add_new_post.twig', array());
})
    ->bind('new_post');

//SHOW ALL POST FROM DB
$app->get('/', function (Request $request) use ($app) {
    $session = $request->getSession();
    $userName = $session->get('user'){'username'};

    if ($userName === null) {
        return new RedirectResponse('/login');
    }
    $sql = "SELECT * FROM posts";
    $posts = $app['db']->fetchAll($sql);
    $countArticle = count($posts);
    return $app['twig']->render('main.twig', array(
        'blogs' => $posts,
        'users' => $userName,
        'count' => $countArticle
    ));
})
    ->bind('blog_posts');

//SHOW ONE POST FROM DB
$app->get('/post/{post}', function (Request $request, $post) use ($app) {
    $session = $request->getSession();
    $userName = $session->get('user'){'username'};

    if ($userName === null) {
        return new RedirectResponse('/login');
    }
    $sql = "SELECT * FROM posts WHERE id = ?";
    $post = $app['db']->fetchAssoc($sql, array((int)$post));

    return $app['twig']->render('oneblog.twig', array(
        'blog' => $post,
        'users' => $userName
    ));
})
    ->bind('blog_post');

//AUTHENTICATION
$app->post('/auth', function (Request $request) use ($app) {
    $session = $request->getSession();

    $dataRequest = $request->request->get('user');

    $username = $dataRequest['username'];
    $password = $dataRequest['password'];

    $sql = "SELECT * FROM users WHERE username = '$username'";
    $user = $app['db']->fetchAssoc($sql);

    if ($user['username'] === $username && $user['password'] === $password) {
        $session->set('user', array('username' => $username));
        return new RedirectResponse('/');
    }

    $response = new Response();
    $response->setStatusCode(401, 'Please sign in.');

    return $app->redirect('/login');
})
    ->bind('auth');

//LOGOUT
$app->get('/logout', function () use ($app) {
    $app['session']->set('user', array('' => $username = null));
    return $app->redirect('/login');
})
    ->bind('session_end');

//LOGIN FORM
$app->get('/login', function () use ($app) {
    return $app['twig']->render('login.twig', array());
});

//USER PROFILE PAGE
$app->get('/profile', function (Request $request) use ($app) {
    $session = $request->getSession();
    $userName = $session->get('user'){'username'};

    if ($userName === null) {
        return new RedirectResponse('/login');
    }
    $sql = "SELECT * FROM users WHERE username = ?";
    $user = $app['db']->fetchAssoc($sql, array($userName));

    return $app['twig']->render('profile.twig', array(
        'user' => $user,
        'users' => $userName
    ));
})
    ->bind('user_profile');

//CHANGE PASSWORD
$app->post('/change', function (Request $request) use ($app) {
    $session = $request->getSession();
    $userName = $session->get('user'){'username'};

    if ($userName === null) {
        return new RedirectResponse('/login');
    }
    $changePassword = $request->request->get('change');

    $newPass = $changePassword['newPass'];
    $oldPass = $changePassword['oldPass'];

    $sql = "SELECT * FROM users WHERE username = ?";
    $user = $app['db']->fetchAssoc($sql, array($userName));
    $oldPassFromDB = $user['password'];
    $t['password'] = $newPass;
    $r['username'] = $userName;
    if ($oldPass === $oldPassFromDB) {
        $app['db']->update('users', $t, $r);
    }
    return $app->redirect('/profile');
})
    ->bind('change_password');

$app->run();