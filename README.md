# PHP Url
PHP class for handling URLs.

- Parse Url string Customized and create objects
- check relative or absolute url
- Ability to avoid changing url object by readOnly mode 
- add or modify any part of the url objects
- add or modify query parameters

Installation
------------

This package is listed on [Packagist](https://packagist.org/packages/mtchabok/url).

```
composer require mtchabok/url
```

How To Usage
------------

#### Create Url Object ####
```php
use \Mtchabok\Url\Url;

$url = new Url('https://www.domain.com/dir1/dir2/file.ext?param1=val1&param2=val2');
// or use the static function `parse`:
$url = Url::parse('https://www.domain.com/dir1/dir2/file.ext?param1=val1&param2=val2');

// change host
$url->setHost('www.domain2.org');

// return the URL as string
echo $url->toString();
// or
echo $url;
```

#### Modify Url Object ####
```php
// change url path
$url->setPath('/dir2/dir1/');

// add path
$url->addPath('./dir3/file.ext2');

// change scheme
$url->setScheme('http');

// set query param
$url->setQueryParam('param1', 'test');
$url->setQueryParam('param4', ['p4_1'=>'val41', 'p4_2'=>'val42']);

// return path string
// if path not set on object ,return $default argument
$url->getPath('/notfound')
```

#### For More Usage Documentation, Use This Url Class By IDE ####
