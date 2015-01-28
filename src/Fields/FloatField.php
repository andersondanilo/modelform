<?php

namespace ModelForm\Fields;

use Illuminate\Support\Facades\Form;

class FloatField extends Field
{
    public function getCleanedValue()
    {
        return (float)str_replace(',','.',parent::getCleanedValue());
    }
}