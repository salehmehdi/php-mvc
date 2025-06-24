<?php

namespace App\Core\System;

use Dotenv\Dotenv;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
use Jenssegers\Mongodb\Connection as MongoConnection;

class Application
{
    protected string $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = rtrim($basePath, '/');
    }


    public function run(): void
    {
        $this->bootstrap();
        $this->loadRoutes();
        $this->handleRequest();
    }

    protected function bootstrap(): void
    {
        $this->loadEnvironment();
        $this->bootEloquent();
    }

    protected function loadEnvironment(): void
    {
        Dotenv::createImmutable($this->basePath)->load();
    }

   protected function bootEloquent(): void
    {
        $config  = require $this->basePath . '/config/database.php';
        $capsule = new Capsule;

        $capsule->getDatabaseManager()->extend('mongodb', function ($settings) {
            return new MongoConnection($settings);
        });
    
        foreach ($config['connections'] as $name => $settings) 
        {
            $capsule->addConnection($settings, $name);
        }

        $default = $config['default'] ?? 'mysql';
        $capsule->getDatabaseManager()->setDefaultConnection($default);

        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        $capsule->setEventDispatcher(new Dispatcher(new Container));
    }

    protected function loadRoutes(): void
    {
        $routeFile = $this->basePath . '/routes/web.php';
        if (file_exists($routeFile)) {
            require $routeFile;
        }
    }

    protected function handleRequest(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';

        try {
            $result = Route::dispatch($method, $uri);
            echo $result;
        } catch (\Throwable $e) {
            $this->handleException($e);
        }
    }

    protected function handleException(\Throwable $e): void
    {
        http_response_code(strpos($e->getMessage(), 'bulunamadı') !== false ? 404 : 500);
        $msg = getenv('APP_ENV') === 'production' ? 'Bir hata oluştu.' : 'Hata: ' . $e->getMessage();
        echo $msg;
    }
}
