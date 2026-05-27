<?php

namespace App\Core;

class View
{
    private static ?string $layout = null;
    private static array $sections = [];
    private static ?string $currentSection = null;

    public static function render(string $view, array $data = []): string
    {
        self::$layout = null;
        self::$sections = [];

        extract($data);

        $viewPath = __DIR__ . '/../Views/' . str_replace('.', '/', $view) . '.php';

        if (!file_exists($viewPath)) {
            http_response_code(404);
            return '<h1>View Not Found</h1><p>The view does not exist: ' . htmlspecialchars($view) . '</p>';
        }

        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        if (self::$layout) {
            $layoutPath = __DIR__ . '/../Views/layouts/' . self::$layout . '.php';
            if (!file_exists($layoutPath)) {
                throw new \Exception("Layout not found: " . self::$layout);
            }
            extract($data);
            ob_start();
            require $layoutPath;
            return ob_get_clean();
        }

        return $content;
    }

    public static function layout(string $name): void
    {
        self::$layout = $name;
    }

    public static function section(string $name): void
    {
        self::$currentSection = $name;
        ob_start();
    }

    public static function endSection(): void
    {
        if (self::$currentSection) {
            self::$sections[self::$currentSection] = ob_get_clean();
            self::$currentSection = null;
        }
    }

    public static function yield(string $name): string
    {
        return self::$sections[$name] ?? '';
    }
}
