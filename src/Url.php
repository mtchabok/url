<?php
/**
 * Created by PhpStorm.
 * User: mtchabok
 * Date: 23/04/2019
 * Time: 10:48 AM
 */

namespace Mtchabok\Url;

/**
 * Class Url
 * @package Mtchabok\Url
 */
class Url
{

	protected static $_urlPattern = '^((?P<scheme>[a-zA-Z0-9]*)\:)?(\/\/((?P<user>[^\@\:]*)?(\:(?P<pass>[^\@]*)?)?\@)?(?P<host>[^\/\:\?\#]*)?(\:(?P<port>[^\/]\d*)?)?)?(?P<path>[^\?\#]*)?(\?(?P<query>[^\#]*)?)?(\#(?P<fragment>.*)?)?$';

	private $_readOnly    = false;

	private $_scheme    = '';
	private $_user      = '';
	private $_pass      = null;
	private $_host      = '';
	private $_port      = 0;
	private $_path      = '';
	private $_query     = '';
	private $_queryArray= [];
	private $_fragment  = '';





	/** @return string */
	public function toString() :string
	{
		$url = '';
		if($this->hasScheme()) $url.= "{$this->getScheme()}:";
		if($this->hasHost()){
			$url.= '//';
			if($this->hasUser())
				$url.= $this->getUser().($this->getPass() ?":{$this->getPass()}" :'').'@';
			$url.= $this->getHost();
		}
		if($this->getPath()) $url.= $this->getPath();
		if($this->hasQuery()) $url.= "?{$this->getQuery()}";
		if($this->hasFragment()) $url.= "#{$this->getFragment()}";
		return $url;
	}







	/** @return bool */
	public function isRelative() :bool
	{ return !$this->hasHost() && substr($this->getPath(),0,1) != '/'; }

	/** @return bool */
	public function isAbsolute() :bool
	{ return $this->hasScheme() && $this->hasHost() && (!$this->hasPath() || substr($this->getPath(), 0, 1)=='/'); }


	/**
	 * @param string|Url $url
	 * @return bool
	 */
	public function equals($url) :bool
	{
		if(!$url instanceof Url) $url = new static($url);
		return $this->equalsScheme($url) && $this->equalsUser($url) && $this->equalsPass($url) && $this->equalsHost($url) && $this->equalsPort($url)
			&& $this->equalsPath($url) && $this->equalsQuery($url) && $this->equalsFragment($url);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	



	/** @return bool */
	public function isReadOnly():bool
	{ return (bool) $this->_readOnly; }

	/**
	 * @param bool $readOnly
	 * @return Url
	 */
	public function setReadOnly(bool $readOnly) :Url
	{ if(true!==$this->_readOnly) $this->_readOnly = (bool) $readOnly; return $this; }








	/** @return bool */
	public function hasScheme() :bool
	{ return !empty($this->_scheme); }

	/**
	 * @param array|string|Url $scheme
	 * @return bool
	 */
	public function equalsScheme($scheme) :bool
	{ return in_array($this->getScheme(), is_array($scheme) ?$scheme :[($scheme instanceof Url ?$scheme->getScheme() :(string) $scheme)]); }

	/**
	 * @param string $default=null
	 * @return string
	 */
	public function getScheme(string $default = null) :string
	{ return (string) (empty($this->_scheme) ?$default :$this->_scheme); }

	/**
	 * @param string $scheme
	 * @return $this|false
	 */
	public function setScheme(string $scheme)
	{
		if($this->isReadOnly()) return false;
		$this->_scheme = strtolower((string) $scheme);
		return $this;
	}













	/** @return bool */
	public function hasUser() :bool
	{ return !empty($this->_user); }

	/**
	 * @param array|string|Url $user
	 * @return bool
	 */
	public function equalsUser($user) :bool
	{ return in_array($this->getUser(), is_array($user) ?$user :[($user instanceof Url ?$user->getUser() :(string) $user)]); }

	/**
	 * @param string $default=null
	 * @return string
	 */
	public function getUser(string $default = null) :string
	{ return (string) (empty($this->_user) ?$default :$this->_user); }

	/**
	 * @param string $user
	 * @return $this|false
	 */
	public function setUser(string $user)
	{
		if($this->isReadOnly()) return false;
		$this->_user = (string) $user;
		return $this;
	}









	/** @return bool */
	public function hasPass()
	{ return null!==$this->_pass; }

	/**
	 * @param string|Url $pass
	 * @return bool
	 */
	public function equalsPass($pass) :bool
	{ return $this->getPass()===($pass instanceof Url ?$pass->getPass() :(string) $pass); }

	/**
	 * @param string|null $default=null
	 * @return string|null
	 */
	public function getPass($default = null)
	{ return null===$this->_pass ?$default :$this->_pass; }

	/**
	 * @param string|null $pass
	 * @return $this|false
	 */
	public function setPass($pass)
	{
		if($this->isReadOnly()) return false;
		$this->_pass = null===$pass ?null :(string) $pass;
		return $this;
	}














	/** @return bool */
	public function hasHost() :bool
	{ return !empty($this->_host); }

	/**
	 * @param array|string|Url $host
	 * @return bool
	 */
	public function equalsHost($host) :bool
	{ return in_array($this->getHost(), is_array($host) ?$host :[($host instanceof Url ?$host->getHost() :(string) $host)]); }

	/**
	 * @param string $pattern
	 * @return bool|int|array
	 */
	public function matchHost($pattern)
	{ return (false===($return=preg_match($pattern, $this->getHost(), $return))) ?false :($return ?$return :false); }

	/**
	 * @param string $default=null
	 * @return string
	 */
	public function getHost($default = null) :string
	{ return (string) (empty($this->_host) ?$default :$this->_host); }

	/**
	 * @param string|null $host
	 * @return $this|false
	 */
	public function setHost(string $host)
	{
		if($this->isReadOnly()) return false;
		$this->_host = strtolower((string) $host);
		return $this;
	}
















	/** @return bool */
	public function hasPort()
	{ return !empty($this->_port); }

	/**
	 * @param array|string|int|Url $port
	 * @return bool
	 */
	public function equalsPort($port) :bool
	{ return in_array($this->getPort(), is_array($port) ?$port :[($port instanceof Url ?$port->getPort() :intval($port))]); }

	/**
	 * @param int $default=null
	 * @return int
	 */
	public function getPort(int $default = null) :int
	{ return (int) (empty($this->_port) ?intval($default) :(int) $this->_port); }

	/**
	 * @param int|string $port
	 * @return $this|false
	 */
	public function setPort($port)
	{
		if($this->isReadOnly()) return false;
		$this->_port = intval($port);
		return $this;
	}









	/** @return bool */
	public function hasPath() :bool
	{ return !empty($this->_path); }

	/**
	 * @param array|string|Url $path
	 * @return bool
	 */
	public function equalsPath($path) :bool
	{ return in_array($this->getPath(), is_array($path) ?$path :[($path instanceof Url ?$path->getPath() :(string) $path)]); }

	/**
	 * @param string $pattern
	 * @return bool|int|array
	 */
	public function matchPath($pattern)
	{ return (false===($return=preg_match($pattern, $this->getPath(), $return))) ?false :($return ?$return :false); }

	/**
	 * @param string $default=null
	 * @return string
	 */
	public function getPath(string $default = null) :string
	{ return (string) (empty($this->_path) ?$default :$this->_path); }

	/**
	 * @param string|null $path
	 * @return $this|false
	 */
	public function setPath(string $path)
	{
		if($this->isReadOnly()) return false;
		$this->_path = static::normalizePath((string) $path);
		return $this;
	}

	/**
	 * @param string $path
	 * @return $this|false
	 */
	public function addPath(string $path)
	{
		if($this->isReadOnly()) return false;
		if('/'==substr($this->_path, -1) && '/'==substr($path, 0, 1))
			$this->_path = static::normalizePath($this->_path.substr($path,1));
		elseif('/'==substr($this->_path, -1) || '/'==substr($path, 0, 1))
			$this->_path = static::normalizePath($this->_path.$path);
		elseif(strlen($this->_path))
			$this->_path = static::normalizePath("{$this->_path}/{$path}");
		else
			$this->_path = static::normalizePath($path);
		return $this;
	}









	/** @return bool */
	public function hasQuery() :bool
	{ return !empty($this->_query); }

	/**
	 * @param string|array|Url $query
	 * @return bool
	 */
	public function equalsQuery($query) :bool
	{
		if($query instanceof Url) $query = $query->getQueryArray();
		elseif(!is_array($query)) $query = parse_str((string) $query, $query);
		foreach ($query as $k=>$v){
			if($this->getQueryParam($k)!=$v) return false;
		}
		return true;
	}

	/**
	 * @param string $default=null
	 * @return string
	 */
	public function getQuery(string $default = null) :string
	{ return (string) (empty($this->_query) ?$default :$this->_query); }

	/**
	 * @param array $default=null
	 * @return array
	 */
	public function getQueryArray(array $default = null) :array
	{ return (array) (empty($this->_queryArray) ?$default :$this->_queryArray); }

	/**
	 * @param string|array $query
	 * @return $this|false
	 */
	public function setQuery($query)
	{
		if($this->isReadOnly()) return false;
		if(is_array($query)){
			$this->_queryArray = $query;
			$this->_query = http_build_query($query);
		}elseif (is_string($query)){
			$this->_query = substr($query, 0, 1)=='?' ?substr($query, 1) :$query;
			$this->_queryArray = [];
			parse_str($this->_query, $this->_queryArray);
		}
		return $this;
	}

	/**
	 * @param string|array $query
	 * @return $this|false
	 */
	public function addQuery($query)
	{
		if($this->isReadOnly()) return false;
		if(!is_array($query)) parse_str((string) $query, $query);
		$this->_queryArray = array_merge($this->_queryArray, $query);
		$this->_query = http_build_query($this->_queryArray);
		return $this;
	}



	/**
	 * @param string $name
	 * @param string|array $default=null
	 * @return string|array|null
	 */
	public function getQueryParam(string $name, $default = null)
	{ return isset($this->_queryArray[$name]) ?$this->_queryArray[$name] :(null===$default ?null :$default); }

	/**
	 * @param string $name
	 * @param string|array $value=null
	 * @return $this|false
	 */
	public function setQueryParam(string $name, $value = null)
	{
		if($this->isReadOnly()) return false;
		if(null===$value)
			unset($this->_queryArray[$name]);
		else
			$this->_queryArray[$name] = $value;
		$this->_query = http_build_query($this->_queryArray);
		return $this;
	}












	/** @return bool */
	public function hasFragment()
	{ return !empty($this->_fragment); }

	/**
	 * @param array|string|Url $fragment
	 * @return bool
	 */
	public function equalsFragment($fragment) :bool
	{ return in_array($this->getFragment(), is_array($fragment) ?$fragment :[($fragment instanceof Url ?$fragment->getFragment() :(string) $fragment)]); }

	/**
	 * @param string|int $default=null
	 * @return string|null
	 */
	public function getFragment($default = null)
	{ return null===$this->_fragment ?$default :$this->_fragment; }

	/**
	 * @param string $fragment
	 * @return $this|false
	 */
	public function setFragment($fragment)
	{ if($this->isReadOnly()) return false; $this->_fragment = (string) $fragment; return $this; }















	/** @return Url */
	public static function current()
	{
		$url = !empty($_SERVER['REQUEST_SCHEME']) ?"{$_SERVER['REQUEST_SCHEME']}:" :('http'.(!empty($_SERVER['HTTPS']) ?'s:' :':'));
		$url.= "//{$_SERVER['HTTP_HOST']}";
		$url.= substr($_SERVER['REQUEST_URI'], 0, 1)=='/'
			?$_SERVER['REQUEST_URI'] :"/{$_SERVER['REQUEST_URI']}";
		return new static($url);
	}







	/**
	 * @param string $url
	 * @return Url
	 */
	public static function parse($url)
	{ return new static($url); }





	/**
	 * @param string $path
	 * @return string
	 */
	public static function normalizePath($path)
	{
		$path = explode('/', $path);
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
	 * Url constructor.
	 * @param string|Url $url
	 * @param bool $readOnly=null
	 */
	public function __construct($url = null, $readOnly = null)
	{
		if(null!==$url){
			$result = [];
			preg_match('!'.static::$_urlPattern.'!', trim((string) $url), $result);

			$tempReadOnly = (bool) $this->_readOnly;
			$this->_readOnly = false;
			foreach (['scheme', 'user', 'pass', 'host', 'port', 'path', 'query', 'fragment'] as $name){
				if (isset($result[$name])) $this->{'set'.ucfirst($name)}($result[$name]);
			}
			if($tempReadOnly!==$this->_readOnly) $this->_readOnly = (bool) $tempReadOnly;
		}
		if(null!==$readOnly)
			$this->setReadOnly((bool) $readOnly);
	}


	public function __get($name)
	{}

	public function __set($name, $value)
	{}

	public function __isset($name)
	{}

	public function __unset($name)
	{}

	public function __toString()
	{ return $this->toString(); }

	public function __clone()
	{
		$this->_readOnly = false;
	}


}
