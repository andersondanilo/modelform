<?php

namespace ModelForm\Test;

use ModelForm\Fields\CharField;
use ModelForm\FormSet;
use ModelForm\Form;
use Illuminate\Database\Eloquent\Model as Eloquent;

class MyModel extends Eloquent {
    public $id;
    public $name;
    public $age;
}

class MyForm extends Form
{
    public function makeFields()
    {
        $this->name = new CharField(['label'=>'My name']);
        $this->age = new CharField(['label'=>'My age']);
    }
}

class MyFormSet extends FormSet
{
    public function makeForm()
    {
        return new MyForm();
    }
}

class MyModelForm extends Form
{
    public function makeModel() 
    {
        return new MyModel();
    }

    public function makeFields()
    {
        $this->name = new CharField(['label'=>'My name']);
        $this->age = new CharField(['label'=>'My age']);
    }
}

class MyModelFormSet extends FormSet
{
    public function makeForm()
    {
        return new MyModelForm();
    }
}

class FormSetTest extends TestCase
{
    public function testSetFormSet()
    {
        $formSet = new MyFormSet();
        $this->assertEquals(1, count($formSet));
        $this->assertEquals('MyForm-0-name', $formSet[0]->name->getHtmlName());

        $formSet->setData(array(
            'MyForm-0-name' => 'Position 0',
            'MyForm-0-age' => '0',

            'MyForm-2-name' => 'Position 2',
            'MyForm-2-age' => '2',

            'MyForm-1-name' => 'Position 1',
        ));

        $this->assertEquals(3, count($formSet));

        $this->assertEquals('Position 0', $formSet[0]->name->value);
        $this->assertEquals('0', $formSet[0]->age->value);

        $this->assertEquals('Position 2', $formSet[1]->name->value);
        $this->assertEquals('2', $formSet[1]->age->value);

        $this->assertEquals('Position 1', $formSet[2]->name->value);
    }

    public function testModelFormSet() 
    {
        $formSet = new MyModelFormSet();
        $this->assertEquals(1, count($formSet));
        $this->assertEquals('MyModelForm-0-name', $formSet[0]->name->getHtmlName());

        $formSet->setData(array(
            'MyModelForm-0-id' => '',
            'MyModelForm-0-name' => 'Position 0',
            'MyModelForm-0-age' => '0',

            'MyModelForm-2-id' => '',
            'MyModelForm-2-name' => 'Position 2',
            'MyModelForm-2-age' => '2',

            'MyModelForm-1-id' => '',
            'MyModelForm-1-name' => 'Position 1',
        ));

        $this->assertEquals(3, count($formSet));

        $this->assertEquals('Position 0', $formSet[0]->name->value);
        $this->assertEquals('0', $formSet[0]->age->value);

        $this->assertEquals('Position 2', $formSet[1]->name->value);
        $this->assertEquals('2', $formSet[1]->age->value);

        $this->assertEquals('Position 1', $formSet[2]->name->value);
    }
}