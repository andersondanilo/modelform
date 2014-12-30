<?php

namespace ModelForm\Fields;

use Form; // use laravel form builder

class CharField extends Field
{
    public function __toString()
    {
        return Form::text($this->name, $this->value, $this->attributes);
    }
}