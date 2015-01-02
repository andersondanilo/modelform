<?php

namespace ModelForm;

use Illuminate\Support\Facades\Validator,
    Illuminate\Support\Collection;

class Form extends Collection
{
    public $_data = array();
    public $_model;
    public $_prefix;
    public $_validator = null;
    public $_formSet = null;
    public $_formSetPos = null;
    public $_isKnockout = false;

    public function __construct($params=array())
    {
        if(isset($params['prefix']))
            $this->setPrefix($params['prefix']);

        if(isset($params['model']))
            $this->setModel($params['model']);
        else {
            $newModel = $this->makeModel();
            if($newModel)
                $this->setModel($newModel);
        }

        if($this->_model && $this->_model->getKeyName())
        {
            $primaryKey = $this->_model->getKeyName();
            $this->$primaryKey = new Fields\CharField();
        }

        if(isset($params['data']))
            $this->setData($params['data']);

        if(isset($params['validator']))
            $this->setValidator($params['validator']);
        else
            $this->setValidator($this->makeValidator());

        $this->makeFields();
    }

    public function setData($data)
    {
        $this->_data = $data;
    }

    public function makeValidator()
    {
        return Validator::make($this->_data, []);
    }

    public function getData()
    {
        return $this->_data;
    }

    public function setModel($model)
    {
        $this->_model = $model;
        if(is_object($model) && !$this->_prefix) {
            $aux = explode('\\',get_class($model));
            $this->setPrefix(array_pop($aux));
        }
    }

    public function getModel()
    {
        return $this->_model;
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

    public function __set($key, $value)
    {
        if($value instanceof Fields\Field) {
            $value->form = $this;
            $value->name = $key;
            $this[$key] = $value;
            $this->$key = $value;
        } else {
            trigger_error("Undefined property '$key'", E_USER_ERROR);
        }
    }

    public function getValue($name)
    {
        if(array_key_exists($name, $this->_data))
            return $this->_data[$name];

        if($this->_model)
            return $this->_model->$name;
    }

    public function getCleanedData()
    {
        $result = array();
        foreach($this as $key => $field) {
            $result[$key] = $field->cleanedValue;
        }
        return $result;
    }

    public function jsonSerialize()
    {
        return $this->getCleanedData();
    }

    public function toJson($options=0)
    {
        return json_encode($this);
    }    

    public function isValid()
    {
        return $this->getValidator()->passes();
    }

    public function makeModel() 
    {
        return null;
    }

}