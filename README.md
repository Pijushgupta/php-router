# PHP Router

A lightweight, high-performance PHP routing engine with support for static routes, dynamic parameters, and multiple HTTP verbs. Written with modern PHP practices and elite performance in mind using Grouped Regex and Hash Map matching.

## Features

- **Blazing Fast**: `O(1)` Hash Map lookups for static routes and Grouped Regex for dynamic routes.
- **Dynamic Parameters**: Support for segments like `/user/{id}`.
- **HTTP Verbs**: Explicit `GET` and `POST` methods.
- **PSR-4 Compliant**: Ready for Composer autoloading out of the box.
- **Custom 404 Handling**: Easily define your own "Not Found" pages.

## Installation

You can install the router via Composer:

```bash
composer require pijush_gupta/php-router
```

If you are not using Composer, simply require the file:

```php
require_once 'src/Router.php';
```

## User Guide

### 1. Basic Routing

Define basic routes using static paths.

```php
use phpRouter\Router;

Router::get('/', function() {
    echo 'Welcome Home!';
});

Router::post('/submit', function($postData) {
    print_r($postData);
});
```

### 2. Dynamic Routing

Use curly braces `{}` to define dynamic segments in your URLs. Let the router extract these and pass them directly to your callback.

```php
Router::get('/user/{id}', function($id) {
    echo "Viewing profile for user ID: " . $id;
});

Router::get('/post/{category}/{slug}', function($category, $slug) {
    echo "Category: $category, Slug: $slug";
});
```

### 3. Controller Callbacks

You don't have to use closures. You can use standard PHP callbacks, like an array containing an object and a method name.

```php
class UserController {
    public function showProfile($id) {
        echo "Profile of $id";
    }
}

$controller = new UserController();
Router::get('/profile/{id}', [$controller, 'showProfile']);
```

### 4. Handling Query Parameters

For `GET` requests, query parameters are automatically gathered and passed to your closure if defined.

```php
// URL: /search?query=php&sort=asc
Router::get('/search', function($query, $sort) {
    echo "Searching for $query ordered by $sort.";
});
```

### 5. Custom 404 Pages

Set a custom handler that triggers if no route matches the requested URI.

```php
Router::setNotFound(function() {
    http_response_code(404);
    echo "<h1>404 - Not Found</h1>";
    echo "<p>The page you are looking for does not exist.</p>";
});
```

### 6. Dispatching the Router

By default, the router will automatically dispatch routes when the script terminates via its destructor. However, you can explicitly dispatch it anywhere in your code lifecycle:

```php
Router::dispatch();
```

## License

This project is open-sourced software licensed under the [MIT license](LICENSE).
