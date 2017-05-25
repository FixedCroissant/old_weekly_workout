<?php
class Form_User extends App_Form
{
	public function __construct($options = null)
	{
		parent::__construct($options);

		$this->setAttrib('id','add_user')
			->setName('AddUser');

		// element: unityid
		$unityid = new App_Form_Element_Text('unityid');
		$unityid->setLabel('adduser_unityid')
				->setAttrib('size','9')
				->setAttrib('maxlength','9')
				->setRequired('true')
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->addValidator('NotEmpty',true);

		// element: studentid
		$studentid = new App_Form_Element_Text('studentid');
		$studentid->setLabel('adduser_studentid')
				->setAttrib('size','9')
				->setAttrib('maxlength','9')
				->setRequired('true')
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->addValidator('NotEmpty',true);

		// element: password
		$password1 = new App_Form_Element_Password('password1');
		$password1->setLabel('adduser_password1')
				->setAttrib('size','15')
				->setRequired(true)
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->addValidator('NotEmpty');

		// confirm password element
		$password2 = new App_Form_Element_Password('password2');
		$password2->setLabel('adduser_password2')
				->setAttrib('size','15')
				->setRequired(true)
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->addValidator('Identical')
				->addValidator('NotEmpty')
				->getValidator('Identical')
					->setMessage(' - The passwords do not match!');

		// element: firstname
		$firstname = new App_Form_Element_Text('firstname');
		$firstname->setLabel('adduser_firstname')
				->setAttrib('size', '30')
				->setRequired(true)
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->addValidator('NotEmpty',true);

		// element: lastname
		$lastname = new App_Form_Element_Text('lastname');
		$lastname->setLabel('adduser_lastname')
				->setAttrib('size', '30')
				->setRequired(true)
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->addValidator('NotEmpty',true);

		// element: email address
		$email = new App_Form_Element_Text('email');
		$email->setLabel('adduser_email')
			->setAttrib('size', '30')
			->setRequired(true)
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->addValidator('EmailAddress')
			->getValidator('EmailAddress')
				->setMessage(' - The email address is not valid!');

		// element: role
		$role = new Zend_Form_Element_Select('role');
		$role->setLabel('adduser_role')
			->addMultiOption('student', 'Student')
			->addMultiOption('admin', 'Administrator')
			->setValue('Student');

		// add user button
		$adduser = new Zend_Form_Element_Submit('add');
		$adduser->setAttrib('id','add')
				->setLabel('adduser_button');
		
		// add elements to the form
		$elements = array($unityid, $studentid, $password1, $password2, $firstname, $lastname, $email, $role, $adduser);
		$this->addElements($elements);

		// add error summary decorator
		$this->addDecorator(new App_Form_Decorator_FormErrors(
					array('placement'=>Zend_Form_Decorator_Abstract::PREPEND,
						'message'=>'login_error_message')));

	}

	public function isValid($data)
	{
		$password2 = $this->getElement('password2');
		$password2->getValidator('Identical')->setToken($data['password1']);
		return parent::isValid($data);
	}
}
