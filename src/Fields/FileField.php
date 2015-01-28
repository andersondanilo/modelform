<?php

namespace ModelForm\Fields;

use Illuminate\Support\Facades\Form;

class FileField extends Field
{
    public function getCleanedValue()
    {
        return $this->value;
    }
}