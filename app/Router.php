<?php
// Maps a method and path to a controller. {id} is digits. Other params are one segment.

class Router
{
    private $routes = [];

    public function get($pattern, $handler)
    {
        $this->add('GET', $pattern, $handler);
    }

    public function post($pattern, $handler)
    {
        $this->add('POST', $pattern, $handler);
    }

    public function dispatch($method, $path)
    {
        if ($method === 'HEAD') {
            $method = 'GET';
        }
        foreach ($this->routes as $route) {
            if ($route[0] !== $method) {
                continue;
            }
            $regex = self::compile($route[1]);
            if (!preg_match($regex, $path, $matches)) {
                continue;
            }
            $params = [];
            foreach ($matches as $key => $value) {
                if (!is_int($key)) {
                    $params[$key] = $value;
                }
            }
            call_user_func($route[2], $params);
            return;
        }
        SiteController::notFound();
    }

    private function add($method, $pattern, $handler)
    {
        $this->routes[] = [$method, $pattern, $handler];
    }

    private static function compile($pattern)
    {
        $regex = preg_replace_callback('/\{([a-z_]+)\}/', function ($match) {
            if ($match[1] === 'id') {
                return '(?P<id>[0-9]+)';
            }
            return '(?P<' . $match[1] . '>[^/]+)';
        }, $pattern);
        return '#^' . $regex . '$#';
    }
}
