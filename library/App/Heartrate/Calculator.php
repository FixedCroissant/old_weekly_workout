<?php
/**
*	Calculates the heartrate beat count per minute for a given intensity level
*
*	This class provides several heartrate calculator functions that are useful
*	in providing values such as an individual's estimated maximum heartrate
*	value, the heartrate reserve value based on the Karvonen method, and any
*	given heartrate beat per minute (bpm) value for a desired intensity level.
*
*	@author David Conner
*	@version 1.0
*
*	@param	int		$age	The age of the exerciser
*	@param	int		$rhr	The resting heartrate of the exerciser
*	@param	string	$sex	The sex of the exerciser
*/
class App_Heartrate_Calculator
{
	// class properties
	protected $_age;
	protected $_rhr;
	protected $_sex;

	// class constants for Karvonen HRR calculations
	const MAX_HR_MALES = '220';
	const MAX_HR_FEMALES = '226';

	// constructor to initialize object
	public function __construct($age, $rhr, $sex)
	{
		$this->_age = $age;
		$this->_rhr = $rhr;
		$this->_sex = $sex;
	}

	/**
	*	This method uses the sex of the exerciser to determine the estimated
	*	heartrate maximum to be used in all related bpm calculations.
	*/
	protected function estimatedMaxHeartrate()
	{
		// set the maximum heartrate value based on sex
		if ($this->_sex == 'm')
		{
			$est_max = self::MAX_HR_MALES;
		}
		if ($this->_sex == 'f') 
		{
			$est_max = self::MAX_HR_FEMALES;
		}
		return $est_max;
	}
	
	/**
	*	Utilizes the estimated heartrate maximum from which to subtract
	*	the exerciser's age giving the individual's maximum bpm value.
	*/
	public function getMaxHeartrate()
	{
		$max = $this->estimatedMaxHeartrate() - $this->_age;
		return $max;
	}

	/**
	*	Simply returns the resting heart rate value
	*/
	public function getRestingHeartrate()
	{
		return $this->_rhr;
	}

	/**
	*	Calculates the heartrate reserve value which will be used to adjust the
	*	bpm values using the intensity level requested by the exerciser.
	*/
	public function getHeartrateReserve()
	{
		$hrr = $this->getMaxHeartrate() - $this->_rhr;
		return $hrr;
	}

	/**
	*	Calculates the beats per minute (BPM) for the exerciser based on the
	*	requested intensity level. This calculation is based on the Karvonen
	*	Formula.
	*/
	public function calculateBPM($percent)
	{
		$bpm = (int)(($this->getHeartrateReserve() * $percent) + $this->_rhr);
		return $bpm;
	}
	
	/**
	 * calculateHeartrateRange 
	 * 
	 * Calculates the range of heartrate values for an individual
	 *
	 * @return array 
	 */
	public function calculateHeartrateRange()
	{
		$range = array(
					'60'		=> $this->calculateBPM(.60),
					'65'		=> $this->calculateBPM(.65),
					'70'		=> $this->calculateBPM(.70),
					'75'		=> $this->calculateBPM(.75),
					'80'		=> $this->calculateBPM(.80),
					'85'		=> $this->calculateBPM(.85),
					'90'		=> $this->calculateBPM(.90),
					'max'		=> $this->getMaxHeartrate()
					);
		return $range;
	}
}
