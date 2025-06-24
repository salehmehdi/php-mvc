<?php
namespace App\Core\System;

class Application 
{
    protected string $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;
    }

    public function run(): void
    {
        $this->loadRoutes();
        $this->handleRequest();
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
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        try {
            $result = Route::dispatch($method, $uri);
            echo $result;
        } catch (\Exception $e) {
            $this->handleException($e);
        }
    }

    protected function handleException(\Exception $e): void
    {
        if (strpos($e->getMessage(), 'bulunamadı') !== false) {
            http_response_code(404);
        } else {
            http_response_code(500);
        }
        
        echo "Hata: " . $e->getMessage();
    }
}