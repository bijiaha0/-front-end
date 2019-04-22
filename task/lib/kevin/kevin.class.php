<?php

/**
 * The kevin class file
 * copyright:Kevin<3301647@qq.com>
 * http://kevincom.sourceforge.net/
 * The author disclaims copyright to this source code.  In place of
 * a legal notice, here is a blessing:
 * 
 *  May you do good and not evil.
 *  May you find forgiveness for yourself and forgive others.
 *  May you share freely, never taking more than you give.
 */

/**
 * The kevin class.
 * 
 * @package   framework
 */
class kevin {

	
    /**
     * Format time 0915 to 09:15
     *  20150810 to 2015-08-10
     * @param  string $time 
     * @access public
     * @return string
     */
    public static function formatTime($time)
    {
		switch (strlen($time)) {
			case 4:
				return substr($time, 0, 2) . ':' . substr($time, 2, 2);
			case 8:
				return substr($time, 0, 4) . '-' . substr($time, 4, 2). '-' . substr($time, 6, 2);
			default:
				return $time;
		}
    }

	/**
	 * The getBeginEndTime.
	 * 
	 * @access public
	 * @param  类型 $type 
	 * @param  int $delta 
	 * @return array{begin,end}.
	 */
	public static function getBeginEndTime($period) {
		if ('' == $period) {
			$type = 'thisMonth';
		}
		//$this->app->loadClass('date');
		$period = strtolower($period);
		//此处组合为年+月 共6位数字
		if (is_numeric($period) && 6 == strlen($period)) {
			$period .= '0';
		}
		//此处判断为最近时间,小于等于4位数字
		if (is_numeric($period) && 4 >= strlen($period)) {
			$begin	 = date('Y-m-d', strtotime('-' . $period . ' day'));
			$end	 = date('Y-m-d', strtotime('+1 day'));
		}
		//此处判断的7为数字组成为年+月+季度
		else if (is_numeric($period) && 7 == strlen($period)) {
			//日期截取
			$year	 = substr($period, 0, 4);
			$month	 = substr($period, 4, 2);
			if ('00' == $month) {
				$month = '';
			}
			$season = substr($period, 6, 1);
			if ('0' == $season) {
				$season = '';
			}
			$monthArray = kevin::getMonthBySeason($month, $season);
			if (1 == count($monthArray)) {
				$currentMonth	 = current($monthArray);
				$begin			 = $year . '-' . $currentMonth . '-01';
				return kevin::getMonthBeginEnd($begin);
			} else {
				$begin			 = $year . '-' . current($monthArray) . '-01 00:00:00';
				$last			 = $year . '-' . end($monthArray) . '-' . '01 00:00:00';
				$Array			 = kevin::getMonthBeginEnd($last);
				$Array['begin']	 = $begin;
				return $Array;
			}
		} else {
			switch ($period) {
				case 'today':
					$begin	 = kevin::formatTime(date::today());
					$end	 = $begin;
					break;
				case 'yesterday':
					$begin	 = kevin::formatTime(date::yesterday());
					$end	 = $begin;
					break;
				case 'thisweek':
					return(date::getThisWeek());
					break;
				case 'lastweek':
					return(date::getLastWeek());
					break;
				case 'nextmonth':
					return(kevin::getNextMonth());
					break;
				case 'thismonth':
					return(date::getThisMonth());
					break;
				case 'lastmonth':
					return(date::getLastMonth());
					break;

				case 'thisseason':
					return(date::getThisSeason());
					break;
				case 'thisyear':
					return(date::getThisYear());
					break;
				case 'lastyear':
					return(date::getLastYear());
					break;
				case 'future':
					$begin	 = kevin::formatTime(date::tomorrow());
					$end = '2109-01-01';//end furture
					break;
				case 'all': 
					$begin	 = '1970-01-01';
					$end = '2109-01-01';//end furture
					break;
				case 'before':
					$begin	 = '1970-01-01';
					$end	 = kevin::formatTime(date::yesterday());
					break;
				default:
					$begin	 =  kevin::formatTime($period);
					$end	 = $begin;
			}
			if(10 == strlen($end)) $end = $end.' 23:59:59';
		}
		return array('begin' => $begin, 'end' => $end);
	}

	/**
	 * The get year month
	 * 
	 * @access public
	 * @param  string $period 
	 * @return array of year month.
	 */
	public static function getFirstYearMonth($period) {
		$arrayK	 = kevin::getBeginEndTime($period);
		$begin	 = $arrayK['begin'];
		$year	 = substr($begin, 0, 4);
		$month	 = substr($begin, 5, 2);
		return array('year' => $year, 'month' => $month);
	}
 
	/**
	 * The getMonth.
	 * 
	 * @access public
	 * @param  date $now 
	 * @param  int $delta 
	 * @return int of month[1 to 12].
	 */
	public static function getMonth($delta, $now = 'time()') {
		$delta = (int) $delta;
		if ($now == 'time()') {
			$now = time();
		}
		$m	 = (int) date('m', $now);
		$m	 = $m + $delta;
		$m	 = $m % 12;
		if ($m <= 0) {
			$m+=12;
		}
		return $m;
	}
	
	/**
	 * The strtotime.
	 * 
	 * @access public
	 * @param  string $time 
	 * @return new date.
	 */
	public static function getMonthBeginEnd($time) {
		$newTime	 = strtotime($time);
		$begin		 = date('Y-m-01 00:00:00', $newTime); //to first day
		$tempTime	 = strtotime($begin);
		$end		 = date('Y-m-d', strtotime("+1 month -1 day", $tempTime)) . ' 23:59:59'; //to last day
		return array('begin' => $begin, 'end' => $end);
	}

	/**
	 * The get month by Season.
	 * 
	 * @access public
	 * @param  int $month 
	 * @param  int $season 
	 * @return array of month.
	 */
	public static function getMonthBySeason($month = '', $season = '') {
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
     * Get begin and end time of this month.
     * 
     * @access public
     * @return array
     */
    public static function getNextMonth()
    {
        $begin   = date('Y-m', strtotime('next month')) .  '-01 00:00:00';
		$year   = substr($begin, 0,4);
		$month   = substr($begin, 5,2);
		$days	 = cal_days_in_month(CAL_GREGORIAN, $month, $year);
		$end	 = $year . '-' . $month . '-' . $days . ' 23:59:59';
        return array('begin' => $begin, 'end' => $end);
    }
	
	/**
	 * The strtotime.
	 * 
	 * @access public
	 * @param  string $time 
	 * @param  date $now 
	 * @return new date.
	 */
	public static function strtotimeMonth($time, $now = 'time()') {
		if ($now == 'time()') {
			$now = time();
		}
		$newTime		 = strtotime($time, $now);
		$firstday		 = date('Y-m-01', $now); //to first day
		$newFirstday	 = strtotime("$firstday " . $time);
		$newFirstday_s	 = date('Y-m-d', $newFirstday);
		$newLastday_s	 = date('Y-m-d', strtotime("$newFirstday_s +1 month -1 day")); //to last day
		$newLastday		 = strtotime($newLastday_s);

		if ($newTime > $newLastday) {
			return $newLastday;
		} elseif ($newTime < $newFirstday) {
			return $newFirstday;
		}
		return $newTime;
	}
}
