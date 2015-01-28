<?php

namespace ModelForm\Test;

use ModelForm\Fields\CharField;
use ModelForm\Form;

class FieldsTest extends TestCase
{
    public function testCreateField()
    {
        $form = new Form();
        $form->name = new CharField(['label'=>'Testing']);

        $this->assertEquals('name', $form->name->name);
        $this->assertEquals($form, $form->name->form);
        $this->assertEquals('Testing', $form->name->label);
    }
}