<?php

namespace ModelForm\Test;

use ModelForm\Fields\CharField;
use ModelForm\Form;

class TestSingleForm extends Form
{
    public function makeFields()
    {
        $this->name = new CharField(['label'=>'My name']);
        $this->age = new CharField(['label'=>'Age']);
    }
}

class FormTest extends TestCase
{
    public function testCreateField()
    {
        $form = new TestSingleForm();
        $this->assertEquals(2, count($form));
        $this->assertEquals('TestSingleForm-name', $form->name->getHtmlName());

        $form->setData(array(
            'TestSingleForm-name' => 'Testing',
        ));

        $this->assertEquals('Testing', $form->name->value);
    }
}