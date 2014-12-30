<?php

namespace ModelForm\Fields;

use ModelForm\Fields\Label;

use Illuminate\Support\Facades\Form;

class Field
{
    public $form;
    public $name;
    public $attributes = array();
    public $label;
    public $widget;

    public function __construct(array $params=array())
    {
        if(isset($params['label']))
            $this->label = $params['label'];
        if(isset($params['widget']))
            $this->widget = $params['widget'];
        if(isset($params['required']) && $params['required'])
            $this->form->getValidator()->mergeRules($this->name, 'required');
    }

    public function required()
    {
        $attribute = $this->name;
        $rules = $this->form->getValidator()->getRules();
        if(!array_key_exists($attribute, $rules))
            return false;
        if(in_array('required', $rules[$attribute]))
            return true;
        return false;
    }

    public function htmlName()
    {
        return ($this->form->_prefix ?: '') . $this->name;
    }

    public function errors()
    {
        return new ModelForm\ErrorList($this->form->getValidator()->errors()->get($this->name));
    }

    public function __get($name)
    {
        switch ($name) {
            case 'value':
                return $this->form->getValue($name);
            case 'labelTag':
                return $this->labelTag();
            case 'required':
                return $this->required();
            case 'htmlName':
                return $this->htmlName();
        }
        trigger_error("Undefined property '$name'", E_USER_ERROR);
    }

    public function __invoke(array $attributes)
    {
        foreach($attributes as $key => $value)
            $this->attributes[$key] = $value;
        return $this;
    }

    public function labelTag($attributes=array())
    {
        if($this->required()) {
            if(!isset($attributes['class']))
                $attributes['class'] = '';
            $attributes['class'] += ' required';
        }
        return Form::label($this->name, $this->label, $attributes);
    }

    public function __toString()
    {
        return 'field not implemented';
    }
}