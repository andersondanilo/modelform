<?php

namespace ModelForm\Fields;

use ModelForm\Fields\Label;

class Field
{
    public $form;
    public $name;
    public $attributes;

    public function __construct(array $params)
    {
        $this->label = new Label();
        $this->label->field = $this;

        if(isset($params['label']))
            $this->label->text = $params['label'];
    }

    public function __get($name)
    {
        if($name == 'value')
            return $this->form->getValue($name);
        trigger_error("Undefined property '$name'", E_USER_ERROR);
    }

    public function __invoke(array $attributes)
    {
        foreach($attributes as $key => $value)
            $this->attributes[$key] = $value;
        return $this;
    }

    public function __toString()
    {
        return 'field not implemented';
    }
}