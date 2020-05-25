# PHP Url
PHP class for handling URLs.

- Parse Url string Customized and create objects
- check relative url
- add or modify any part of the url objects
- add or modify query parameters
- compare any part of url with another url or part of that
- support PSR-7 method`s

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

$url = Url::newUrl('https://www.domain.com/dir1/dir2/file.ext?param1=val1&param2=val2');

$url = Url::newUrl()
	->setScheme('https')
	->setAuthority('www.domain.com')
	->setPath('/dir1/dir2/file.ext')
	->setQuery('param1=val1&param2=val2')
;

```

#### Modify Url Object ####
```php
use \Mtchabok\Url\Url;
$url = Url::newUrl();
// change scheme
$url->setScheme('http'); // return $url object
$url->scheme = 'http';

// change authority = user: sam, pass: 123, host: domain.com
$url->setAuthority('sam:123@domain.com'); // return $url object

// change url path.
$url->setPath('/dir2/dir1/'); // return $url object
$url->path = '/dir2/dir1/';

// add path
$url->addPath('./dir3/file.ext2'); // return $url object
$url->path = $url::normalizePath($url->path, './dir3/file.ext2');

// change query
$url->setQuery('p12=32&name=sara'); // return $url object
$url->query = 'p12=32&name=sara';

// set query param
$url->getQuery()->set('param1', 'test'); // return Url Query object
$url->query->set('param1', 'test'); // return Url Query object
$url->getQuery()->set('param4', ['p4_1'=>'val41', 'p4_2'=>'val42']); // return Url Query object
$url->query['param4'] = ['p4_1'=>'val41', 'p4_2'=>'val42']; // return Url Query object

// change fragment
$url->setFragment('index12'); // return $url object
$url->fragment = 'index12';

```

#### Check exist or set Url Properties ####
```php
use \Mtchabok\Url\Url;
$url = Url::newUrl();
// check scheme set
$url->hasScheme(); // return bool

// check userInfo set
$url->hasUserInfo(); // return bool
$url->hasUser(); // return bool
$url->hasPass(); // return bool

// check host set
$url->hasHost(); // return bool

// check port set
$url->hasPort(); // return bool

// check path set
$url->hasPath(); // return bool

// check query set
$url->hasQuery(); // return bool

// and ...
```

#### Get Url Properties ####
```php
use \Mtchabok\Url\Url;
$url = Url::newUrl();
// scheme
$url->getScheme(); // return current scheme or empty string
$url->scheme; // return current scheme or null
$url->getScheme('file'); // reutrn current scheme or 'file'

// user info
$url->getUserInfo(); // return current userinfo or empty string
$url->getUserInfo('test:123'); // return current userinfo or 'test:123'

// host
$url->getHost();  // return current host or empty string
$url->getHost('host.org');  // return current host or 'host.org'

// path
$url->getPath();  // return current path or empty string
$url->getPath('/dir1/dir2/file');  // return current path or '/dir1/dir2/file'

```

#### PSR-7 and Equals Method`s ####
```php
use \Mtchabok\Url\Url;
$url = Url::newUrl('https://www.domain.com/dir1/dir2/file.ext');

$url2 = $url->withHost('site.com'); // return new Url Object with host 'site.com'

$url->equalsHost($url2->getHost()); // return false
$url->equalsHost($url2); // return false

```

#### For More Usage Documentation, Use This Url Class By IDE ####
