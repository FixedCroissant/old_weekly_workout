<?php

class App_Auth_Adapter implements Zend_Auth_Adapter_Interface
{
	private $_unityid;
	private $_password;

    /**
     * Sets userid and password for authentication
     *
     * @return void
     */
    public function __construct($unityid, $password)
    {
		$this->_unityid = $unityid;
		$this->_password = $password;
	}

	public function getIdentity(){
		return $this->_unityid;
	}

    /**
     * Performs an authentication attempt
     *
     * @throws Zend_Auth_Adapter_Exception If authentication cannot be performed
     * @return Zend_Auth_Result
     */
    public function authenticate()
    {
		$authAdapter = new Zend_Auth_Adapter_DbTable($db);
		$authAdapter->setTableName('users')
					->setIdentityColumn('unityid')
					->setCredentialColumn('password')
					->setCredentialTreatment('sha1(?)');



			if (!$ldap->bind())
		{
			// password is not valid...
			return new Zend_Auth_Result(
					Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID,
					array('unityid'=>$this->_unityid),
					array(App_Translate::translate('invalid credentials'))
					);
		}

		$model = new Model_User();

        // get the user info from the database (unityid is the user's ncsu unity id)
        $user = null;
        try
		{
            $user = $model->getUserById($this->_unityid);

        }
		catch (Exception $e)
		{
            return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND,
                array('unityid'=>$this->_unityid),
				array(App_Translate::translate('invalid unityid: does not exist')));
        }

		return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS,
                        array(
							'unityid'	=>	$this->_unityid,
							'firstname'	=>	$user['firstname'],
                        	'lastname'	=>	$user['lastname'],
							'user_role'	=>	$user['role']
							),
                        array(App_Translate::translate('successful login')));
    }
}
