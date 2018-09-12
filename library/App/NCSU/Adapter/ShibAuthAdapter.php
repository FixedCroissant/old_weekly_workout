<?php

/**
 * Zend Framework 1.11 Authentication adapter for Shibboleth Login
 *
 * @author Joshua Williams <jjwilli10@ncsu.edu>
 */

class App_NCSU_Adapter_ShibAuthAdapter implements Zend_Auth_Adapter_Interface
{
    /**
     * Sets username and password for authentication.
     * @return void
     */
    public function _construct($unityID){

        $this->_unityID = $unityID;
    }

    /**
     * Performs an authentication attempt.
     *
     * @throws Zend_Auth_Adapter_Exception If authentication cannot be performed.
     * @return Zend_Auth_Result
     */
    public function authenticate()
    {
        if (headers_sent()) {
            throw new RuntimeException("You must call this adapter before headers are sent.");
        }
        //Check if Shibboleth is already active.
        if(is_null($this->getShibbolethIdentifier())){
            setrawcookie('SHIB_REFERER',$this->request->getCurrentUri(),0,'/','.ncsu.edu',true,true);
            header(sprintf("Location: %s", App_NCSU_Adapter_ShibAuthAdapter::IDP));
            exit();
        }
        //Get Shibboleth Identification
        $shibIdenitier = $this->getShibbolethIdentifier();

        if(empty($shibIdenitier)){
            return new Zend_Auth_Result(Zend_Auth_Result::FAILURE,$shibIdenitier);
        }
        //By default, if not empty, set auth result as successful.
        return new Zend_Auth_Result(Zend_Auth_result,$shibIdenitier);

    }



    /**
     * Provide Shibboleth information from the server.
     */
    private function getShibbolethIdentifier(){
        if (isset($this->request->server['SHIB_UID'])) {
            return $this->request->server['SHIB_UID'];
        }

        if (isset($this->request->server['REDIRECT_SHIB_UID'])) {
            return $this->request->server['REDIRECT_SHIB_UID'];
        }

        return null;
    }

}
