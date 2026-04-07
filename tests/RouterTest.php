<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use phpRouter\Router;

class RouterTest extends TestCase {
    protected function setUp(): void {
        // Clear routes before each test since they are static
        $reflection = new \ReflectionClass(Router::class);
        $staticRoutes = $reflection->getProperty('staticRoutes');
        $dynamicRoutes = $reflection->getProperty('dynamicRoutes');
        $isDispatched = $reflection->getProperty('isDispatched');
        
        $staticRoutes->setAccessible(true);
        $dynamicRoutes->setAccessible(true);
        $isDispatched->setAccessible(true);
        
        $staticRoutes->setValue(null, []);
        $dynamicRoutes->setValue(null, []);
        $isDispatched->setValue(null, false);

        $_SERVER = [];
        $_POST = [];
    }

    public function testStaticGetRoute() {
        $_SERVER['REQUEST_URI'] = '/home';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        
        $matched = false;
        Router::get('/home', function() use (&$matched) {
            $matched = true;
        });

        Router::dispatch();
        $this->assertTrue($matched);
    }

    public function testDynamicGetRoute() {
        $_SERVER['REQUEST_URI'] = '/user/42';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        
        $userId = null;
        Router::get('/user/{id}', function($id) use (&$userId) {
            $userId = $id;
        });

        Router::dispatch();
        $this->assertEquals('42', $userId);
    }

    public function testPostRoute() {
        $_SERVER['REQUEST_URI'] = '/login';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = ['user' => 'pijush'];
        
        $receivedData = null;
        Router::post('/login', function($post) use (&$receivedData) {
            $receivedData = $post;
        });

        Router::dispatch();
        $this->assertEquals(['user' => 'pijush'], $receivedData);
    }

    public function testObjectMethodCallback() {
        $_SERVER['REQUEST_URI'] = '/profile';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        
        $controller = new class {
            public $called = false;
            public function show() { $this->called = true; }
        };

        Router::get('/profile', [$controller, 'show']);
        
        Router::dispatch();
        $this->assertTrue($controller->called);
    }

    public function test404Handler() {
        $_SERVER['REQUEST_URI'] = '/non-existing';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        
        $notFound = false;
        Router::setNotFound(function() use (&$notFound) {
            $notFound = true;
        });

        Router::dispatch();
        $this->assertTrue($notFound);
    }

    public function testMultipleDynamicRoutes() {
        $_SERVER['REQUEST_URI'] = '/post/hello-world';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        
        $slugParam = null;
        Router::get('/user/{id}', function($id) {});
        Router::get('/post/{slug}', function($slug) use (&$slugParam) {
            $slugParam = $slug;
        });

        Router::dispatch();
        $this->assertEquals('hello-world', $slugParam);
    }
}
