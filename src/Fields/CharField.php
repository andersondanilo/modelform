<?php

namespace ModelForm\Fields;

use Illuminate\Support\Facades\Form;

class CharField extends Field
{
    public function __toString()
    {
        return Form::text($this->htmlName, $this->value, $this->attributes);
    }
}