<?php

namespace App\Core\System;

abstract class View 
{
    /**
     * View dosyalarının bulunduğu ana dizin
     */
    private static $viewsPath = 'views/';
    
    /**
     * View render et
     * 
     * @param string $view View dosyasının adı (örn: 'login', 'auth.login', 'member/dashboard')
     * @param array $data View'e gönderilecek data array'i (opsiyonel)
     * @return string Render edilmiş HTML içeriği
     */
    public static function make($view, $data = [])
    {
        try 
        {
            // View dosyasının tam yolunu oluştur
            $viewPath = self::getViewPath($view);
            
            // View dosyasının varlığını kontrol et
            if (!file_exists($viewPath)) {
                throw new \Exception("View file not found: {$view} at {$viewPath}");
            }
            
            // Data array'ini extract et (değişken olarak kullanabilmek için)
            if (!empty($data) && is_array($data)) {
                extract($data, EXTR_SKIP);
            }
            
            // Output buffering başlat
            ob_start();
            
            // View dosyasını include et
            include $viewPath;
            
            // Buffer içeriğini al ve temizle
            $content = ob_get_clean();
            
            return $content;
            
        } catch (\Exception $e) {
            // Hata durumunda buffer'ı temizle
            if (ob_get_level()) {
                ob_end_clean();
            }
            
            // Hata mesajını göster veya log'la
            self::handleError($e, $view, $data);
            
            return '';
        }
    }
    
    /**
     * View dosyasının tam yolunu oluştur
     * 
     * @param string $view
     * @return string
     */
    private static function getViewPath($view)
    {
        // Nokta ile ayrılmış view isimlerini slash'e çevir (auth.login -> auth/login)
        $view = str_replace('.', '/', $view);
        
        // .php uzantısını ekle
        $view = ltrim($view, '/') . '.php';
        
        // Tam yolu oluştur
        return self::getBasePath() . self::$viewsPath . $view;
    }
    
    /**
     * Projenin ana dizinini al
     * 
     * @return string
     */
    private static function getBasePath()
    {
        // Mevcut dosyadan geriye doğru giderek proje kök dizinini bul
        $currentDir = __DIR__;
        
        // Core/System dizininden çıkıp proje köküne git
        while (!file_exists($currentDir . '/composer.json') && dirname($currentDir) !== $currentDir) {
            $currentDir = dirname($currentDir);
        }
        
        return $currentDir . '/';
    }
    

    
    /**
     * JSON response döndür
     * 
     * @param array $data
     * @param int $statusCode
     * @return void
     */
    public static function json($data = [], $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    /**
     * Redirect işlemi
     * 
     * @param string $url
     * @param int $statusCode
     * @return void
     */
    public static function redirect($url, $statusCode = 302)
    {
        http_response_code($statusCode);
        header('Location: ' . $url);
        exit;
    }
    
    /**
     * View dosyasının varlığını kontrol et
     * 
     * @param string $view
     * @return bool
     */
    public static function exists($view)
    {
        $viewPath = self::getViewPath($view);
        return file_exists($viewPath);
    }
    
    /**
     * View'lerin bulunduğu dizini değiştir
     * 
     * @param string $path
     * @return void
     */
    public static function setViewsPath($path)
    {
        self::$viewsPath = rtrim($path, '/') . '/';
    }
    
    /**
     * Mevcut views path'ini al
     * 
     * @return string
     */
    public static function getViewsPath()
    {
        return self::$viewsPath;
    }
    

    
    /**
     * Hata durumunu handle et
     * 
     * @param \Exception $e
     * @param string $view
     * @param array $data
     * @return void
     */
    private static function handleError(\Exception $e, $view, $data)
    {
        // Development ortamında hata detaylarını göster
        if (self::isDebugMode()) {
            echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border: 1px solid #f5c6cb; border-radius: 5px; margin: 10px;'>";
            echo "<h4>View Error:</h4>";
            echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<p><strong>View:</strong> " . htmlspecialchars($view) . "</p>";
            echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . ":" . $e->getLine() . "</p>";
            echo "</div>";
        }
        
        // Hata log'la
        error_log("View Error: " . $e->getMessage() . " | View: {$view} | Data: " . print_r($data, true));
    }
    
    /**
     * Debug mode kontrolü
     * 
     * @return bool
     */
    private static function isDebugMode()
    {
        // .env dosyasından APP_DEBUG değerini kontrol et
        // Varsayılan olarak true döndür (development için)
        return $_ENV['APP_DEBUG'] ?? true;
    }
    

    
    /**
     * View için helper fonksiyonlar
     */
    
    /**
     * Asset URL'i oluştur
     * 
     * @param string $path
     * @return string
     */
    public static function asset($path)
    {
        $baseUrl = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        
        return $protocol . '://' . $baseUrl . '/assets/' . ltrim($path, '/');
    }
    
    /**
     * URL oluştur
     * 
     * @param string $path
     * @return string
     */
    public static function url($path = '')
    {
        $baseUrl = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        
        return $protocol . '://' . $baseUrl . '/' . ltrim($path, '/');
    }
    
    /**
     * HTML escape et
     * 
     * @param string $string
     * @return string
     */
    public static function e($string)
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}