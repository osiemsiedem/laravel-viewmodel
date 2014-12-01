<?php namespace OsiemSiedem\View;

use ArrayAccess;
use JsonSerializable;
use ReflectionObject;
use ReflectionMethod;
use ReflectionProperty;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;

abstract class ViewModel implements ArrayAccess, Arrayable, Jsonable, JsonSerializable {

	/**
	 * Create a new ViewModel instance.
	 *
	 * @param  array  $data
	 */
	public function __construct(array $data = [])
	{
		$this->set($data);
	}

	/**
	 * Convert the model instance to an array.
	 *
	 * @return  array
	 */
	public function toArray()
	{
		$reflection = new ReflectionObject($this);

		$properties = $this->propertiesToArray($reflection);
		$methods    = $this->methodsToArray($reflection);

		return array_merge($properties, $methods);
	}

	/**
	 * Convert all the public properties to an array.
	 *
	 * @param   ReflectionObject  $reflection
	 * @return  array
	 */
	protected function propertiesToArray(ReflectionObject $reflection)
	{
		$properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);

		$data = [];

		foreach ($properties as $property)
		{
			if ($property->class !== __CLASS__)
			{
				$data[$property->name] = $this->{$property->name};
			}
		}

		return $data;
	}

	/**
	 * Convert all the public methods to an array.
	 *
	 * @param   ReflectionObject  $reflection
	 * @return  array
	 */
	protected function methodsToArray(ReflectionObject $reflection)
	{
		$methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

		$data = [];

		foreach ($methods as $method)
		{
			if ($method->class !== __CLASS__)
			{
				$data[$method->name] = $this->{$method->name}();
			}
		}

		return $data;
	}

	/**
	 * Convert the model instance to JSON.
	 *
	 * @param   int  $options
	 * @return  string
	 */
	public function toJson($options = 0)
	{
		return json_encode($this->toArray(), $options);
	}

	/**
	 * Convert the model into something JSON serializable.
	 *
	 * @return  array
	 */
	public function jsonSerialize()
	{
		return $this->toArray();
	}

	/**
	 * Determine if the given attribute exists.
	 *
	 * @param   string  $key
	 * @return  bool
	 */
	public function has($key)
	{
		return ($this->hasAccessibleMethod($key) || $this->hasAccessibleProperty($key));
	}

	/**
	 * Determine if the given method is public.
	 *
	 * @param   string  $key
	 * @return  bool
	 */
	protected function hasAccessibleMethod($key)
	{
		if (method_exists($this, $key))
		{
			$reflection = new ReflectionMethod($this, $key);

			return $reflection->isPublic();
		}

		return false;
	}

	/**
	 * Determine if the given property is public.
	 *
	 * @param   string  $key
	 * @return  bool
	 */
	protected function hasAccessibleProperty($key)
	{
		if (isset($this->$key))
		{
			$reflection = new ReflectionProperty($this, $key);

			return $reflection->isPublic();
		}

		return false;
	}

	/**
	 * Get an attribute from the model.
	 *
	 * @param   string  $key
	 * @return  mixed
	 */
	public function get($key, $default = null)
	{
		if ($this->hasAccessibleMethod($key))
		{
			return $this->$key();
		}

		if ($this->hasAccessibleProperty($key))
		{
			return $this->$key;
		}

		return $default;
	}

	/**
	 * Set a given attribute on the model.
	 *
	 * @param   mixed  $key
	 * @param   mixed  $value
	 * @return  $this
	 */
	public function set($key, $value = null)
	{
		if (is_array($key))
		{
			foreach ($key as $k => $v)
			{
				$this->$k = $v;
			}
		}
		else
		{
			$this->$key = $value;
		}

		return $this;
	}

	/**
	 * Remove an attribute from the model.
	 *
	 * @param   mixed  $key
	 * @return  $this
	 */
	public function forget($key)
	{
		unset($this->$key);

		return $this;
	}

	/**
	 * Determine if the given offset exists.
	 *
	 * @param   string  $offset
	 * @return  bool
	 */
	public function offsetExists($offset)
	{
		return $this->has($offset);
	}

	/**
	 * Get the value for a given offset.
	 *
	 * @param   string  $offset
	 * @return  mixed
	 */
	public function offsetGet($offset)
	{
		return $this->get($offset);
	}

	/**
	 * Set the value at the given offset.
	 *
	 * @param   string  $offset
	 * @param   mixed   $value
	 * @return  void
	 */
	public function offsetSet($offset, $value)
	{
		$this->set($offset, $value);
	}

	/**
	 * Unset the value at the given offset.
	 *
	 * @param   string  $offset
	 * @return  void
	 */
	public function offsetUnset($offset)
	{
		$this->forget($offset);
	}

	/**
	 * Convert the model to its string representation.
	 *
	 * @return  string
	 */
	public function __toString()
	{
		return $this->toJson();
	}

	/**
	 * Determine if the given attribute exists.
	 *
	 * @param   string  $key
	 * @return  void
	 */
	public function __isset($key)
	{
		return $this->has($key);
	}

	/**
	 * Dynamically retrieve attributes on the model.
	 *
	 * @param   string  $key
	 * @return  mixed
	 */
	public function __get($key)
	{
		return $this->get($key);
	}

}
