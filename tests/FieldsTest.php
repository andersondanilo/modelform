<?php

namespace ModelForm\Test;

use ModelForm\Fields\CharField;
use ModelForm\Form;

class FieldsTest extends TestCase
{
    public function testFieldAcceptNameAndLabel()
    {
        $form = new Form();
        $form->name = new CharField(['label'=>'Testing']);
        $this->assertEquals('name', $form->name->name);
        $this->assertEquals($form, $form->name->form);
        $this->assertEquals('Testing', $form->name->label);
    }

    public function testFieldRenderHtmlOptions() {
        $form = new Form();
        $form->description = new CharField(['label'=>'Description']);
        $htmlOptions = [
            'maxlength' => 10,
        ];
        $this->assertEquals(
            '<input maxlength="10" name="Form-description" type="text">',
            $form->description->text($htmlOptions)
        );
    }
}