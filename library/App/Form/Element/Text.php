<?php
/**
*
*	This class will allow modifications to the decorators used to
*	render the text element
*
*/
class App_Form_Element_Text extends Zend_Form_Element_Text
{

    public function loadDefaultDecorators()
    {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->addDecorator('ViewHelper')
                ->addDecorator('Description', array('tag' => 'p', 
                            'class' => 'description',
                            'placement'=>Zend_Form_Decorator_Abstract::PREPEND))
                ->addDecorator('HtmlTag', array('tag' => 'dd',
                                                'id'  => $this->getName() . '-element'))
                ->addDecorator(new App_Form_Decorator_Label(array('tag' => 'dt',
                            'escape' => false)));
        }
    }
}
