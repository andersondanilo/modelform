<?php

namespace ModelForm;

use Illuminate\Support\Facades\Validator,
    Illuminate\Support\Collection,
    Illuminate\Support\MessageBag;

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
        else
        {
            $aux = explode('\\', get_class($this));
            $this->setPrefix(array_pop($aux));
        }

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
            $this->setValidator($this->makeValidator($this->_data));

        $this->makeFields();
    }

    public function makeFields()
    {
        // This method should be overloaded
    }

    public function setData($data)
    {
        $this->_data = $this->decodeHtmlData($data);
    }

    public function decodeHtmlData($data) 
    {
        $dataAux = array();
        foreach ($data as $key => $value)
        {
            if(strpos($key, $this->getPrefix()) == 0)
            {
                $aux = explode('-', $key);
                array_shift($aux);
                $correct = true;
                if(count($aux) > 1 && is_numeric($aux[0])) {
                    if($aux[0] != $this->_formSetPos) {
                        $correct = false;
                    }
                    array_shift($aux);
                }
                if($correct)
                    $key = implode('-', $aux);
            }
            $dataAux[$key] = $value;
        }
        return $dataAux;
    }

    public function makeValidator($data)
    {
        return Validator::make($data, []);
    }

    public function getData()
    {
        return $this->_data;
    }

    public function setModel($model)
    {
        $this->_model = $model;
    }

    public function getModel()
    {
        return $this->_model;
    }

    public function setPrefix($prefix) 
    {
        $this->_prefix = $prefix;
    }

    public function getPrefix() 
    {
        return $this->_prefix;
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
            $cleanedValue = $field->cleanedValue;
            $cleanMethod = 'clean'.camel_case(ucfirst($key));
            if(method_exists($this, $cleanMethod))
                $cleanedValue = $this->$cleanMethod($cleanedValue);
            $result[$key] = $cleanedValue;
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

    public function save() 
    {
        $model = $this->getModel();
        $data = $this->getCleanedData();
        unset($data[$model->getKeyName()]);
        $model->fill($data);
        if($this->_formSet && $this->_formSet->relation) {
            $this->_formSet->relation->save($model);
        }
        else
            $model->save();
    }

    public function makeModel() 
    {
        return null;
    }

    public function errors()
    {
        return $this->getValidator()->messages();
    }

    public static function mergeErrors($forms)
    {
        $args = [];
        foreach($forms as $form)
            $args[] = $form->errors()->toArray();
        $messages = call_user_func_array('array_merge_recursive', $args);
        return new MessageBag($messages);
    }
}