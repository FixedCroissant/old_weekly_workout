<?php

class Model_HeartrateData extends Zend_Db_Table
{

	/**
	 * _name 
	 * 
	 * @var string
	 */
	protected $_name = 'hrdata';

	/**
	 * _primary 
	 * 
	 * @var string
	 */
	protected $_primary = 'unityid';

	
	public function getHeartrateDataCount($unityid)
	{

	}

	public function getHeartrateData($unityid)
	{
		
		$row = $this->fetchRow($this->select()->where('unityid = ?', $unityid));
		if (!$row)
		{
			return $row = NULL;
		}

		return $row->toArray();
	}


	/**
	 * addHeartrateData - insert the heartrate data into the database
	 * 
	 * @param mixed $unityid 
	 * @param int $age 
	 * @param string $sex 
	 * @param int $rhr 
	 * @param array $hr_range 
	 * @param mixed $userid 
	 * @return void
	 */
	public function addHeartrateData($unityid,$age,$sex,$rhr,$hr_range,$userid)
	{
		$data = array(
					'unityid'      => $unityid,
					'age'          => $age,
					'sex'          => $sex,
					'rhr'          => $rhr,
					'hr_60'        => $hr_range['60'],
					'hr_65'        => $hr_range['65'],
					'hr_70'        => $hr_range['70'],
					'hr_75'        => $hr_range['75'],
					'hr_80'        => $hr_range['80'],
					'hr_85'        => $hr_range['85'],
					'hr_90'        => $hr_range['90'],
					'hr_max'       => $hr_range['max'],
					'submitted_by' => $userid,
					'submitted_at' => NULL
				);           		

		if ($this->find($unityid))
		{
			$this->deleteHeartrateData($unityid);
		}

		$this->insert($data);
	}

	public function deleteHeartrateData($unityid)
	{
		$where = $this->getAdapter()->quoteInto('unityid = ?', $unityid);
		$this->delete($where);
	}
}
