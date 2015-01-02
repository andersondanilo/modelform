<?php

namespace ModelForm;

use Illuminate\Support\Collection;

class FormSet extends Collection
{
    public $lastPosition = 0;
    public $query;
    public $_knockout;

    public function __construct(array $params = array())
    {
        foreach($params as $k => $v) {
            if(property_exists($this, $k))
                $this->$k = $v;
        }

        $this->makeForms();
    }

    public function makeForm()
    {
        return null;
    }

    public function makeForms() {
        $models = [];
        
        if($this->query)
            $models = $this->query->all();
        else
            $models[] = $this->makeForm();

        foreach($models as $model)
            $this[] = $this->configureForm($model);
    }

    public function configureForm($form)
    {
        $form->_formSet = $this;
        $form->_formSetPos = $this->lastPosition++;
        return $form;
    }

    public function isValid()
    {
        foreach($this->form as $form) {
            if(!$this->form->isValid())
                return false;
        }
        return true;
    }

    public function makeKnockout()
    {
        $form = $this->makeForm();
        $form->_isKnockout = true;
        return $form;
    }

    public function & __get($name)
    {
        if($name == 'knockout')
        {
            if(!$this->_knockout)
                $this->_knockout = $this->makeKnockout();
            return $this->_knockout;
        }
        trigger_error("Undefined property '$name'", E_USER_ERROR);
    }

    public function getDefaultData()
    {
        $form = $this->makeForm();
        return $form->jsonSerialize();
    }

    public function jsonSerialize()
    {
        $result = array();
        foreach($this as $form)
            $result[] = $form->jsonSerialize();
        return $result;
    }

    public function toJson($options=0)
    {
        return json_encode($this);
    }
}