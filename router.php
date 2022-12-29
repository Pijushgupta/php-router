<?php

namespace phpRouter;

class Router {
	/**
	 * $router - to store the routes and callback
	 */
	private $routes = [];

	/**
	 * $serverUri - to store the current url
	 */
	private $serverUri;



	/**
	 * to add routes 
	 */
	public function __construct(string $path, callable $callback) {

		if ($path && $callback) {
			$this->routes[$path] = $callback;
		}
	}

	/**
	 * to run methods associated with routes
	 */
	private function run() {
		$this->serverUri = $_SERVER['REQUEST_URI'];



		foreach ($this->routes as $path => $callback) {
			if ($this->serverUri === $path) $callback();
		}
	}



	/**
	 * run the run method after object creation in done, so user don't have to call run method manually 
	 */
	public function __destruct() {
		if (empty($this->routes) || $this->routes == NULL) return;
		$this->run();
	}
}
