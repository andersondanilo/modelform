<?php

namespace ModelForm;

use Illuminate\Support\Facades\Validator,
    ArrayAccess,
    IteratorAggregate;

class Form implements \ArrayAccess, \IteratorAggregate
{
    public $_data = array();
    public $_instance;
    public $_fields = array();
    public $_prefix;
    public $_validator = null;

    public function __construct($params=array())
    {
        if(isset($params['prefix']))
            $this->setPrefix($params['prefix']);

        if(isset($params['instance']))
            $this->setInstance($params['instance']);

        if(isset($params['data']))
            $this->setData($params['data']);

        if(isset($params['validator']))
            $this->setValidator($params['validator']);
        else
            $this->setValidator(Validator::make($this->_data, []));

        $this->makeFields();
    }

    public function setData($data)
    {
        $this->_data = $data;
    }

    public function getData()
    {
        return $this->_data;
    }

    public function setInstance($instance)
    {
        $this->_instance = $instance;
        if(is_object($instance) && !$this->_prefix) {
            $this->setPrefix(basename(get_class($instance)).'_');
        }
    }

    public function getInstance()
    {
        return $this->_instance;
    }

    public function setPrefix($prefix) 
    {
        $this->_prefix = $prefix;
    }

    public function setValidator($validator)
    {
        $this->_validator = $validator;
    }

    public function getValidator()
    {
        return $this->_validator;
    }

    public function __call($method, $args) 
    {
        if(array_key_exists($method, $this->_fields)) 
            return $this->_fields[$method]($args ? $args[0] : null);
        trigger_error("Undefined method '$method'", E_USER_ERROR);
    }

    public function __set($key, $value)
    {
        if($value instanceof Fields\Field) {
            $value->form = $this;
            $value->name = $key;
            $this->_fields[$key] = $value;
            $this->$key = $value;
        } else {
            trigger_error("Undefined property '$key'", E_USER_ERROR);
        }
    }

    public function getValue($name)
    {
        if(array_key_exists($name, $this->_data))
            return $this->_data[$name];

        if($this->_instance)
            return $this->_instance->$name;
    }

    public function offsetSet($offset, $value)
    {
        $this->_data[$offset] = $value;
    }

    public function offsetExists($offset)
    {
        return isset($this->_data[$offset]);
    }

    public function offsetUnset($offset) {
        $this->_data[$offset] = null;
    }

    public function offsetGet($offset)
    {
        return $this->getValue($offset);
    }

    public function isValid()
    {
        return $this->getValidator()->passes();
    }

    public function getIterator()
    {
        return new ArrayIterator($this->_fields);
    }

}