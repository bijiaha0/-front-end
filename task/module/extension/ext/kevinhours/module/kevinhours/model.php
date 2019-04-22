<?php

class kevinhoursModel extends model {

	/**
	 * $kevincalendar model.
	 * 
	 * @var model
	 * @access public
	 */
	public $kevincalendar = null;

	/**
	 * account.
	 * 
	 * @var model
	 * @access public
	 */
	public $account = "";

	/**
	 * user.
	 * 
	 * @var model
	 * @access public
	 */
	public $user = null;

	/**
	 * $realname.
	 * 
	 * @var model
	 * @access public
	 */
	public $realname = "";

	/**
	 * $accountdept 当前用户所属部门.
	 * 
	 * @access public
	 */
	public $accountdept = "";
	
	/**
	 * $isSelfUser 是否当前用户.
	 * 
	 * @access public
	 */
	public $isSelfUser = false;

	/**
	 * $clockitems 打卡记录.
	 * 
	 * @access public
	 */
	public $clockInor = null;

	/**
	 * $employeesActive 激活用户列表.
	 * 
	 * @access public
	 */
	public $employeesActive = array();

	/**
	 * $employeesInactive 未激活用户列表.
	 * 
	 * @access public
	 */
	public $employeesInactive = array();
	
	/**
	 * $employeesInactive 未激活用户列表.
	 * 
	 * @access public
	 */
	public $employeesAll = array();
	
	/**
	 * Construct function, load model of todo.
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		$this->loadModel('kevincom');
		$this->loadModel('kevincalendar');
		$this->loadModel('todo');
	}

	/**
	 * Create batch todo
	 * 
	 * @access public
	 * @return void
	 */
	public function batchcreate() {
		if ($this->account == "")
			return false;
		$todos		 = fixer::input('post')->cleanInt('date')->get();
		$todoArray	 = array();
		$currentObj	 = new stdclass();
		for ($i = 0; $i < $this->config->kevinhours->batchcreate; $i++) {
			if ($todos->names[$i] != '' || isset($todos->bugs[$i + 1]) || isset($todos->tasks[$i + 1])) {
				//判断项目id如果是否存在,如有不存在则不提交
				$this->GetProjectByIDorCashCode($todos->project[$i]);
			}
			if ('' != $todos->names[$i]) {

				$currentObj->begin	 = (int) $todos->begins[$i];
				$currentObj->end	 = (int) $todos->ends[$i];
				$todoArray[]		 = $currentObj;
			}
		}
		//date
		$todo			 = new stdclass();
		$todo->account	 = $this->account;
		$isDateFuture	 = false;
		$currentDate	 = '';
		$todo->date		 = $this->post->date;
		$currentDate	 = $todo->date;
		//判断日期是否超前
		$isDateFuture	 = $this->isDateFuture($todo->date);

		//验证时间是否重叠
		if ('' != $currentDate) {
			if ($this->isTodoTimeOverlap($currentDate, $todoArray))
				return;
		}

		$todo->pri	 = 3;

		//loop
		for ($i = 0; $i < $this->config->kevinhours->batchcreate; $i++) {
			if ($todos->names[$i] != '' || isset($todos->bugs[$i + 1]) || isset($todos->tasks[$i + 1])) {

				$todo->hourstype = $todos->hourstypes[$i];
				$todo->type = $todos->types[$i];
				//判断项目id如果是否存在。并获得项目代号
				$todo->project	 = $this->GetProjectByIDorCashCode($todos->project[$i]);
				$todo->name		 = isset($todos->names[$i]) ? $todos->names[$i] : '';
				$todo->desc		 = '';
				$todo->begin	 = isset($todos->begins[$i]) ? $todos->begins[$i] : 2400;
				$todo->end		 = isset($todos->ends[$i]) ? $todos->ends[$i] : 2400;
				$todo->minutes	 = $this->kevincom->getWorkMinutes($todos->minutes[$i]);
				if ($todo->begin == 0)
					$todo->begin	 = 2400;
				if ($todo->end == 0)
					$todo->end		 = 2400;
				if ($isDateFuture) {
					$todo->status = "wait";
				} else {
					$todo->status = isset($todos->status[$i]) ? $todos->status[$i] : "wait";
				}

				$todo->private	 = 0;
				$todo->idvalue	 = 0;

				$this->dao->insert(TABLE_TODO)->data($todo)->autoCheck()->exec();
				if (dao::isError()) {
					echo js::error(dao::getError());
					die(js::reload('parent'));
				}
				$this->loadModel('action')->create('todo', $this->dao->lastInsertID(), 'opened');
			} else {
				unset($todos->types[$i]);
				unset($todos->pris[$i]);
				unset($todos->names[$i]);
				unset($todos->descs[$i]);
				unset($todos->begins[$i]);
				unset($todos->ends[$i]);
			}
		}
	}

	/**
	 * calculateCalendarTable function, 构造表格字符串.
	 * $type 月份类型
	 * @access public
	 * @return void
	 */
	function calculateCalendarTable($type) {
		$firstDayOfCurrentMonthTimes = strtotime($type . '01');
		$dayNum						 = cal_days_in_month(CAL_GREGORIAN, date('m', $firstDayOfCurrentMonthTimes), date('Y', $firstDayOfCurrentMonthTimes)); // 天数
		$endDayOfCurrentMonthTimes	 = strtotime($type . $dayNum);
		if ($this->account == '')
			return;
		$hasPrivTodoEdit			 = common::hasPriv('kevinhours', 'edit');
		if ($hasPrivTodoEdit) {
			$hasPrivTodoEdit = $this->isSelfUser;
			if (!$hasPrivTodoEdit) {
				$hasPrivTodoEdit = common::hasPriv('kevinhours', 'modifyOtherHours');
			}
		}

		$hasPrivTodoFinish = common::hasPriv('kevinhours', 'finish');
		if ($hasPrivTodoFinish) {
			$hasPrivTodoFinish = $this->isSelfUser;
		}

		$hasPrivTodoBatchcreate = common::hasPriv('kevinhours', 'batchcreate');
		if ($hasPrivTodoBatchcreate) {
			$hasPrivTodoBatchcreate = $this->isSelfUser;
		}

		$hasPrivTodoCreate = common::hasPriv('kevinhours', 'create');
		if ($hasPrivTodoCreate) {
			$hasPrivTodoCreate = $this->isSelfUser;
		}

		$dayChar['nor']					 = 'W';
		$dayChar['hol']					 = 'S';
		$dayChar['law']					 = 'H';
		if (is_null($this->kevincalendar))
			$this->loadModel('kevincalendar');
		$this->kevincalendar->account	 = $this->account;
		$statusArray					 = $this->kevincalendar->getInofoByMonth($type, 'todo');
		$countOfThisMonth				 = count($statusArray);
		if ($countOfThisMonth < 42) {
			return "";
		}
		//加班时间统计
		$normalOveHours		 = 0;
		$weekendOveHours	 = 0;
		$holidayOveHours	 = 0;
		$strOneTodo			 = "";
		$strTodosBlock		 = "";
		$CalendarTableString = '<table class = "k-table">';
		$CalendarTableString .= '<tr class = "k-title-tr">';
		$CalendarTableString .= '<th style = "width: 145px;" class = "k-title-tr k-Color-W">星期一</th>';
		$CalendarTableString .= '<th style = "width: 145px;" class = "k-title-tr k-Color-W">星期二</th>';
		$CalendarTableString .= '<th style = "width: 145px;" class = "k-title-tr k-Color-W">星期三</th>';
		$CalendarTableString .= '<th style = "width: 145px;" class = "k-title-tr k-Color-W">星期四</th>';
		$CalendarTableString .= '<th style = "width: 145px;" class = "k-title-tr k-Color-W">星期五</th>';
		$CalendarTableString .= '<th style = "width: 145px;" class = "k-title-tr k-Color-S">星期六</th>';
		$CalendarTableString .= '<th style = "width: 145px;" class = "k-title-tr k-Color-S">星期天</th>';
		$CalendarTableString .= '</tr>';
		for ($index = 0; $index < 6; $index++) {
			$CalendarTableString .= '<tr class = "k-table-tr2" style="vertical-align:top;">';
			for ($weekday = 0; $weekday < 7; $weekday++) {
				$showDay	 = '&nbsp;';
				$status		 = '';
				$type		 = '';
				$currentItem = current($statusArray);
				$day		 = $currentItem->date;
				$status		 = $currentItem->status;
				$type		 = $currentItem->type;
				$showDay	 = date('j', strtotime($day));
				next($statusArray); //索引下移
				$bgColor	 = 'k-Color-' . $dayChar[$status];
				if($day==date('Y-m-d')){$daystyle='border:5px solid #00f;';$showDay='<strong>'.$showDay.'</strong>';}
				else $daystyle='';
				$CalendarTableString .= "<td class = 'k-table";
				if ('C' != $type) {
					$CalendarTableString .= " text-muted";
				}
				$CalendarTableString .= "' style='".$daystyle."'><div class = 'div-k-day $bgColor'>";
				$CalendarTableString .= $showDay;

				if (common::hasPriv('kevinhours', 'modifyOtherHours') || $this->isShowOperateButton($day)) {
					//打印批量添加按钮
					if ($hasPrivTodoBatchcreate) {
						$CalendarTableString .= "<div class='pull-right'>";

						$CalendarTableString .= html::a(helper::createLink('kevinhours', 'batchcreate'
									, "date=" . str_replace('-', '', $day), '', true)
								, "<i class='icon-plus-sign'></i>", '', "data-toggle='modal' data-type='iframe' data-width='85%'");
						$CalendarTableString .= "</div>";
					}
				}

				//如果有注释
				if(count($currentItem->desc)){
					$CalendarTableString .= "<div class='pull-right'>";
					$CalendarTableString .= mb_substr($currentItem->desc,0,6, 'utf-8');
					$CalendarTableString .= "</div>";
				}

				$CalendarTableString .= "</div>";

				if (!empty($currentItem->todos)) {
					$abnorflag=$this->checkabnormal($currentItem->todos,$showDay);
					$strTodosBlock = '<table width=100% class="table-striped k-item" >';
					//每行 todo
					foreach ($currentItem->todos as $todo) {
						$href			 = helper::createLink('kevinhours', 'view', "id=$todo->id", '', true);
						$tempBgColor	 = 'k-' . $todo->status;
						$hours			 = substr("" . ($todo->minutes / 60), 0, 4);
						$hourtype		 = $todo->hourstype;
						$hourTypeString	 = '';
						$strOneTodo		 = "\r\n";
						if ($hourtype == '') {
							$hourtype = 'nor';
						}
						//统计加班
						if ('ove' === $hourtype) {
							$tempTimes = strtotime($todo->date);
							if ($tempTimes >= $firstDayOfCurrentMonthTimes && $tempTimes <= $endDayOfCurrentMonthTimes) {
								if ($this->lang->kevinhours->hoursTypeLaw == $status) {
									$holidayOveHours += $hours;
								} else if ($this->lang->kevinhours->hoursTypeHol == $status) {
									$weekendOveHours += $hours;
								} else {
									$normalOveHours += $hours;
								}
							}
						}
						$tempTitle = '"' . $todo->name
								. '. ' . $todo->begin . '~' . $todo->end
								. '. ' . $this->lang->kevinhours->hoursTypeList[$hourtype]
								. '. ' . $this->lang->kevinhours->statusList[$todo->status] . '"';
						$strOneTodo .= "<tr class = 'text-center'>";
						$strOneTodo .= '<td class ="w-20px" >' . $hours . '</td>';
						$strOneTodo .= '<td class ="w-15px" style= "background:'
							. $this->lang->kevinhours->hoursTypeColor[$hourtype] . '">'
							. $this->lang->kevinhours->hoursTypeChar[$hourtype] . '</td>';

						$strOneTodo .= "<td class = 'text-left w-auto' >";
						//name and actions
						$strOneTodo .= "<table width=100% class='table-fixed'><tr class = '$tempBgColor'><td class = 'text-left w-auto' >";
						$strOneTodo .= "<b><a href='$href' data-toggle='modal' data-type='iframe' title=$tempTitle>";
						$strOneTodo .= mb_substr($todo->name, 0, 10, 'utf-8'); //截取字符串前几位
						$strOneTodo .= '</a></b></td>';
						if (common::hasPriv('kevinhours', 'modifyOtherHours') || $this->isShowOperateButton($day)) {
							//actions
							if ('done' != $todo->status && $hasPrivTodoFinish) {
								$strOneTodo .= '<td class = "text-right w-15px ">';
								$strOneTodo .= html::a(helper::createLink('kevinhours', 'finish'
											, "id=$todo->id", '', true), "<i class='icon-todo-finish icon-ok-sign'></i> ", '', " data-toggle='modal' data-type='iframe'");
								$strOneTodo .= '</td>';
							}
							//edit button
							if ($hasPrivTodoEdit) {
								$strOneTodo .= '<td class = "text-right w-15px">';
								$strOneTodo .= html::a(helper::createLink('kevinhours', 'edit'
											, "id=$todo->id", '', true), "<i class='icon-pencil'></i> ", '', " data-toggle='modal' data-type='iframe'");
								$strOneTodo .= '</td>';
							}
						}

						$strOneTodo .= '</tr></table>';

						$strOneTodo .= '</td></tr>';
						$strTodosBlock .= $strOneTodo;
					}
					if($abnorflag)$strTodosBlock .="<tr><td colspan=3 style='color:red;background:yellow;' title='正常工作时间不足或超出8小时！'><strong><font size='3'>！！异常考勤！！</font></strong></td></tr>";
					$strTodosBlock .= '</table>';
					$CalendarTableString .= $strTodosBlock;
				} else
					$CalendarTableString .= "<div></div></td>";
			}
			$CalendarTableString .= '</tr>';
		}
		$CalendarTableString .= '</table>';
		$oveHoursTableString = '';
		$totalOvehoursBare	 = $weekendOveHours + $holidayOveHours + $normalOveHours;
		$totalOveHours		 = $weekendOveHours * 2 + $holidayOveHours * 3 + $normalOveHours * 1.5;
		$totalOveHours		 = number_format($totalOveHours, 2);
		$totalCash			 = $totalOveHours * $this->getPersonalRatePay();
		$oveHoursTableString = '<table  class = "overworktable"><tr>'
			. '<td>加班小时：</td>'
			. '<td title = "平时加班" class="boxoverhours2">' . $normalOveHours . '</td>'
			. '<td title = "周末加班" class="boxoverhours">' . $weekendOveHours . '</td>'
			. '<td title = "假日加班" class="boxoverhours2">' . $holidayOveHours . '</td>'
			. '<td title = "合计时间" class="boxoverhours">' . $totalOvehoursBare . '</td>'
			. '<td title = "加权时间" class="boxoverhours2">' . $totalOveHours . '</td>';
		if ($this->isSelfUser) {
			$oveHoursTableString .= '<td title = "加班费估算"><font size=4 color=#FF00FF><b>加班费估算: ' . $totalCash . '元</td>'
				. '<td style = "min-width:50px;">&nbsp;</td>'
				. '<td align=right>'
				. html::a(helper::createLink('kevinhours', 'ratepay', '', '', true)
					, "<i class='icon-yen'></i>修改小时工资", '', "class='btn btn-info' data-toggle='modal' data-type='iframe'")
				. '</td>';
		}
		$oveHoursTableString .='</tr></table>';

		return array('todoTable' => $CalendarTableString, 'oveHoursTable' => $oveHoursTableString);
	}

	/**
	 * get dept grade info by account
	 * $account
	 * @access public
	 * @return string of dept grade
	 */
	public function getdeptinfobyaccount($account){
		$accinfo=$this->dao->select('b.grade')->from(TABLE_USER)->alias('a')
				->leftJoin(TABLE_DEPT)->alias('b')->on('a.dept=b.id')
				->where('account')->eq($account)
				->fetch();
		if(isset($accinfo->grade))return $accinfo->grade;
		else return 0;
	}
	
	/**
	 * check abnormal absent
	 * @param $todos 
	 * @param $showDay
	 * @access public
	 * @return boolean $abnorflag
	 */
	public function checkabnormal(&$todos,$showDay){
		if (!empty($todos)){
			$norHoursArray = array();
			$holHoursArray = array();
			$annHoursArray = array();
			$excHoursArray = array();
			foreach ($todos as $todo) {
				$hours	  = substr("" . ($todo->minutes / 60), 0, 4);
				$hourtype = $todo->hourstype;
				if('nor' == $hourtype)
				{
					if(array_key_exists($showDay, $norHoursArray)) $norHoursArray[$showDay] += $hours;
					else $norHoursArray[$showDay] = $hours;
				}
				else if('hol' ==$hourtype)
				{
					//请假
					if(array_key_exists($showDay, $holHoursArray)) $holHoursArray[$showDay] += $hours;
					else $holHoursArray[$showDay] = $hours;
				}
				else if('ann' == $hourtype)
				{
					//年假
					if(array_key_exists($showDay, $annHoursArray)) $annHoursArray[$showDay] += $hours;
					else $annHoursArray[$showDay] = $hours;
				}
				else if('rep' == $hourtype)
				{
					//调休
					if(array_key_exists($showDay, $excHoursArray)) $excHoursArray[$showDay] += $hours;
					else $excHoursArray[$showDay] = $hours;
				}
			}
			if(array_key_exists($showDay, $norHoursArray)){
				$totalnorhours=0;
				if(array_key_exists($showDay, $norHoursArray))$totalnorhours+=$norHoursArray[$showDay];
				if(array_key_exists($showDay, $holHoursArray))$totalnorhours+=$holHoursArray[$showDay];
				if(array_key_exists($showDay, $annHoursArray))$totalnorhours+=$annHoursArray[$showDay];
				if(array_key_exists($showDay, $excHoursArray))$totalnorhours+=$excHoursArray[$showDay];
				if($totalnorhours!=8)return true;else return false;
			}else return false;
		}else return false;
	}
	
	/**
	 * calculateClockTable function, 构造打卡字符串.
	 * $type 月份类型
	 * @access public
	 * @return void
	 */
	function calculateClockTable($type) {
		$firstDayOfCurrentMonthTimes = strtotime($type . '01');
		$dayNum						 = cal_days_in_month(CAL_GREGORIAN, date('m', $firstDayOfCurrentMonthTimes), date('Y', $firstDayOfCurrentMonthTimes)); // 天数
		$endDayOfCurrentMonthTimes	 = strtotime($type . $dayNum);
		if ($this->account == '')
			return;

		$dayChar['nor']					 = 'W';
		$dayChar['hol']					 = 'S';
		$dayChar['law']					 = 'H';
		if (is_null($this->kevincalendar))
			$this->loadModel('kevincalendar');
		$this->kevincalendar->account	 = $this->account;
		$statusArray					 = $this->kevincalendar->getInofoByMonth($type, 'clock');
		$countOfThisMonth				 = count($statusArray);
		if ($countOfThisMonth < 42) {
			return "";
		}

		$datebegin	 = current($statusArray)->date;
		$dateend	 = end($statusArray)->date;
		reset($statusArray);

		$items = $this->getClockItems($datebegin, $dateend);

		//加班时间统计
		$normalOveHours		 = 0;
		$weekendOveHours	 = 0;
		$holidayOveHours	 = 0;
		$strOneTodo			 = "";
		$strTodosBlock		 = "";
		$CalendarTableString = '<table class = "k-table">';
		$CalendarTableString .= '<tr class = "k-title-tr">';
		$CalendarTableString .= '<th style = "width: 145px;" class = "k-title-tr k-Color-W">星期一</th>';
		$CalendarTableString .= '<th style = "width: 145px;" class = "k-title-tr k-Color-W">星期二</th>';
		$CalendarTableString .= '<th style = "width: 145px;" class = "k-title-tr k-Color-W">星期三</th>';
		$CalendarTableString .= '<th style = "width: 145px;" class = "k-title-tr k-Color-W">星期四</th>';
		$CalendarTableString .= '<th style = "width: 145px;" class = "k-title-tr k-Color-W">星期五</th>';
		$CalendarTableString .= '<th style = "width: 145px;" class = "k-title-tr k-Color-S">星期六</th>';
		$CalendarTableString .= '<th style = "width: 145px;" class = "k-title-tr k-Color-S">星期天</th>';
		$CalendarTableString .= '</tr>';
		for ($index = 0; $index < 6; $index++) {
			$CalendarTableString .= '<tr class = "k-table-tr2" style="vertical-align:top;">';
			for ($weekday = 0; $weekday < 7; $weekday++) {
				$showDay	 = '&nbsp;';
				$status		 = '';
				$type		 = '';
				$currentItem = current($statusArray);
				$day		 = $currentItem->date;
				$status		 = $currentItem->status;
				$type		 = $currentItem->type;
				$showDay	 = date('j', strtotime($day));
				next($statusArray); //索引下移
				$bgColor	 = 'k-Color-' . $dayChar[$status];
				$CalendarTableString .= "<td class = 'k-table";
				if ('C' != $type) {
					$CalendarTableString .= " text-muted";
				}
				$CalendarTableString .= "' ><div class = 'div-k-day $bgColor'>";
				$CalendarTableString .= $showDay;
				$CalendarTableString .= "</div>";

				$isDetail = array_key_exists($day, $items);
				if ($isDetail) {
					$curItems		 = $items[$day];
					$strTodosBlock	 = '<table width=100% class="table-striped k-item" >';
					//每行 todo
					foreach ($curItems as $todo) {
						$action		 = $todo->action;
						$strOneTodo	 = "\r\n";
						$strOneTodo .= "<tr class = 'text-center'>";

						$strOneTodo .= '<td>' . $this->lang->kevinhours->$action . '</td>';
						$strOneTodo .= '<td>' . $this->formatTime($todo->time) . '</td>';
						$strOneTodo .= '</tr>';
						$strTodosBlock .= $strOneTodo;
					}
					$strTodosBlock .= '</table>';
					$CalendarTableString .= $strTodosBlock;
				} else {
					$CalendarTableString .= "<div></div>";
				}
				$CalendarTableString .= "</td>";
			}
			$CalendarTableString .= '</tr>';
		}
		$CalendarTableString .= '</table>';
		return array('clock' => $CalendarTableString);
	}

	/**
	 * Check user must be self.
	 * 
	 * @param  string|date $date 
	 * @param  string      $account 
	 * @access public
	 * @return void
	 */
	public function checkUserMustSelf($account = '') {
		if ($account != '' && $account != $this->app->user->account) {
			$this->dao->logError('ErrorKevinMustBeSelf', '', '');
			die(js::error(dao::getError()));
		}
		$this->account = $this->app->user->account;
		return $this->account;
	}

	/**
	 * clock action
	 *      
	 * @access public
	 * @param  string    $action 
	 * @param  bool    $ok 
	 * @return id
	 */
	public function clockact($action = 'in', $ok = false) {
		$account = $this->app->user->account;
		if ('admin' == $account) {
			$this->dao->logError('KevinClockactPriv', '', '');
			return;
		}

		$ip = $this->Getip();
		if ($this->config->kevinhours->ClockNetAddress != substr($ip, 0, 7)) {
			$this->dao->logError('KevinClockactPriv', '', '');
			return; //内网才可以打卡
		}

		$this->getTodayClockInfor(); //get clock

		if (!$this->clockInor->isExistIn && $action != 'in') {
			$this->dao->logError('KevinClockInFirst', '', '');
			return; //必须先上班打卡
		}
		$this->clockInor->itemTarget->action = $action;

		$itemArray	 = $this->clockInor->itemArray;
		$data		 = $this->clockInor->itemTarget;

		$timeOut			 = "";
		$timeIn				 = "";
		$itemExist			 = NULL;
		$isExist			 = false;
		$showExistWarning	 = false;
		if ($itemArray) {
			if ($this->clockInor->isExistIn) {
				$item	 = $itemArray['in'][0];
				$timeIn	 = $item->time;
				if ($action == 'in') {
					$this->dao->logError('KevinClockInOnly1', '', '');
					return; //上班只能打一次卡
				}
			}
			if ($this->clockInor->isExistOut) {
				$item	 = $itemArray['out'][0];
				$timeOut = $item->time;
				if ($action == 'out') {
					$isExist	 = true;
					$itemExist	 = $item;
				}
			}

			if ($isExist) {
				if ((int) $itemExist->time == $data->time) {//时间相同
					$this->dao->logError('KevinClockInMinutes', '', '');
					return; //重复打卡~~
				}
				if (!$ok) {
					$showExistWarning = true;
				}
			}
		}

		//获得目标时间
		if ($action == 'in') {
			$timeIn = $data->time;
		} else {
			$timeOut = $data->time;
		}

		if ($timeIn && $timeOut && $timeIn >= $timeOut) {
			$this->clockInor->showView = false;
			//存在时进行处理，如果不需要提示，进行更新
			$this->dao->logError('KevinClockInlaterOut', '', '');
			return;
		}

		if ($showExistWarning) {
			$this->clockInor->showView	 = true;
			$this->clockInor->item		 = $itemArray[$action][0];
			//存在时进行处理，如果不需要提示，进行更新
			$this->dao->logError('KevinRecordExist', '', '');
			return;
		}

		if ($isExist) {
			$id = $itemExist->id;
			//更新数据库
			$this->dao->update(TABLE_KEVINCLOCKACT)->set('time')->eq($data->time)
				->where('id')->eq($id)->exec();
			return $id;
		} else {
			$this->dao->insert(TABLE_KEVINCLOCKACT)->data($data)->exec();
			return $this->dao->lastInsertID();
		}
	}

	/**
	 * Create the member link.
	 * 
	 * @param  int    $dept 
	 * @access public
	 * @return string
	 */
	public static function createMemberLink($dept) {
		$linkHtml = html::a(helper::createLink('kevinhours', 'count', "type=thisMonth&account=&dept={$dept->id}&hourstype=all&employeetype=all"), $dept->name, '_self', "id='dept{$dept->id}'");
		return $linkHtml;
	}

	public static function createMemberLinkOfBrowse($dept) {
		$linkHtml = html::a(helper::createLink('kevinhours', 'browse', "param={$dept->id}"), $dept->name, '_self', "id='dept{$dept->id}'");
		return $linkHtml;
	}

	/**
	 * Create the member link.
	 * 
	 * @param  int    $dept 
	 * @access public
	 * @return string
	 */
	public static function createMemberLinkOfIndex($dept) {
		$linkHtml = html::a(helper::createLink('kevinhours', 'index', "type=&account=&deptID={$dept->id}"), $dept->name, '_self', "id='dept{$dept->id}'");
		return $linkHtml;
	}

	public function getAccountsByPeriod($dept, $year = '', $month = '', $season = '') {
		extract($this->getBeginAndEndDate($year, $month, $season));
		$accountArray	 = $this->kevincom->getDeptEmployeesAccount($dept, $this->config->kevinhours->isShowDeletedAccount);
		$accounts		 = $this->dao->select('DISTINCT(account)')->from(TABLE_TODO)
			->where('account')->in($accountArray)
			->andWhere("date >= '$begin'")
			->andWhere("date <= '$end'")
			->andWhere("hourstype != 'hol'")
			->andWhere("hourstype != 'ann'")
			->andWhere("status = 'done'")
			->query();

		$accountArray = array();
		foreach ($accounts as $account) {
			$accountArray[$account->account] = $account->account;
		}
		return $accountArray;
	}

	public function getBeginAndEndDate($year = '', $month = '', $season = '') {
		if ('' == $month && $season == '') {
			$this->app->loadClass('date');
			return date::getLastSeason();
		}
		if ('' == $year)
			$year = date('Y');
		if ('' == $month) {
			$begin	 = date('Y-m-d H:i:s', mktime(0, 0, 0, $season * 3 - 2, 1, $year));
			$endDay	 = date('t', mktime(0, 0, 0, $season * 3, 1, $year)); // Get end day.
			$end	 = date('Y-m-d H:i:s', mktime(23, 59, 59, $season * 3, $endDay, $year));
			return array('begin' => $begin, 'end' => $end);
		}
		$begin	 = $year . '-' . $month . '-01 00:00:00';
		$days	 = cal_days_in_month(CAL_GREGORIAN, $month, $year);
		$end	 = $year . '-' . $month . '-' . $days . ' 23:59:59';
		return array('begin' => $begin, 'end' => $end);
	}

	/**
	 * get Dispatch Accounts By ClassDept.
	 * 通过派遣部门进行分类，
	 * @param  int    $ClassDeptID, 分类部门，按照他的子部门进行分类
	 * @param  bool    $isShowDeleted, 是否显示删除的用户
	 * @access public
	 * @return string
	 */
	public function getDispatchAccountsByClassDept($deptID, $ClassDeptID) {
		if (!$ClassDeptID) {
			return false;
		}

		$this->DispachServiceClass->SourceDept = $this->dao->select('id,name')
			->from(TABLE_DEPT)
			->where('id')->eq($deptID)
			->fetch();
		if (!$this->DispachServiceClass->SourceDept)
			return false;

		//部门的成员
		$path = "%," . $deptID . ",%";

		$stmt = $this->dao->select('id')->from(TABLE_DEPT)
			->where('path')->like($path)
			->query();

		$SourceDeptList								 = array();
		while ($row										 = $stmt->fetch())
			$SourceDeptList[]							 = $row->id;
		$this->DispachServiceClass->SourceDeptList	 = $SourceDeptList; //list

		$TotalAccountSource = $this->kevincom->getDeptEmployeesAccount($deptID, true);
		//遍历月份数组
		foreach ($this->DispachServiceClass->ClassDeptArray as $currentDept) {
			$path = "%," . $currentDept->id . ",%";

			$stmt = $this->dao->select('id')->from(TABLE_DEPT)
				->where('path')->like($path)
				->andWhere('deleted')->eq(0)->fi()
				->query();

			$DispatchDeptArray	 = array();
			while ($row				 = $stmt->fetch()) {
				$DispatchDeptArray[] = $row->id;
			}

			$currentDept->DispatchDeptArray = $DispatchDeptArray;

			//Get Account
			$stmt = $this->dao->select('account')->from(TABLE_USER)
				->where('dept')->in($SourceDeptList)
				->andWhere('deptdispatch')->in($DispatchDeptArray)
				->query();

			$accountArray				 = array();
			while ($row						 = $stmt->fetch())
				$accountArray[]				 = $row->account;
			$currentDept->accountArray	 = $accountArray;
		}

		return true;
	}

	public function getCashCodeArrayByDept($year, $month = '', $season = '') {
		//获得月份数组

		if (0 == count($this->DispachServiceClass->monthArray))
			return false;
		$monthArray = $this->DispachServiceClass->monthArray;
		//遍历月份数组
		foreach ($this->DispachServiceClass->ClassDeptArray as $currentDept) {
			$currentDept->cashCodeArray = array();
			if (empty($currentDept->accountArray)) {
				continue;
			}

			$stmt = $this->dao->select('DISTINCT(a.cashCode),b.code AS name')->from(TABLE_HOURSCASHCODE)->alias('a')
				->leftJoin(TABLE_PROJECT)->alias('b')->on('a.cashCode = b.name')
				->where('a.dept')->in($this->DispachServiceClass->SourceDeptList)
				->andWhere('a.deptdispatch')->in($currentDept->DispatchDeptArray)
				->andWhere('a.year')->eq($year)
				->andWhere('a.month')->in($monthArray)
				->query();

			while ($cashCode = $stmt->fetch()) {
				if (!in_array($cashCode->cashCode, $currentDept->cashCodeArray)) {
					$currentDept->cashCodeArray[$cashCode->cashCode] = $cashCode;
				}
			}

			$currentDept->total = 0.0;
			foreach ($currentDept->cashCodeArray as $cashCodeInor) {
				$this->getCashCodeHoursByDept($year, $monthArray, $currentDept, $cashCodeInor);
				$currentDept->total += $cashCodeInor->total;
			}
		}

		return true;
	}

	public function getCashCodeHoursByDept($year, $monthArray, & $currentDept, & $cashCodeInor) {

		$cashCodeInor->hoursArray = array();

		$stmt = $this->dao->select('month,SUM(total) AS total')->from(TABLE_HOURSCASHCODE)
			->where('dept')->in($this->DispachServiceClass->SourceDeptList)
			->andWhere('deptdispatch')->in($currentDept->DispatchDeptArray)
			->andWhere('year')->eq($year)
			->andWhere('month')->in($monthArray)
			->andWhere('cashCode')->eq($cashCodeInor->cashCode)
			->groupBy('month')
			->fetchGroup('month');

		$cashCodeInor->total = 0.0;
		foreach ($monthArray as $month) {
			$month								 = (int) $month;
			$cashCodeInor->hoursArray[$month]	 = 0;
			if (array_key_exists((int) $month, $stmt)) {
				$total								 = (double) $stmt[$month][0]->total;
				$cashCodeInor->hoursArray[$month]	 = $total;
				$cashCodeInor->total += $total;
			}
		}

		return true;
	}

	public function getDetailCashCodeHours($deptID, $year, $month = '', $season = '', $classdept = 0) {
		$cashCodeArray	 = array();
		//获得月份数组
		$monthArray		 = $this->DispachServiceClass->monthArray;
		if (0 == count($monthArray))
			return $cashCodeArray;
		$accountArray	 = $this->kevincom->getDeptEmployeesAccount($deptID, $this->config->kevinhours->isShowDeletedAccount);
		//遍历月份数组
		foreach ($this->DispachServiceClass->ClassDeptArray as $currentDept) {
			if (empty($currentDept->accountArray)) {
				continue;
			}
			foreach ($monthArray as $currntMonth) {
				$stmt = $this->dao->select('a.*, b.code As name, c.realname')->from(TABLE_HOURSCASHCODE)->alias('a')
					->leftJoin(TABLE_PROJECT)->alias('b')->on('a.cashCode = b.name')
					->leftJoin(TABLE_USER)->alias('c')->on('a.account = c.account')
					->where('a.account')->in($currentDept->accountArray)
					->andWhere('a.year')->eq($year)
					->andWhere('a.month')->eq($currntMonth)
					->query();

				while ($cashCode = $stmt->fetch()) {
					$cashCode->classDept = $currentDept->name;
					$cashCodeArray[]	 = $cashCode;
				}
			}
		}

		return $cashCodeArray;
	}

	//获得访客真实ip
	function Getip() {
		$ip	 = null;
		$ips = null;
		if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
			$ip = $_SERVER["HTTP_CLIENT_IP"];
		}
		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) { //获取代理ip
			$ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
		}
		if ($ip) {
			$ips = array_unshift($ips, $ip);
		}

		$count = count($ips);
		for ($i = 0; $i < $count; $i++) {
			if (!preg_match("/^(10|172\.16|192\.168)\./i", $ips[$i])) {//排除局域网ip
				$ip = $ips[$i];
				break;
			}
		}
		$tip = empty($_SERVER['REMOTE_ADDR']) ? $ip : $_SERVER['REMOTE_ADDR'];
		return $tip;
	}

	public function getListOfProject($projectID, $date, $pager = null) {
		$stmt = $this->getStmtFromSql($projectID, $date);
		return $this->getTodosOfProject($stmt, $pager);
	}

	/*
	 * 判断项目代号是否存在，不存在则提示
	 * 返回值：项目id
	 */

	public function GetProjectByIDorCashCode($project) {
		//判断项目代号是否都是数字，不是则提示
		if (!ctype_digit($project)) {
			echo "<script type='text/javascript'>alert('项目代号:$project 不是数字，请输入正确的代号！');location='javascript:history.back()';</script>";
			exit;
		}
		$projectArray = $this->dao->select('id')
			->from(TABLE_PROJECT)
			->where('id')->eq($project)
			->orWhere('cashCode')->eq($project)
			->andWhere('id')->ge($this->config->kevinhours->startProject)
			->andWhere('id')->le($this->config->kevinhours->endProject)
			->fetchAll();
		if (1 == count($projectArray)) {//单条记录，符合要求
			return $projectArray[0]->id;
		} else if (0 == count($projectArray)) {//不存在
			echo "<script type='text/javascript'>alert('项目代号:$project 不存在，请输入正确的代号！');location='javascript:history.back()';</script>";
			exit;
		} else {//多条记录
			echo "<script type='text/javascript'>alert('项目代号:$project 不唯一！');location='javascript:history.back()';</script>";
			exit;
		}
	}

	public function getStmtFromSql($projectID, $date) {

		extract(kevin::getBeginEndTime($date));

		$stmt = $this->dao->select('a.*,b.code,b.realname')->from(TABLE_TODO)->alias('a')
			->leftJoin(TABLE_USER)->alias('b')->on('a.account = b.account')
			->where('a.project')->eq($projectID)
			->andWhere("a.date >= '$begin'")
			->andWhere("a.date <= '$end'")
			->andWhere('a.status')->eq('done')
			->orderBy('a.account');
		return $stmt;
	}

	/**
	 * getTodayClockInfor
	 *      
	 * @access public
	 * @param  string    $action 
	 * @param  bool    $ok 
	 * @return id
	 */
	public function getTodayClockInfor() {
		$account		 = $this->app->user->account;
		$time			 = time();
		$data			 = new stdclass();
		$data->account	 = $account;
		$data->date		 = date("Y-m-d", $time); //2010-08-29
		$data->time		 = (int) date("Hi", $time);
		if (!$this->clockInor) {
			$this->clockInor = new stdclass();
		}
		$this->clockInor->itemTarget = $data;
		$this->clockInor->isExistIn	 = false;
		$this->clockInor->isExistOut = false;
		$this->clockInor->showView	 = false;

		//查询是否存在记录
		$itemArray = $this->dao->select('*')->from(TABLE_KEVINCLOCKACT)
			->where('account')->eq($account)
			->andWhere('date')->eq($data->date)
			->fetchGroup('action');

		$this->clockInor->itemArray = $itemArray;

		if ($itemArray) {
			if (array_key_exists('in', $itemArray)) {
				$this->clockInor->isExistIn = true;
			}
			if (array_key_exists('out', $itemArray)) {
				$this->clockInor->isExistOut = true;
			}
		}
	}

	public function getTodosOfProject($stmt, $pager = null) {
		$stmt	 = $stmt->beginIF($pager != null)->page($pager)->fi()
			->query();
		$todos	 = array();
		while ($todo	 = $stmt->fetch()) {
			$todo->begin = date::formatTime($todo->begin);
			$todo->end	 = date::formatTime($todo->end);
			$todos[]	 = $todo;
		}
		return $todos;
	}

	public function getTodosOfUndoneByThisMonth() {
		$begin	 = date('Y-m-') . '01';
		$end	 = date('Y-m-d');
		$todoIDs = $this->dao->select('id')->from(TABLE_TODO)
			->where('account')->eq($this->app->user->account)
			->andWhere('status')->ne('done')
			->andWhere("date >= '$begin'")
			->andWhere("date <= '$end'")
			->fetchAll('id');
		return array_keys($todoIDs);
	}

	public function getTodosTimeByDate($date, $todoID = '') {
		$this->setAccount();
		$stmt		 = $this->dao->select('begin, end')->from(TABLE_TODO)
			->where('date')->eq($date)
			->andWhere('account')->eq($this->account)
			->beginIF($todoID != '')->andWhere('id')->ne($todoID)->fi()
			->orderBy('end')
			->fetchAll();
		if (!$stmt)
			return array();
		$timesArray	 = array();
		foreach ($stmt as $tempIndex => $tempObj) {
			$currentObj			 = new stdclass();
			$currentObj->begin	 = (int) $tempObj->begin;
			$currentObj->end	 = (int) $tempObj->end;
			$timesArray[]		 = $currentObj;
		}
		return $timesArray;
	}

	/**
	 * 获得上个月的锁定日
	 * 
	 * @access public
	 * @return date
	 */
	public function getLockedDayOfLastMonth() {
		$endDay	 = '';
		if (isset($_SESSION['endDayForKevinHours']))
			$endDay	 = $this->session->endDayForKevinHours;
		else {
			$year	 = date('Y');
			$month	 = date('m');
			$lock	 = $this->getLockedDate($year, $month);
			$endDay	 = date('d', $lock);
			$this->session->set('endDayForKevinHours', $endDay);
		}
		return $endDay;
	}

	/**
	 * 获得某月的锁定的日期
	 * 
	 * @param  string $year 
	 * @param  string $month 
	 * @param  int $workingdays 
	 * @access public
	 * @return int 
	 */
	public function getLockedDate($year = '', $month = '') {
		$endDay = $this->config->kevinhours->endDay;
		if($this->config->kevinhours->MonthLockEarly == $month) {
			//按照 endDayType == 0 来锁定
		} else 	if ($this->config->kevinhours->endDayType == 1 && !empty($this->kevincalendar)) { // working day
			$endDay = $this->kevincalendar->getWorkingDay($year, $month, $this->config->kevinhours->endDay);
		} else { //No. Day
			//按照 endDayType == 0 来锁定
		}
		return strtotime("$year-$month-$endDay");
	}

	/**
	 * 获得上个月的锁定日期
	 * 
	 * @access public
	 * @return date
	 */
	public function getLockedDateOfLastMonth() {
		$year	 = date('Y');
		$month	 = date('m');
		$endDay	 = '01';
		if (isset($_SESSION['endDayForKevinHours']))
			$endDay	 = $this->session->endDayForKevinHours;
		else {
			$lock	 = $this->getLockedDate($year, $month);
			$endDay	 = date('d', $lock);
			$this->session->set('endDayForKevinHours', $endDay);
			return $lock;
		}
		return strtotime("$year-$month-$endDay");
	}

	/**
	 * 获得本月的锁定日期
	 * 
	 * @access public
	 * @return date
	 */
	public function getLockedDateOfThisMonth() {
		$year	 = date('Y', strtotime('+1 month'));
		$month	 = kevin::getMonth(1);
		$lock	 = $this->getLockedDate($year, $month);
		return $lock;
	}

	//获得最近一段时间涉及到的项目总工时
	public function getRecentProjectTotalTime($type) {
		$this->setAccount();
		$begin	 = '';
		$end	 = '';

		extract(kevin::getBeginEndTime($type));

		$todos = $this->dao->select("SUM( CASE WHEN a.hourstype = 'nor' THEN a.minutes ELSE 0 END) as nor
				, SUM( CASE WHEN a.hourstype = 'ove' THEN a.minutes ELSE 0 END) as ove
				, SUM(CASE WHEN (a.hourstype = 'nor' or a.hourstype = 'ove') THEN a.minutes ELSE 0 END) as total")
			->from(TABLE_TODO)->alias('a')
			->leftJoin(TABLE_PROJECT)->alias('b')->on('a.project = b.id')
			->where('a.account')->eq($this->account)
			->andWhere('a.status')->eq('done')
			->andWhere('a.date')->ge($begin)
			->beginIF($end != '')->andWhere('a.date')->le($end)->fi()
			->orderBy('total_desc')
			->fetchAll();
		return $todos;
	}

	/**
	 * 获得显示帐号，保存到 $account public变量内.
	 * 
	 * @var model
	 * @access public
	 */
	public function getShowAccount($account = "") {
		if ('0' == $account) {
			$account = $this->app->user->account;
		}
		$this->user = new stdClass ();

		if ($account == "") { //没有输入时处理
			$hasSessionUser = isset($_SESSION['kevin_user_account']);
			if ($hasSessionUser) {
				$account = $_SESSION['kevin_user_account'];
			} else {
				$account = $this->app->user->account;
			}
		}

		$this->user->browseDeptHours = common::hasPriv('kevinhours', 'browseDeptHours');
		$this->user->checkAll		 = common::hasPriv('kevinhours', 'checkAll');
		$this->isSelfUser			 = ($account == $this->app->user->account);

		$stmt	 = $this->dao->select('a.realname,a.dept,a.account,a.deptdispatch')->from(TABLE_USER)->alias('a')
				->where('a.account')->eq($account)
				->query();
		$user	 = $stmt->fetch();
		if ($this->isSelfUser||$this->user->checkAll) {
			$this->employeesAll[$user->account]=new stdClass();
			$this->employeesAll[$user->account]->realname=$user->realname;
			$user = $this->app->user;
		} else {
			if (!$this->user->browseDeptHours && !$this->user->checkAll) {
				$this->employeesAll[$this->user->account]->realname=$this->user->realname;
				unset($_SESSION['kevin_user_account']);
				die(js::error($this->lang->kevinhours->ErrorCheckAll) . js::locate('back'));
			}
			if (!$this->user->checkAll) {
				//查询是否用户和本人是同一个科室的。或者服务科室
				$this->user->employees = $this->getDeptEmployeesPairs($this->app->user->dept);
				if (!array_key_exists($account, $this->user->employees)) {
					unset($_SESSION['kevin_user_account']);
					die(js::error($this->lang->kevinhours->ErrorCheckAll) . js::locate('back'));
				}
			}

			if (!$user) {//清空指定的session
				unset($_SESSION['kevin_user_account']);
				die(js::error($this->lang->kevinhours->ErrorCheckAll) . js::locate('back'));
			}
		}
		//save infor to user
		$this->user->selfAccount		 = $this->app->user->account;
		$this->user->selfRealName		 = $this->app->user->realname;
		$this->user->realname			 = $user->realname;
		$this->user->account			 = $user->account;
		$this->user->dept				 = $user->dept;
		$this->account					 = $account;
		$this->realname					 = $this->user->realname;
		$_SESSION['kevin_user_account']	 = $account;
		return;
	}

	public function getserviceList($dept, $year = '', $month = '', $season = '') {
		extract($this->getBeginAndEndDate($year, $month, $season));
		$accountArray	 = $this->kevincom->getDeptEmployeesAccount($dept, $this->config->kevinhours->isShowDeletedAccount);
		$stmt			 = $this->dao->select('a.*,b.realname')->from(TABLE_TODO)->alias('a')
			->leftJoin(TABLE_USER)->alias('b')->on('a.account = b.account')
			->where('a.account')->in($accountArray)
			->andWhere("a.date >= '$begin'")
			->andWhere("a.date <= '$end'")
			->andWhere("a.hourstype != 'hol'")
			->andWhere("a.hourstype != 'ann'")
			->andWhere("a.status = 'done'")
			->orderBy('a.date')
			->query();

		$todos = array();

		while ($todo = $stmt->fetch()) {
			$todos[] = $todo;
		}
		return $todos;
	}

	public function getStmtByPeriod($dept, $year = '', $month = '', $season = '') {
		extract($this->getBeginAndEndDate($year, $month, $season));
		$accountArray	 = $this->kevincom->getDeptEmployeesAccount($dept, $this->config->kevinhours->isShowDeletedAccount);
		$stmt			 = $this->dao->select('a.*,b.realname')->from(TABLE_TODO)->alias('a')
			->leftJoin(TABLE_USER)->alias('b')->on('a.account = b.account')
			->where('a.account')->in($accountArray)
			->andWhere("a.date >= '$begin'")
			->andWhere("a.date <= '$end'")
			->andWhere("a.hourstype != 'hol'")
			->andWhere("a.hourstype != 'ann'")
			->andWhere("a.status = 'done'");

		return $stmt;
	}

	public function getTodosByAccountAndPeriod($account, $year, $month) {
		extract($this->getBeginAndEndDate($year, $month, ''));
		$stmt = $this->dao->select('a.*,b.realname')->from(TABLE_TODO)->alias('a')
			->leftJoin(TABLE_USER)->alias('b')->on('a.account = b.account')
			->where('a.account')->eq($account)
			->andWhere("a.date >= '$begin'")
			->andWhere("a.date <= '$end'")
			->andWhere("a.hourstype != 'hol'")
			->andWhere("a.hourstype != 'ann'")
			->andWhere("a.status = 'done'")
			->query();

		$todos = array();

		while ($todo = $stmt->fetch()) {
			$todos[] = $todo;
		}
		return $todos;
	}

	public function getWorkMinutes($workhours, $begin = '', $end = '') {
		if ($workhours == '') {
			$endArray	 = explode(':', $end);
			$beginTime	 = 60 * ((int) substr($begin, 0, 2)) + ((int) substr($begin, 2, 2));
			$endTime	 = 60 * ((int) substr($end, 0, 2)) + ((int) substr($end, 2, 2));
			if ($begin > $end)
				return $beginTime + 60 * 12 - $endTime;
			else
				return $endTime - $beginTime;
		}
		$array = explode(':', $workhours);
		return 60 * $array[0] + $array[1];
	}

	public function getYearList($year = 10) {
		$yearList = array();
		for ($i = -1; $i < $year; $i++) {
			$yearList[date('Y') - $i] = (date('Y') - $i) . '年';
		}
		return $yearList;
	}

	public function isRecordNotExist($account, $year, $month, $cashCode, $project, $isCashCode) {
		if ('' == $cashCode && '' == $project)
			return -1; //错误
		if ($isCashCode) {
			$ret = $this->dao->select('id')->from(TABLE_HOURSCASHCODE)
				->where('account')->eq($account)
				->andWhere('year')->eq($year)
				->andWhere('month')->eq($month)
				->andWhere('cashCode')->eq($cashCode)
				->fetch();
			if ($ret)
				return $ret->id; //更新
			return 0; //插入
		}
		else {
			$ret = $this->dao->select('id')->from(TABLE_HOURSPROJECT)
				->where('account')->eq($account)
				->andWhere('year')->eq($year)
				->andWhere('month')->eq($month)
				->andWhere('project')->eq($project)
				->andWhere('cashCode')->eq($cashCode)
				->fetch();
			if ($ret)
				return $ret->id; //更新
			return 0;
		}
		return -1;
	}

	public function isShowDetailTodo($isShowDetail) {
		if ('' == $isShowDetail) {
			if (!isset($_SESSION['isShowDetailTodo'])) {
				$isShowDetail = 'false';
				$this->session->set('isShowDetailTodo', 'false');
			}
		} else {
			if ('true' === $isShowDetail) {
				$this->session->set('isShowDetailTodo', $isShowDetail);
			} else {
				$this->session->set('isShowDetailTodo', 'false');
			}
		}
	}

	public function updateCashStat($dept, $year, $month, $season) {
		//获得范围内的stmt
		//$stmt			 = $this->getStmtByPeriod($dept, $year, $month, $season);
		//获得月份数组
		$monthArray		 = kevin::getMonthBySeason($month, $season);
		//代办所有者数组
		$accountArray	 = $this->getAccountsByPeriod($dept, $year, $month, $season);
		$accountInors	 = $this->dao->select('account,dept,deptdispatch')->from(TABLE_USER)
			->where('account')->in($accountArray)
			->fetchGroup('account');

		//遍历成员数组
		foreach ($accountArray as $account) {
			if (!array_key_exists($account, $accountInors)) {
				continue;
			}
			$currentAccount = $accountInors[$account][0];
			//遍历月份数组
			foreach ($monthArray as $month) {
				$todos				 = $this->getTodosByAccountAndPeriod($account, $year, $month);
				//代办项目代号数组
				$cashCodeArray		 = array();
				//代办项目人时数组（总的，未分摊）
				$manHoursArray		 = array();
				//项目代号数组
				$projectArray		 = array();
				//项目代号时间数组
				$projectHoursArray	 = array();
				$currentHours		 = 0;
				foreach ($todos as $todo) {
					//当前考勤时间（小时）
					$currentHours	 = $this->showHours($todo->minutes);
					//当前项目代号
					$currentProject	 = $todo->project;
					//获得项目付费号
					$cashCode		 = $this->kevincom->getCashCodeByProject($currentProject);
					//如果不存在，则添加一条付费号于付费号数组
					if (!in_array($cashCode, $cashCodeArray)) {
						$cashCodeArray[] = $cashCode;
					}
					//如果不存在，则添加一条项目代号
					if (!in_array($currentProject, $projectArray)) {
						$projectArray[] = $currentProject;
					}
					//获得付费号对应的人时
					if (array_key_exists($cashCode, $manHoursArray))
						$manHoursArray[$cashCode] += $currentHours;
					else
						$manHoursArray[$cashCode]			 = $currentHours;
					//获得付费号对应的人时
					if (array_key_exists($currentProject, $projectHoursArray))
						$projectHoursArray[$currentProject] += $currentHours;
					else
						$projectHoursArray[$currentProject]	 = $currentHours;
				}
				//项目代号数组排序,按数字大小
				sort($cashCodeArray, SORT_NUMERIC);

				//以下是往付费号表中写数据
				$cashCodeArrayLength = count($cashCodeArray);
				$perHours			 = 0;
				$amountto			 = 0;
				//遍历付费号数组
				for ($cashCodeIndex = 0; $cashCodeIndex < $cashCodeArrayLength; $cashCodeIndex++) {
					//如果无付费号
					if ($cashCodeArray[$cashCodeIndex] == '' && $cashCodeArrayLength > 1) {
						$amountto	 = $manHoursArray[''];
						$perHours	 = $amountto / ($cashCodeArrayLength - 1);
						//保留小数点一位并四舍五入，除最后一条外平均分摊的时间
						$perHours	 = number_format($perHours, 1);
						continue;
					}
					//获得项目付费号
					$project = $cashCodeArray[$cashCodeIndex];
					//未均摊前初始时间
					$hours	 = $manHoursArray[$project];
					//判断数据库中是否存在记录
					$ret	 = $this->isRecordNotExist($account, $year, $month, $project, $project, true);
					//如果是项目付费号的最后一条
					if ($cashCodeIndex == $cashCodeArrayLength - 1) {
						//均摊所得时间
						$addHours	 = $amountto - $perHours * ($cashCodeArrayLength - 2);
						//总计时间
						$totalHours	 = $hours + $addHours;

						//判断返回值大小
						if (0 == $ret) {
							//写入到数据库
							$this->dao->insert(TABLE_HOURSCASHCODE)
								->set('account')->eq($account)
								->set('dept')->eq($currentAccount->dept)
								->set('deptdispatch')->eq($currentAccount->deptdispatch)
								->set('year')->eq($year)
								->set('month')->eq($month)
								->set('cashCode')->eq($project)
								->set('hours')->eq($hours)
								->set('amountto')->eq($addHours)
								->set('total')->eq($totalHours)
								->exec();
						} else if (-1 == $ret)
							continue;
						else {
							//更新数据库
							$this->dao->update(TABLE_HOURSCASHCODE)
								->set('account')->eq($account)
								->set('dept')->eq($currentAccount->dept)
								->set('deptdispatch')->eq($currentAccount->deptdispatch)
								->set('year')->eq($year)
								->set('month')->eq($month)
								->set('cashCode')->eq($project)
								->set('hours')->eq($hours)
								->set('amountto')->eq($addHours)
								->set('total')->eq($totalHours)
								->where('id')->eq($ret)
								->exec();
						}

						if (dao::isError()) {
							echo js::error(dao::getError());
							die(js::reload('parent'));
						}
						continue;
					}
					//均摊所得时间
					$addHours	 = $perHours;
					//总计时间
					$totalHours	 = $hours + $addHours;

					//判断返回值大小
					if (0 == $ret) {
						//写入到数据库
						$this->dao->insert(TABLE_HOURSCASHCODE)
							->set('account')->eq($account)
							->set('dept')->eq($currentAccount->dept)
							->set('deptdispatch')->eq($currentAccount->deptdispatch)
							->set('year')->eq($year)
							->set('month')->eq($month)
							->set('cashCode')->eq($project)
							->set('hours')->eq($hours)
							->set('amountto')->eq($addHours)
							->set('total')->eq($totalHours)
							->exec();
					} else if (-1 == $ret)
						continue;
					else {
						//更新数据库
						$this->dao->update(TABLE_HOURSCASHCODE)
							->set('account')->eq($account)
							->set('dept')->eq($currentAccount->dept)
							->set('deptdispatch')->eq($currentAccount->deptdispatch)
							->set('year')->eq($year)
							->set('month')->eq($month)
							->set('cashCode')->eq($project)
							->set('hours')->eq($hours)
							->set('amountto')->eq($addHours)
							->set('total')->eq($totalHours)
							->where('id')->eq($ret)
							->exec();
					}
					if (dao::isError()) {
						echo js::error(dao::getError());
						die(js::reload('parent'));
					}
				}
				/*
				//以下是往项目时间表中写数据
				$projectArrayLength = count($projectArray);
				//遍历项目号数组
				for ($projectIndex = 0; $projectIndex < $projectArrayLength; $projectIndex++) {
					//获得项目号
					$project	 = $projectArray[$projectIndex];
					//获得项目付费号
					$cashCode	 = $this->kevincom->getCashCodeByProject($project);
					//项目花费时间
					$hours		 = $projectHoursArray[$project];
					//判断数据库中是否存在记录
					$ret		 = $this->isRecordNotExist($account, $year, $month, $cashCode, $project, false);
					if (0 == $ret) {
						//插入到数据库
						$this->dao->insert(TABLE_HOURSPROJECT)
							->set('account')->eq($account)
							->set('dept')->eq($currentAccount->dept)
							->set('deptdispatch')->eq($currentAccount->deptdispatch)
							->set('year')->eq($year)
							->set('month')->eq($month)
							->set('project')->eq($project)
							->set('cashCode')->eq($cashCode)
							->set('hours')->eq($hours)
							->exec();
					} else if (-1 == $ret)
						continue;
					else {
						//更新数据库
						$this->dao->update(TABLE_HOURSPROJECT)
							->set('account')->eq($account)
							->set('dept')->eq($currentAccount->dept)
							->set('deptdispatch')->eq($currentAccount->deptdispatch)
							->set('year')->eq($year)
							->set('month')->eq($month)
							->set('project')->eq($project)
							->set('cashCode')->eq($cashCode)
							->set('hours')->eq($hours)
							->where('id')->eq($ret)
							->exec();
					}

					if (dao::isError()) {
						echo js::error(dao::getError());
						die(js::reload('parent'));
					}
				}//for
				 
				 */
			}
		}
	}

	public function updateRatePay($newRatePay) {
		$this->dao->update(TABLE_USER)
			->set('ratepay')->eq($newRatePay)
			->where('account')->eq($this->app->user->account)
			->exec();
	}

	/**
	 * add Default Time Sag. add 4 hours
	 * 
	 * @param  time $time 
	 * @access public
	 * @return time
	 */
	public function addDefaultTimeSag($time) {
		$hour	 = substr($time, 0, 2);
		$minute	 = substr($time, 2, 2);
		$hour += 4;
		if ($hour >= 24)
			$hour	 = $hour - 24;
		return sprintf('%02d%02d', $hour, $minute);
	}

	public function userbatchedit() {
		$oldUsers		 = $this->dao->select('id, account')->from(TABLE_USER)->where('id')->in(array_keys($this->post->account))->fetchPairs('id', 'account');
		$accountGroup	 = $this->dao->select('id, account')->from(TABLE_USER)->where('account')->in($this->post->account)->fetchGroup('account', 'id');

		$accounts = array();
		foreach ($this->post->account as $id => $account) {
			$users[$id]['account']		 = $account;
			$users[$id]['code']			 = $this->post->code[$id];
			$users[$id]['realname']		 = $this->post->realname[$id];
			$users[$id]['email']		 = $this->post->email[$id];
			$users[$id]['dept']			 = $this->post->dept[$id] == 'ditto' ? (isset($prev['dept']) ? $prev['dept'] : 0) : $this->post->dept[$id];
			$users[$id]['deptdispatch']	 = $this->post->deptdispatch[$id] == 'ditto' ? (isset($prev['dept']) ? $prev['dept'] : 0) : $this->post->deptdispatch[$id];


			if (isset($accountGroup[$account]) and count($accountGroup[$account]) > 1)
				die(js::error(sprintf($this->lang->user->error->accountDupl, $id)));
			if (in_array($account, $accounts))
				die(js::error(sprintf($this->lang->user->error->accountDupl, $id)));
			if (!validater::checkAccount($users[$id]['account']))
				die(js::error(sprintf($this->lang->user->error->account, $id)));
			if ($users[$id]['realname'] == '')
				die(js::error(sprintf($this->lang->user->error->realname, $id)));
			if ($users[$id]['email'] and ! validater::checkEmail($users[$id]['email']))
				die(js::error(sprintf($this->lang->user->error->mail, $id)));

			$accounts[$id]	 = $account;
			$prev['dept']	 = $users[$id]['dept'];
		}

		foreach ($users as $id => $user) {
			$this->dao->update(TABLE_USER)->data($user)->where('id')->eq((int) $id)->exec();
			if ($user['account'] != $oldUsers[$id]) {
				$oldAccount = $oldUsers[$id];
				$this->dao->update(TABLE_USERGROUP)->set('account')->eq($user['account'])->where('account')->eq($oldAccount)->exec();
				if (strpos($this->app->company->admins, ',' . $oldAccount . ',') !== false) {
					$admins = str_replace(',' . $oldAccount . ',', ',' . $user['account'] . ',', $this->app->company->admins);
					$this->dao->update(TABLE_COMPANY)->set('admins')->eq($admins)->where('id')->eq($this->app->company->id)->exec();
				}
				if (!dao::isError() and $this->app->user->account == $oldAccount)
					$this->app->user->account = $users['account'];
			}
		}
	}

	/**
	 * Batch update todos.
	 * 
	 * @access public
	 * @return array
	 */
	public function batchUpdate() {
		$todos		 = array();
		$allChanges	 = array();
		$data		 = fixer::input('post')->get();
		$todoIDList	 = $this->post->todoIDList ? $this->post->todoIDList : array();

		/* Adjust whether the post data is complete, if not, remove the last element of $todoIDList. */
		if ($this->session->showSuhosinInfo)
			array_pop($taskIDList);

		if (!empty($todoIDList)) {
			foreach ($todoIDList as $todoID) {
				//判断项目id如果是否存在,如有不存在则不提交
				$this->GetProjectByIDorCashCode($data->project[$todoID]);
			}
			/* Initialize todos from the post data. */
			foreach ($todoIDList as $todoID) {
				$todo			 = new stdclass();
				$todo->date		 = $data->dates[$todoID];
				$todo->type		 = 'custom';
				$todo->hourstype = $data->hourstypes[$todoID];
				$todo->pri		 = $data->pris[$todoID];
				//判断项目id如果是否存在，并获得项目代号
				$todo->project	 = $this->GetProjectByIDorCashCode($data->project[$todoID]);
				if (!$this->isDateFuture($todo->date))
					$todo->status	 = $data->status[$todoID];
				else
					$todo->status	 = 'wait';
				$todo->name		 = $todo->type == 'custom' ? $data->names[$todoID] : '';
				$todo->begin	 = $data->begins[$todoID];
				$todo->end		 = $data->ends[$todoID];
				if ($todo->begin == 0)
					$todo->begin	 = 2400;
				if ($todo->end == 0)
					$todo->end		 = 2400;
				$todo->minutes	 = $this->getWorkMinutes($data->minutes[$todoID]);

				if ($todo->type == 'task')
					$todo->idvalue	 = isset($data->tasks[$todoID]) ? $data->tasks[$todoID] : 0;
				if ($todo->type == 'bug')
					$todo->idvalue	 = isset($data->bugs[$todoID]) ? $data->bugs[$todoID] : 0;

				$todos[$todoID] = $todo;
			}

			foreach ($todos as $todoID => $todo) {
				$oldTodo		 = $this->getTodoById($todoID);
				if ($oldTodo->type != 'custom')
					$oldTodo->name	 = '';
				$this->dao->update(TABLE_TODO)->data($todo)
					->autoCheck()
					->checkIF(true, $this->config->todo->edit->requiredFields, 'notempty')
					->where('id')->eq($todoID)
					->exec();

				if ($oldTodo->status != 'done' and $todo->status == 'done')
					$this->loadModel('action')->create('todo', $todoID, 'finished', '', 'done');

				if (!dao::isError()) {
					$todo->date			 = str_replace('-', '', $todo->date);
					$allChanges[$todoID] = common::createChanges($oldTodo, $todo);
				} else {
					die(js::error('todo#' . $todoID . dao::getError(true)));
				}
			}
		}

		return $allChanges;
	}

	/**
	 * 获得日期列表,前14天后5天便于手机端输入日期
	 * 现在设置日期修改为14天,如果修改日期为一周,则需要将参数before修改为7
	 */
	public function buildDateList($before = -14, $after = 5) {
		$dates	 = array();
		$today	 = date('Y-m-d');
		for ($i = $before; $i <= $after; $i++) {
			$currentDate		 = date("Y-m-d", strtotime("$i day"));
			$dates[$currentDate] = $currentDate;
		}
		return $dates;
	}

	/**
	 * Create a todo.
	 * 
	 * @param  date   $date 
	 * @access public
	 * @return void
	 */
	public function create($date) {
		if ($this->account == "")
			return false;
		if (($this->post->end == $this->post->begin)) {
			$this->dao->logError('ErrorTodoHoursSameTime', '', '');
			return;
		}
		//验证时间是否重叠
		if (isset($_POST['date'])) {
			//获得时间，用于验证时间重叠
			$todo		 = new stdclass();
			$todo->id	 = "";
			$todo->begin = $this->post->begin;
			$todo->end	 = $this->post->end;
			if ($this->isTodoTimeOverlapOne($this->post->date, $todo))
				return;
		}

		$minutes = $this->kevincom->getWorkMinutes($this->post->minutes, $this->post->begin, $this->post->end);
		$todo	 = fixer::input('post')
			->add('account', $this->account)
			->add('idvalue', 0)
			->add('type', 'custom')
			->add('pri', 3)
			->add('minutes', $minutes)
			->setIF($this->post->begin == 0, 'begin', '2400')
			->setIF($this->post->end == 0, 'end', '2400')
			->skipSpecial($this->config->kevinhours->editor->create['id'])
			->remove('bug, task, projectNameBox, projectName')
			->get();
		$this->dao->insert(TABLE_TODO)->data($todo)
			->autoCheck()
			->checkIF(true, $this->config->kevinhours->create->requiredFields, 'notempty')
			->exec();
		return $this->dao->lastInsertID();
	}

	/**
	 * Change the status of a todo.
	 * 
	 * @param  string $todoID 
	 * @param  string $status 
	 * @access public
	 * @return void
	 */
	public function finish($todoID) {
		$todoDate = $this->getTodoDateById($todoID);
		if ($this->isDateFuture($todoDate)) {
			$this->dao->logError('ErrorKevinAfterToday', '', '');
			return;
		} else if (!$this->timeVerificated($todoDate)) { //时间验证
			return;
		}

		$this->dao->update(TABLE_TODO)->set('status')->eq('done')->where('id')->eq((int) $todoID)->exec();
		$this->loadModel('action')->create('todo', $todoID, 'finished', '', 'done');
		return;
	}

	/**
	 * Format time 0915 to 09:15
	 * 
	 * @param  string $time 
	 * @access public
	 * @return string
	 */
	public function formatTime($time) {
		$len	 = strlen($time);
		if ($len < 3 || $len > 4)
			return '';
		if ($time == '2400')
			return '00:00';
		$hour	 = substr($time, 0, $len - 2);
		if ($len == 3)
			$hour	 = '0' . $hour;

		return $hour . ':' . substr($time, -2);
	}

	/**
	 * get Clocks items.
	 * 
	 * @param  string|date $date 
	 * @param  date      $begin 
	 * @param  date      $end 
	 * @access public
	 * @return void
	 */
	public function getClockItems($begin, $end) {
		$stmt = $this->dao->select('*')->from(TABLE_KEVINCLOCKACT)
			->where('account')->eq($this->account)
			->andWhere("date >= '$begin'")
			->andWhere("date <= '$end'")
			->orderBy('date, time')
			->fetchGroup('date');
		return $stmt;
	}

	public function getDeptArray() {
		$depts = $this->dao->select('id,name')->from(TABLE_DEPT)
			->where('id')->ne(0)
			->fetchPairs('id', 'name');
		if (empty($depts))
			return array();
		return $depts;
	}

	public function getDeptEmployeesPairs($deptId) {
		$employees		 = array();
		$employees['']	 = '';
		$deptArray		 = $this->dao->select('id')->from(TABLE_DEPT)
			->where('parent')->eq($deptId)
			->fetchAll('id');
		$deptIdArray	 = array();
		foreach ($deptArray as $dept) {
			$deptIdArray[] = $dept->id;
		}
		$deptIdArray[] = $deptId;
		for ($i = 0; $i < count($deptIdArray); $i++) {
			$deptEmployees = $this->dao->select('*')->from(TABLE_USER)
				->where("(dept = $deptIdArray[$i] OR deptdispatch = $deptIdArray[$i])")
				->beginIF($this->config->kevinhours->isShowDeletedAccount == false)->andWhere('deleted')->eq(0)->fi()
				->fetchAll('account');
			foreach ($deptEmployees as $key => $employee) {
				$this->employeesAll[$employee->account] = new stdClass();
				$this->employeesAll[$employee->account]->realname = $employee->realname;
				$this->employeesAll[$employee->account]->isexternal = in_array($employee->dept,$deptIdArray)?0:1;
				if ((int) ($employee->locked) == 0) {
					$this->employeesAll[$employee->account]->locked = 0;
					$this->employeesActive[$employee->account] = $employee->realname;
				} else {
					$this->employeesActive[$employee->account] =$employee->realname;
					$this->employeesAll[$employee->account]->locked = 1;
				}
			}
		}
		$employees = array_merge($employees, $this->employeesActive, $this->employeesInactive);
		return $employees;
	}
	
	public function getDeptTodos($begin, $end, $deptId = 0) {
		$todos			 = array();
		$accountArray	 = $this->kevincom->getDeptEmployeesAccount($deptId, $this->config->kevinhours->isShowDeletedAccount);
		$stmt			 = $this->dao->select('a.*,b.code,b.realname')->from(TABLE_TODO)->alias('a')
			->leftJoin(TABLE_USER)->alias('b')->on('a.account = b.account')
			->where('a.account')->in($accountArray)
			->andWhere("a.date >= '$begin'")
			->andWhere("a.date <= '$end'")
			->andWhere("a.status = 'done'")
			->orderBy('b.code, a.date')
			->query();

		while ($todo = $stmt->fetch()) {
			$todo->begin = $this->formatTime($todo->begin);
			$todo->end	 = $this->formatTime($todo->end);

			$todos[] = $todo;
		}
		return $todos;
	}

	public function getGuestManHoursList($date = 'today', $account = '', $hourstype = ''
	, $deptId = 0, $employeetype = '', $pager = null) {
		$todos = array();
		extract(kevin::getBeginEndTime($date));

		if ($account == '') {
			$accountArray	 = $this->kevincom->getDeptEmployeesAccount($deptId, $this->config->kevinhours->isShowDeletedAccount);
			$stmt			 = $this->dao->select('c.cashCode,c.name as projectname,b.code,b.realname,a.*')->from(TABLE_TODO)->alias('a')
				->leftJoin(TABLE_USER)->alias('b')->on('a.account = b.account')
				->leftJoin(TABLE_PROJECT)->alias('c')->on(' a.project = c.id')
				->where('a.account')->in($accountArray)
				->andWhere("a.date >= '$begin'")
				->andWhere("a.date <= '$end'")
				->andWhere("a.status = 'done'");
			if ($employeetype == 'formal')
				$stmt			 = $stmt->andWhere("b.code != ''");
			else if ($employeetype == 'all')
				$stmt			 = $stmt->andWhere("b.code >= ''");
			else
				$stmt			 = $stmt->andWhere("b.code <= ''");
			if ('all' != $hourstype)
				$stmt			 = $stmt->andWhere('hourstype')->eq($hourstype);
			$stmt			 = $stmt->orderBy('a.account, a.date')
				->query();
		}
		else {
			$stmt	 = $this->dao->select('c.cashCode,c.name as projectname,b.code,b.realname,a.*')->from(TABLE_TODO)->alias('a')
				->leftJoin(TABLE_USER)->alias('b')->on('a.account = b.account')
				->leftJoin(TABLE_PROJECT)->alias('c')->on(' a.project = c.id')
				->where('a.account')->eq($account)
				->andWhere("a.status = 'done'")
				->andWhere("a.date >= '$begin'")
				->andWhere("a.date <= '$end'");
			if ('all' != $hourstype)
				$stmt	 = $stmt->andWhere('hourstype')->eq($hourstype);
			$stmt	 = $stmt->orderBy('account, date')
				->page($pager)
				->query();
		}

        while ($todo = $stmt->fetch()) {
			$todo->begin = $this->formatTime($todo->begin);
			$todo->end	 = $this->formatTime($todo->end);
			$todos[] = $todo;
		}
		return $todos;
	}

	public function getHourAndMinutes($time) {
		$hours	 = substr($time, 0, 2);
		$minutes = substr($time, 2, 2);
		return $hours . ':' . $minutes;
	}

	/**
	 * Get todo list of a user.
	 * 
	 * @param  date   $date 
	 * @param  string $account 
	 * @param  string $status   all|today|thisweek|lastweek|before, or a date.
	 * @param  int    $limit    
	 * @access public
	 * @return void
	 */
	public function getList($date = 'today', $account = '', $status = 'all', $limit = 0, $pager = null, $orderBy = "date, begin, status") {
		$this->setAccount();
		$todos = array();
		extract(kevin::getBeginEndTime($date));

		$todos = $this->dao->select('a.*,b.realname,c.name as projectname')->from(TABLE_TODO)->alias('a')
			->leftJoin(TABLE_USER)->alias('b')->on('a.account = b.account')
			->leftJoin(TABLE_PROJECT)->alias('c')->on('c.id = a.project')
			->where('a.account')->eq($this->account)
			->andWhere("a.date >= '$begin'")
			->andWhere("a.date <= '$end'")
			->beginIF($status != 'all' and $status != 'undone')->andWhere('status')->in($status)->fi()
			->beginIF($status == 'undone')->andWhere('status')->ne('done')->fi()
			->orderBy($orderBy)
			->beginIF($limit > 0)->limit($limit)->fi()
			->page($pager)
			->fetchAll();

		/* Set session. */
		$sql = explode('WHERE', $this->dao->get());
		$sql = explode('ORDER', $sql[1]);
		$this->session->set('todoReportCondition', $sql[0]);
		foreach ($todos as $todo) {
			$todo->begin = $this->formatTime($todo->begin);
			$todo->end	 = $this->formatTime($todo->end);
		}
		return $todos;
	}

	public function getManHoursList($date = 'today', $account = '', $hourstype = ''
	, $deptId = 0, $employeetype = '', $limit = 0, $orderBy = "date") {
		$todos = array();
		extract(kevin::getBeginEndTime($date));

		if ($account == '') {
			$accountArray	 = $this->kevincom->getDeptEmployeesAccount($deptId, $this->config->kevinhours->isShowDeletedAccount);
			$stmt			 = $this->dao->select('a.*,b.code,b.realname')->from(TABLE_TODO)->alias('a')
				->leftJoin(TABLE_USER)->alias('b')->on('a.account = b.account')
				->where('a.account')->in($accountArray)
				->andWhere("a.date >= '$begin'")
				->andWhere("a.date <= '$end'")
				->andWhere("a.status = 'done'");
			if ($employeetype == 'formal')
				$stmt			 = $stmt->andWhere("b.code != ''");
			else if ($employeetype == 'all')
				$stmt			 = $stmt->andWhere("b.code >= ''");
			else
				$stmt			 = $stmt->andWhere("b.code <= ''");
			if ('all' != $hourstype)
				$stmt			 = $stmt->andWhere('hourstype')->eq($hourstype);
			$stmt			 = $stmt->orderBy('a.date, b.code')
				->beginIF($limit > 0)->limit($limit)->fi()
				->query();
		}
		else {
			$stmt	 = $this->dao->select('a.*,b.realname as realname,b.code as code')->from(TABLE_TODO)->alias('a')
				->leftJoin(TABLE_USER)->alias('b')->on('a.account=b.account')
				->where('a.account')->eq($account)
				->andWhere("a.status = 'done'")
				->andWhere("a.date >= '$begin'")
				->andWhere("a.date <= '$end'");
			if ('all' != $hourstype)
				$stmt	 = $stmt->andWhere('hourstype')->eq($hourstype);
			$stmt	 = $stmt->orderBy($orderBy)
				->beginIF($limit > 0)->limit($limit)->fi()
				->query();
		}

		/* Set session. */
		$sql = explode('WHERE', $this->dao->get());
		$sql = explode('ORDER', $sql[1]);
		$this->session->set('todoReportCondition', $sql[0]);

		while ($todo = $stmt->fetch()) {
			$todo->begin = $this->formatTime($todo->begin);
			$todo->end	 = $this->formatTime($todo->end);
			$todos[]	 = $todo;
		}
		return $todos;
	}
	
	/**
	 * Get over list of a user.
	 * 
	 * @param  date   $date 
	 * @param  string $account 
	 * @param  string $status   all|today|thisweek|lastweek|before, or a date.
	 * @param  int    $limit    
	 * @access public
	 * @return void
	 */
	public function getOverMonth($begin,$end, $account,  & $todos) {
		if ($account == '') $account = $this->account;
		$todos = array();
		$monthList = array();
		kevin::getMonthList($begin,$end,$monthList);
		if(count($monthList) == 0) return false;
		//初始化
		foreach ($monthList as $month) {
			$todos[$month] = new stdclass();
			$todo = & $todos[$month] ;
			$todo->month = $month;
			$todo->account = $account;
			$todo->minutesSum = 0;
		}

		$todosQry = $this->dao->select("DATE_FORMAT(`date`,'%Y-%m') as month, account, sum(minutes) as minutesSum")->from(TABLE_TODO)
			->where('account')->eq($account)
			->andWhere("date >= '$begin'")
			->andWhere("date <= '$end'")
			->andWhere('status')->eq('done')
			->andWhere('hourstype')->eq('ove')
			->groupBy("account,month")
			->orderBy("month")
			->fetchAll();
		foreach ($todosQry as $item) {
			if(!array_key_exists($item->month, $monthList)) continue;
			$todos[$item->month]->minutesSum = $item->minutesSum;
		}
		return true;
	}
	
		/**
	 * Get over list of a user.
	 * 
	 * @param  date   $date 
	 * @param  string $account 
	 * @param  string $status   all|today|thisweek|lastweek|before, or a date.
	 * @param  int    $limit    
	 * @access public
	 * @return void
	 */
	public function getOverMonthByDept($begin,$end, $employeesActive,  & $todos) {
		$todos = array();
		$monthList = array();
		foreach ($employeesActive as $key=>$employeeActive) {
		//if ($account == '') $account = $this->account;

		kevin::getMonthList($begin,$end,$monthList);
		if(count($monthList) == 0) return false;
		//初始化
		foreach ($monthList as $month) {
			$todos[$month][$key] = new stdclass();
			$todo = & $todos[$month][$key] ;
			$todo->month = $month;
			$todo->account = $key;
			$todo->minutesSum = 0;
		}

		$todosQry = $this->dao->select("DATE_FORMAT(`date`,'%Y-%m') as month, account, sum(minutes) as minutesSum")->from(TABLE_TODO)
			->where('account')->eq($key)
			->andWhere("date >= '$begin'")
			->andWhere("date <= '$end'")
			->andWhere('status')->eq('done')
			->andWhere('hourstype')->eq('ove')
			->groupBy("account,month")
			->orderBy("month")
			->fetchAll();
		foreach ($todosQry as $item) {
			if(!array_key_exists($item->month, $monthList)) continue;
			$todos[$item->month][$key]->minutesSum = $item->minutesSum;
		}
//		die(js::alert(var_dump($todos)));
		}
		return true;
	}
	
	/**
	 * Get over list of a user.
	 * 
	 * @param  date   $date 
	 * @param  string $account 
	 * @param  string $status   all|today|thisweek|lastweek|before, or a date.
	 * @param  int    $limit    
	 * @access public
	 * @return void
	 */
	public function getOverMonthByDepta($begin,$end,$employeesActive, & $todos) {
//		die(js::alert(var_dump($employeesActive)));
		$todos = array();
		$monthList = array();
		$empkeyArray=array();
		$j=0;
		foreach ($employeesActive as $empkey=>$emp){
			$empkeyArray[$j]=$empkey;
			$j++;
		}
		$account='';
		foreach ($employeesActive as $key=>$employeeActive) {
		if ($account == '') $account = $this->account;

		kevin::getMonthList($begin,$end,$monthList);
		if(count($monthList) == 0) return false;
		//初始化
		foreach ($monthList as $month) {
			$todos[$month][$key] = new stdclass();
			$todo = & $todos[$month][$key] ;
			$todo->month = $month;
			$todo->account = $key;
			$todo->minutesSum = '0';
		}}
			
		$todosQry=$this->dao->select("DATE_FORMAT(`date`,'%Y-%m') as month,account,sum(minutes) as minutesSum")->from(TABLE_TODO)
					->where('account')->in($empkeyArray)
					->andWhere("date >= '$begin'")
					->andWhere("date <= '$end'")
					->andWhere('status')->eq('done')
					->andWhere('hourstype')->eq('ove')
					->groupBy('account,month')
					->orderBy("month")
					->fetchAll();
		foreach ($todosQry as $item) {
			if(!array_key_exists($item->month, $monthList)) continue;
			$todos[$item->month][$item->account]->minutesSum = $item->minutesSum;
		}
//		die(js::alert(var_dump($todos)));
//		}
		return true;
	}
	
	/**
	 * Get over list of a user.
	 * 
	 * @param  date   $date 
	 * @param  string $account 
	 * @param  string $status   all|today|thisweek|lastweek|before, or a date.
	 * @param  int    $limit    
	 * @access public
	 * @return void
	 */
	public function getOverMonthDept($begin,$end,  $deptID, & $todos) {
		$accountArray = array();
		foreach ($this->employeesActive as $key => $items) {
			$accountArray[] = $key;
		}
			
		$todos = array();
		if(count($accountArray) == 0) return false;
		
		$monthList = array();
		kevin::getMonthList($begin,$end,$monthList);
		if(count($monthList) == 0) return false;
		//初始化
		foreach ($monthList as $month) {
			$todos[$month] = new stdclass();
			$todo = & $todos[$month] ;
			$todo->month = $month;
			$todo->account = $account;
			$todo->minutesSum = 0;
		}

		$todosQry = $this->dao->select("DATE_FORMAT(`date`,'%Y-%m') as month, account, sum(minutes) as minutesSum")->from(TABLE_TODO)
			->where('account')->in($accountArray)
			->andWhere("date >= '$begin'")
			->andWhere("date <= '$end'")
			->andWhere('status')->eq('done')
			->andWhere('hourstype')->eq('ove')
			->groupBy("account,month")
			->orderBy("month")
			->fetchAll();
		foreach ($todosQry as $item) {
			if(!array_key_exists($item->month, $monthList)) continue;
			$todos[$item->month]->minutesSum = $item->minutesSum;
		}
		return true;
	}
	
	public function getOverManHoursList($date = 'today', $deptId = 0) {
		$todos = array();
		extract(kevin::getBeginEndTime($date));

		$accountArray	 = $this->kevincom->getDeptEmployeesAccount($deptId, $this->config->kevinhours->isShowDeletedAccount);
		$stmt			 = $this->dao->select('a.*,b.code,b.realname')->from(TABLE_TODO)->alias('a')
			->leftJoin(TABLE_USER)->alias('b')->on('a.account = b.account')
			->where('a.account')->in($accountArray)
			->andWhere("a.date >= '$begin'")
			->andWhere("a.date <= '$end'")
			->andWhere("a.status = 'done'")
			->andWhere('hourstype')->eq('ove')
			->orderBy('b.code, a.date')
			->query();

		/* Set session. */
		$sql = explode('WHERE', $this->dao->get());
		$sql = explode('ORDER', $sql[1]);
		$this->session->set('todoReportCondition', $sql[0]);

		while ($todo = $stmt->fetch()) {
			$todo->begin = $this->formatTime($todo->begin);
			$todo->end	 = $this->formatTime($todo->end);
			$todos[]	 = $todo;
		}
		return $todos;
	}

	public function getProductWorkingHours($productID = 0, $date = 'thisMonth', $projectArray, $pager = null) {
		$stmt = $this->getProductStmtFromSql($productID, $date, $projectArray);
		return $this->getTodosOfProduct($stmt, $pager);
	}

	public function getPersonalRatePay() {
		$rateObj = $this->dao->select('ratepay')->from(TABLE_USER)
			->where('account')->eq($this->app->user->account)
			->fetch();
		if (!$rateObj)
			return 0;
		return $rateObj->ratepay;
	}

	public function getProductStmtFromSql($productID = 0, $date = 'thisMonth', $projectArray) {
		extract(kevin::getBeginEndTime($date));

		$stmt = $this->dao->select('a.*,b.code,b.realname')->from(TABLE_TODO)->alias('a')
			->leftJoin(TABLE_USER)->alias('b')->on('a.account = b.account')
			->where('a.project')->in($projectArray)
			->andWhere("a.date >= '$begin'")
			->andWhere("a.date <= '$end'")
			->andWhere('a.status')->eq('done')
			->orderBy('a.account');
		return $stmt;
	}

	public function getTodosOfProduct($stmt, $pager = null) {
		$stmt	 = $stmt->beginIF($pager != null)->page($pager)->fi()
			->query();
		$todos	 = array();
		while ($todo	 = $stmt->fetch()) {
			$todo->begin = date::formatTime($todo->begin);
			$todo->end	 = date::formatTime($todo->end);
			$todos[]	 = $todo;
		}
		return $todos;
	}

	public function getPersonalManHoursList($date = 'thisWeek', $hourstype = '') {
		$this->setAccount();
		$todos = array();
		extract(kevin::getBeginEndTime($date));

		$stmt	 = $this->dao->select('*')->from(TABLE_TODO)
			->where('account')->eq($this->account)
			->andWhere("status = 'done'")
			->andWhere("date >= '$begin'")
			->andWhere("date <= '$end'");
		if ('all' != $hourstype)
			$stmt	 = $stmt->andWhere('hourstype')->eq($hourstype);
		$stmt	 = $stmt
			->query();

		/* Set session. */
		$sql = explode('WHERE', $this->dao->get());
		$sql = explode('ORDER', $sql[1]);
		$this->session->set('todoReportCondition', $sql[0]);

		while ($todo = $stmt->fetch()) {
			$todo->begin = $this->formatTime($todo->begin);
			$todo->end	 = $this->formatTime($todo->end);
			$todos[]	 = $todo;
		}
		return $todos;
	}

	public function getProjectNameArray() {
		$projectStats	 = $this->loadModel('project')->getProjectStats();
		$projectsArray	 = $this->getRecentProject();
		foreach ($projectStats as $project) {
			if (array_key_exists($project->id, $projectsArray))
				continue;
			$projectsArray[$project->id] = $project->id . ' : ' . $project->name;
		}
		$projects = $this->dao->select('*')
			->from(TABLE_PROJECT)
			->where('id')->lt(10)
			->andWhere('deleted')->eq('0')
			->fetchAll();
		foreach ($projects as $project) {
			if ($project->id == 3 || $project->id == 4)
				continue;
			$projectsArray[$project->id] = $project->id . ' : ' . $project->name;
		}
		return $projectsArray;
	}

	//获得最近的项目代号列表
	public function getRecentProject() {
		$this->setAccount();
		$begin				 = date('Y-m-d', strtotime('-' . $this->config->kevinhours->recentDays . ' day'));
		$projects			 = $this->dao->select("distinct(a.project),b.name AS projectName")
			->from(TABLE_TODO)->alias('a')
			->leftJoin(TABLE_PROJECT)->alias('b')->on('a.project = b.id')
			->where('project')->ge(10)
			->andWhere('account')->eq($this->account)
			->andWhere('a.`date`')->gt($begin)
			->orderBy('a.`date` desc')
			->fetchAll('project');
		$projectArray		 = array();
		$projectArray['']	 = '';
		foreach ($projects as $project) {
			$projectArray[$project->project] = $project->project . ':' . $project->projectName;
		}
		return $projectArray;
	}

	//获得最近一段时间涉及到的项目正常加班和总计工时
	public function getRecentProjectAndTime($type) {
		$this->setAccount();
		$begin	 = '';
		$end	 = '';

		extract(kevin::getBeginEndTime($type));

		$todos = $this->dao->select("a.project, b.name
					, SUM( CASE WHEN a.hourstype = 'nor' THEN a.minutes ELSE 0 END) as nor
					, SUM( CASE WHEN a.hourstype = 'ove' THEN a.minutes ELSE 0 END) as ove
					, SUM(CASE WHEN (a.hourstype = 'nor' or a.hourstype = 'ove') THEN a.minutes ELSE 0 END) as total")
			->from(TABLE_TODO)->alias('a')
			->leftJoin(TABLE_PROJECT)->alias('b')->on('a.project = b.id')
			->where('a.account')->eq($this->account)
			->andWhere('a.status')->eq('done')
			->andWhere('a.date')->ge($begin)
			->beginIF($end != '')->andWhere('a.date')->le($end)->fi()
			->groupBy('project')
			->orderBy('total_desc')
			->fetchAll('project');
		return $todos;
	}

	//获得带有项目关键词的项目代号
	public function getRelevantProject($keywords) {
		$projectArray	 = array();
		$projects		 = $this->dao->select('*')
			->from(TABLE_PROJECT)
			->where('name')->like('%' . $keywords . '%')
			->andWhere('id')->lt($this->config->kevinhours->projectIDMax)
			->limit($this->config->kevinhours->showProjectMax)
			->fetchAll();
		foreach ($projects as $project) {
			$projectArray[$project->id] = $project->name;
		}
		return $projectArray;
	}

	//获得todo日期
	public function getTodoDateById($todoID) {
		$todo = $this->dao->select('date')
			->from(TABLE_TODO)
			->where('id')->eq($todoID)
			->fetch();
		if (!$todo)
			return '';
		return $todo->date;
	}

	//比对考勤日期和今天的时期
	public function isDateFuture($date) {
		if ('' == $date)
			return true;
		$todoTime	 = strtotime($date);
		$todayTime	 = strtotime(date('Y-m-d'));
		if ($todayTime < $todoTime)
			return true;
		return false;
	}

	public function getStmtForPersonalManHoursList($date = 'thisWeek', $hourstype = '') {
		extract(kevin::getBeginEndTime($date));

		$stmt = $this->dao->select('*')->from(TABLE_TODO)
				->where('account')->eq($this->app->user->account)
				->andWhere("status = 'done'")
				->andWhere("date >= '$begin'")
				->andWhere("date <= '$end'")
				->beginIF($hourstype != 'all')->andWhere('hourstype')->eq($hourstype)->fi();
		return $stmt;
	}

	public function getStmtForManHoursList($date = 'today', $account = '', $hourstype = ''
	, $deptId = 0, $employeetype = '', $orderBy = "date",$deptcount='0') {
		extract(kevin::getBeginEndTime($date));

		if ($account == '') {
//			$accountArray	 = $this->kevincom->getDeptEmployeesAccount($deptId, $this->config->kevinhours->isShowDeletedAccount);
			$accountArray=$this->getDeptChildEmpsAccount($deptId, $this->config->kevinhours->isShowDeletedAccount,$deptcount);
			$stmt			 = $this->dao->select('a.*,b.code,b.realname')->from(TABLE_TODO)->alias('a')
				->leftJoin(TABLE_USER)->alias('b')->on('a.account = b.account')
				->where('a.account')->in($accountArray)
				->andWhere("a.date >= '$begin'")
				->andWhere("a.date <= '$end'")
				->andWhere("a.status = 'done'")
				->beginIF($employeetype == 'formal')->andWhere("b.code != ''")->fi()
				->beginIF($employeetype == 'all')->andWhere("b.code >= ''")->fi()
				->beginIF($employeetype == 'informal')->andWhere("b.code <= ''")->fi()
				->beginIF($hourstype != 'all')->andWhere('hourstype')->eq($hourstype)->fi()
				->orderBy('a.date, b.code');
		} else {
			$stmt = $this->dao->select('*')->from(TABLE_TODO)
				->where('account')->eq($account)
				->andWhere("status = 'done'")
				->andWhere("date >= '$begin'")
				->andWhere("date <= '$end'")
				->beginIF($hourstype != 'all')->andWhere('hourstype')->eq($hourstype)->fi()
				->orderBy($orderBy);
		}

		return $stmt;
	}

	public function getDeptChildEmpsAccount($deptId, $isShowDeleted = false,$deptcount=0)
	{
		$employees = array();
		$deptArray = $this->dao->select('id,path')->from(TABLE_DEPT)
			->where('path')->like('%,' . $deptId . ',%')
			->fetchAll();
		$deptIdArray = array();
		foreach($deptArray as $alldeptchild){
			$childarray=explode (','.$deptId.',', $alldeptchild->path);
			$childparts=explode(',',rtrim($deptId.','.$childarray[1],','));
			foreach ($childparts as  $key=>$childpart) {
				if($childpart!=''){
				if($deptcount!='x'){
					if($key<=$deptcount){
						$deptIdArray[$childpart] = $childpart;	
					}
				}else{
					$deptIdArray[$childpart] = $childpart;	
				}
				}
			}
		}
		$deptIdArray[$deptId]=$deptId;
		$employees[''] = '';
		$this->employeesActive['']='';
		foreach($deptIdArray as $id){
			$deptEmployees = $this->dao->select('account,realname')->from(TABLE_USER)
			->where('dept')->eq($id)->orWhere('deptdispatch')->eq($id)
			->beginIF(!$isShowDeleted)->andWhere('deleted')->eq(0)->fi()
			->beginIF(!$isShowDeleted)->andWhere('locked')->lt("2030-01-01 00:00:00")->fi()
			->fetchAll('account');
			foreach($deptEmployees as $key => $employee)
			{
				$employees[$employee->account] = $employee->account;
				$this->employeesActive[$employee->account] = $employee->realname;
			}
		}
//		if(count($employees) == 1) return array();
		return $employees;
	}
    /**
     * Get info of a todo.
     * 
     * @param  int    $todoID 
     * @access public
     * @return object|bool
     */
    public function getTodoById($todoID)
    {
        $todo = $this->dao->findById((int)$todoID)->from(TABLE_TODO)->fetch();
        if(!$todo) return false;
        $todo->date = str_replace('-', '', $todo->date);
        return $todo;
    }
	
	public function getTodosForPersonalManHoursList($stmt, $pager = null) {
		$todos	 = array();
		if (null == $stmt)
			return $todos;
		$stmt	 = $stmt->beginIF($pager != null)->page($pager)->fi()
			->query();

		/* Set session. */
		$sql = explode('WHERE', $this->dao->get());
		$sql = explode('ORDER', $sql[1]);
		$this->session->set('todoReportCondition', $sql[0]);

		while ($todo = $stmt->fetch()) {
			$todo->begin = $this->formatTime($todo->begin);
			$todo->end	 = $this->formatTime($todo->end);
			$todos[]	 = $todo;
		}
		return $todos;
	}

	public function getTodosForManHoursList($stmt, $pager = null) {
		$todos	 = array();
		if ($stmt == null)
			return $todos;
		$stmt	 = $stmt->beginIF($pager != null)->page($pager)->fi()
			->query();

		/* Set session. */
		$sql	 = explode('WHERE', $this->dao->get());
		$sql	 = explode('ORDER', $sql[1]);
		$this->session->set('todoReportCondition', $sql[0]);
		while ($todo	 = $stmt->fetch()) {
			$todo->begin = $this->formatTime($todo->begin);
			$todo->end	 = $this->formatTime($todo->end);

			$todos[] = $todo;
		}
		return $todos;
	}

	public function isShowOperateButton($date) {
		$lastMonthEndDay	 = $this->getLockedDayOfLastMonth();
		$lastMonthLockedDate = date('Y-m-') . $lastMonthEndDay . ' ' . $this->config->kevinhours->endTime;
		if (time() > strtotime($lastMonthLockedDate)) {
			$lockeDate = date('Y-m-') . '01';
		} else {
			$lockeDate = date('Y-m-', strtotime('-1 month')) . '01';
		}
		if (strtotime($date) >= strtotime($lockeDate)) {
			return true;
		}
		return false;
	}

	/**
	 * Check time overlap,one todo
	 * 
	 * @param  string|date $currentDate 
	 * @param  Array|todo   $todoArray 
	 * @access public
	 * @return void
	 */
	public function isTodoTimeOverlap($currentDate, $todoArray) {
		$currentDateTimes = $this->getTodosTimeByDate($currentDate);
		foreach ($currentDateTimes as $tempTimeObj) {
			if ($tempTimeObj->begin > $tempTimeObj->end)
				$tempTimeObj->end += 2400;
			foreach ($todoArray as $todo) {
				if ($todo->begin > $todo->end)
					$todo->end += 2400;
				if ($todo->begin > $tempTimeObj->begin && $todo->begin < $tempTimeObj->end) {
					$this->dao->logError('ErrorKevinTimeOverly', '', '');
					return true;
				}
				if ($todo->end > $tempTimeObj->begin && $todo->end < $tempTimeObj->end) {
					$this->dao->logError('ErrorKevinTimeOverly', '', '');
					return true;
				}
				if ($todo->begin <= $tempTimeObj->begin && $todo->end >= $tempTimeObj->end) {
					$this->dao->logError('ErrorKevinTimeOverly', '', '');
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Initial Dispach Service Class
	 * 
	 * @param  string|date $currentDate 
	 * @param  todo|Item   $todo 
	 * @access public
	 * @return void
	 */
	public function InitialDispachServiceClass($month, $season,$ClassDeptID) {
		//根据月份和季度获得月份列表
		$this->DispachServiceClass				 = new stdclass();
		$this->DispachServiceClass->monthArray	 = kevin::getMonthBySeason($month, $season);
		$monthArray								 = array();
		//遍历月份数组
		foreach ($this->DispachServiceClass->monthArray as $month) {
			$monthArray[] = (int) $month;
		}
		$this->DispachServiceClass->monthArray = $monthArray;
		
		$this->DispachServiceClass->ClassDeptArray = $this->dao->select('id, name')->from(TABLE_DEPT)
			->where('parent')->eq($ClassDeptID)
			->fetchAll();
	}

	/**
	 * Check time overlap,one todo
	 * 
	 * @param  string|date $currentDate 
	 * @param  todo|Item   $todo 
	 * @access public
	 * @return void
	 */
	public function isTodoTimeOverlapOne($currentDate, $todo) {
		$todo->begin		 = (int) $todo->begin;
		$todo->end			 = (int) $todo->end;
		$currentDateTimes	 = $this->getTodosTimeByDate($currentDate, $todo->id);
		foreach ($currentDateTimes as $tempTimeObj) {
			if ($tempTimeObj->begin > $tempTimeObj->end)
				$tempTimeObj->end += 2400;
			if ($todo->begin > $todo->end)
				$todo->end += 2400;
			if ($todo->begin > $tempTimeObj->begin && $todo->begin < $tempTimeObj->end) {
				$this->dao->logError('ErrorKevinTimeOverly', '', '');
				return true;
			}
			if ($todo->end > $tempTimeObj->begin && $todo->end < $tempTimeObj->end) {
				$this->dao->logError('ErrorKevinTimeOverly', '', '');
				return true;
			}
			if ($todo->begin <= $tempTimeObj->begin && $todo->end >= $tempTimeObj->end) {
				$this->dao->logError('ErrorKevinTimeOverly', '', '');
				return true;
			}
		}
		return false;
	}

	public function createIndexLink($account,$realname,$type,$deptID,$params){
        $linkHtml = html::a(helper::createLink('kevinhours', 'index', "type=$type&account=$account&deptID=$deptID"),"<i class='icon-user'>&nbsp;</i>" .$realname, '_self', "");
        return $linkHtml;
    }
	
	public function createTodoLink($account,$realname,$type,$deptID,$params){
		$staticparams=explode('&',$params);
		$linkHtml = html::a(helper::createLink('kevinhours', 'todo', "type=$type&account=$account&deptID=$deptID&status=$staticparams[0]&orderBy=$staticparams[1]&recTotal=$staticparams[2]&recPerPage=$staticparams[3]&pageID=$staticparams[4]"),"<i class='icon-user'>&nbsp;</i>" .$realname, '_self', "");
        return $linkHtml;
	}
	
	public function createMyLink($account,$realname,$type,$deptID,$params){
		$linkHtml = html::a(helper::createLink('kevinhours', 'my', "type=$type&account=$account&deptID=$deptID"),"<i class='icon-user'>&nbsp;</i>" .$realname, '_self', "");
        return $linkHtml;
	}
	
	public function createoverlink($account,$realname,$type,$deptID,$params){
		$linkHtml = html::a(helper::createLink('kevinhours','over',"type=$type&account=$account&deptID=$deptID"),"<i class='icon-user'>&nbsp;</i>" .$realname, '_self', "");
        return $linkHtml;
	}
	
	public function createoverdeptink($account,$realname,$type,$deptID,$params){
		$linkHtml = html::a(helper::createLink('kevinhours','over',"type=$type&account=$account&deptID=$deptID"),"<i class='icon-user'>&nbsp;</i>" .$realname, '_self', "");
        return $linkHtml;
	}
	
	public function overtreetitle($type = 'thisYear', $account = '', $deptID = '',$method,$userFunc,$params){
		if($type == '') $type = 'thisYear';
		
		if($account==''&&isset($_SESSION['kevin_user_account']))$account=$_SESSION['kevin_user_account'];
		$this->getShowAccount($account);
		
		if(!$deptID)$deptID = $this->user->dept;

		if($this->session->accmethod!=$method){

		if(isset($_SESSION['currentdeptID'])&&$method=='index')$deptID=$_SESSION['currentdeptID'];
		elseif(isset($_COOKIE['currentdeptID']))$deptID=$_COOKIE['currentdeptID'];
		}
		$this->session->set('accmethod',$method);
		setcookie('currentdeptID',$deptID);
		
		$this->session->set('currentdeptID',$deptID);

		$deptArray=$this->dao->select('id,name')->from(TABLE_DEPT)->where('deleted')->eq(0)->fetchPairs('id','name');
		if(common::hasPriv('kevinhours', 'checkAll')){
			$this->loadModel('dept');
			$deptinfo=$this->dao->select('name,parent')->from(TABLE_DEPT)->where('id')->eq($deptID)->fetch();
			$deptchilds=$deptchildbydept= $this->dept->getSons($deptID);$deptchilds=$deptchildbydept= $this->dept->getSons($deptID);
		}elseif(common::hasPriv('kevinhours', 'browseDeptHours')){
			$this->loadModel('dept');
			$path = $this->dao->select('path')->from(TABLE_DEPT)->where('id')->eq($this->app->user->dept)->fetch('path');
			$parentjudge=explode(',', $path);
			if(in_array($deptID,$parentjudge)) $deptinfo=false;
			else $deptinfo=$this->dao->select('name,parent')->from(TABLE_DEPT)->where('id')->eq($deptID)->fetch();
			$deptchilds=$deptchildbydept=$this->dept->getSons($deptID);
		}else {
			if($deptID == $this->user->dept) $deptinfo=false;
			else $deptinfo=$this->dao->select('name,parent')->from(TABLE_DEPT)->where('id')->eq($deptID)->fetch();
			$deptinfo=false;
			$deptchilds=false;
		}
		if(!isset($deptArray[$deptID])) die(js::error($this->lang->kevinhours->KevinDeptinfoNotExist) . js::locate('back'));
		$deptchildmenu=array();
		$simplename=explode('/',$deptArray[$deptID]);
		$deptchildmenu[$deptID]=$simplename[count($simplename)-1];
		if(isset($deptchilds)&&$deptchilds){
			foreach($deptchilds as $deptchild){
				$deptchildmenu[$deptchild->id]=$deptchild->name;
			}
		}
		$grouparray=$this->dao->select('role,name')->from(TABLE_GROUP)->where('role')->ne('')->fetchPairs('role','name');
		
//		$this->commonWebParam($type);
		if ('' == $type) {
			$type = date('Ym');
		}
		$currentDate = "";
		if(is_numeric($type)){
			$length1 = strlen($type);
			if ( 6 == $length1) {
				$currentDate = date('Y-m-d', strtotime($type.'01'));
			}
			else if ( $length1 == 8) {
				$currentDate = date('Y-m-d', strtotime($type));
			} else if ($length1== 4) {
				$currentDate = date('Y-m-d', strtotime($type.'-01-01'));
			}
		}
		if($currentDate == "") $currentDate = date('Y-m-d');
		$currentMonth	 = date('Y-m', strtotime($currentDate)) . '-01';

		$lastMonth	 = date('Ym', strtotime("$currentMonth -1 month"));
		$nextMonth	 = date('Ym', strtotime("$currentMonth +1 month"));
		$thisMonth	 = date('Ym');

		$methodName		 = $this->app->getMethodName();
		$yearList		 = $this->getYearList();
		$monthList		 = $this->lang->kevinhours->month;
		$currentYear	 = date('Y', strtotime("$currentMonth"));
		$currentMonth	 = date('m', strtotime("$currentMonth"));
		
		extract(kevin::getBeginEndTime($type));
//		$this->CheckOutEmployeesPairs($deptID);
		if($this->user->checkAll){}
		elseif($this->user->browseDeptHours){
			$deptArray=$deptchildmenu;
		}else
			$deptArray='';
		if($this->user->browseDeptHours||$this->user->checkAll){
			
		if (!empty($deptArray)) {
			$selectmenu=html::select('dept', $deptArray, $deptID, "class='form-control chosen' onchange='gotoOtherDept(this.value);'").'</br>';
		} 
		
		$parentstr='';
		if(!isset($deptchildmenu)) $deptchildmenu='';
		if(!isset($deptinfo)) $deptinfo=false;
		if(!isset($deptchildbydept))$deptchildbydept='';
		
		
		if($deptinfo){
			if($deptinfo->parent){ 
			if($method=='overdept')
				$parentstr="&nbsp;&nbsp;&nbsp;<a class='btn pull-center' id='gotoparent' href='/zentao/kevinhours-overdept-".$type.'-'.$deptinfo->parent."'><i class='icon-level-up'></i></a>";
			else
				$parentstr= "&nbsp;&nbsp;&nbsp;<a class='btn pull-center' id='gotoparent' href='/zentao/kevinhours-".$method.'-'.$type.'--'.$deptinfo->parent."'><i class='icon-level-up'></i></a>";
			}
		}
		
		$deptchildmenustr='';
		if(isset($deptinfo->name)&&$deptinfo)
			$deptchildmenustr.='<tr id='.$deptID.'><td>'.html::a(helper::createLink('kevinhours', $method, "type=$type&account=&deptID=$deptID"),"<i class='icon-building'>&nbsp;</i>", '', "").$deptinfo->name. '</td></tr>';
		 if ($deptchildbydept) {
//			$deptchildmenustr='<th>'.$this->lang->kevinhours->overdept.'</th>';
			foreach ($deptchildbydept as $deptChild) {	
				if($method=='overdept')
					$deptchildmenustr.='<tr id='.$deptChild->id.'><td>'.html::a(helper::createLink('kevinhours', 'over', "type=$type&account=&deptID=$deptChild->id"),"<i class='icon-user'>&nbsp;</i>", '', "") . html::a(helper::createLink('kevinhours', 'overdept', "type=$type&deptID=$deptChild->id"),"<i class='icon-building'>&nbsp;</i>", '', "").$deptChild->name. '</td></tr>';
				else
					$deptchildmenustr.='<tr id='.$deptChild->id.'><td>'.html::a(helper::createLink('kevinhours', $method, "type=$type&account=&deptID=$deptChild->id"),"<i class='icon-building'>&nbsp;</i>", '', "").$deptChild->name. '</td></tr>';
			}
		}else $deptchildmenustr.='<tr><td>'.$this->lang->kevinhours->nothing.'</td></tr>';
		
		if ($this->user->browseDeptHours || $this->user->checkAll) {
			if (!$this->user->checkAll) {
				$deptID					 = $this->user->checkAll ? $this->user->dept : $this->app->user->dept;
//				$this->view->deptArray	 = array();
			} else {
				if ('' == $deptID) {
					$deptID = $this->user->dept;
				}
//				$this->view->deptArray = $this->kevincom->getDeptOptionMenu();
			}
			$this->getDeptEmployeesPairs($deptID);
		}

//		$this->view->deptID	 = $deptID;
		$employeesAll	 = $this->employeesAll;
		$employmenu='';
		if ($employeesAll) {
			$threestyle='&nbsp';
			$twostyle='&nbsp';
			$onestyle='&nbsp';
//			$employmenu.= '<th>'.$this->lang->kevinhours->over.'</th>';
			$j=0;
			$employmenu.= "<tr onclick='hideindeptmember(0);'><th><strong class='pull-left'>&nbsp;".$this->lang->kevinhours->indeptmember."</strong> <strong class='pull-right'><i class='icon-angle-right'></i></strong></th></tr>";
			foreach ($employeesAll as $key => $name) {					
				if(isset($name->isexternal)&&$name->isexternal==0){
					if(isset($name->locked)&&$name->locked==0){
						if ($key) {
							$params[0]=$key;$params[1]=$employeesAll[$key]->realname;
							$linkHtml = call_user_func_array($userFunc,array('account'=>$params[0],'realname'=>$params[1],'type'=>$type,'deptID'=>$deptID,'params'=>$params[2]));
							$tempKey = str_replace(".", "-", $key);
							$employmenu.= "<tr id=$tempKey class='indeptmember'><td>" .$twostyle. $linkHtml;
							if($key==$this->session->kevin_user_account) $employmenu.= html::icon($this->lang->icons['story']);
							$employmenu.= '</td></tr>';
							$j++;
						}
					}
				}
			}
			if($j==0)$employmenu.= "<tr class='indeptmember'><td>".$threestyle.$this->lang->kevinhours->nothing.'</td></tr>';
			$j=0;
			$employmenu.= "<tr class='text-right indeptmember' id ='InactiveUsers' onclick='hideInactive(0);'><td><i class='icon-angle-left'><strong><small>&nbsp{$this->lang->kevinhours->inactive}</small></strong></td></tr>";
			foreach ($employeesAll as $key => $name) {					
				if(isset($name->isexternal)&&$name->isexternal==0){
					if(isset($name->locked)&&$name->locked==1){
						if ($key) {
							$params[0]=$key;$params[1]=$employeesAll[$key]->realname;
							$linkHtml = call_user_func_array($userFunc,array('account'=>$params[0],'realname'=>$params[1],'type'=>$type,'deptID'=>$deptID,'params'=>$params[2]));
							$tempKey = str_replace(".", "-", $key);
							$employmenu.= "<tr id=$tempKey class='hideInactive'><td class='indeptmember'>" .$twostyle. $linkHtml;
							if($key==$this->session->kevin_user_account) $employmenu.= html::icon($this->lang->icons['story']);
							$employmenu.= '</td></tr>';
							$j++;
						}
					}			
				}
			}
			if($j==0)$employmenu.= "<tr class='hideInactive'><td class='indeptmember'>".$threestyle.$this->lang->kevinhours->nothing.'</td></tr>';
			$j=0;
			$employmenu.= "<tr onclick='hideindeptmember(1);'><th><strong class='pull-left'>&nbsp;".$this->lang->kevinhours->externalmember."</strong> <strong class='pull-right'><i class='icon-angle-right'></i></strong></th></tr>";
			foreach ($employeesAll as $key => $name) {					
				if(isset($name->isexternal)&&$name->isexternal==1){
					if(isset($name->locked)&&$name->locked==0){
						if ($key) {
							$params[0]=$key;$params[1]=$employeesAll[$key]->realname;
							$linkHtml = call_user_func_array($userFunc,array('account'=>$params[0],'realname'=>$params[1],'type'=>$type,'deptID'=>$deptID,'params'=>$params[2]));
							$tempKey = str_replace(".", "-", $key);
							$employmenu.= "<tr id=$tempKey class='externalmember'><td>" .$twostyle.$linkHtml;
							if($key==$this->session->kevin_user_account) $employmenu.= html::icon($this->lang->icons['story']);
							$employmenu.= '</td></tr>';
							$j++;
						}
					}
				}
			}
			if($j==0)$employmenu.= "<tr class='externalmember'><td>".$threestyle.$this->lang->kevinhours->nothing.'</td></tr>';
			$j=0;
			$employmenu.= "<tr id ='InactiveUsers' class='text-right externalmember' onclick='hideInactive(1);'><td><i class='icon-angle-left'><strong><small>&nbsp{$this->lang->kevinhours->inactive}</small></strong></td></tr>";
				foreach ($employeesAll as $key => $name) {					
					if(isset($name->isexternal)&&$name->isexternal==1){
						if(isset($name->locked)&&$name->locked==1){
							if ($key) {
								$params[0]=$key;$params[1]=$employeesAll[$key]->realname;
								$linkHtml = call_user_func_array($userFunc,array('account'=>$params[0],'realname'=>$params[1],'type'=>$type,'deptID'=>$deptID,'params'=>$params[2]));
								$tempKey = str_replace(".", "-", $key);
								$employmenu.= "<tr id=$tempKey class='hidextInactive'><td class='externalmember'>" .$twostyle.$linkHtml;
								if($key==$this->session->kevin_user_account) echo html::icon($this->lang->icons['story']);
								$employmenu.= '</td></tr>';
								$j++;
							}
						}			
					}
				}
				if($j==0)$employmenu.="<tr class='hidextInactive'><td class='externalmember'>".$threestyle.$this->lang->kevinhours->nothing.'</td></tr>';
			
	}else $employmenu.='<tr><td>'.$threestyle.$this->lang->kevinhours->nothing.'</td></tr>';
	
		$treeHTML="<div class='side' style='width:".$this->config->kevinhours->sideWidth."px;'><div class='side-body'><div class='panel panel-sm'><div class='panel-heading'><strong><ul class='nav'><li id='NextLinkOver'></li></ul>".$selectmenu."</strong></div><div class='panel-body table table-condensed  table-fixed' style = 'min-height:400px'><div class='nobr'><button class=' btn pull-left ".(($method=='over')? 'active':'')."' id='accountreverse' type='button' onclick='onButtonMember();'>".$this->lang->kevinhours->account."</button><button class=' btn  pull-left ".(($method=='overdept')? 'active':'')."' id='deptchildreverse' type='button' onclick='onButtonDeptChild();'>".$this->lang->kevinhours->dept."</button>".$parentstr."</div><a class='side-handle' data-id='companyTree' onclick='onsplice();'><i id='slipicon' class='icon-caret-left'></i></a><table id='deptchildlist' class='hidden nav ".(($method=='over')? 'hidden':'')."'>".$deptchildmenustr."</table><table class='table table-condensed table-fixed table-hover table-striped ".(($method=='overdept')?'hidden':'')."' id='deptmember'>".$employmenu."</table></div></div></div></div><script>$('.hideInactive').addClass('hidden');$('.hidextInactive').addClass('hidden');function onButtonMember(){if($('#deptmember').hasClass('hidden')){ $('#deptchildlist').addClass('hidden');$('#deptmember').removeClass('hidden');$('#deptchildreverse').removeClass('active');$('#accountreverse').addClass('active');}else $('#deptchildlist').addClass('hidden');}function onButtonDeptChild(){if($('#deptchildlist').hasClass('hidden')){ $('#deptchildlist').removeClass('hidden');$('#deptmember').addClass('hidden');$('#deptchildreverse').addClass('active');$('#accountreverse').removeClass('active');}else $('#deptmember').addClass('hidden');}function onsplice(){if($('#slipicon').hasClass('icon-caret-right')){var width='".$this->config->kevinhours->sideWidth."px';var mainmargin='".($this->config->kevinhours->sideWidth+20)."px';$('.side').css('width',width);$('.main').css('margin-left',mainmargin);$('.main').resize();}else{ $('.side').css('width','');$('.main').css('margin-left','');$('.main').resize();}}
		function hideInactive(type){if(type==0)$('.hideInactive').toggleClass('hidden');else $('.hidextInactive').toggleClass('hidden');}function hideindeptmember(type){if(type==0)$('.indeptmember').toggleClass('hidden');else $('.externalmember').toggleClass('hidden');}	function gotoOtherDept(deptID)
	{
		link = createLink('kevinhours', '".$method."', 'type=&account=".$this->user->account."&deptID='+ deptID);
		location.href = link;
	}</script>";
		return $treeHTML;
		}
	}
		
	/**
	 * 设定帐号，保存到 $account public变量内.
	 * 
	 * @var model
	 * @access public
	 */
	public function setAccount($account = "") {
		if ($account == "@") {
			$this->account = $this->app->user->account;
		} else if ($account == "") {
			if (!$this->account) {
				$this->account = $this->app->user->account;
			}
		} else {
			$this->account = $account;
		}
	}

	public function setIsIncludeAnnSession($isIncludeAnn = '') {
		if ('' == $isIncludeAnn) {
			if (isset($_SESSION['isIncludeAnn']))
				$isIncludeAnn = $this->session->isIncludeAnn;
		}
		else {
			if (0 == $isIncludeAnn)
				$this->session->set('isIncludeAnn', '');
			else
				$this->session->set('isIncludeAnn', $isIncludeAnn);
		}
		return $isIncludeAnn;
	}

	public function setSelectedDept($deptId) {
		$arrayStr	 = $this->session->todoReportCondition;
		$array		 = explode('AND', $arrayStr);
		$key		 = array_search(0, $array);
		if ($key !== false)
			array_splice($array, $key, 1);
		return implode('AND', $array);
	}

	public function setSelectedPerson($person) {
		$arrayStr	 = $this->session->todoReportCondition;
		$array		 = explode('AND', $arrayStr);
		$account	 = 'account = ' . '\'' . $person . '\' ';
		$array[0]	 = $account;
		return implode('AND', $array);
	}

	public function showHours($workhours, $i = 1) {
		$workhour	 = ((float) $workhours / 60);
		$workhour	 = number_format($workhour, $i);
		return $workhour;
	}

	public function showWorkHours($workhours) {
		if ($workhours > 60) {
			if (0 == (int) ($workhours % 60))
				return ((int) ($workhours / 60)) . ':00';
			return ((int) ($workhours / 60)) . ':' . ((int) ($workhours % 60));
		}
		else if ($workhours == 60) {
			return((int) $workhours / 60) . ':00';
		}
		return '0:' . ((int) $workhours % 60);
	}

	public function timeVerificated($lastTime) {
		//获得当前时间
		$currentTime	 = date('Y-m-d H:i:s');
		//获得上月考勤修改的截止时间为本月的某天某时
		$year			 = date('Y');
		$month			 = date('m');
		$endDay			 = $this->getLockedDayOfLastMonth();
		$endDayTimes	 = strtotime($year . '-' . $month . '-' . $endDay . ' ' . $this->config->kevinhours->endTime);
		//获得上月月底及本月开始的时间戳
		$lastMonthTimes	 = strtotime($year . '-' . $month . '-1 00:00:00');
		//判断当前时间与代办时间之差是否大于等于设置的时间期限
		$day			 = strtotime($currentTime) - strtotime($lastTime);
		$day			 = $day / 3600 / 24;
		$isOK			 = true;
		if (0 != $this->config->kevinhours->limitDate) {
			if ($day > $this->config->kevinhours->limitDate) {
				$isOK = false;
			}
		}

		if ($isOK) {
			//判断是否是本月时间
			if (strtotime($lastTime) >= $lastMonthTimes) {
				return true; //如果为本月时间,可以修改
			} else {//非本月时间,进行判断
				//当前时间是否超过上月考勤修改时间
				if (strtotime($currentTime) < $endDayTimes) {//未超过,可以修改上月的,但上上月的不可以修改
					//获得上月第一天
					$lastMonthFirstDay		 = date('Y-m-01 00:00:00', strtotime('-1 month'));
					$lastMonthFirstDayTimes	 = strtotime($lastMonthFirstDay); //获得时间戳
					//判断是否为上月考勤
					if (strtotime($lastTime) >= $lastMonthFirstDayTimes) {
						return true; //上月考勤可以修改
					}
					//上月考勤之前的,不得修改
				}
				//已超出,不得修改
			}
		}

		$this->dao->logError('ErrorKevinTimeOut', '', '');
		return false; //已超出,不得修改
	}

	/**
	 * update a todo.
	 * 
	 * @param  int    $todoID 
	 * @access public
	 * @return void
	 */
	public function update($todoID) {
		$oldTodo		 = $this->getTodoById($todoID);
		if (($this->post->end == $this->post->begin)) {
			$this->dao->logError('ErrorTodoHoursSameTime', '', '');
			return;
		}
		$this->account = $oldTodo->account;

		//验证时间是否重叠
		if (isset($_POST['date'])) {
			//获得时间，用于验证时间重叠
			$todo		 = new stdclass();
			$todo->id	 = $todoID;
			$todo->begin = $this->post->begin;
			$todo->end	 = $this->post->end;
			$todoArray[] = $todo;
			if ($this->isTodoTimeOverlapOne($this->post->date, $todo))
				return;
		}
		$minutes	 = $this->getWorkMinutes($this->post->minutes, $this->post->begin, $this->post->end);
		$todo		 = fixer::input('post')
			->add('minutes', $minutes)
			->setIF($this->post->begin == 0, 'begin', '2400')
			->setIF($this->post->end == 0, 'end', '2400')
			->skipSpecial($this->config->todo->editor->edit['id'])
			->remove('projectName')
			->remove('comment')
			->get();
		$todo->private = 0;
		$this->dao->update(TABLE_TODO)->data($todo)
			->autoCheck()
			->checkIF(true, $this->config->todo->edit->requiredFields, 'notempty')
			->where('id')->eq($todoID)
			->exec();
		$todo->date	 = str_replace('-', '', $todo->date);
		if (!dao::isError())
			return common::createChanges($oldTodo, $todo);
	}

}
