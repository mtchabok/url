<?php
namespace Mtchabok\Url;
use Mtchabok\Url\Extra\UrlQuery;

/**
 * Class Url
 * @package Mtchabok\Url
 *
 * @method bool hasScheme()
 * @method bool hasUser()
 * @method bool hasPass()
 * @method bool hasHost()
 * @method bool hasPath()
 * @method bool hasFragment()
 *
 * @method string getScheme(string $default=null)
 * @method string getUser(string $default=null)
 * @method string getPass(string $default=null)
 * @method string getHost(string $default=null)
 * @method string getPath(string $default=null)
 * @method string getFragment(string $default=null)
 *
 * @method bool equalsScheme(string|string[]|Url|UrlModel $scheme)
 * @method bool equalsUser(string|string[]|Url|UrlModel $user)
 * @method bool equalsPass(string|string[]|Url|UrlModel $pass)
 * @method bool equalsUserInfo(string|string[]|Url|UrlModel $userInfo)
 * @method bool equalsHost(string|string[]|Url|UrlModel $host)
 * @method bool equalsAuthority(string|string[]|Url|UrlModel $authority)
 * @method bool equalsPath(string|string[]|Url|UrlModel $path)
 * @method bool equalsFragment(string|string[]|Url|UrlModel $fragment)
 *
 * @method Url setScheme(string|Url|UrlModel $scheme)
 * @method Url setUser(string|Url|UrlModel $user)
 * @method Url setPass(string|Url|UrlModel $pass)
 * @method Url setHost(string|Url|UrlModel $host)
 * @method Url setFragment(string|Url|UrlModel $fragment)
 *
 * @method Url withScheme(string|Url|UrlModel $scheme)
 * @method Url withUser(string|Url|UrlModel $user)
 * @method Url withPass(string|Url|UrlModel $pass)
 * @method Url withUserInfo(string|Url|UrlModel $userInfo)
 * @method Url withHost(string|Url|UrlModel $host)
 * @method Url withPort(int|Url|UrlModel $port)
 * @method Url withAuthority(string|Url|UrlModel $authority)
 * @method Url withPath(string|Url|UrlModel $path)
 * @method Url withFragment(string|Url|UrlModel $fragment)
 *
 */
class Url extends UrlModel
{
	const STRUCTURE_REGEX = <<<REGEXP
#^(?:(?P<scheme>[^\:\/]*)\:)?(?:\/\/(?:(?P<user>[^\@\:]*)(?:\:(?P<pass>[^\@]*))?\@)?(?P<host>[^\/\:\?\&\#]*)(?:\:(?P<port>[^\/\?\&\#]*))?)?(?P<path>[^\?\&\#]*)?(?:[\?|\&](?P<query>[^\#]*))?(?:\#(?P<fragment>.*))?$#u
REGEXP;






	/**
	 * @param string[] $path [optional]
	 * @return string
	 */
	public static function normalizePath(string ...$path)
	{
		$pathNormalized = '';
		foreach ($path as $p){
			if(!strlen($p)) continue;
			if(strlen($pathNormalized)){
				$hasSlashEndPath = '/'==substr($pathNormalized, -1);
				if($hasSlashEndPath && '/'==substr($p, 0, 1))
					$pathNormalized.= substr($p,1);
				elseif($hasSlashEndPath || '/'==substr($p, 0, 1))
					$pathNormalized.= $p;
				else
					$pathNormalized.= "/{$p}";
			}else $pathNormalized = $p;
		}
		$path = strlen($pathNormalized) ?explode('/', $pathNormalized) :[];
		$pathNormalized = [];
		foreach ($path as $segment){
			if($segment!='.'){
				if($segment=='..') array_pop($pathNormalized);
				else $pathNormalized[] = $segment;
			}
		}
		return implode('/', $pathNormalized);
	}

	/**
	 * @param string|array|object|Url|UrlModel $url
	 * @param array $onlyItems [optional] [scheme, authority, user, pass, host, port, path, query, fragment]
	 * @return string[]
	 */
	public static function parse($url, array $onlyItems = null) :array
	{
		if(!$onlyItems) $onlyItems = ['scheme', 'authority', 'path', 'query', 'fragment'];
		$parsed = [];
		if(is_string($url)){
			if(!preg_match(static::STRUCTURE_REGEX, $url, $url, PREG_UNMATCHED_AS_NULL))
				$url = [];
		}elseif (is_object($url)){
			$url = get_object_vars((object) $url);
		}
		if (is_array($url)){
			if(false!== $pos=array_search('authority', $onlyItems)){
				unset($onlyItems[$pos]);
				$onlyItems+= ['user','pass','host','port'];
			}
			$onlyItems = array_unique($onlyItems);
			foreach (['scheme','user','pass','host','port','path','query','fragment'] as $n){
				if(array_key_exists($n, $url) && in_array($n, $onlyItems))
					$parsed[$n] = (string) $url[$n];
			}
		}
		return $parsed;
	}

	/**
	 * @param string|array|object|Url|UrlModel $url
	 * @param array $onlyItems [optional] [scheme, authority, user, pass, host, port, path, query, fragment]
	 * @return string
	 */
	public static function build($url, array $onlyItems = null) :string
	{ return ($url instanceof Url ?$url :static::newUrl($url))->toString($onlyItems); }

	/**
	 * @param string|array|Url|UrlModel $url1
	 * @param string|array|Url|UrlModel $url2
	 * @param array $onlyItems [optional] [scheme, authority, user, pass, host, port, path, query, fragment]
	 * @return bool
	 */
	public static function equals($url1, $url2, array $onlyItems = null) :bool
	{ return ($url1 instanceof Url ?$url1 :static::newUrl($url1))->equalsWith($url2, $onlyItems); }





	/**
	 * @param string|array|Url $url [optional]
	 * @return Url
	 */
	public static function newUrl($url = null)
	{ return new static($url); }

	/**
	 * @param string|array|object|UrlModel $url [optional]
	 * @return UrlModel
	 */
	public static function newUrlModel($url = null) :UrlModel
	{ return new UrlModel($url); }

	/** @return Url */
	public static function newCurrentUrl()
	{
		$url = !empty($_SERVER['REQUEST_SCHEME']) ?"{$_SERVER['REQUEST_SCHEME']}:" :('http'.(!empty($_SERVER['HTTPS']) ?'s:' :':'));
		$url.= "//{$_SERVER['HTTP_HOST']}";
		$url.= substr($_SERVER['REQUEST_URI'], 0, 1)=='/'
			?$_SERVER['REQUEST_URI'] :"/{$_SERVER['REQUEST_URI']}";
		return static::newUrl($url);
	}












	/** @return bool */
	public function isRelative() :bool
	{ return !$this->hasScheme() || !$this->hasHost(); }

	/** @return bool */
	public function isRelativePath() :bool
	{ return !$this->hasHost() && $this->hasPath() && substr($this->getPath(),0,1)!='/'; }

	/** @return bool */
	public function isEmpty() :bool
	{
		return !$this->hasScheme() && !$this->hasAuthority() && !$this->hasPath() && !$this->hasQuery() && !$this->hasFragment();
	}

	/**
	 * @param string|array|UrlModel $url
	 * @param array $itemsOnly [optional] [scheme, authority, user, pass, host, port, path, query, fragment]
	 * @return bool
	 */
	public function equalsWith($url, array $itemsOnly = null) :bool
	{
		if(!$itemsOnly) $itemsOnly = ['scheme', 'authority', 'path', 'query', 'fragment'];
		if(!$url instanceof Url && $url instanceof UrlModel) $url = static::newUrlModel($url);
		$return = false;
		foreach ($itemsOnly as $name){
			$return = true;
			$m = 'equals'.ucfirst($name);
			if(!$this->$m($url)) return false;
		} return $return;
	}

	/**
	 * @param string|array|UrlModel $url
	 * @param array $itemsOnly [optional] [scheme, authority, user, pass, host, port, path, query, fragment]
	 * @return Url
	 */
	public function with($url, array $itemsOnly = null)
	{
		if(!$itemsOnly) $itemsOnly = ['scheme','authority','path','query','fragment'];
		$urlObj = clone $this;
		if($url instanceof Url){
			foreach ($itemsOnly as $item){
				if($url->{'has'.ucfirst($item)}())
					$urlObj->{'set'.ucfirst($item)}($url->{'get'.ucfirst($item)}());
			}
		}else{
			if (!$url instanceof UrlModel) $url = static::newUrlModel($url);
			if(array_key_exists('authority', ($itemsOnly = array_flip($itemsOnly)))){
				unset($itemsOnly['authority']);
				$itemsOnly+= ['user'=>'','pass'=>'','host'=>'','port'=>''];
			} $itemsOnly = array_keys($itemsOnly);
			foreach ($itemsOnly as $item){
				if(isset($url->{$item}))
					$urlObj->{'set'.ucfirst($item)}($url->{$item});
			}
		}
		return $urlObj;
	}


	/**
	 * @param string|array|UrlModel $url
	 * @param array $itemsOnly [optional] [scheme, authority, user, pass, host, port, path, query, fragment]
	 * @return Url
	 */
	public function without($url, array $itemsOnly = null)
	{
		if(!$itemsOnly) $itemsOnly = ['scheme','authority','path','query','fragment'];
		if(array_key_exists('authority', ($itemsOnly = array_flip($itemsOnly)))){
			unset($itemsOnly['authority']);
			$itemsOnly+= ['user'=>'','pass'=>'','host'=>'','port'=>''];
		} $itemsOnly = array_keys($itemsOnly);
		$urlObj = clone $this;
		if($url instanceof Url) $url = static::newUrlModel($url);
		elseif (!$url instanceof UrlModel) $url = static::newUrlModel($url);
		foreach (['scheme','user'=>'','pass'=>'','host'=>'','port'=>'','fragment'] as $item){
			$Item = ucfirst($item);
			if(in_array($item,$itemsOnly) && !empty($url->{$item}) && $urlObj->{"has{$Item}"}()){
				$urlObj->{"set{$Item}"}(null);
			}
		}
		if(in_array('path', $itemsOnly) && !empty($url->path) && $urlObj->hasPath()){
			$p1 = explode('/',$url->path);
			$p2 = explode('/',$urlObj->getPath());
			if(!$p2[0] && $p1[0]) array_unshift($p1,'');
			elseif ($p2[0] && !$p1[0]) array_shift($p1);
			if($p1==array_slice($p2, 0, count($p1))) {
				$p2 = array_slice($p2, count($p1));
				if(false!==$p2) $urlObj->setPath(implode('/', $p2));
			}
		}
		if(in_array('query', $itemsOnly) && !$url->query->isEmpty() && $urlObj->hasQuery()){
			foreach ($url->query->export() as $k=>&$v){
				if($urlObj->getQuery()->has($k)) $urlObj->getQuery()->set($k, null);
			}
		}
		return $urlObj;
	}





	/** @return bool */
	public function hasParent() :bool
	{ return isset($this->parent) && $this->parent instanceof UrlModel; }

	/**
	 * @param string|array|UrlModel|Url $default
	 * @return Url|null
	 */
	public function getParent($default = null)
	{
		if($this->hasParent()){
			return static::newUrl($this->parent);
		}elseif ($default instanceof Url)
			return $default;
		elseif ($default instanceof UrlModel || !is_null($default))
			return static::newUrl($default);
		return null;
	}

	/**
	 * @param string|array|UrlModel|Url $parent
	 * @return bool
	 */
	public function equalsParent($parent) :bool
	{
		if($this->hasParent())
			return $this->getParent()->equalsWith($parent);
		elseif ($parent instanceof Url)
			return $parent->equalsWith(null);
		elseif (!is_null($parent))
			return static::newUrl($parent)->equalsWith(null);
		return true;
	}

	/**
	 * @param string|array|UrlModel|Url $parent
	 * @return $this
	 */
	public function setParent($parent)
	{
		$P = null;
		if($parent instanceof Url){
			$P = $parent;
			$this->parent = static::newUrlModel($parent);
		}elseif ($parent instanceof UrlModel)
			$this->parent = $parent;
		elseif (is_string($parent) || is_array($parent))
			$this->parent = static::newUrlModel($parent);
		else
			$this->parent = null;
		if($this->hasParent() && !$this->isEmpty()){
			if(!$P) $P = static::newUrl($this->parent);
			if($P->hasScheme() && $this->hasScheme()){
				$this->setScheme(null);
			}
			if($P->hasAuthority() && $this->hasAuthority()){
				if($P->hasUserInfo() && $this->hasUserInfo())
					$this->setUserInfo(null);
				if($P->hasHost() && $this->hasHost()){
					$this->setHost(null);
				}
			}
			if($P->hasPath() && $this->hasPath()){
				$p1 = explode('/',$P->getPath());
				$p2 = explode('/',$this->getPath());
				if(!$p2[0] && $p1[0]) array_unshift($p1,'');
				elseif ($p2[0] && !$p1[0]) array_shift($p1);
				if($p1==array_slice($p2, 0, count($p1))) {
					$p2 = array_slice($p2, count($p1));
					if(false!==$p2) $this->setPath(implode('/', $p2));
				}
			}
			if($P->hasQuery() && $this->hasQuery()){
				foreach ($P->getQuery()->export() as $k=>&$v){
					if($this->getQuery()->has($k)) $this->getQuery()->set($k, null);
				}
			}
			if($P->hasFragment() && $this->hasFragment()){
				$this->setFragment(null);
			}
		}
		return $this;
	}

	/**
	 * @param string|array|UrlModel|Url $parent
	 * @return Url
	 */
	public function withParent($parent)
	{
		$url = clone $this;
		$url->setParent($parent);
		return $url;
	}








	/** @return bool */
	public function hasUserInfo() :bool
	{ return $this->hasUser(); }

	/**
	 * @param string $default [optional]
	 * @return string
	 */
	public function getUserInfo(string $default = null) :string
	{
		$userInfo = '';
		if($this->hasUser())
			$userInfo.= $this->getUser() . ($this->hasPass() ?":{$this->getPass()}" :'');
		return (string) (empty($userInfo) ?$default :$userInfo);
	}

	/**
	 * @param string $userInfo
	 * @return $this
	 */
	public function setUserInfo(string $userInfo)
	{
		$result = [];
		preg_match('#^(?P<user>[^\:]*)(?:\:(?P<pass>.*))?$#', $userInfo, $result);
		foreach (['user', 'pass'] as $name) $this->{'set'.ucfirst($name)}(isset($result[$name]) ?$result[$name] :null);
		return $this;
	}









	/** @return bool */
	public function hasAuthority() :bool
	{ return $this->hasHost(); }

	/**
	 * @param string $default [optional]
	 * @return string
	 */
	public function getAuthority(string $default = null) :string
	{
		$authority = '';
		if($this->hasHost()){
			if($this->hasUserInfo())
				$authority.= "{$this->getUserInfo()}@";
			$authority.=$this->getHost();
			if($this->hasPort())
				$authority.= ":{$this->getPort()}";
		}
		return (string) (empty($authority) ?$default :$authority);
	}

	/**
	 * @param string $authority
	 * @return $this
	 */
	public function setAuthority(string $authority)
	{
		$result = [];
		preg_match('!'.'^(?:(?P<user>[^\@\:]*)(?:\:(?P<pass>[^\@]*))?\@)?(?P<host>[^\:\/]*)?(?:\:(?P<port>[^\/]*))?$'.'!u', (string) $authority, $result, PREG_UNMATCHED_AS_NULL);
		foreach (['user', 'pass', 'host', 'port'] as $name)
			$this->{'set'.ucfirst($name)}(isset($result[$name]) ?$result[$name] :null);
		return $this;
	}













	/** @return bool */
	public function hasPort() :bool
	{ return isset($this->port) && is_int($this->port) && $this->port; }

	/**
	 * @param int $default [optional]
	 * @return int
	 */
	public function getPort(int $default = null) :int
	{ return $this->hasPort() ?$this->port :(int) $default; }

	/**
	 * @param int|int[]|Url $port
	 * @return bool
	 */
	public function equalsPort($port) :bool
	{
		$ports = [];
		foreach (is_array($port)?$port:[$port] as $p){
			if($p instanceof UrlModel)
				$ports[] = isset($p->port) && is_numeric($p->port) ?(int) $p->port :0;
			elseif (is_numeric($p)) $ports[] = (int)$p;
		}
		return in_array($this->getPort(), $ports);
	}

	/**
	 * @param int|string|UrlModel $port
	 * @return $this
	 */
	public function setPort($port)
	{
		if(is_numeric($port))
			$this->port = (int) $port;
		elseif ($port instanceof Url)
			$this->port = $port->getPort(0);
		elseif ($port instanceof UrlModel)
			$this->port = (isset($port->port) && is_numeric($port->port)) ?(int) $port->port :0;
		else
			$this->port = 0;
		return $this;
	}










	/**
	 * @param string $path
	 * @return $this
	 */
	public function setPath(string $path)
	{
		if(is_null($path) || strlen($path = static::normalizePath((string) $path))===0)
			$this->path = null;
		else $this->path = $path;
		return $this;
	}

	/**
	 * @param string $path
	 * @param bool $prepend [optional]
	 * @return $this
	 */
	public function addPath(string $path, bool $prepend = null)
	{
		if($this->hasPath()) {
			$this->path = $prepend
				? static::normalizePath((string)$path, (string)$this->path)
				: static::normalizePath((string)$this->path, (string)$path);
			if(!strlen((string) $this->path)) $this->path = null;
		}else
			$this->setPath($path);
		return $this;
	}








	/** @return bool */
	public function hasQuery() :bool
	{ return isset($this->query) && $this->query instanceof UrlQuery && !$this->query->isEmpty(); }

	/** @return UrlQuery */
	public function getQuery() :UrlQuery
	{
		if(!isset($this->query)) $this->query = new UrlQuery();
		elseif (!$this->query instanceof UrlQuery) $this->query = new UrlQuery($this->query);
		return $this->query;
	}

	/**
	 * @param string|array|UrlQuery|Url $query
	 * @return bool
	 */
	public function equalsQuery($query) :bool
	{
		if(isset($this->query) && $this->query instanceof UrlQuery){
			return $this->query->equals($query);
		}elseif ($query instanceof UrlModel){
			return !isset($query->query) || !$query->query instanceof UrlQuery || $query->query->isEmpty();
		}else
			return is_null($query);
	}

	/**
	 * @param null|string|array|UrlQuery|Url $query
	 * @return $this
	 */
	public function setQuery($query)
	{ $this->getQuery()->import($query, true); return $this; }

	/**
	 * @param string|array|UrlQuery|Url $query
	 * @return $this
	 */
	public function addQuery($query)
	{ $this->getQuery()->import($query); return $this; }








	/**
	 * @param array $onlyItems [optional] [scheme, authority, user, pass, host, port, path, query, fragment]
	 * @return string
	 */
	public function toString(array $onlyItems = null) :string
	{
		if(!$onlyItems) $onlyItems = ['scheme', 'authority', 'path', 'query', 'fragment'];
		$url = [];
		foreach ($onlyItems as $item){
			if($this->{'has'.ucfirst($item)}())
				$url[$item] = (string) $this->{'get'.ucfirst($item)}();
		}
		$urlString = '';
		if(array_key_exists('authority', $url)){
			$urlString = "//{$url['authority']}/";
		}elseif (array_key_exists('host', $url)){
			if(array_key_exists('user', $url))
				$urlString.= $url['user'] . (array_key_exists('pass', $url) ?":{$url['pass']}" :'') . '@';
			$urlString = "//{$urlString}{$url['host']}" . (array_key_exists('port', $url) ?":{$url['port']}" :'') . '/';
		}
		if(array_key_exists('path', $url))
			$urlString.= strlen($urlString) ?(substr($url['path'],0,1)=='/' ?substr($url['path'],1) :$url['path']) :$url['path'];
		if(array_key_exists('scheme', $url)) $urlString = "{$url['scheme']}:{$urlString}";
		if(array_key_exists('query', $url)) $urlString.= "?{$url['query']}";
		if(array_key_exists('fragment', $url)) $urlString.= "#{$url['fragment']}";
		return $urlString;
	}

	/**
	 * @param array $onlyItems [optional] [scheme, authority, user, pass, host, port, path, query, fragment]
	 * @return array
	 */
	public function toArray(array $onlyItems = null) :array
	{
		if(!$onlyItems) $onlyItems = ['scheme', 'authority', 'path', 'query', 'fragment'];
		$url = [];
		foreach ($onlyItems as $item){
			if($this->{'has'.ucfirst($item)}())
				$url[$item] = (string) $this->{'get'.ucfirst($item)}();
		}
		return $url;
	}

	/**
	 * @param array $onlyItems [optional] [scheme, authority, user, pass, host, port, path, query, fragment]
	 * @return UrlModel
	 */
	public function toModel(array $onlyItems = null)
	{
		$urlModel = static::newUrlModel($this->toArray($onlyItems));
		$urlModel->parent = clone $this->parent;
		return $urlModel;
	}




	public function __get($name)
	{}

	public function __toString()
	{ return $this->toString(); }

	public function __call($name, $args)
	{
		if(preg_match('#^(?P<action>has|get|equals|set|without|with)(?P<name>[A-Z]\w*)$#', $name, $result)){
			$name = lcfirst($result['name']);
			$exist = isset($this->{$name}) && is_string($this->{$name});
			if(!isset($args[0])) $args[0] = null;
			switch ($result['action']){
				case 'has': return $exist;
				case 'get': return (string) ($exist ?$this->{$name} :$args[0]);
				case 'equals':
					$value = $exist ?$this->$name :null;
					if($args[0] instanceof Url){
						if($exist) return $args[0]->{'has'.ucfirst($name)}() && $value==$args[0]->{'get'.ucfirst($name)}();
						else return !$args[0]->{'has'.ucfirst($name)}();
					}elseif ($args[0] instanceof UrlModel){
						if($exist) return isset($args[0]->$name) && is_string($args[0]->$name) && $value==$args[0]->$name;
						else return !isset($args[0]->$name) || !is_string($args[0]->$name);
					}elseif (is_array($args[0])) return in_array($value, $args[0]);
					return $value===$args[0];
				case 'set':
					if($args[0] instanceof UrlModel) $this->{$name} = $args[0]->$name;
					else $this->{$name} = $args[0];
					return $this;
				case 'without':
					return $this->without($args[0], [$name]);
				case 'with':
					$url = clone $this;
					$url->{'set'.ucfirst($name)}($args[0]);
					return $url;
			}
		}
		return null;
	}
}
