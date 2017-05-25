<?php
/**
*
*	This class will allow a modification to the element decorators
*	applied to the radio element
*
*/
class App_Form_Element_Radio extends Zend_Form_Element_Radio
{
    public function loadDefaultDecorators()
    {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->addDecorator('ViewHelper')
                ->addDecorator(new App_Form_Decorator_Label(array('tag' => 'dt',
                            'escape' => false,
                            'placement'=>Zend_Form_Decorator_Abstract::PREPEND)))
                ->addDecorator('HtmlTag', array('tag' => 'dd',
                            'id'  => $this->getName() . '-element'));

        }
    }
}
