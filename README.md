![Travis](https://travis-ci.org/andersondanilo/modelform.svg?branch=master)
[![Code Climate](https://codeclimate.com/github/andersondanilo/modelform/badges/gpa.svg)](https://codeclimate.com/github/andersondanilo/modelform)
[![Test Coverage](https://codeclimate.com/github/andersondanilo/modelform/badges/coverage.svg)](https://codeclimate.com/github/andersondanilo/modelform)
[![Latest Stable Version](https://poser.pugx.org/andersondanilo/modelform/v/stable.svg)](https://packagist.org/packages/andersondanilo/modelform)
[![Latest Unstable Version](https://poser.pugx.org/andersondanilo/modelform/v/unstable.svg)](https://packagist.org/packages/andersondanilo/modelform) 
[![License](https://poser.pugx.org/andersondanilo/modelform/license.svg)](https://packagist.org/packages/andersondanilo/modelform)

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
        $this->name = new CharField(['label' => 'Name']);
        $this->age = new IntegerField(['label' => 'Age']);
    }
}
```
    
Instancializing Form

```php
$simpleForm = new SimpleForm(['data' => Input::old() ?: Input::all()]);
```
    
Rendering model in view

```php
    {{ $simpleForm->name->label() }}:
    {{ $simpleForm->name->text(['class' => 'form-control']) }}
```
    
Acessing values after

```php
    $name = $simpleForm->name->value;
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
        $this->name = new CharField(['label' => 'Name']);
        $this->age = new IntegerField(['label' => 'Age']);
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

You can instancialize without model and then the model is created by form, or start with a already existing model.

```php
$model10 = MyModel::find(10);
$form = new SimpleForm(['model' => $model10, 'data' => Input::old() ?: Input::all()]);
```

Validating

```php
if(!$simpleForm->isValid()) {
    return Redirect::back()->withErrors($simpleForm->errors())->withInput();
}
```

Saving your model
```php
$simpleForm->save();
```

Formsets
--------
```php
use ModelForm\FormSet;

class SimpleFormSet extends FormSet
{
    public function makeForm($model=null)
    {
        return new SimpleForm(['model'=>$model]);
    }
}
```

Create the empty formset instance:
```php
$simpleFormSet = new SimpleFormSet(['data' => Input::old() ?: Input::all());
```

Or create a formset filled with a model relation:
```php
$addressFormSet = new AddressFormSet(['relation'=>$customer->addresses(), 'data' => Input::old() ?: Input::all());
```

The validation and saving of formset is the symmetric with form.
```php
if(!$addressFormSet->isValid()) {
    return Redirect::back()->withErrors($addressFormSet->errors())->withInput();
}
$addressFormSet->save();
```
