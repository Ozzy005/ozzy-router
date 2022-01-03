<?php

/**
 *
 * @author Rafael Arend
 *
 **/

namespace OzzyRouter;

use Http\Request;
use Exception;

class Router
{
    private static array $enabledVerbs = ['POST', 'GET', 'PUT', 'PATCH', 'DELETE'];
    private static array $routes = [];
    private static array $action = [];
    private static string $middleware;
    private static Request $request;

    public function __construct(Request $request)
    {
        self::$request = $request;
    }

    private static function addRoute(string $verb, array $args): void
    {
        $route = $args[0];
        $action = $args[1];

        $addRoute = function ($route) use ($verb, $action) {
            self::$routes[$verb][$route] = [
                'controller' => $action[0],
                'method' => $action[1],
                'middleware' => self::$middleware
            ];
        };

        $addRoute($route);

        if ($route[-1] !== '/') {
            $addRoute($route . '/');
        }
    }

    private static function get(array $args): void
    {
        self::addRoute('GET', $args);
    }

    private static function post(array $args): void
    {
        self::addRoute('POST', $args);
    }

    private static function put(array $args): void
    {
        self::addRoute('PUT', $args);
    }

    private static function patch(array $args): void
    {
        self::addRoute('PATCH', $args);
    }

    private static function delete(array $args): void
    {
        self::addRoute('DELETE', $args);
    }

    private static function parseRoutes(): void
    {
        $verb = strtoupper(self::$request->requestMethod);
        $rewriteUri = self::$request->rewriteUri;

        foreach (self::$routes[$verb] as $key => $value) {
            if ($rewriteUri === $key) {
                if ($value['middleware']) {
                    $value['middleware']::handle();
                }

                self::$action = $value;
                break;
            }
        }

        if (!self::$action) {
            throw new Exception("$verb route $rewriteUri not exist");
        }
    }

    public static function dispatch(): void
    {
        self::parseRoutes();
        $controller = new self::$action['controller'];
        $controller->{self::$action['method']}(self::$request);
    }

    public static function __callStatic(string $name, array $args): void
    {
        $method = strtoupper($name);

        if (!in_array($method, self::$enabledVerbs, true)) {
            throw new Exception("Unsupported $method Verb");
        }

        self::$method($args);
    }

    public static function middleware(string $middleware, callable $callable): void
    {
        self::$middleware = $middleware;
        call_user_func($callable);
        self::$middleware = '';
    }
}
