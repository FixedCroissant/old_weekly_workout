<?php

class App_Auth_Storage_Session implements Zend_Auth_Storage_Interface
{
    protected $_session;

    public function __construct($session)
    {
        $this->_session  = $session;
    }

    /**
     * Defined by Zend_Auth_Storage_Interface
     *
     * @return boolean
     */
    public function isEmpty()
    {
        return !isset($this->_session->{'unityid'});
    }

    /**
     * Defined by Zend_Auth_Storage_Interface
     *
     * @return mixed
     */
    public function read()
    {
        return array(
                'unityid'=>$this->_session->{'unityid'},
                'firstname'=>$this->_session->{'firstname'},
                'lastname'=>$this->_session->{'lastname'},
                'role'=>$this->_session->{'role'});
    }

    /**
     * Defined by Zend_Auth_Storage_Interface
     *
     * @param  mixed $contents
     * @return void
     */
    public function write($contents)
    {
        //print_r($contents);
        
        $this->_session->{'unityid'} = $contents['unityid'];
        $this->_session->{'firstname'} = $contents['firstname'];
        $this->_session->{'lastname'} = $contents['lastname'];
        $this->_session->{'role'} = $contents['role'];
    }

    /**
     * Defined by Zend_Auth_Storage_Interface
     *
     * @return void
     */
    public function clear()
    {
        unset($this->_session->{'unityid'});
        unset($this->_session->{'name'});
        unset($this->_session->{'role'});
    }

    public function getUserName()
    {
        return $this->_session->{'firstname'};
    }

    public function setUserName($firstname)
    {
        $this->_session->{'firstname'}=$firstname;
    }

    public function getUserId()
    {
        return $this->_session->{'unityid'};
    }

    public function getUserRole()
    {
        return $this->_session->{'role'};
    }
}
