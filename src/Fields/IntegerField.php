<?php

namespace ModelForm\Fields;

use Illuminate\Support\Facades\Form;

class IntegerField extends Field
{
    public function getCleanedValue()
    {
        return is_numeric(parent::getCleanedValue()) ? (int)parent::getCleanedValue() : null;
    }
}