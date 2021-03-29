<?php

require_once 'vendor/autoload.php';

use Mimj\Router;
use App\Controllers\Posts;
use App\Controllers\Home;

$templates_directory = dirname(__FILE__) . DIRECTORY_SEPARATOR . "templates";
$GLOBALS['template_directory'] = $templates_directory;

$router = new Router();

$router->add('/', ['controller' => Home::class, 'action' => 'index']);
$router->add('/posts', ['controller' => Posts::class, 'action' => 'index']);
$router->add('/posts/<id:\d+>/edit', ['controller' => Posts::class, 'action' => 'edit']);
$router->add('/admin/home', ['controller' => \App\Controllers\Admin\Home::class, 'action' => 'index']);

// this will match any route and first parameter will be controller
// and second parameter will be action
$router->add('/{controller}/{action}', ['controller' => 'Post', 'action' => 'new']);

$router->add('/admin/{action}/{controller}', ['controller' => Posts::class, 'action' => 'new']);
$router->add('/admin/{action}/<id:[0-9]{3}>/{controller}', ['controller' => Posts::class, 'action' => 'new']);

$url = $_SERVER['REQUEST_URI'];

$router->dispatch($_SERVER['REQUEST_URI']);