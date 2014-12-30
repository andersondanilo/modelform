<?php

namespace ModelForm;

class Form
{
    public $_data;
    public $_instance;
    public $_fields = array();

    public function __construct($params)
    {
        if($params['instance'])
            $this->setInstance($params['instance']);

        if($params['data'])
            $this->setData($params['data']);

        $this->initializeFields();
    }

    public function setData($data)
    {
        $this->_data = $data;
    }

    public function setInstance($instance)
    {
        $this->_instance = $instance;
    }

    public function __set($key, $value)
    {
        if($value instanceof Field) {
            $value->form = $this;
            $value->name = $key;
            $this->_fields[$key] = $value;
        }
        trigger_error("Undefined property '$name'", E_USER_ERROR);
    }

    public function getValue($name)
    {
        if(array_key_exists($name, $this->_data))
            return $this->_data[$name];

        if($this->_instance)
            return $this->_instance->$name;
    }
}