<?php

namespace ModelForm\Fields;

use ModelForm\Fields\Label;

use Illuminate\Support\Facades\Form;

class Field
{
    public $form;
    public $name;
    public $_options = array();
    public $label = '';

    /*
    public $formMethods = array(
        'input', 'text', 'textArea',
        'password', 'hidden', 'email', 'url',
        'file', 'textarea', 'select', 'selectRange',
        'checkbox', 'radio'
    );
    */

    public function __construct(array $params=array())
    {
        foreach($params as $k => $v) {
            $k2 = "_$k";
            if(property_exists($this, $k))
                $this->$k = $v;
            else if(property_exists($this, $k2))
                $this->$k2 = $v;
            else
                trigger_error("Undefined property '$k'", E_USER_ERROR);
        }
    }

    public function setOptions($options) {
        $this->_options = $options;
        return $this;
    }

    public function getOptions()
    {
        if(is_callable($this->_options)) {
            $aux = $this->_options;
            $this->_options = $aux();
        }
        return $this->_options;
    }

    public function getRequired()
    {
        $attribute = $this->name;
        $rules = $this->form->getValidator()->getRules();
        if(!array_key_exists($attribute, $rules))
            return false;
        if(in_array('required', $rules[$attribute]))
            return true;
        return false;
    }

    public function getHtmlName($position=null)
    {
        if($this->form->_formSet && !isset($position))
            $position = $this->form->_formSetPos;

        $htmlName = $this->name;

        if(!is_null($position))
            $htmlName = $position.'-'.$htmlName;

        if($this->form->_prefix)
            $htmlName = $this->form->_prefix . '-' . $htmlName;

        return $htmlName;
    }

    public function getErrors()
    {
        return new ModelForm\ErrorList($this->form->getValidator()->errors()->get($this->name));
    }

    public function __get($name)
    {
        switch ($name) {
            case 'value':
                return $this->form->getValue($this->name);
            case 'options':
                return $this->getOptions();
            case 'cleanedValue':
                return $this->getCleanedValue();
            case 'required':
                return $this->getRequired();
            case 'htmlName':
                return $this->getHtmlName();
        }
        trigger_error("Undefined property '$name'", E_USER_ERROR);
    }

    public function __set($name, $value)
    {
        $auxName = "_$name";
        if(property_exists($this, $auxName))
            $this->$auxName = $value;
        else
            trigger_error("Undefined property '$name'", E_USER_ERROR);
    }

    public function __call($method, $arguments)
    {
        if(!$arguments)
            $arguments = array();

        //if(in_array($method, $this->formMethods)) {
            $realArguments = array();
            $realArguments[] = $this->htmlName;
            if($method == 'select')
                $realArguments[] = $this->options;

            if(in_array($method, array('checkbox', 'radio'))) {
                $value = array_shift($arguments);
                $realArguments[] = $value;
                $realArguments[] = $value == $this->value;
            } else if(!in_array($method, ['file', 'password', 'checkbox', 'radio'])) {
                $realArguments[] = $this->value;
            }

            $options = array_shift($arguments);

            if(!$options)
                $options = [];

            if($this->form->_isKnockout) {
                if(isset($options['data-bind']) && $options['data-bind'])
                    $options['data-bind'] .= ', ';
                else
                    $options['data-bind'] = '';
                $options['data-bind'] .= 'attr: {';
                $options['data-bind'] .= 'name: '.$this->getKnockoutName();
                if(!in_array($method, ['file', 'checkbox', 'radiobox', 'option']))
                    $options['data-bind'] .= ', value: '.$this->name;
                $options['data-bind'] .= '}';

            }

            $realArguments[] = $options;

            foreach($arguments as $argument)
                $realArguments[] = $argument;

            return call_user_func_array("Form::$method", $realArguments);
        // }
    }

    public function label($attributes=array())
    {
        if($this->required) {
            if(!isset($attributes['class']))
                $attributes['class'] = '';
            $attributes['class'] .= ' required';
        }
        return Form::label($this->name, $this->label, $attributes);
    }

    public function getCleanedValue()
    {
        return $this->value;
    }

    public function getKnockoutName() 
    {
        return "'".$this->getHtmlName("'+\$index()+'")."'";
    }
}