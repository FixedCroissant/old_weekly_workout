<?php

class Model_WorkoutData extends Zend_Db_Table
{
	protected $_name = 'wkdata';
	protected $_primary = 'workout_id';

	/**
	 * addWorkout
	 *
	 * Inserts the workout data into the database and returns an
	 * array containing the workout data
	 * 
	 * @param mixed $unityid 
	 * @param timestamp $date 
	 * @param int $week 
	 * @param int $prehr 
	 * @param int $peakhr 
	 * @param int $posthr 
	 * @param int $pushupNo 
	 * @param string $pushupType 
	 * @param int $crunches 
	 * @param string $workoutType 
	 * @param int $workoutLength 
	 * @param float $workoutGrade 
	 * @param string $workoutComments 
	 * @param mixed $submittedBy 
	 * @return array
	 */
	public function addWorkout($unityid,
                                    $date,
                                    $week,
                                    $prehr,
                                    $peakhr,
                                    $posthr,
                                    $pushupNo,
                                    $pushupType,
                                    $crunches,
                                    $workoutType,
                                    $workoutLength,
                                    $workoutGrade,
                                    $workoutComments,
                                    $submittedBy)
	{
		$data = array(
                                'unityid'          => $unityid,
                                'date'             => $date,
                                'week'             => $week,
                                'pre_hr'           => $prehr,
                                'peak_hr'          => $peakhr,
                                'post_hr'          => $posthr,
                                'pushup_no'        => $pushupNo,
                                'pushup_type'      => $pushupType,
                                'crunches'         => $crunches,
                                'workout_type'     => $workoutType,
                                'workout_length'   => $workoutLength,
                                'workout_grade'    => $workoutGrade,
                                'workout_comments' => $workoutComments,
                                'submitted_by'     => $submittedBy,
                                'submitted_at'     => NULL
                                );
		$row = $this->createRow($data);
		$row->save();
		return $row;
	}

	/**
	 * workoutExists
	 *
	 * Checks to see if a workout already exists for a given date
	 * 
	 * @param mixed $unityid 
	 * @param mixed $workoutDate 
	 * @return boolean 
	 */
	public function workoutExists($unityid, $workoutDate)
	{
		$row = $this->fetchRow($this->select()
				->where("unityid = ?", $unityid)
				->where("date = ?", $workoutDate));
		if (!$row)
		{
                    return false;
		}

		return true;
	}

	/**
	 * calculateGrade 
	 * 
	 * This function assigns a grade and comment to each workout submitted
	 *
	 * @param mixed $pre_hr 
	 * @param mixed $peak_hr 
	 * @param mixed $post_hr 
	 * @param mixed $pushup_num 
	 * @param mixed $crunches 
	 * @param mixed $workout_time 
	 * @access public
	 * @return array 
	 */
	public function calculateGrade($prehr,
                                        $peakhr,
                                        $posthr,
                                        $pushups,
                                        $crunches,
                                        $wklength,
                                        $hr60,
                                        $hr90)
	{
	
		$score = 33.34;

		if ($prehr >= 120) {
			$comment .= "Your pre-workout heart rate is very high.<br/> ";
		}

		if ($peakhr < ($hr60) || $peakhr > (($hr90))) {
			$score += -4.00;
			$comment .= "Your peak workout heart rate is not in the correct range. <br/>";
		}

		if ($posthr >= 135) {
			$comment .= "Your post-workout heart rate is very high. <br/>";
		}

		if ($pushups < 1) {
			$score += -4.00;
			$comment .= "You didn't do any pushups. <br/>";
		}

		if ($crunches < 1) {
			$score += -4.00;
			$comment .= "You didn't do any crunches. <br/>";
		}

		if ($wklength < 30) {
			$score += -4.00;
			$comment .= "You should workout at least 30 minutes. <br/>";
		}

		if (round($score) >= 33) {
			$comment = "Great Workout!";
		}

		$grade = array (
			'score'   => $score,
			'comment' => $comment
		);

		return $grade;
	}

	/**
	 * getWeek
	 *
	 * Returns the weekly grade for each student that did at least one workout in the given week
	 *
	 * @param string $sql
	 * @param int $week
	 * @return array
	 */
	public function getWeeklyGrade($week)
	{
                // hacked solution since I can't find documentation showing how
                // multiple tables can be accessed using ZF classes and PDO
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql = 'select concat_ws(":",u.unityid,u.studentid) as id, round(sum(workout_grade)) as grade, u.firstname, u.lastname from users as u join wkdata as w on u.unityid = w.unityid where week = ? and u.role="student" and u.unityid !="dstudent" group by u.unityid order by u.lastname asc';
	//	$rowset = $this->fetchAll($this->select()
	//	->from(array('u' => 'users'), array('unityid','firstname','lastname'))
	//	->join(array('w' => 'wkdata'), ('u.unityid = w.unityid'),array('grade' => 'round(sum(workout_grade))','week'))
	//	->where('w.week = ?', $week)
	//	->where('u.role = student')
	//	->group('u.unityid')
	//	->order(array('lastname asc','firstname asc'))
	//	);
		$stmt = $db->query($sql,array($week));
		$result = $stmt->fetchAll();
		return $result;
	}

	/**
	 * getNumberOfWorkouts
	 *
	 * Returns the number of workouts performed in a given week
	 * 
	 * @param mixed $unityid 
	 * @param mixed $week 
	 * @return void
	 */
	public function getNumberOfWorkouts($unityid,$week)
	{
		$rowset = $this->fetchAll($this->select()
				->where('unityid = ?', $unityid)
				->where('week = ?', $week));
		$count = count($rowset);
		return $count;
	}

	/**
	 * getAllWorkoutsByDate
	 *
	 * Returns an array that contains all of the workouts except
	 * those that occur during the current week
	 * 
	 * @param mixed $unityid 
	 * @param int $week 
	 * @return array
	 */
	public function getAllWorkoutsByDate($unityid,$startDate,$weekStart)
	{
		if (!$weekStart == NULL && !startDate == NULL)
		{
      $rowset = $this->fetchAll($this->select()
              ->where('unityid = ?', $unityid)
              ->where('date >= ?', $startDate)
              ->where('date < ?', $weekStart)
              ->order('week ASC')
              ->order('date ASC'));
		}
		else
		{
      $rowset = $this->fetchAll($this->select()
              ->where('unityid = ?', $unityid)
              ->order('week ASC')
              ->order('date ASC'));
    }

    return $rowset->toArray();
	}

  /**
	 * getAllWorkouts
	 *
	 * Returns an array that contains all of the workouts except
	 * those that occur during the current week
	 * 
	 * @param mixed $unityid 
	 * @return array
	 */
	public function getAllWorkouts($unityid)
	{
    $rowset = $this->fetchAll($this->select()
              ->where('unityid = ?', $unityid)
              ->order('week ASC')
              ->order('date ASC'));

    return $rowset->toArray();
	}
  
	/**
	 * getThisWeeksWorkouts
	 *
	 * Returns an array containing the workouts that occur during the
	 * current week
	 * 
	 * @param mixed $unityid 
	 * @param int $week 
	 * @return array
	 */
	public function getThisWeeksWorkouts($unityid, $weekStart, $weekEnd)
	{
		if (!$weekStart == NULL) 
		{
      $rowset = $this->fetchAll($this->select()
        ->where('unityid = ?', $unityid)
        ->where('date >= ?', $weekStart)
        ->where('date <= ?', $weekEnd)
        ->order('date ASC'));
      return $rowset->toArray();
		}
	}

	public function deleteWorkout($workout_id)
	{
		$row = $this->find($workout_id)->current();
		if ($row)
		{
			$row->delete();
			return true;
		}
		else
		{
			throw new Zend_Exception("Delete function failed: Could not find row!");
		}
	}

	public function deleteAllWorkoutsForStudent($unityid)
	{
		$rowset = $this->fetchAll($this->select()
				->where('unityid = ?', $unityid));
		foreach ($rowset as $row)
		{
			$row->delete();
		}
	}

}
