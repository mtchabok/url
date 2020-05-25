<?php
namespace Mtchabok\Url\Extra;
use ArrayAccess, IteratorAggregate, ArrayIterator, Countable;

/**
 * Class UrlQuery
 * @package Mtchabok\Url
 */
class UrlQuery implements ArrayAccess, IteratorAggregate, Countable
{
	protected $params = [];

	/** @return bool */
	public function isEmpty() :bool
	{ return count($this->params)===0; }

	/**
	 * @param string|array|UrlQuery|Url $params
	 * @param bool $clearBeforeImport
	 * @return $this
	 */
	public function import($params, bool $clearBeforeImport = false)
	{
		if($clearBeforeImport) $this->clear();
		if(is_string($params)){
			parse_str(ltrim($params, '?'), $temp);
			$params = $temp;
			unset($temp);
		}elseif($params instanceof Url) $params = $params->query->export();
		elseif($params instanceof UrlQuery) $params = $params->export();
		if(is_array($params)){
			foreach ($params as $name=>$param)
				$this->params[$name] = $param;
		}
		return $this;
	}

	/** @return array */
	public function export() :array
	{ return $this->params; }



	/** @return $this */
	public function clear()
	{ $this->params = []; return $this; }






	/**
	 * @param string $name
	 * @return bool
	 */
	public function has(string $name) :bool
	{ return array_key_exists($name, $this->params); }

	/**
	 * @param string $name
	 * @param string|array|null $default [optional]
	 * @return string|array|null
	 */
	public function get(string $name, $default = null)
	{ return array_key_exists($name, $this->params) ?$this->params[$name] :$default; }

	/**
	 * @param string|array|UrlQuery|Url $name
	 * @param string|array $param [optional]
	 * @return bool
	 */
	public function equals(string $name, $param = null) :bool
	{
		if(is_null($param)){
			if(is_string($name)){
				parse_str($name, $temp);
				$name = $temp;
				unset($temp);
			}elseif($name instanceof Url) $name = $name->query->export();
			elseif($name instanceof UrlQuery) $name = $name->export();
			if(is_array($name) && count($name)===$this->count()){
				foreach ($name as $n=>$p){
					if(!array_key_exists($n, $this->params) || $this->params[$n]!=$p)
						return false;
				}
			}
			return false;
		}else
			return array_key_exists($name, $this->params) && $this->params[$name]==$param;
	}

	/**
	 * @param string $name
	 * @param string|array|null $value [optional]
	 * @return $this
	 */
	public function set(string $name, $value = null)
	{
		if(is_null($value))
			unset($this->params[$name]);
		elseif (is_string($value) || is_array($value))
			$this->params[$name] = $value;
		return $this;
	}




	public function offsetExists($offset)
	{ return $this->has($offset); }

	public function offsetGet($offset)
	{ return $this->get($offset); }

	public function offsetSet($offset, $value)
	{ $this->set($offset, $value); return $this; }

	public function offsetUnset($offset)
	{ $this->set($offset, null); return $this; }

	public function getIterator()
	{ return new ArrayIterator($this->params); }

	public function count() :int
	{ return count($this->params); }



	public function __construct($params = null)
	{ if(!is_null($params)) $this->import($params); }

	public function __toString()
	{ return http_build_query($this->params); }

}