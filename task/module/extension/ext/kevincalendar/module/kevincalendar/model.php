<?php

class kevincalendarModel extends model {

	/**
	 * $account.
	 * 
	 * @var account
	 * @access public
	 */
	public $account = "";

	/**
	 * Construct function, load model of kevincalendar.
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
	}

	public function batchcreate() {
		$kevincalendars = fixer::input('post')->get();
		for ($i = 0; $i < $this->config->kevincalendar->batchcreate; $i++) {
			if ($kevincalendars->dates[$i] != '') {
				$kevincalendar			 = new stdclass();
				$kevincalendar->date	 = $kevincalendars->dates[$i];
				$kevincalendar->status	 = $kevincalendars->statuses[$i];
				$kevincalendar->desc	 = $kevincalendars->descs[$i];

				$this->dao->insert(TABLE_KEVINCALENDAR)->data($kevincalendar)->autoCheck()->exec();
				if (dao::isError()) {
					echo js::error(dao::getError());
					die(js::reload('parent'));
				}
			} else {
				unset($kevincalendars->dates[$i]);
				unset($kevincalendars->statuses[$i]);
				unset($kevincalendars->descs[$i]);
			}
		}
	}

	public function create() {
		$kevincalendar = fixer::input('post')->get();
		$this->dao->insert(TABLE_KEVINCALENDAR)->data($kevincalendar)
			->autoCheck()
			->exec();
		return $this->dao->lastInsertID();
	}

	public function getById($kevincalendarID) {
		$kevincalendar = $this->dao->findById((int) $kevincalendarID)->from(TABLE_KEVINCALENDAR)->fetch();
		if (!$kevincalendar)
			return false;
		return $kevincalendar;
	}

	public function getInofoByMonth($type = 'thisMonth', $methodName = '') {
		$allDateArray = $this->getKevinCalendar();

		$currentMonthDays	 = $this->getMonthArray($type);
		$firstDay			 = current($currentMonthDays); //本月第一天
		$lastDay			 = end($currentMonthDays); //本月最后一天
		$firstDayTimes		 = strtotime($firstDay);
		$endDayTimes		 = strtotime($lastDay);
		$week				 = (int) date("w", strtotime($firstDay)); //获得是周几,周末为0,周一为1
		$lastMonthDays		 = array();
		$tempDay			 = '';
		$lastMonthIndex		 = (0 == $week) ? 6 : ($week - 1); //获得上月几天
		for ($i = 1; $i <= $lastMonthIndex; $i++) {
			$tempDay				 = date('Y-m-d', strtotime("$firstDay -$i day"));
			$lastMonthDays[$tempDay] = $tempDay;
		}
		$nextMonthDays	 = array();
		$nextDays		 = 42 - count($currentMonthDays) - count($lastMonthDays);
		$endDay			 = end($currentMonthDays);
		for ($i = 1; $i <= $nextDays; $i++) {
			$tempDay				 = date('Y-m-d', strtotime("$endDay +$i day"));
			$nextMonthDays[$tempDay] = $tempDay;
		}
		$totalMonthDays	 = array_merge($lastMonthDays, $currentMonthDays, $nextMonthDays);
		ksort($totalMonthDays);
		$globalFirstDay	 = current($totalMonthDays);
		$globalEndDay	 = end($totalMonthDays);
		if ('todo' === $methodName) {
			$todos = $this->getTodosByBeginAndEnd($globalFirstDay, $globalEndDay); //获得todo
		} else {
			$todos = array();
		}
		$totalMonthObjArray = array();
		foreach ($totalMonthDays as $tempDay) {
			$tempObj	 = new stdclass();
			$tempStatus	 = '';
			$tempDesc	 = '';
			$tempID		 = '';
			//是否存在于记录的日历中
			if (array_key_exists($tempDay, $allDateArray)) {
				$tempStatus	 = $allDateArray[$tempDay]->status;
				$tempDesc	 = $allDateArray[$tempDay]->desc;
				$tempID		 = $allDateArray[$tempDay]->id;
			} else {
				$unix	 = strtotime($tempDay); //获得日期的 Unix 时间戳
				$week	 = date("w", $unix); //获得是周几,周末为0,周一为1
				if ($week > 5 || $week < 1) {
					$tempStatus = 'hol';
				} else {
					$tempStatus = 'nor';
				}
			}
			$tempObj->status = $tempStatus;
			$tempObj->desc	 = $tempDesc;
			$tempObj->date	 = $tempDay;
			$tempObj->id	 = $tempID;
			$tempType		 = '';
			if (strtotime($tempDay) < $firstDayTimes) {
				$tempType = 'F';
			} else if (strtotime($tempDay) > $endDayTimes) {
				$tempType = 'N';
			} else {
				$tempType = 'C';
			}
			$tempObj->type	 = $tempType;
			$tempTodoArray	 = array();
			if (array_key_exists($tempDay, $todos)) {
				$tempTodoArray = $todos[$tempDay];
			}
			$tempObj->todos					 = $tempTodoArray;
			$totalMonthObjArray[$tempDay]	 = $tempObj;
		}
		return $totalMonthObjArray;
	}

	public function getList($begin, $end, $orderBy = "date") {
		$kevincalendars	 = array();
		$stmt			 = $this->dao->select('*')->from(TABLE_KEVINCALENDAR)
			->where('deleted')->eq(0)
			->andWhere("date >= '$begin'")
			->andWhere("date <= '$end'")
			->orderBy($orderBy)
			->query();

		while ($kevincalendar = $stmt->fetch()) {
			$kevincalendars[$kevincalendar->date] = $kevincalendar;
		}
		return $kevincalendars;
	}
	
	public function getMonthArray($date = '') {
		$this->app->loadClass('date');
		$date = strtolower($date);
		if (is_numeric($date) && 6 == strlen($date)) {
			$date	 = $date . '01';
			$begin	 = date('Y-m', strtotime("$date")) . '-01 00:00:00';
			$end	 = date('Y-m', strtotime("$date +1 month")) . '-00 23:59:59';
		} else {
			if ($date == 'thismonth') {
				extract(date::getThisMonth());
			} elseif ($date == 'lastmonth') {
				extract(date::getLastMonth());
			} elseif ($date == 'nextmonth') {
				$begin	 = date('Y-m', strtotime('+1 month')) . '-01 00:00:00';
				$end	 = date('Y-m', strtotime('+2 month')) . '-00 23:59:59';
			} else {
				extract(date::getThisMonth());
			}
		}

		$dateArray	 = array();
		//获得起始相差天数
		$DValue		 = floor((strtotime($end) - strtotime($begin)) / (24 * 60 * 60));
		for ($i = 0; $i <= $DValue; $i++) {
			$currentDate			 = date('Y-m-d', strtotime("$begin +$i day"));
			$dateArray[$currentDate] = $currentDate;
		}
		return $dateArray;
	}

	public function getKevinCalendar() {
		$kevincalendarArray	 = array();
		$kevincalendars		 = $this->dao->select('*')->from(TABLE_KEVINCALENDAR)
			->where('deleted')->eq(0)
			->fetchAll('date');
		foreach ($kevincalendars as $kevincalendar) {
			$kevincalendarArray[$kevincalendar->date] = $kevincalendar;
		}
		return $kevincalendarArray;
	}

	public function getMonthBySeason($month = '', $season = '') {
		$monthArray = array();
		if ('' == $month && $season == '') {
			return $monthArray;
		}
		if ('' == $season) {
			$monthArray[] = $month;
			return $monthArray;
		}
		for ($i = 1; $i < 4; $i++) {
			$currentMonth	 = sprintf("%02d", 3 * ($season - 1) + $i);
			$monthArray[]	 = $currentMonth;
		}
		return $monthArray;
	}

	/**
	 * 获得某个周期内所有日期工作类型数组
	 * 如果周期为全部设成thisyear
	 * return array  
	 * e.g array['2015-01-01'] = 'law';
	 */
	public function getStatusArray($date = 'thisMonth') {
		extract(kevin::getBeginEndTime($date));

		$kevincalendars	 = $this->getList($begin, $end);
		$dateArray		 = array();
		//获得起始相差天数
		$DValue			 = floor((strtotime($end) - strtotime($begin)) / (24 * 60 * 60));
		for ($i = 0; $i <= $DValue; $i++) {
			$currentDate = date('Y-m-d', strtotime("$begin +$i day"));
			if (array_key_exists($currentDate, $kevincalendars)) {
				$dateArray[$currentDate] = $kevincalendars[$currentDate]->status;
			} else {
				$unix					 = strtotime($currentDate); //获得日期的 Unix 时间戳
				$week					 = date("w", $unix); //获得是周几,周末为0,周一为1
				if ($week > 5 || $week < 1)
					$dateArray[$currentDate] = 'hol';
				else
					$dateArray[$currentDate] = 'nor';
			}
		}
		return $dateArray;
	}

	/**
	 * 获得特定月份的日期工作类型数组
	 * return: array 
	 * e.g:array[1] = 'nor';
	 */
	public function getStatusArrayOfMonth($year = '', $month = '') {
		if ('' == $month)
			$month	 = date('m');
		if ('' == $year)
			$year	 = date('Y');

		$begin			 = $year . '-' . $month . '-01 00:00:00';
		$days			 = cal_days_in_month(CAL_GREGORIAN, $month, $year);
		$end			 = $year . '-' . $month . '-' . $days . ' 23:59:59';
		$kevincalendars	 = array();
		$stmt			 = $this->dao->select('*')->from(TABLE_KEVINCALENDAR)
			->where('deleted')->eq(0)
			->andWhere("date >= '$begin'")
			->andWhere("date <= '$end'")
			->orderBy('date')
			->query();

		while ($kevincalendar = $stmt->fetch()) {
			$tempDay					 = date('j', strtotime($kevincalendar->date));
			$kevincalendars[$tempDay]	 = $kevincalendar->status;
		}
		for ($i = 1; $i <= $days; $i++) {
			$date	 = $year . '-' . $month . '-' . $i; //日期
			$date	 = date('Y-m-d', strtotime($date));
			//是否存在于记录的日历中
			if (!array_key_exists($i, $kevincalendars)) {
				$unix				 = strtotime($date); //获得日期的 Unix 时间戳
				$week				 = date("w", $unix); //获得是周几,周末为0,周一为1
				if ($week > 5 || $week < 1)
					$kevincalendars[$i]	 = 'hol';
				else
					$kevincalendars[$i]	 = 'nor';
			}
		}
		ksort($kevincalendars);
		return $kevincalendars;
	}

	public function getTodosByBeginAndEnd($begin, $end) {
		if ($this->account == "")
			$this->account	 = $this->app->user->account;
		$stmt			 = $this->dao->select('a.*,b.realname')->from(TABLE_TODO)->alias('a')
			->leftJoin(TABLE_USER)->alias('b')->on('a.account = b.account')
			->where('a.account')->eq($this->account)
			->andWhere("a.date >= '$begin'")
			->andWhere("a.date <= '$end'")
			->orderBy('date, begin')
			->fetchGroup('date');
		return $stmt;
	}

	public function getStatusByDate($date = '') {
		if ('' == $date)
			return '';
		$kevincalendar = $this->dao->select('*')->from(TABLE_KEVINCALENDAR)
			->where('deleted')->eq(0)
			->andWhere('date')->eq($date)
			->fetch();
		if (!$kevincalendar)
			return '';
		return $kevincalendar->status;
	}

	/**
	 * 获得某月的第几个工作日的日期
	 * 
	 * @param  string $year 
	 * @param  string $month 
	 * @param  int $workingdays 
	 * @access public
	 * @return int 
	 */
	public function getWorkingDay($year = '', $month = '', $workingdays = 1) {
		$dayArray	 = $this->getStatusArrayOfMonth($year, $month);
		$totalDays	 = count($dayArray);
		$tempDay	 = 0;
		for ($i = 1; $i <= $totalDays; $i++) {
			if ('nor' == $dayArray[$i]) {
				$tempDay += 1;
				if ($workingdays == $tempDay)
					return $i;
			}
		}
		return $totalDays; //没有则返回月底
	}

	public function getYearList($year = 10) {
		$yearList = array();
		for ($i = -1; $i < $year; $i++) {
			$yearList[date('Y') - $i] = (date('Y') - $i) . '年';
		}
		return $yearList;
	}

	public function update($kevincalendarID) {
		$kevincalendar = fixer::input('post')->get();
		$this->dao->update(TABLE_KEVINCALENDAR)->data($kevincalendar)
			->where('id')->eq($kevincalendarID)
			->autoCheck()
			->exec();
	}

}
