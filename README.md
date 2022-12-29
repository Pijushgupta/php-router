#### Php Router

###### Clone this repository

```sh
git clone https://github.com/Pijushgupta/php-router.git

```

###### Adding it to the project

```php
include_once 'Location of file';
```

###### OR use Composer/auto-loader

```php
composer require pijush_gupta/php-router:dev-main
```

###### Using it in the project

```php
use phpRouter\Router;
```

```php
new Router('/', function () {
	echo 'Home';
});
```

###### Get method with Parameters

```
https://example.com?a=Pijush&b=Gupta
```

```php
new Router('/',function($a,$b){ //exact number of variables and name as query vars from url.
	echo $a . $b;
})
```

###### Post method

```
https://example.com/
```

```php
new Router('/',function($post){ //variable name for the param can be anything
	var_dump($post);
});
```
