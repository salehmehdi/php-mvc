<?php
namespace App\Core\System;

abstract class Route 
{
    protected static array $routes = [];

    public static function get(string $uri, array $options): void 
    {
        self::addRoute('GET', $uri, $options);
    }

    public static function post(string $uri, array $options): void 
    {
        self::addRoute('POST', $uri, $options);
    }

    protected static function addRoute(string $method, string $uri, array $options): void 
    {
        $pattern = self::convertUriToRegex($uri);
        self::$routes[$method][] = [
            'pattern' => $pattern,
            'options' => $options,
            'original_uri' => $uri // Debug için
        ];
    }

    protected static function convertUriToRegex(string $uri): string 
    {
        if ($uri === '/') {
            return '#^/?$#';
        }
        
        $uri = trim($uri, '/'); 
        $regex = preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $uri);
        return '#^' . str_replace('/', '\/', $regex) . '/?$#';
    }

   
    public static function dispatch(string $method, string $uri) 
    {
        if (!isset(self::$routes[$method])) {
            return self::notFound($uri);
        }

        $normalizedUri = trim($uri, '/');
        
        foreach (self::$routes[$method] as $route) 
        {
            if ($route['original_uri'] === '/' && ($uri === '/' || $uri === '')) {
                return self::executeController($route['options'], []);
            }
            
            if (preg_match($route['pattern'], $normalizedUri, $matches)) 
            {
                $params = array_filter(
                    $matches, 
                    fn($k) => !is_int($k), 
                    ARRAY_FILTER_USE_KEY
                );
                
                return self::executeController($route['options'], $params);
            }
        }

        return self::notFound($uri);
    }

    protected static function executeController(array $options, array $params)
    {
        if (empty($options['uses'])) {
            throw new \Exception("Route için 'uses' tanımı bulunmuyor.");
        }

        [$controllerPath, $action] = explode('@', $options['uses'], 2);
        $fqcn = 'App\Http\Controllers\\' . $controllerPath;

        if (!class_exists($fqcn)) {
            throw new \Exception("Controller sınıfı bulunamadı: {$fqcn}");
        }

        $controller = new $fqcn();

        if (!method_exists($controller, $action)) {
            throw new \Exception("Metot bulunamadı: {$fqcn}::{$action}()");
        }

        return call_user_func_array([$controller, $action], $params);
    }

    protected static function notFound(string $uri) 
    {
        header("HTTP/1.0 404 Not Found");
        throw new \Exception("Rota bulunamadı: {$uri}");
    }
}