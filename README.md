What is
=======
ModelForm is a PHP Form Abstraction for Laravel based on Django Forms and Formset, but completely integrated with the Laravel FormBuilder.

Instalation
===========
To get the latest version of ModelForm simply require it in your composer.json file

    php composer.phar require "andersondanilo/modelform:dev-master"

Examples of usage
================

Simple example: Without model
-----------------------------

```php
// SimpleForm.php
use ModelForm\Form;
use ModelForm\Fields\CharField;
use ModelForm\Fields\IntegerField;

class SimpleForm extends Form
{
    public function makeFields()
    {
        $this->name = new CharField(['label'=>'Name']);
        $this->age = new IntegerField(['label'=>'Name']);
    }
}
```
    
Instancializing model

```php
$model = new SimpleForm(['data' => Input::old() ?: Input::all()]);
```
    
Rendering model in view

```php
    {{ $informationForm->name->label() }}:
    {{ $informationForm->name->text(['class'=>'form-control']) }}
```
    
Acessing values after

```php
    $name = $informationForm->name->value;
```

With Model and Validator
------------------------

```php
use ModelForm\Form;
use ModelForm\Fields\CharField;
use ModelForm\Fields\IntegerField;

class SimpleForm extends Form
{
    public function makeFields()
    {
        $this->name = new CharField(['label'=>'Name']);
        $this->age = new IntegerField(['label'=>'Name']);
    }
    
    public function makeModel()
    {
        return new MyModel();
    }
    
    public function makeValidator($data)
    {
        return Validator::make($data, [
            'name' => 'required'
        ]);
    }
}
```
Validating
