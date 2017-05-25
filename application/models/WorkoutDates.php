<?php

class Model_WorkoutDates extends Zend_Db_Table
{
	protected $_name = 'wkdates';
	protected $_primary = 'week';

	/**
	 * insertWorkoutDates - insert workout dates into database
	 *
	 * Writes the workout dates to the wkdates table
	 *
	 * @param int $month 
	 * @param int $day 
	 * @param int $year 
	 * @param int $weeks 
	 * @return void
	 */
	public function insertWorkoutDates($month, $day, $year, $weeks)
	{
		if(Zend_Date::isDate($month.$day.$year))
		{
			$dateArray = array(
							'year'	=> $year,
							'month'	=> $month,
							'day'	=> $day
							);
			Zend_Date::setOptions(array('fix_dst' => true));
			$date = new Zend_Date($dateArray);
			$start = $date->get(Zend_Date::TIMESTAMP);
			
			for($i=1; $i<=$weeks; $i++)
			{
/**
 *	Here's some crazy magic that just might work. 
 *	The dates must be calculated as accurately as possible so students can submit
 *	workouts only during the current week - no late entries! The DST issue
 *	wreaks havoc on this stipulation because those weeks are different.
 *	Using the following logic seems to prevent any problems by calculating
 *	the start date of the week, then calculating the end date by subtracting
 *	one second from the start date of the next week. By using this method the Zend
 *	Date object takes care of the DST changes for us.
 *	
 */

				$date->add('7', Zend_Date::DAY);
				$end = $date->get(Zend_Date::TIMESTAMP) - 1;

				$row = array(
						'week'  => $i,
						'start' => $start,
						'end'   => $end
						);
				$start = $date->get(Zend_Date::TIMESTAMP);
			
				$this->insert($row);
			}
		}
		else
		{
			throw new Zend_Exception('Please enter a valid date!');
		}
	}

	/**
	 * deleteWorkoutDates - uses where clause to delete all rows at once 
	 * 
	 * @return void
	 */
	public function deleteWorkoutDates()
	{
		$where = 'week != 0';
		$this->delete($where);
	}

	/**
	 * getWorkoutDates - gets the dates from the database 
	 *
	 * Retrieves an array that contains the entire rowset of the
	 * wkdates table
	 *
	 * @return array
	 */
	public function getWorkoutDates()
	{
		$select = $this->select();
		$select->order('week');
		return $this->fetchAll($select)->toArray();

	}

	/**
	 * isWorkoutWeekValid 
	 *
	 * Used to check whether or not the workout date falls within the
	 * timestamp range of the current week
	 *
	 * @param mixed $currentDate 
	 * @param mixed $workoutDate 
	 * @return int 
	 */
	public function isWorkoutWeekValid($currentDate,$workoutDate)
	{
		$datesArray = $this->getWorkoutDates();
		foreach ($datesArray as $key => $weekArray)
		{
			$week  = $weekArray['week'];
			$start = $weekArray['start'];
			$end   = $weekArray['end'];

			if ($workoutDate >= $start && $workoutDate <= $end)
			{
				return array(
						'week' => $week,
						'start' => $start,
						'end' => $end
						);
			}
		}

		return 0;
	}

	public function occursThisWeek($currentDate, $weekRow)
	{
		if ($currentDate >= $weekRow['start'] && $currentDate <= $weekRow['end'])
		{
			return true;
		}
	
		return false;
	}

	/**
	 * getCurrentWeek 
	 *
	 * Returns an array that contains the current week and the
	 * corresponding start and end timestamps
	 *
	 * @param mixed $currentDate 
	 * @return array
	 */
	public function getCurrentWeek($currentDate)
	{
		$datesArray = $this->getWorkoutDates();
		$count = count($datesArray);
		foreach ($datesArray as $key => $weekArray)
		{
			$week	=	$weekArray['week'];
			$start	=	$weekArray['start'];
			$end	=	$weekArray['end'];

			if ($currentDate >= $start && $currentDate <= $end)
			{
				$currentWeek = array(
								'week' => $week,
								'start' => $start,
								'end' => $end
								);
				return $currentWeek;
			}
		}
		// in case the current week is beyond the possible workout dates
		$currentWeek = $count + 1;
		return $currentWeek;
	}

	/**
	 * getNumberOfWeeks 
	 * 
	 * This method will count the number of rows in the wkdates table
	 *
	 * @return int
	 */
	public function getNumberOfWeeks()
	{
		$datesArray = $this->getWorkoutDates();
		$count = count($datesArray);
		return $count;
	}

	public function getStartingDate($week)
	{
		$row = $this->fetchRow($this->select()->where('week = ?', $week));
		$rowArray = $row->toArray();
		$start = $rowArray['start'];
		return $start;
	}

	public function getEndingDate($week)
	{
		$row = $this->fetchRow($this->select()->where('week = ?', $week));
		$rowArray = $row->toArray();
		$end = $rowArray['end'];
		return $end;
	}

}
