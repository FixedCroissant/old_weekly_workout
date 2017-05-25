<?php
/**
*
*	This class is used to modify the default decorators used in rendering
*	the form labels
*
*/
class App_Form_Decorator_Label extends Zend_Form_Decorator_Label
{
    /**
     * Get class with which to define label
     *
     * Appends 'error' to class, if there is an error in the form for the
     * associated element
     *
     * @return string
     */
    public function getClass()
    {
        $class = parent::getClass();

        $element = $this->getElement();

        if ($element->hasErrors()){
            if (!empty($class)){
                $class .= ' invalid';
            }else{
                $class = 'invalid';
            }
        }
       
        return $class;
    }
}
