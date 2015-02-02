<?php

namespace ModelForm\Test;

use ModelForm\Fields\CharField;
use ModelForm\Form;

class FormBuilderTest extends TestCase
{
    public function toDomElement($html)
    {
        $doc = new \DOMDocument();
        $doc->loadHTML($html);
        $body = $doc->getElementsByTagName('body')->item(0);
        return $body->childNodes->item(0);
    }

    public function testTextInput()
    {
        $form = new Form();
        $form->name = new CharField(['label'=>'Testing']);
        
        $element = $this->toDomElement($form->name->text());
        $this->assertEquals('input', $element->tagName);
        $this->assertEquals('Form-name', $element->getAttribute('name'));
        $this->assertEquals('', $element->getAttribute('value'));

        $form->setData(['Form-name' => 'Jack']);

        $element = $this->toDomElement($form->name->text());
        $this->assertEquals('Jack', $element->getAttribute('value'));
    }

    public function testEmailInput()
    {
        $form = new Form();
        $form->email = new CharField(['label'=>'My Email']);
        
        $element = $this->toDomElement($form->email->email());
        $this->assertEquals('input', $element->tagName);
        $this->assertEquals('Form-email', $element->getAttribute('name'));
        $this->assertEquals('email', $element->getAttribute('type'));
        $this->assertEquals('', $element->getAttribute('value'));

        $form->setData(['Form-email' => 'Jack']);

        $element = $this->toDomElement($form->email->text());
        $this->assertEquals('Jack', $element->getAttribute('value'));
    }
}