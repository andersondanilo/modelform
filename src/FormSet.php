<?php

namespace ModelForm;

use Illuminate\Support\Collection;
use Illuminate\Support\MessageBag;

class FormSet extends Collection
{
    public $lastPosition = 0;
    public $query;
    public $relation;
    public $models;
    public $_knockout;

    public function __construct(array $params = array())
    {
        foreach($params as $k => $v) {
            if(property_exists($this, $k))
                $this->$k = $v;
        }

        $this->makeForms();

        if(isset($params['data']))
            $this->setData($params['data']);
    }

    public function getPrefix()
    {
        $baseForm = $this->makeForm();
        return $baseForm->getPrefix();
    }

    public function setData($data)
    {
        $oldForms = $this->items;
        $this->items = array();
        $dataByPos = array();
        $baseForm = $this->makeForm();
        $baseModel = $baseForm->getModel();
        $prefix = $baseForm->getPrefix();
        $primaryKey = $baseModel ? $baseModel->getKeyName() : null;

        foreach($data as $key => $value) {
            if(strpos($key, $prefix) === 0) {
                $keyAux = explode('-',$key);
                $pos = $keyAux[1];
                if(is_numeric($pos))
                {
                    if(!isset($dataByPos[$pos]))
                        $dataByPos[$pos] = array();
                    $dataByPos[$pos][$key] = $value;
                }
            }
        }

        foreach($dataByPos as $pos => $data)
        {
            $primaryKeyEncoded = $this->getPrefix().'-'.$pos.'-'.$primaryKey;
            if($primaryKey && !array_key_exists($primaryKeyEncoded, $data)) {
                throw new \Exception("Invalid data, primary key not found");
            }
            if($primaryKey && $data[$primaryKeyEncoded]) {
                $form = $this->makeForm();
                $model = $this->getModelByPrimaryKey($data[$primaryKeyEncoded]);
                if(!$model)
                    throw new \Exception("Invalid model, not exists from formset");
                $form->setModel($model);
            }
            else
                $form = $this->makeForm();
            $form->_formSetPos = $pos;
            $form->setData($data);
            $this[] = $this->configureForm($form);

        }
    }

    public function makeForm()
    {
        return null;
    }

    public function makeForms() {
        if(count($this) > 0)
            trigger_error("forms already exists", E_USER_ERROR);
        else
        {
            if(!$this->models) {
                if($this->query)
                    $this->models = $this->query->get();
                else if($this->relation)
                    $this->models = $this->relation->get();
            }
            
            if(!$this->models)
                $this->models[] = $this->makeForm()->getModel();

            foreach($this->models as $model) {
                $form = $this->makeForm();
                $form->setModel($model);
                $this[] = $this->configureForm($form);
            }
        }
    }

    public function configureForm($form, $pos=null)
    {
        if(is_null($pos))
            $pos = $this->lastPosition++;
        $form->_formSet = $this;
        $form->_formSetPos = $pos;
        return $form;
    }

    public function isValid()
    {
        foreach($this as $form) {
            if(!$form->isValid())
                return false;
        }
        return true;
    }

    public function save()
    {
        $models = array();
        foreach($this as $form) {
            $form->save();
            $models[] = $form->getModel();
        }
        return $models;
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

    /**
     * Return a model of formset by id
     */
    public function getModelByPrimaryKey($id)
    {
        foreach($this->models as $model)
        {
            $primaryKey = $model->getKeyName();
            if($model->$primaryKey == $id)
                return $model;
        }
    }

    public function toJson($options=0)
    {
        return json_encode($this);
    }

    public function errors()
    {
        $args = [];
        foreach($this as $form)
            $args[] = $form->errors()->toArray();
        if($args) {
            $messages = call_user_func_array('array_merge_recursive', $args);
            return new MessageBag($messages);
        }
        return new MessageBag([]);
    }
}