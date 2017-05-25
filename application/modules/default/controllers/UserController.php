<?php

class UserController extends Zend_Controller_Action
{

	protected $_flashMessenger = NULL;

	/**
	 * _flashMessage 
	 *
	 *	Sets up the flash messenger to record and publish errors
	 * 
	 * @param mixed $message 
	 * @return void
	 */
	protected function _flashMessage($message) {
			$flashMessenger->addMessage($message);
	}


	public function init()
	{
		$this->view->pageHeader = "Update Password";
		$this->view->headTitle('Update Password');
	}

	public function getForm()
	{
		return new Form_UpdatePassword(array(
			'action' => 'update',
			'method' => 'post'
			));
	}

	public function indexAction()
	{
		$this->_redirect('user/update');
	}

	public function updateAction()
	{
		// setup the flashmessenger to send errors to the form
		$flashMessenger = $this->_helper->FlashMessenger;
		$flashMessenger->setNamespace('passwordErrors');
		$this->view->flashmessages = $flashMessenger->getMessages();

		// get the unityid from Zend_Auth
		$auth = Zend_Auth::getInstance();
		$unityid = $auth->getIdentity()->unityid;
		$this->view->unityid = $unityid;

		// get the update password form
		$form = $this->getForm();
		$request = $this->getRequest();
		
		if ($request->isPost())
		{
			if ($form->isValid($_POST))
			{
				$password1 = $form->getValue('password1');
			//	$password2 = $form->getValue('password2');
			//	if (!$password1 == $password2)
			//	{
			//		$this->_flashMessage("The passwords are not identical");
			//		$this->render('index');
			//		return;
			//	}
				
				// update the password in the database
				$userModel = new Model_User();
				$result = $userModel->updatePassword($unityid, $password1);
				if (!$result)
				{
					$this->_flashMessage("Your password could not be updated!");
					$this->render('update');
					return;
				}

				$this->_redirect('/user/confirm');
			}
		}

		$this->view->form = $form;
	}

	public function confirmAction()
	{


	}


}
