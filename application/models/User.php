<?php

class Model_User extends Zend_Db_Table_Abstract
{
	protected $_name = 'users';
	protected $_primary = 'unityid';

	public function createUser ($timesstamp,$unityid, $studentid, $password, $firstname, $lastname, $email, $role,$semester,$semesterString)
	{
		// create a new row
		$rowUser = $this->createRow();
		if($rowUser)
		{
		    $rowUser->created_at = $timesstamp;
			$rowUser->unityid    = $unityid;
			$rowUser->studentid  = $studentid;
			$rowUser->password   = sha1($password);
			$rowUser->first_name  = $firstname;
			$rowUser->last_name   = $lastname;
			$rowUser->email      = $email;
			$rowUser->role       = $role;
			$rowUser->semester   = $semester;
			$rowUser->semesterString = $semesterString;
			$rowUser->save();
			// return the new user
			return $rowUser;
		}
		else
		{
			throw new Zend_Exception("Could not create user!");
		}
	}

	public function getUserById($unityid)
	{
		$select = $this->select()->where('unityid = ?', $unityid);
		$user = $this->fetchRow($select);
		if (!$user)
		{
			throw new Exception("user [$unityid] not found!");
		}
		return $user->toArray();
	}

	/**
	 * getUsers - retrieve a list of users
	 *
	 */
	 public static function getUsers()
	 {
		 $userModel = new self();
		 $select = $userModel->select()->from('users');

		 return $userModel->fetchAll($select);
	}

	/**
	 * updateUser - update user information stored in the database 
	 * 
	 * @param mixed $unityid 
	 * @param mixed $firstname 
	 * @param mixed $lastname 
	 * @param mixed $email 
	 * @param mixed $role
     * @param mixed $semester
     * @param mixed $semesterTerm
	 * @return void
	 */
	public function updateUser($unityid, $studentid, $firstname, $lastname, $password, $email, $role, $semester, $semesterString)
	{
		// fetch the user's row
		$rowUser = $this->find($unityid)->current();

		if($rowUser)
		{
			// update row values
			$rowUser->unityid   = $unityid;
      $rowUser->studentid = $studentid;
			$rowUser->firstname = $firstname;
			$rowUser->lastname  = $lastname;
      $rowUser->password  = sha1($password);
			$rowUser->email     = $email;
			$rowUser->role      = $role;
            $rowUser->semester   = $semester;
            $rowUser->semesterString = $semesterString;
			$rowUser->save();

			// return the updated user
			return $rowUser;
		}
		else
		{
			throw new Zend_Exception("User update failed. User not found!");
		}
	}

	public function updatePassword($unityid, $password)
	{
		// fetch the user's row
		$rowUser = $this->find($unityid)->current();

		if ($rowUser)
		{
			// update the password
			$registry = Zend_Registry::getInstance();
			$salt = $registry->config->salt;
			$password = $salt.$password;

			$rowUser->password = sha1($password);
			$rowUser->save();
			return true;
		}
		else
		{
			return false;
			//throw new Zend_Exception("Password update failed. User not found!");
		}
	}

	public function deleteUser($unityid)
	{
		// fetch user's row
		$rowUser = $this->find($unityid)->current();
		if ($rowUser)
		{
			$rowUser->delete();
		}
		else
		{
			throw new Zend_Exception("Could not delete user. User not found!");
		}
	}
}
