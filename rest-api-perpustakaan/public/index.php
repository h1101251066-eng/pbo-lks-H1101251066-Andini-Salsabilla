<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Config\Env;
use App\Contracts\BookRepositoryInterface;
use App\Controllers\BookController;
use App\Core\Container;
use App\Core\Request;
use App\Core\Router;
use App\Database\Connection;
use App\Repositories\MySQLBookRepository;
use App\Services\BookService;

Env::load(__DIR__ . '/../.env');

// ---------- Container (Dependency Injection) ----------
$container = new Container();

$container->singleton(PDO::class, fn() => Connection::get());

$container->singleton(
    BookRepositoryInterface::class,
    fn($c) => new MySQLBookRepository($c->make(PDO::class))
);

$container->singleton(
    BookService::class,
    fn($c) => new BookService($c->make(BookRepositoryInterface::class))
);

$container->singleton(
    BookController::class,
    fn($c) => new BookController($c->make(BookService::class))
);

// ---------- Routing ----------
$router = new Router();

$router->get('/api/books', fn($req) => $container->make(BookController::class)->index($req));
$router->get('/api/books/{id}', fn($req, $id) => $container->make(BookController::class)->show($req, $id));
$router->post('/api/books', fn($req) => $container->make(BookController::class)->store($req));
$router->put('/api/books/{id}', fn($req, $id) => $container->make(BookController::class)->update($req, $id));
$router->delete('/api/books/{id}', fn($req, $id) => $container->make(BookController::class)->destroy($req, $id));

$router->dispatch(new Request());