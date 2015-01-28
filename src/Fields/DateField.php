<?php

namespace ModelForm\Fields;

use Illuminate\Support\Facades\Form;

class DateField extends Field
{
    public $_dateFormat = 'Y-m-d';

    public function getCleanedValue()
    {
        return $this->value ? \DateTime::createFromFormat($this->_dateFormat, $this->value) : null;
    }
}