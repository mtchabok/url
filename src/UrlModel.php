<?php
namespace Mtchabok\Url;
use Mtchabok\Url\Extra\UrlQuery;

/**
 * Class UrlModel
 * @package Mtchabok\Url
 */
class UrlModel
{
	/** @var UrlModel */
	public $parent = null;
	/** @var string */
	public $scheme = null;
	/** @var string */
	public $user = null;
	/** @var string */
	public $pass = null;
	/** @var string */
	public $host = null;
	/** @var int */
	public $port = 0;
	/** @var string */
	public $path = null;
	/** @var UrlQuery */
	public $query = null;
	/** @var string */
	public $fragment = null;

	/**
	 * UrlModel constructor.
	 * @param string|array|object|UrlModel $url [optional]
	 */
	public function __construct($url = null)
	{
		$this->query = new UrlQuery();
		if(!is_null($url)){
			if(!is_array($url)) $url = Url::parse($url);
			foreach (['scheme', 'user', 'pass', 'host', 'fragment'] as $n) {
				if (array_key_exists($n, $url) && is_string($url[$n]))
					$this->$n = $url[$n];
			}
			if (array_key_exists('path', $url) && is_string($url['path']))
				$this->path = strlen($url['path']) ?Url::normalizePath($url['path']) :'';
			if (array_key_exists('port', $url) && is_numeric($url['port']))
				$this->port = (int) $url['port'];
			if (array_key_exists('query', $url)){
				if($url['query'] instanceof UrlQuery)
					$this->query = clone $url['query'];
				else
					$this->query->import($url['query'], true);
			}
		}
	}
}