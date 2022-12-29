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
	 *  $queryVar - to store query vars for get Request
	 */
	private $queryVar = null;


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

		$passedUrl = parse_url($_SERVER['REQUEST_URI']);
		$methodType = $_SERVER['REQUEST_METHOD'];
		$this->serverUri = $passedUrl['path'];

		if (array_key_exists('query', $passedUrl)) {
			parse_str($passedUrl['query'], $this->queryVar);
		}

		foreach ($this->routes as $path => $callback) {
			if ($this->serverUri === $path) {
				/**
				 * checking the number of param callback function can receive
				 */
				$reflection = new \ReflectionFunction($callback);
				$numParams = $reflection->getNumberOfParameters();

				/**
				 * checking if the number of param callback function can receive 
				 * against the query var provided in URL 
				 * It is to prevent malicious  intention 
				 */


				if ($methodType == 'GET' && $this->queryVar != null && $numParams === count($this->queryVar)) {
					/**
					 * sending query var to the callable function 
					 */
					$callback(...$this->queryVar);
				} elseif ($methodType == 'POST' && $numParams > 0) {
					/**
					 * sending post value(s) to the callable function
					 */
					$callback($_POST);
				} else {
					/**
					 * calling the callback function 
					 */
					$callback();
				}
			}
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
