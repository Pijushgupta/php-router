<?php

/** 
 * Author : Pijush Gupta <pijush@live.com>
 * Author URI: https://www.linkedin.com/in/pijush-gupta-php/
 */

namespace phpRouter;

class Router {
    /**
     * @var array Static routes for O(1) lookup
     */
    private static $staticRoutes = [];

    /**
     * @var array Dynamic routes containing regex placeholders
     */
    private static $dynamicRoutes = [];

	/**
	 * $isDispatched - to track if routing has been executed
	 */
	private static $isDispatched = false;

	/**
	 * $notFoundCallback - custom callback for 404 handling
	 */
	private static $notFoundCallback = null;

	/**
	 * Constructor for quick single-route usage (backward compatibility)
	 */
	public function __construct(?string $path = null, ?callable $callback = null) {
		if ($path && $callback) {
			$this->addRoute('GET', $path, $callback);
		}
	}

	/**
	 * Register a GET route
	 */
	public static function get(string $path, callable $callback) {
		self::addRoute('GET', $path, $callback);
	}

	/**
	 * Register a POST route
	 */
	public static function post(string $path, callable $callback) {
		self::addRoute('POST', $path, $callback);
	}

	/**
	 * Register a generic route
	 */
	private static function addRoute(string $method, string $path, callable $callback) {
        $method = strtoupper($method);
        
        if (strpos($path, '{') === false) {
            self::$staticRoutes[$method][$path] = $callback;
        } else {
            self::$dynamicRoutes[$method][] = [
                'path' => $path,
                'callback' => $callback
            ];
        }
	}

	/**
	 * Register a custom 404 handler
	 */
	public static function setNotFound(callable $callback) {
		self::$notFoundCallback = $callback;
	}

	/**
	 * to run methods associated with routes
	 */
	public static function dispatch() {
		if (self::$isDispatched) return;
		self::$isDispatched = true;

		$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
		$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
		$query = [];
		parse_str($_SERVER['QUERY_STRING'] ?? '', $query);

        if (isset(self::$staticRoutes[$method][$uri])) {
            $callback = self::$staticRoutes[$method][$uri];
            if ($method === 'GET') {
                $callback(...array_values($query));
            } else {
                $callback($_POST);
            }
            return;
        }

        if (isset(self::$dynamicRoutes[$method])) {
            $regexes = [];
            foreach (self::$dynamicRoutes[$method] as $index => $route) {
                $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $route['path']);
                $regexes[] = $pattern . '(*MARK:' . $index . ')';
            }

            $combinedRegex = '#^(?:' . implode('|', $regexes) . ')$#';

            if (preg_match($combinedRegex, $uri, $matches)) {
                $routeIndex = $matches['MARK'];
                $route = self::$dynamicRoutes[$method][$routeIndex];
                
                $params = array_filter($matches, function($val, $key) {
                    return is_string($key) && $key !== 'MARK' && $val !== '';
                }, ARRAY_FILTER_USE_BOTH);

                $callback = $route['callback'];

                if ($method === 'GET') {
                    $callback(...array_values($params));
                } else {
                    $callback($params); 
                }
                return;
            }
        }

		// Handle 404
		http_response_code(404);
		if (self::$notFoundCallback) {
			call_user_func(self::$notFoundCallback);
		}
	}

	/**
	 * Keep the destructor for backward compatibility
	 */
	public function __destruct() {
		self::dispatch();
	}
}
