<?php

class Admin_UserController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
		$this->view->headTitle('Manage Users');
    }

    public function indexAction()
    {
        $this->_helper->redirector('list');
    }

    public function addAction()
    {
        $this->view->pageHeader = 'Add New User';
        $form = new Form_User();
        if ($this->getRequest()->isPost())
        {
            if ($form->isValid($_POST)) 
            {
                // get the salt value from the config file
                $registry = Zend_Registry::getInstance();
                $salt = $registry->config->salt;

                // get the form values needed for the model
                $time = date("Y-m-d H:i:s",time());
                $unityid = $form->getValue('unityid');
                $studentid = $form->getValue('studentid');
                $password = $form->getValue('password1');
                $password = $salt.$password;
                $firstname = $form->getValue('firstname');
                $lastname =	$form->getValue('lastname');
                $email = $form->getValue('email');
                $role = $form->getValue('role');
                $term = $form->getValue('semester');
                $semesterOptions = $form->semester->getMultiOptions();
                //Get String Representations of the Dropdown.
                $semesterTerm = $semesterOptions[$term];

                // create an instance of the user model and add the user to db
                $userModel = new Model_User();
                $userModel->createUser(
                                        $time,
                                        $unityid,
                                        $studentid,
                                        $password,
                                        $firstname,
                                        $lastname,
                                        $email,
                                        $role,
                                        $term,
                                        $semesterTerm);
                return $this->_forward('list');
            }
        }
        $form->setAction('add');
        $this->view->form = $form;
    }

    public function listAction()
    {
            $this->view->pageHeader = 'Current Users';
            $currentUsers = Model_User::getUsers();
            if ($currentUsers->count() > 0)
            {
                    $this->view->users = $currentUsers;
            }
            else
            {
                    $this->view->users = null;
            }
    }

    public function deleteAction()
    {
     if ($this->getRequest()->isPost()) {
            $del = $this->getRequest()->getPost('del');
            if ($del == 'Yes') { 
                $unityid = $this->getRequest()->getPost('unityid');
                $user = new Model_User();
                $hrdata = new Model_HeartrateData();
                $wkdata = new Model_WorkoutData();
                $wkdata->deleteAllWorkoutsForStudent($unityid);
                $hrdata->deleteHeartrateData($unityid);
                $user->deleteUser($unityid);
            }
            $this->_helper->redirector('list');
        } else {
            $unityid = $this->_getParam('unityid');
            $userModel = new Model_User();
            $user = $userModel->getUserById($unityid);
            $this->view->user = $user;
        } 
    }
    
    /** This function will allow the admin to select a student 
     * from the roster and display a page that contains the students'
     *  heartrate and workout data.
     * @param string $unityid The user's Unity ID
     * @param array $user The user data
     * @param array $hr The heart rate data for the user
     * @param array $wkdata The workout data for the user
     */
    public function viewAction() {
      
        $unityid = $this->_getParam('unityid');
        $userModel = new Model_User();
        $user = $userModel->getUserById($unityid);
        $hrModel = new Model_HeartrateData();
        $hr = $hrModel->getHeartrateData($unityid);
        $wkdataModel = new Model_WorkoutData();
        $wkdata = $wkdataModel->getAllWorkouts($unityid);
        // send data to the view
        $this->view->pageHeader = 'Student Workouts';
        $this->view->user = $user;
        $this->view->hr = $hr;
        $this->view->wkdata = $wkdata;
     
      
    }
    /**
     * This method will allow the admin to edit/update the user info
     * for a selected user
     * 
     */
    public function updateAction()
    {
        $this->view->pageHeader = 'Update User';
        $form = new Form_User();
        if ($this->getRequest()->isPost())
        {
            if ($form->isValid($_POST)) 
            {
                // get the salt value from the config file
                $registry = Zend_Registry::getInstance();
                $salt = $registry->config->salt;

                // get the form values needed for the model
                $unityid = $form->getValue('unityid');
                $studentid = $form->getValue('studentid');
                $password = $form->getValue('password1');
                $password = $salt.$password;
                $firstname = $form->getValue('firstname');
                $lastname =	$form->getValue('lastname');
                $email = $form->getValue('email');
                $term = $form->getValue('semester');
                //DropDown options.
                $semesterOptions = $form->semester->getMultiOptions();
                //Get String Representations of the Dropdown.
                $semesterTerm = $semesterOptions[$term];
                $role = $form->getValue('role');


                // create an instance of the user model and add the user to db
                $userModel = new Model_User();
                $userModel->updateUser( $unityid,
                                        $studentid,
                                        $firstname,
                                        $lastname,
                                        $password,
                                        $email,
                                        $role,
                                        $term,
                                        $semesterTerm
                                        );
                return $this->_forward('list');
            }
        }
        // get the current user info from the database
        $unityid = $this->_getParam('unityid');
        $userModel = new Model_User();
        $user = $userModel->getUserById($unityid);
        
        // insert the current values into the form
        $form->getElement('unityid')->setValue($user['unityid']);
        $form->getElement('studentid')->setValue($user['studentid']);
        $form->getElement('first_name')->setValue($user['first_name']);
        $form->getElement('last_name')->setValue($user['last_name']);
        $form->getElement('email')->setValue($user['email']);
        $form->getElement('add')->setLabel('update_user');
        
        // send the form to the view object
        $form->setAction('update');
        $this->view->form = $form;
    }
}

