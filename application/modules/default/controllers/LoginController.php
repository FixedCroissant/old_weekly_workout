<?php

class LoginController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        //Pull any messages out.
        //$this->view->messages = $this->_helper->flashMessenger->getMessages();
        $this->_helper->layout->setLayout('login');
        $this->view->headTitle('Login');

    }

    public function getForm()
    {
        return new Form_Login(array(
            'action' => 'index',
            'method' => 'post'
        ));
    }

    public function _getAuthAdapter($unityid, $password)
    {

        $db = Zend_Db_Table::getDefaultAdapter();
        $authAdapter = new Zend_Auth_Adapter_DbTable($db);
        $authAdapter->setTableName('users')
            ->setIdentityColumn('unityid')
            ->setCredentialColumn('password')
            ->setCredentialTreatment('sha1(?)');
        $registry = Zend_Registry::getInstance();
        $salt = $registry->config->salt;
        $password = $salt.$password;
        $authAdapter->setIdentity($unityid)
            ->setCredential($password);
        return $authAdapter;
    }

    public function indexAction()
    {
        // get the date ranges the app will be active
        // instantiate the view objects
        $datesModel = new Model_WorkoutDates();
        $numberOfWeeks = $datesModel->getNumberOfWeeks();
        $startDate = $datesModel->getStartingDate(1);
        $endDate = $datesModel->getEndingDate($numberOfWeeks);
        $start = new Zend_Date($startDate, Zend_Date::TIMESTAMP);
        $end = new Zend_Date($endDate, Zend_Date::TIMESTAMP);
        $now = new Zend_Date();
        $current = new Zend_Date($now, Zend_Date::TIMESTAMP);
        $this->view->current = $current->get(Zend_Date::DATE_LONG);
        //Get Workout Data.
        $workoutData = new Model_WorkoutData();
        //$grades = $this->_session->grades = $workoutData->getWeeklyGrade($week);

       // $weeklyWorkoutData = $workoutData->getAllWorkoutsByDate("jjwill10",$start->getTimestamp(),1);




        if ($current < $start || $current > $end)
        {
            $this->view->start = $start->get(Zend_Date::DATE_LONG);
            $this->view->end = $end->get(Zend_Date::DATE_LONG);
        }

        $form = $this->getForm();
        $this->view->form = $form;
        $request = $this->getRequest();
        //return var_dump($request->getRequestUri()=="/login/shibboleth");
       // return var_dump(Zend_Auth::getInstance()->hasIdentity());

        //Process the form or check if the person is logged in.
        if ($request->isPost())
        {
            $data = $form->getValues();

            if (!$form->isValid($_POST))
            {
                $form->populate($data);
                return $this->render('index');
            }

            $unityid = $form->getValue('unityid');
            $password = $form->getValue('password');

            //This weeks data.
            $weeklyWorkoutData = $workoutData->getThisWeeksWorkouts($unityid,$startDate,$endDate);

            // authentication process
            $authAdapter = $this->_getAuthAdapter($unityid, $password);

            // authenticate the user
            $auth = Zend_Auth::getInstance();

            $result = $auth->authenticate($authAdapter);

            //Handle independent logins.
            if ($result->isValid())
            {
                $storage = $auth->getStorage();
                $storage->write($authAdapter->getResultRowObject(array(
                    'unityid',
                    'firstname',
                    'lastname',
                    'email',
                    'role'
                )));

                $hrdata = new Model_HeartrateData();
                $row = $hrdata->getHeartrateData($unityid);


                // If heartrate data exists
                if ($row)
                {
                    /**
                     * Use the following workaround to solve the issue of storing an array of data in the Zend Session
                     * component for use in other areas of the application. First, create the Zend Session Namespace,
                     * then create the array, and finally iterate through the array data that you'd like to store.
                     * The trick is to store the array data in a temporary array then assign that array to the
                     * namespace array.
                     */

                    $ns2 = new Zend_Session_Namespace('HeartrateData');
                    $ns2->array = array();
                    $tmp2 = $ns2->array;
                    foreach ($row as $key => $value)
                    {
                        $tmp2[$key] = $value;
                    }
                    $ns2->array = $tmp2;

                    //Reset values for the view.
                    $this->view->start = $start;
                    $this->view->end = $end;
                    $this->view->ns2 = $ns2;
                    $this->view->thisWeeksWorkouts = $weeklyWorkoutData;

                    return $this->_helper->redirector('index','index');
                    //return $this->_forward('index','index');
                }
                // if no heartrate data exists
                else
                {
                    return $this->_helper->redirector('add','calculator');
                }
            }

            //Redirect based off of invalid credentials.
            else
            {
                // invalid credentials
                $form->populate(array('unityid' => $unityid));
                $this->view->formError = 'Invalid username and/or password';
                return $this->render('index');
            }

        }

        //Login Via Shibboleth.
        else if(Zend_Auth::getInstance()->hasIdentity() && $request->getRequestUri() == "/login/shibboleth" ) {

            //else {
            //get requested URI.
            //$requestShibboleth = $this->getRequest()->getRequestUri();
            //return var_dump($request);

            //return var_dump(!Zend_Auth::getInstance());

            //return var_dump($request->isPost());
            //Check if logged in.
            //return var_dump(Zend_Auth::getInstance());

            //check already logged in people.
            $adapter = $this->_getAuthAdapter("jjwill10","password");
            $auth    = Zend_Auth::getInstance();
            //Check if they have an identity.
            $result  = $auth->authenticate($adapter);
            //If valid authentication....
            if ($result->isValid()) {
                //Get UnityID
                $loggedInUnityID  = $auth->getIdentity();
                //Grab this weeks data.
                $weeklyWorkoutData = $workoutData->getThisWeeksWorkouts($loggedInUnityID,$startDate,$endDate);

                //Pulling correct information.
                //return print_r($adapter->getResultRowObject(array('unityid','firstname','lastname','email')));

                $this->view->form = $form;
                $storage = $auth->getStorage();
                $storage->write($adapter->getResultRowObject(array(
                    'unityid',
                    'firstname',
                    'lastname',
                    'email',
                    'role'
                )));

                $hrdata = new Model_HeartrateData();
                //Get logged in user's HR data (if any).
                $row = $hrdata->getHeartrateData($loggedInUnityID);

                // If heartrate data exists
                if ($row)
                {
                    /**
                     * Use the following workaround to solve the issue of storing an array of data in the Zend Session
                     * component for use in other areas of the application. First, create the Zend Session Namespace,
                     * then create the array, and finally iterate through the array data that you'd like to store.
                     * The trick is to store the array data in a temporary array then assign that array to the
                     * namespace array.
                     */

                    $ns2 = new Zend_Session_Namespace('HeartrateData');
                    $ns2->array = array();
                    $tmp2 = $ns2->array;

                    foreach ($row as $key => $value)
                    {
                        //echo $value;
                        $tmp2[$key] = $value;
                    }
                    $ns2->array = $tmp2;

                    //below properly provides information.
                    //return var_dump($ns2->array);

                    //Reset values for the view.
                    $this->view->start = $start;
                    $this->view->end = $end;
                    $this->view->ns2 = $ns2;
                    //return var_dump($weeklyWorkoutData);
                    $this->view->thisWeeksWorkouts = $weeklyWorkoutData;

                    return $this->_helper->redirector('index','index');
                    //return $this->_forward('index','index');
                }
                // if no heartrate data exists
                else
                {
                     $this->_helper->redirector('add','calculator');
                     //$this->_forward('add','calculator');
                }

                //return $this->_helper->redirector('add','calculator');
                return $this->_forward('add','calculator');
            }
        }
        //End handle logins via shibboleth.

        $this->view->form = $form;
    }

    public function logoutAction()
    {
        //Sessions are used within the namespace so must clear out the cookies.
        Zend_Session::destroy(true,false);
        Zend_Auth::getInstance()->clearIdentity();
        //$this->_helper->flashMessenger->addMessage('Successfully logged out.');
        $this->_helper->redirector('index'); // back to login page
    }

    public function shibbolethAction(){
        //Function automatically goes to the /controllerName/action. within scripts.

        //CALL SHIB.
        //Get Shibboleth adapter from DB.
        //$shibDB = Zend_Db_Table::getDefaultAdapter();
        //Create new Adapter for Shibboleth.
        // library/App/NCSU/Adapter/ShibAuthAdapter.
        //Is loading the proper class, to se
        //$shibAuth =  new App_NCSU_Adapter_ShibAuthAdapter();
       //Un comment to see it is loading the appropriate class.
        //return var_dump($shibAuth->authenticate());
        //END CALL SHIB.

        //Get Current Term
        $currentSemester = $this->getCurrentTerm();
        //Get String Representation
        $currentSemesterString = $this->getTermString($currentSemester);

        //Set up account data for use with the creation of the account.
        //This is a temporary account, it will eventually be data through Shibboleth.
        $acctData = array(
            'timestamp'=>date("Y-m-d H:i:s",time()),
            'unityid'=>'jjwill10',
            'password'=>'',
            'firstname'=>'fakefirstname',
            'lastname'=>'fakelastname',
            'email'=>'fakeemail@fakeemail.com',
            'role'=>'student',
            'studentid'=>'123456789',
            'term'=>$currentSemester,
            'stringSemester'=>$currentSemesterString
        );
        //End temporary account.


        $registry = Zend_Registry::getInstance();
        $password = '1234567';
        $salt = $registry->config->salt;

        //Create a new user.
        $userModel = new Model_User();

        $myusers = new Model_User();
        $row = $myusers->fetchRow($myusers->select()->where('unityid = ?','jjwill10'));
        //Get Count
        $userExist = count($row);

        //Find user information, specifically the unity id.
        //$userInformation = $row->unityid;

        //First check for existence, if not, then create.
        if($userExist==1) {
            //$this->_helper->flashMessenger->addMessage('There is an account that already exists with this user.');

            //Log in as the user.
            //Will pull information from Shibboleth when needed.
            $adapter  = $this->_getAuthAdapter('jjwill10','password');
            $auth = Zend_Auth::getInstance();
            //Authenticate
            $result = $auth->authenticate($adapter);
            //return var_dump($result->isValid());

            if ($result->isValid()) {
                //pulls correctly.
                //$identity = $auth->getIdentity();
                //Pulls correctly.
                //return var_dump($identity);

                //store in storage..
                $storage = $auth->getStorage();
                $storage->write($adapter->getResultRowObject(array(
                    'unityid',
                    'firstname',
                    'lastname',
                    'email',
                    'role'
                )));

                $this->view->storage;

                //end store in storage.
                //return var_dump($storage->read());


                //$this->_helper->FlashMessenger('Successful Login');
                $this->_forward('index','Login','default');

            }
            else{
                return var_dump('provided credentials are not acceptable.');
            }
            //End log-in of the user.
        }
        else{
            // create an instance of the user model and add the user to db
            $userModel = new Model_User();
            $userModel->createUser($acctData['timestamp'] ,
                $acctData['unityid'],
                $acctData['studentid'],
                $salt.$password,
                $acctData['firstname'],
                $acctData['lastname'],
                $acctData['email'],
                $acctData['role'],
                $acctData['term'],
                $acctData['stringSemester']
            );

            //Use in place of a  flash message.
            $this->view->hasAccountCreated = TRUE;
        }


        //Go back to main index.
        return $this->_forward('index');
    }

    /*
     * Function that takes the current date and provides a numerical value of what the
     * current term is.
     *  @return string
     */
     private function getCurrentTerm(){
                // SET THE CURRENT TERM BASED ON TODAY'S DATE
                $month=date("n");
                $day=date("j");

                // FIRST 4 MONTHS ARE SPRING TERM
                if ($month <= 4) { $termmonth=1; }
                // SPLIT MAY INTO SPRING AND SUMMER I TERMS
                elseif ($month == 5 && $day <= 15) { $termmonth=1; }
                elseif ($month == 5 && $day > 15) { $termmonth=6; }
                // SPLIT JUNE INTO SUMMER I AND SUMMER II TERMS
                elseif ($month == 6 && $day <= 25) { $termmonth=6; }
                elseif ($month == 6 && $day > 25) { $termmonth=7; }
                // ALL OF JULY IS SUMMER II TERM
                elseif ($month == 7) { $termmonth=7; }
                // REMAINING MONTHS ARE FALL TERM
                else { $termmonth=8; }

         return $term=2 . date("y") . $termmonth;
     }
     /**
      * Function that takes numerical semester stamp and returns a proper string representation.
      * @param  semesterNumericalDate String
      * @return String
      */
      private function getTermString($numberTerm){

          $semester = substr($numberTerm,3,1);
          $year =    substr($numberTerm,1,2);

          //return $semester. ' ' .$year;

          switch($semester){
                //Spring Terms
                    case "1":
                    $stringTerm = "Spring";
                    break;
                //Summer I Terms
                    case "6":
                    $stringTerm = "Summer I";
                    break;
                //Summer II Terms
                    case "7":
                    $stringTerm = "Summer II";
                    break;
                //Fall Terms
                    case "8":
                    $stringTerm = "Fall";
                    break;
                //Default
               default:
                    $stringTerm = "defaultText";
          }
          return $stringTerm. " "."20".$year;
      }
}
