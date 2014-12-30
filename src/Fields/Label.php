<?php

namespace ModelForm\Fields;

class Label
{
    public function $text;

    public function __toString() {
        return $text;
    }
}