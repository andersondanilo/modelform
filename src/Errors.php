<?php

namespace ModelForm;

use Illuminate\Support\Collection;

class ErrorList extends Collection
{
    public $errorClass = 'errorlist';

    public function asUl()
    {
        $result = "<ul class=\"$this->errorClass\">";
        foreach($this as $error) {
            $result .= "<li>$error</li>";
        }
        $result .= '</ul>';
        return $result;
    }

    public function __toString()
    {
        return $this->asUl();
    }
}