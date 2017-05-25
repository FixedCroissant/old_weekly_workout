<?php

class Form_Login extends App_Form
{
	public function __construct($options = null)
	{
		parent::__construct($options);

		$this->setAttrib('id','login_form')
			->setName('Login_Form');

		// element unityid
		$unityid = new App_Form_Element_Text('unityid');
		$unityid->setLabel('login_label_unityid')
				->setAttrib('size','15')
				->setAttrib('maxlength','8')
				->setRequired(true)
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->addFilter('alnum')
				->addFilter('StringToLower')
				->addValidator('NotEmpty');

		// element password
		$password = new App_Form_Element_Password('password');
		$password->setLabel('login_label_password')
				->setAttrib('size','15')
				->setRequired(true)
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->addValidator('NotEmpty');

		//element submit
		$login = new Zend_Form_Element_Submit('login');
		$login->setAttrib('id','login')
				->setLabel('login_button');

		//element array to add to form
		$elements = array($unityid, $password, $login);
		$this->addElements($elements);

		// add error summary decorator
		$this->addDecorator(new App_Form_Decorator_FormErrors(
					array('placement'=>Zend_Form_Decorator_Abstract::PREPEND,
						'message'=>'login_error_message')));
	}

}
