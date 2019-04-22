<?php

class kevinhours extends control {

	public function __construct() {
		parent::__construct();
		$this->app->loadClass('date');
		$this->loadModel('kevincom');
		$this->loadModel('todo');
		$this->loadModel('kevincalendar');
		$this->app->loadClass('kevin'); //加载kevin类
	}

	public function ajaxDeptEmployees($deptId) {
		die(html::select('userIndex', $this->kevinhours->getDeptEmployeesPairs($deptId), '', 'class=form-control'));
	}

	/**
	 * AJAX: get actions of a todo. for web app.
	 * 
	 * @param  int    $todoID 
	 * @access public
	 * @return void
	 */
	public function ajaxGetDetail($todoID) {
		$this->view->actions = $this->loadModel('action')->getList('todo', $todoID);
		$this->view->todo	 = $this->kevinhours->getTodoById($todoID);
		$this->display();
	}

	public function ajaxProjectNameByProject($project) {
		die(html::select('projectNameBox', $this->kevinhours->getProjectNameByProjectPairs($project), '', 'class=form-control'));
	}

	/**
	 * Batch create todo
	 * 
	 * @param  string $date 
	 * @param  string $account 
	 * @access public
	 * @return void
	 */
	public function batchcreate($date = 'today', $account = '') {
		$account = $this->kevinhours->checkUserMustSelf($account);
		if ($date == 'today')
			$date	 = date(DT_DATE1, time());
		//时间验证
		if (!$this->kevinhours->timeVerificated($date)) {
			echo "<script>alert('" . dao::getError(true) . "');</script>";
			if (isonlybody())
				die(js::reload('parent.parent'));
			die(js::locate($this->createLink('kevinhours', 'index'), 'parent'));
		}
		if (!empty($_POST)) {
			$this->kevinhours->batchcreate();
			if (dao::isError())
				die(js::error(dao::getError()));

			/* Locate the browser. */
			$date = str_replace('-', '', $this->post->date);
			if ($date == '') {
				$date = 'future';
			} else if ($date == date('Ymd')) {
				$date = 'today';
			}
			if (isonlybody())
				die(js::reload('parent.parent'));
			die(js::locate($this->createLink('kevinhours', 'index'), 'parent'));
		}
		$currentDate	 = date('Y-m-d', strtotime($date));
		$currentStatus	 = $this->kevincalendar->getStatusByDate($currentDate);
		if ('' == $currentStatus) {
			$unix			 = strtotime($currentDate); //获得日期的 Unix 时间戳
			$week			 = date("w", $unix); //获得是周几,周末为0,周一为1
			if ($week > 5 || $week < 1)
				$currentStatus	 = 'hol';
			else
				$currentStatus	 = 'nor';
		}

		$this->view->projectsArray	 = $this->kevinhours->getProjectNameArray();
		$this->view->todos			 = $this->kevinhours->getList($currentDate);
		$this->view->title			 = $this->lang->kevinhours->common . $this->lang->colon . $this->lang->kevinhours->batchCreate;
		$this->view->position[]		 = html::a($this->createLink('kevinhours', 'todo'), $this->lang->kevinhours->todo);
		$this->view->position[]		 = $this->lang->kevinhours->batchCreate;
		$this->view->date			 = $currentDate;
		$this->view->times			 = date::buildTimeList($this->config->kevinhours->times->begin, $this->config->kevinhours->times->end, $this->config->kevinhours->times->delta);
		$this->view->time			 = date::now();
		$this->view->isonlybody		 = isonlybody();
		$this->view->dateStatus		 = ('nor' == $currentStatus) ? 0 : 1;
		$this->view->isDateFuture	 = $this->kevinhours->isDateFuture($currentDate);
		if ($this->view->isDateFuture) {
			$this->view->statusList['wait'] = $this->lang->kevinhours->statusList['wait'];
		} else {
			$this->view->statusList = $this->lang->kevinhours->statusList;
		}
		$this->display();
	}

	/**
	 * Batch edit todo.
	 * 
	 * @param  string $from example:myTodo, todoBatchEdit.
	 * @param  string $type 
	 * @param  string $account 
	 * @param  string $status 
	 * @access public
	 * @return void
	 */
	public function batchedit($from = '', $type = 'today', $account = '', $status = 'all') {
		/* Get form data for my-todo. */
		if ($from == 'myTodo') {
			/* Initialize vars. */
			$editedTodos	 = array();
			$todoIDList		 = array();
			$columns		 = 10;
			$showSuhosinInfo = false;

			$this->loadModel('task');
			$this->loadModel('bug');
			if ($account == '')
				$account	 = $this->app->user->account;
			$bugs		 = $this->bug->getUserBugPairs($account);
			$tasks		 = $this->task->getUserTaskPairs($account, $status);
			$allTodos	 = $this->kevinhours->getList($type, $account, $status);
			if ($this->post->todoIDList)
				$todoIDList	 = $this->post->todoIDList;

			/* Initialize todos whose need to edited. */
			foreach ($allTodos as $todo) {
				if (in_array($todo->id, $todoIDList)) {
					$editedTodos[$todo->id] = $todo;
				}
			}
			foreach ($editedTodos as $todo) {
				if ($todo->type == 'task')
					$todo->name		 = $this->dao->findById($todo->idvalue)->from(TABLE_TASK)->fetch('name');
				if ($todo->type == 'bug')
					$todo->name		 = $this->dao->findById($todo->idvalue)->from(TABLE_BUG)->fetch('title');
				$todo->begin	 = str_replace(':', '', $todo->begin);
				$todo->end		 = str_replace(':', '', $todo->end);
				$todo->minutes	 = $this->kevinhours->showWorkHours($todo->minutes);
			}

			/* Judge whether the edited todos is too large. */
			$showSuhosinInfo = $this->loadModel('common')->judgeSuhosinSetting(count($editedTodos), $columns);

			/* Set the sessions. */
			$this->app->session->set('showSuhosinInfo', $showSuhosinInfo);

			/* Assign. */
			$title		 = $this->lang->kevinhours->common . $this->lang->colon . $this->lang->kevinhours->batchEdit;
			$position[]	 = html::a($this->createLink('my', 'todo'), $this->lang->my->todo);
			$position[]	 = $this->lang->kevinhours->common;
			$position[]	 = $this->lang->kevinhours->batchEdit;

			if ($showSuhosinInfo)
				$this->view->suhosinInfo	 = $this->lang->suhosinInfo;
			$this->view->projectsArray	 = $this->kevinhours->getProjectNameArray();
			$this->view->bugs			 = $bugs;
			$this->view->tasks			 = $tasks;
			$this->view->editedTodos	 = $editedTodos;
			$this->view->times			 = date::buildTimeList($this->config->kevinhours->times->begin, $this->config->kevinhours->times->end, $this->config->kevinhours->times->delta);
			$this->view->time			 = date::now();
			$this->view->title			 = $title;
			$this->view->position		 = $position;

			$this->display();
		}
		/* Get form data from todo-batchEdit. */
		elseif ($from == 'todoBatchEdit') {
			$allChanges = $this->kevinhours->batchUpdate();
			foreach ($allChanges as $todoID => $changes) {
				if (empty($changes))
					continue;

				$actionID = $this->loadModel('action')->create('todo', $todoID, 'edited');
				$this->action->logHistory($actionID, $changes);
			}

			die(js::locate($this->session->todoList, 'parent'));
		}
	}

	/**
	 * Browse departments and users of a company.
	 * 
	 * @param  int    $param 
	 * @param  string $type 
	 * @param  string $orderBy 
	 * @param  int    $recTotal 
	 * @param  int    $recPerPage 
	 * @param  int    $pageID 
	 * @access public
	 * @return void
	 */
	public function browse($param = 0, $type = 'bydept', $orderBy = 'locked,id', $recTotal = 0, $recPerPage = 20, $pageID = 1) {
		$this->loadModel('search');
		$this->loadModel('company');
		$this->loadModel('dept');
		$this->lang->set('menugroup.company', 'company');

		$deptID = $type == 'bydept' ? (int) $param : 0;
		$this->company->setMenu($deptID);

		/* Save session. */
		$this->session->set('userList', $this->app->getURI(true));

		/* Set the pager. */
		$this->app->loadClass('pager', $static	 = true);
		$pager	 = pager::init($recTotal, $recPerPage, $pageID);

		/* Append id for secend sort. */
		$sort = $this->loadModel('common')->appendOrder($orderBy);

		/* Build the search form. */
		$queryID															 = $type == 'bydept' ? 0 : (int) $param;
		$this->config->company->browse->search['actionURL']					 = $this->createLink('kevinhours', 'browse', "param=myQueryID&type=bysearch");
		$this->config->company->browse->search['queryID']					 = $queryID;
		$this->config->company->browse->search['params']['dept']['values']	 = array('' => '') + $this->dept->getOptionMenu();

		if ($type == 'bydept') {
			$childDeptIds	 = $this->dept->getAllChildID($deptID);
			$users			 = $this->dept->getUsers($childDeptIds, $pager, $sort);
		} else {
			if ($queryID) {
				$query = $this->search->getQuery($queryID);
				if ($query) {
					$this->session->set('userQuery', $query->sql);
					$this->session->set('userForm', $query->form);
				} else {
					$this->session->set('userQuery', ' 1 = 1');
				}
			}
			$users = $this->loadModel('user')->getByQuery($this->session->userQuery, $pager, $sort);
		}
		$this->view->title		= $this->lang->kevinhours->common . $this->lang->colon . $this->lang->dept->common;
		
		$this->view->position[]	 = $this->lang->dept->common;
		$this->view->users		 = $users;
		$this->view->searchForm	 = $this->fetch('search', 'buildForm', $this->config->company->browse->search);
		$this->view->deptTree	 = $this->dept->getTreeMenu($rooteDeptID = 0, array('kevinhoursModel', 'createMemberLinkOfBrowse'));
		$this->view->parentDepts = $this->dept->getParents($deptID);
		$this->view->depts		 = $this->kevinhours->getDeptArray();
		$this->view->orderBy	 = $orderBy;
		$this->view->deptID		 = $deptID;
		$this->view->pager		 = $pager;
		$this->view->param		 = $param;
		$this->view->type		 = $type;

		$this->display();
	}

	/**
	 * clock control
	 *      
	 * @access public
	 * @return void
	 */
	public function clock($type = '', $account = '', $deptID = '') {
		$this->kevinhours->getShowAccount($account);

		/* The title and position. */
		$this->view->title		 = $this->lang->kevinhours->common . $this->lang->colon . $this->lang->kevinhours->clock;
		$this->view->position[]	 = $this->lang->kevinhours->clock;

		if ('' == $type) {
			$type = date('Ym');
		}
		$this->commonWebParam($type);

		$this->view->controlType = 'index';
		$this->view->type		 = $type;

		$this->kevinhours->getTodayClockInfor(); //get clock
		$CalendarTableArray				 = $this->kevinhours->calculateClockTable($type);
		$this->view->CalendarTableString = $CalendarTableArray['clock'];
		$this->view->oveHoursTableString = "";

		$this->view->employees = "";

		if ($this->kevinhours->user->browseDeptHours || $this->kevinhours->user->checkAll) {
			if (!$this->kevinhours->user->checkAll) {
				$deptID					 = $this->kevinhours->user->checkAll ? $this->kevinhours->user->dept : $this->app->user->dept;
				$this->view->deptArray	 = array();
			} else {
				if ('' == $deptID) {
					$deptID = $this->kevinhours->user->dept;
				}
				$this->view->deptArray = $this->kevincom->getDeptOptionMenu();
			}
			$this->view->employees = $this->kevinhours->getDeptEmployeesPairs($deptID);
		}

		$this->view->deptID	 = $deptID;
		$this->view->account = $account;
		$this->display();
	}

	/**
	 * clock action
	 *      
	 * @access public
	 * @param  string    $action 
	 * @param  bool    $ok 
	 * @return void
	 */
	public function clockact($action = 'in', $ok = false) {

		if (($action != 'in' && $action != 'out') || !$action) {
			die(js::error($this->lang->error->KevinClockActionType) . js::closeModal('parent'));
		}

		$id = $this->kevinhours->clockact($action, $ok);
		if (dao::isError()) {
			if (!$this->kevinhours->clockInor->showView) {
				die(js::error(dao::getError()) . js::closeModal('parent'));
			}
			$this->view->action = $action;

			/* The title and position. */
			$this->view->title		 = $this->lang->kevinhours->common . $this->lang->colon . $this->lang->kevinhours->clockact;
			$this->view->position[]	 = $this->lang->kevinhours->clock;
			$this->view->item		 = $this->kevinhours->clockInor->item;

			$this->display();
		} else {
			if (isonlybody()) {
				die(js::reload('parent.parent'));
			}
			die(js::closeModal('parent'));
		}
	}

	public function commonWebParam($type) {
		if ('' == $type) {
			$type = date('Ym');
		}
		$this->view->currentDate = "";
		if(is_numeric($type)){
			$length1 = strlen($type);
			if ( 6 == $length1) {
				$this->view->currentDate = date('Y-m-d', strtotime($type.'01'));
			}
			else if ( $length1 == 8) {
				$this->view->currentDate = date('Y-m-d', strtotime($type));
			} else if ($length1== 4) {
				$this->view->currentDate = date('Y-m-d', strtotime($type.'-01-01'));
			}
		}
		if($this->view->currentDate == "") $this->view->currentDate = date('Y-m-d');
		$currentMonth	 = date('Y-m', strtotime($this->view->currentDate)) . '-01';

		$this->view->lastMonth	 = date('Ym', strtotime("$currentMonth -1 month"));
		$this->view->nextMonth	 = date('Ym', strtotime("$currentMonth +1 month"));
		$this->view->thisMonth	 = date('Ym');

		$this->view->methodName		 = $this->app->getMethodName();
		$this->view->yearList		 = $this->kevinhours->getYearList();
		$this->view->monthList		 = $this->lang->kevinhours->month;
		$this->view->currentYear	 = date('Y', strtotime("$currentMonth"));
		$this->view->currentMonth	 = date('m', strtotime("$currentMonth"));
		
		extract(kevin::getBeginEndTime($type));
		$this->view->begin = $begin;
		$this->view->end = $end;

	}

	public function count($type = '', $account = '', $deptId = '', $hourstype = '', $employeetype = 'all', $recTotal = 0, $recPerPage = 20, $pageID = 1,$deptflag='x') {
		/* Load pager. */
		$this->app->loadClass('pager', $static	 = true);
		$pager	 = pager::init($recTotal, $recPerPage, $pageID);
		if ('' == $type) {
			if (date("d") <= 5)
				$type	 = date('Ym', strtotime("-1 month")) . '0';
			else
				$type	 = date('Ym') . '0';
		}
		if (!empty($_POST)) {
			$deptId			 = $this->post->dept;
			$hourstype		 = $this->post->hourstype;
			$employeetype	 = $this->post->employeetype;
			$year			 = $this->post->year;
			$deptcount		 = $this->post->deptcount;
			$month			 = ($this->post->month == '') ? '00' : $this->post->month;
			$season			 = ($this->post->season == '') ? '0' : $this->post->season;
			//type=年+月+季度 组成的7位数字
			$type			 = $year . $month . $season;
			$account		 = $this->post->userIndex;
			die(js::locate($this->createLink('kevinhours', 'count', "type=$type&account=$account&deptId=$deptId&hourstype=$hourstype&employeetype=$employeetype&recTotal=$recTotal&recPerPage=$recPerPage&pageID=$pageID&deptflag=$deptcount"), 'parent'));
		}

		/* The title and position. */
		$this->view->title		 = $this->lang->kevinhours->common . $this->lang->colon . $this->lang->kevinhours->count;
		$this->view->position[]	 = $this->lang->kevinhours->count;

		//用户"首次"进入统计考勤
		if ($account == '' && $deptId == '') {
			$deptId = $this->kevincom->getDeptIdByAccount($this->app->user->account);
		}
		$year	 = date('Y');
		$month	 = date('m');
		$season	 = '00';
		//判断类型是否是数字
		if (is_numeric($type) && 7 == strlen($type)) {
			//日期截取
			$year	 = substr($type, 0, 4);
			$month	 = substr($type, 4, 2);
			$season	 = substr($type, 6, 1);
		}
		$this->view->year	 = $year;
		$this->view->month	 = ('00' == $month) ? '' : $month;
		$this->view->season	 = ('0' == $season) ? '' : $season;

		$this->view->hourstypeIndex	 = $hourstype;
		$currentDeptName			 = $this->kevincom->getDeptNameById($deptId);
		$this->view->deptName		 = $currentDeptName;
		$this->view->deptParentName	 = $this->kevincom->getParentDeptNameById($deptId);
		if (common::hasPriv('kevinhours', 'checkAll')) {
			$this->view->deptArray	 = $this->kevincom->getDeptOptionMenu();
			$userArray=$this->kevinhours->getDeptChildEmpsAccount($deptId, false,$deptflag);
			$this->view->deptIndex	 = $deptId;
			$this->view->userArray	 = $this->kevinhours->employeesActive;
			$this->view->userIndex	 = $account;
			$this->view->deptcount=$deptflag;

			$this->view->employeetypeIndex = $employeetype;

			$stmt = $this->kevinhours->getStmtForManHoursList($type, $account, $hourstype, $deptId, $employeetype,'date',$deptflag);
			if (null != $stmt) {
				$this->view->allTodos	 = $this->kevinhours->getTodosForManHoursList($stmt);
				$this->view->todos		 = $this->kevinhours->getTodosForManHoursList($stmt, $pager);
			} else {
				$this->view->allTodos	 = array();
				$this->view->todos		 = array();
			}
			$this->view->deptTree	 = $this->loadModel('dept')->getTreeMenu($rooteDeptID = 0, array('kevinhoursModel', 'createMemberLink'));
		} else if (common::hasPriv('kevinhours', 'browseDeptHours')) {
			$deptArray				 = array();
//			$userArray				 = $this->kevinhours->getDeptEmployeesPairs($deptId);
			$userArray=$this->kevinhours->getDeptChildEmpsAccount($deptId, false,$deptflag);
			$deptArray[$deptId]		 = $currentDeptName;
			$this->view->deptArray	 = $deptArray;
			$this->view->deptIndex	 = $deptId;
			$this->view->userArray	 = $this->kevinhours->employeesActive;
			$this->view->userIndex	 = $account;
			$this->view->deptcount=$deptflag;
			
			$stmt = $this->kevinhours->getStmtForManHoursList($type, $account, $hourstype, $deptId, $employeetype,'',$deptflag);
//			$stmt = $this->kevinhours->getStmtForManHoursList($type, $account, $hourstype, $deptId, $employeetype);
			if (null != $stmt) {
				$this->view->allTodos	 = $this->kevinhours->getTodosForManHoursList($stmt);
				$this->view->todos		 = $this->kevinhours->getTodosForManHoursList($stmt, $pager);
			} else {
				$this->view->allTodos	 = array();
				$this->view->todos		 = array();
			}
		} else {
			$stmt = $this->kevinhours->getStmtForPersonalManHoursList($type, $hourstype);
			if (null != $stmt) {
				$this->view->allTodos	 = $this->kevinhours->getTodosForPersonalManHoursList($stmt);
				$this->view->todos		 = $this->kevinhours->getTodosForPersonalManHoursList($stmt, $pager);
			} else {
				$this->view->allTodos	 = array();
				$this->view->todos		 = array();
			}
		}
		$this->view->currentMonth	 = date('m');
		$this->view->yearList		 = $this->kevinhours->getYearList();
		$this->view->calendarArray	 = $this->kevincalendar->getStatusArray($type);
		$this->view->pager			 = $pager;
		$this->display();
	}

	/**
	 * Create a todo.
	 * 
	 * @param  string|date $date 
	 * @param  string      $account 
	 * @access public
	 * @return void
	 */
	public function create($date = 'today', $account = '', $name = '', $project = '', $begin = '', $end = '') {
		$this->kevinhours->checkUserMustSelf($account);
		if ($date == 'today')
			$date = date::today();

		if (!empty($_POST)) {
			if (isset($_POST['date'])) {
				//时间验证
				if (!$this->kevinhours->timeVerificated($this->post->date)) {
					echo "<script>alert('" . dao::getError(true) . "');</script>";
					die(js::reload('parent'));
				}
				//判断日期是否超前
				if ($this->kevinhours->isDateFuture($date))
					$_POST['status'] = 'wait';
			} else
				$_POST['status'] = 'wait';

			//判断项目id如果是否存在
			$_POST['project'] = $this->kevinhours->GetProjectByIDorCashCode($this->post->project);

			$todoID	 = $this->kevinhours->create($date);
			if (dao::isError())
				die(js::error(dao::getError()));
			$this->loadModel('action')->create('todo', $todoID, 'opened');
			$date	 = str_replace('-', '', $this->post->date);
			if ($date == '') {
				$date = 'future';
			} else if ($date == date('Ymd')) {
				$date = 'today';
			}

			if (isonlybody()) {
				unset($_GET['onlybody']); //销毁指定的变量,否则一直是onlybody=yes
				die(js::locate($this->createLink('kevinhours', 'index'), 'parent.parent'));
			}
			die(js::locate($this->createLink('my', 'todo', ''), 'parent'));
		}

		//时间验证
		if (!$this->kevinhours->timeVerificated($date)) {
			echo "<script>alert('" . dao::getError(true) . "');</script>";
			die(js::reload('parent'));
		}
		$this->view->todos			 = $this->kevinhours->getList($date);
		$this->view->projectsArray	 = $this->kevinhours->getProjectNameArray();
		$this->view->datesArray		 = $this->kevinhours->buildDateList();
		$this->view->title			 = $this->lang->kevinhours->common . $this->lang->colon . $this->lang->kevinhours->create;
		$this->view->position[]		 = $this->lang->kevinhours->common;
		$this->view->position[]		 = $this->lang->kevinhours->create;
		$this->view->name			 = $name;
		$this->view->project		 = $project;
		$this->view->begin			 = $begin;
		$this->view->end			 = $end;
		$this->view->date			 = strftime("%Y-%m-%d", strtotime($date));
		$this->view->times			 = date::buildTimeList($this->config->kevinhours->times->begin, $this->config->kevinhours->times->end, $this->config->kevinhours->times->delta);
		$this->display();
	}

	//打印外协工作记录
	public function dispatched($type = '', $account = '', $deptId = '', $hourstype = '', $employeetype = 'informal', $isIncludeAnn = '') {
		/* The title and position. */
		$this->view->title		 = $this->lang->kevinhours->common . $this->lang->colon . $this->lang->kevinhours->dispatched;
		$this->view->position[]	 = $this->lang->kevinhours->common;
		$this->view->position[]	 = $this->lang->kevinhours->dispatched;

		//check account
		if ('' == $deptId) {
			if ('' == $account) {
				$account = $this->app->user->account;
			}
		}

		if (common::hasPriv('kevinhours', 'checkAll')) {
			//全部权限
			if ('' == $deptId) {
				$deptId = $this->kevincom->getDeptIdByAccount($account);
			}
		} else if (common::hasPriv('kevinhours', 'browseDeptHours')) {
			//科室查看
			$deptId = $this->kevincom->getDeptIdByAccount($this->app->user->account);
		} else {//一般用户
			$account = $this->app->user->account;
			$deptId	 = $this->kevincom->getDeptIdByAccount($account);
		}
		$this->view->deptName = $this->kevincom->getDeptNameById($deptId);
		if ('' == $this->view->deptName) {
			die(js::error("Can not get correct department!") . js::closeModal('parent'));
		}

		//是否包含年假
		$isIncludeAnn				 = $this->kevinhours->setIsIncludeAnnSession($isIncludeAnn);
		if (isset($_SESSION['isIncludeAnn']))
			$isIncludeAnn				 = $this->session->isIncludeAnn;
		$this->view->isIncludeAnn	 = $isIncludeAnn;
		$this->view->todos			 = $this->kevinhours->getGuestManHoursList($type, $account, $hourstype, $deptId, $employeetype);
		if (!$this->view->todos) {
			die(js::error("No work record!") . js::closeModal('parent'));
		}
		$this->display();
	}

	/**
	 * Edit a todo.
	 * 
	 * @param  int    $todoID 
	 * @access public
	 * @return void
	 */
	public function edit($todoID, $comment = false) {
		if (!empty($_POST)) {
			if (isset($_POST['date'])) {
				//时间验证
				if (!$this->kevinhours->timeVerificated($this->post->date)) {
					$errmsg = dao::getError(true);
					//如果不具有修改考勤权限则判断时间
					if (!common::hasPriv('kevinhours', 'modifyOtherHours')) {
						echo "<script>alert('" . $errmsg . "');</script>";
						die(js::reload('parent'));
					}
				}
				//判断日期是否超前
				if ($this->kevinhours->isDateFuture($_POST['date']))
					$_POST['status'] = 'wait';
			} else
				$_POST['status']	 = 'wait';
			//判断项目id如果是否存在
			$_POST['project']	 = $this->kevinhours->GetProjectByIDorCashCode($this->post->project);
			$changes			 = $this->kevinhours->update($todoID);
			if (dao::isError())
				die(js::error(dao::getError()));
			if ($changes) {
				$actionID = $this->loadModel('action')->create('todo', $todoID, 'edited', $this->post->comment);
				$this->action->logHistory($actionID, $changes);
			}
			if (isonlybody())
				die(js::closeModal('parent.parent', 'this')); //编辑完成后刷新界面
			die(js::locate($this->createLink('my', 'todo', ''), 'parent'));
		}

		/* Judge a private todo or not, If private, die. */
		$todo = $this->kevinhours->getTodoById($todoID);
		//时间验证
		if (!$this->kevinhours->timeVerificated($todo->date)) {
			$errmsg = dao::getError(true);
			//如果不具有修改考勤权限则判断时间
			if (common::hasPriv('kevinhours', 'modifyOtherHours')) {
				$comment = true;
			} else {
				echo "<script>alert('" . errmsg . "');</script>";
				die(js::reload('parent'));
			}
		}

		//去除私人验证
		$this->view->projectsArray	 = $this->kevinhours->getProjectNameArray();
		$todo->date					 = strftime("%Y-%m-%d", strtotime($todo->date));
		$this->view->title			 = $this->lang->kevinhours->common . $this->lang->colon . $this->lang->kevinhours->edit;
		$this->view->position[]		 = $this->lang->kevinhours->common;
		$this->view->position[]		 = $this->lang->kevinhours->edit;
		$this->view->datesArray		 = $this->kevinhours->buildDateList();
		$this->view->times			 = date::buildTimeList($this->config->kevinhours->times->begin, $this->config->kevinhours->times->end, $this->config->kevinhours->times->delta);
		$this->view->todo			 = $todo;
		$this->view->comment		 = $comment;
		$this->display();
	}

	/**
	 * Finish a todo.
	 * 
	 * @param  int    $todoID 
	 * @access public
	 * @return void
	 */
	public function finish($todoID) {
		//如果不是数字,则默认完成本月
		if (!is_numeric($todoID)) {
			$todoIDs = $this->kevinhours->getTodosOfUndoneByThisMonth();
			foreach ($todoIDs as $tempTodoID) {
				$this->kevinhours->finish($tempTodoID);
				if (dao::isError())
					die(js::error(dao::getError()) . js::closeModal('parent.parent'));
			}
		} else {
			$todo = $this->kevinhours->getTodoById($todoID);

			if ($todo->status != 'done') {
				$this->kevinhours->finish($todoID);
				if (dao::isError())
					die(js::error(dao::getError()) . js::closeModal('parent.parent'));
			}
		}
		if (isonlybody())
			die(js::reload('parent.parent'));
		die(js::reload('parent'));
	}

	/**
	 * index calendar.
	 *      
	 * @access public
	 * @return void
	 */
	public function index($type = '', $account = '', $deptID = '') {
		$this->kevinhours->getShowAccount($account);

		/* The title and position. */
		$this->view->title		 = $this->lang->kevinhours->common . $this->lang->colon . $this->lang->kevinhours->index;
		$this->view->position[]	 = $this->lang->kevinhours->index;
	
		if ('' == $type) {
			$type = date('Ym');
		}
		$this->commonWebParam($type);

		$this->view->controlType		 = 'index';
		$this->view->type				 = $type;
		$CalendarTableArray				 = $this->kevinhours->calculateCalendarTable($type);
		$this->view->CalendarTableString = $CalendarTableArray['todoTable'];
		$this->view->oveHoursTableString = $CalendarTableArray['oveHoursTable'];

		// $this->CheckOutEmployeesPairs($deptID);
		$this->view->account = $account;
			
		$this->view->overtreetitle=$this->kevinhours->overtreetitle($type,$account,$deptID,'index',array('kevinhoursModel','createIndexLink'),array($this->kevinhours->user->account,$this->kevinhours->realname,""));
		$this->display();
	}

	public function CheckOutEmployeesPairs(&$deptID) {
		if ($this->kevinhours->user->browseDeptHours || $this->kevinhours->user->checkAll) {
			if (!$this->kevinhours->user->checkAll) {
				$deptID					 = $this->kevinhours->user->checkAll ? $this->kevinhours->user->dept : $this->app->user->dept;
				$this->view->deptArray	 = array();
			} else {
				if ('' == $deptID) {
					$deptID = $this->kevinhours->user->dept;
				}
				$this->view->deptArray = $this->kevincom->getDeptOptionMenu();
			}
			$this->kevinhours->getDeptEmployeesPairs($deptID);
		}

		$this->view->deptID	 = $deptID;
	}
	
	/**
	 * Manage contacts.
	 * 
	 * @param  int    $listID 
	 * @access public
	 * @return void
	 */
	public function manageContacts($listID = 0) {
		$this->loadModel('user');
		$lists = $this->user->getContactLists($this->app->user->account);

		/* If set $mode, need to update database. */
		if ($this->post->mode) {
			/* The mode is new: append or new a list. */
			if ($this->post->mode == 'new') {
				if ($this->post->list2Append) {
					$this->user->append2ContactList($this->post->list2Append, $this->post->users);
					die(js::locate(inlink('manageContacts', "listID={$this->post->list2Append}"), 'parent'));
				} elseif ($this->post->newList) {
					$listID = $this->user->createContactList($this->post->newList, $this->post->users);
					die(js::locate(inlink('manageContacts', "listID=$listID"), 'parent'));
				}
			} elseif ($this->post->mode == 'edit') {
				$this->user->updateContactList($this->post->listID, $this->post->listName, $this->post->users);
				die(js::locate(inlink('manageContacts', "listID={$this->post->listID}"), 'parent'));
			}
		}
		if ($this->post->users) {
			$mode	 = 'new';
			$users	 = $this->user->getContactUserPairs($this->post->users);
		} else {
			$mode	 = 'edit';
			$listID	 = $listID ? $listID : key($lists);
			if (!$listID)
				die(js::alert($this->lang->user->contacts->noListYet) . js::locate($this->createLink('kevinhours', 'browse'), 'parent'));

			$list				 = $this->user->getContactListByID($listID);
			$users				 = explode(',', $list->userList);
			$users				 = $this->user->getContactUserPairs($users);
			$this->view->list	 = $list;
		}

		$this->view->title		 = $this->lang->company->common . $this->lang->colon . $this->lang->kevinhours->manageContacts;
		$this->view->position[]	 = $this->lang->company->common;
		$this->view->position[]	 = $this->lang->kevinhours->manageContacts;
		$this->view->lists		 = $this->user->getContactLists($this->app->user->account);
		$this->view->users		 = $users;
		$this->view->listID		 = $listID;
		$this->view->mode		 = $mode;
		$this->display();
	}

	/**
	 * Get my.
	 * @param  string $type 
	 * @param  string $account 
	 * @access public
	 * @return void
	 */
	public function my($type = 'thisMonth', $account = '',$deptID='') {

		$this->kevinhours->getShowAccount($account);

		if($deptID=='')$deptID=$this->kevinhours->user->dept;
		/* The title and position. */
		$this->view->title		 = $this->lang->kevinhours->common . $this->lang->colon . $this->lang->kevinhours->my;
		$this->view->position[]	 = $this->lang->kevinhours->my;
	
		$this->commonWebParam($type);

		$this->view->todos		 = $this->kevinhours->getRecentProjectAndTime($type);
		$this->view->countHours	 = $this->kevinhours->getRecentProjectTotalTime($type);
		$this->view->type		 = $type;
		$this->view->pager		 = null;
			
		$this->view->overtreetitle=$this->kevinhours->overtreetitle($type,$this->session->kevin_user_account,$deptID,'my',array('kevinhoursModel','createMyLink'),array($this->kevinhours->account,$this->kevinhours->realname,''));
		$this->display();
	}
	
	/**
	 * My over work. 
	 * 
	 * @param  string $type 
	 * @param  string $account 
	 * @param  string $status 
	 * @param  int    $recTotal 
	 * @param  int    $recPerPage 
	 * @param  int    $pageID 
	 * @access public
	 * @return void
	 */
	public function over($type = 'thisYear', $account = '', $deptID = '') {
		if($type == '') $type = 'thisYear';
		
		$this->kevinhours->getShowAccount($account);
		
		if(!$deptID)$deptID = $this->kevinhours->user->dept;
		if($this->session->accmethod!='over'){
		if(isset($_SESSION['currentdeptID']))$deptID=$_SESSION['currentdeptID'];
		elseif(isset($_COOKIE['currentdeptID']))$deptID=$_COOKIE['currentdeptID'];
		// $this->accountdept=$accdept->dept;
		}
		
		$this->session->set('accmethod','over');
		setcookie('currentdeptID',$deptID);
		
		// $this->view->overtreetitle=$this->kevinhours->overtreetitle($type,$account,$deptID,'over',array('kevinhoursModel','createoverlink'),array($this->kevinhours->account,$this->kevinhours->realname,''));
		$this->session->set('currentdeptID',$deptID);
		$deptArray=$this->dao->select('id,name')->from(TABLE_DEPT)->where('deleted')->eq(0)->fetchPairs('id','name');
		if(common::hasPriv('kevinhours', 'checkAll')){
			$this->loadModel('dept');
			$deptinfo=$this->dao->select('name,parent')->from(TABLE_DEPT)->where('id')->eq($deptID)->fetch();
			$deptchilds= $this->dept->getSons($deptID);
		}elseif(common::hasPriv('kevinhours', 'browseDeptHours')){
			$this->loadModel('dept');
			$path = $this->dao->select('path')->from(TABLE_DEPT)->where('id')->eq($this->app->user->dept)->fetch('path');
			$parentjudge=explode(',', $path);
			if(in_array($deptID,$parentjudge)) $deptinfo=false;
			else $deptinfo=$this->dao->select('name,parent')->from(TABLE_DEPT)->where('id')->eq($deptID)->fetch();
			$deptchilds= $this->dept->getSons($deptID);
		}else {

			$deptinfo=false;
			$deptchilds=false;
		}
		if(!isset($deptArray[$deptID])) die(js::error($this->lang->kevinhours->KevinDeptinfoNotExist) . js::locate('back'));
		$deptchildmenu=array();
		$simplename=explode('/',$deptArray[$deptID]);
		$deptchildmenu[$deptID]=$simplename[count($simplename)-1];
		if($deptchilds){
			foreach($deptchilds as $deptchild){
				$deptchildmenu[$deptchild->id]=$deptchild->name;
			}
		}
		$this->view->deptchildmenu=$deptchildmenu;
		$this->view->deptChilds	=$deptchilds;
		$this->view->deptinfo=$deptinfo;
		$this->view->deptname=$deptArray[$deptID];
		$this->view->method='over';

		/* The title and position. */
		$this->view->title		 = $this->lang->kevinhours->common . $this->lang->colon . $this->lang->kevinhours->over;
		$this->view->position[]	 = $this->lang->kevinhours->over;
		$this->commonWebParam($type);
		$this->view->type			 = $type;
		$this->CheckOutEmployeesPairs($deptID);
		$this->view->todos	 = array();
		$this->kevinhours->getOverMonth($this->view->begin, $this->view->end , $account,$this->view->todos);

		$this->view->account		 = $account;
		$this->display();
	}
	
	/**
	 * Dept over work. 
	 * 
	 * @param  string $type 
	 * @param  string $account 
	 * @param  string $status 
	 * @param  int    $recTotal 
	 * @param  int    $recPerPage 
	 * @param  int    $pageID 
	 * @access public
	 * @return void
	 */
	public function overdept($type = 'thisYear',$deptID = '') {
		if($type == '') $type = 'thisYear';
		$this->kevinhours->getShowAccount('');
		$account='';
		if(isset($_SESSION['kevin_user_account']))$account=$_SESSION['kevin_user_account'];
		
		if(!$deptID)$deptID = $this->kevinhours->user->dept;
		if($this->session->accmethod!='over'){
		if(isset($_SESSION['currentdeptID']))$deptID=$_SESSION['currentdeptID'];
		elseif(isset($_COOKIE['currentdeptID']))$deptID=$_COOKIE['currentdeptID'];
		// $this->accountdept=$accdept->dept;
		}
		
		$this->session->set('accmethod','over');
		setcookie('currentdeptID',$deptID);
		
		$this->session->set('currentdeptID',$deptID);
		$deptArray=$this->dao->select('id,name')->from(TABLE_DEPT)->where('deleted')->eq(0)->fetchPairs('id','name');
		
		if(common::hasPriv('kevinhours', 'checkAll')){
			
			$this->loadModel('dept');
			$deptinfo=$this->dao->select('name,parent')->from(TABLE_DEPT)->where('id')->eq($deptID)->fetch();
			$deptchilds=$deptchildbydept= $this->dept->getSons($deptID);
		}elseif(common::hasPriv('kevinhours', 'browseDeptHours')){
			$this->loadModel('dept');
			$path = $this->dao->select('path')->from(TABLE_DEPT)->where('id')->eq($this->app->user->dept)->fetch('path');
			$parentjudge=explode(',', $path);
			if(in_array($deptID,$parentjudge)){$deptinfo=false;}
			else $deptinfo=$this->dao->select('name,parent')->from(TABLE_DEPT)->where('id')->eq($deptID)->fetch();
			//SELECT * from zt_dept where path like"%,106,%" order by grade,`order`
			$deptchildbydept=$this->dept->getSons($deptID);
			
			$deptchilds= $this->dao->select('*')->from(TABLE_DEPT)->where('path')->like('%,'.$this->kevinhours->user->dept.',%')->orderBy('grade,order')->fetchAll();
		}else {
			if($deptID == $this->kevinhours->user->dept) $deptinfo=false;
			else $deptinfo=$this->dao->select('name,parent')->from(TABLE_DEPT)->where('id')->eq($deptID)->fetch();
			$deptchilds=false;
			
		}
		if(!isset($deptArray[$deptID])) die(js::error($this->lang->kevinhours->KevinDeptinfoNotExist) . js::locate('back'));
		$deptchildmenu=array();
		if($deptchilds){
			foreach($deptchilds as $deptchild){
				$deptchildmenu[$deptchild->id]=$deptchild->name;
			}
		}
		if(common::hasPriv('kevinhours', 'browseDeptHours')&&!common::hasPriv('kevinhours', 'checkAll')){
			$keysofchild=array_keys($deptchildmenu);
			
			if(!in_array((int)$deptID,$keysofchild))die(js::error($this->lang->kevinhours->accessDenied) . js::locate('back'));
		}
		$this->view->deptchildmenu=$deptchildmenu;

		if(isset($deptchildbydept))
			$this->view->deptChilds	=$deptchildbydept;
		$this->view->deptinfo=$deptinfo;
		$this->view->deptname=$deptArray[$deptID];
		$this->view->method='overdept';
		
		/* The title and position. */
		$deptname=$this->dao->select('name')->from(TABLE_DEPT)->where('id')->eq($deptID)->fetch();
		$this->view->title		 = $this->lang->kevinhours->common . $this->lang->colon . $this->lang->kevinhours->overdept;
		$this->view->position[]	 = $this->lang->kevinhours->overdept;
		$this->commonWebParam($type);
		$this->view->type			 = $type;
		$this->view->deptID			 = $deptID;
		$this->view->deptname		 = $deptname->name;
		$this->CheckOutEmployeesPairs($deptID);
		$this->view->todos	 = array();
		$this->kevinhours->getOverMonthByDepta($this->view->begin, $this->view->end ,$this->kevinhours->employeesActive,$this->view->todos);
		
		$this->display();
	}
	
	//打印加班
	public function printover($type = '', $account = '', $deptId = '',$tabauthflag=0) {
		/* The title and position. */
		$this->view->title		 = $this->lang->kevinhours->common . $this->lang->colon . $this->lang->kevinhours->printover;
		$this->view->position[]	 = $this->lang->kevinhours->common;
		$this->view->position[]	 = $this->lang->kevinhours->printover;

		//check account
		if ('' == $deptId) {
			if ('' == $account) {
				$account = $this->app->user->account;
			}
		}

		if (common::hasPriv('kevinhours', 'checkAll')) {
			//全部权限
			if ('' == $deptId) {
				$deptId = $this->kevincom->getDeptIdByAccount($account);
			}
		} else if (common::hasPriv('kevinhours', 'browseDeptHours')) {
			//科室查看
			$deptId = $this->kevincom->getDeptIdByAccount($this->app->user->account);
		} else {//一般用户
			$account = $this->app->user->account;
			$deptId	 = $this->kevincom->getDeptIdByAccount($account);
		}

		$this->view->deptName = $this->kevincom->getDeptNameById($deptId);
		if ('' == $this->view->deptName) {
			die(js::error("Can not get correct department!") . js::closeModal('parent'));
		}

		if (isset($this->session->tabauthflag))	
			$tabauthflag = $this->session->tabauthflag;

		if($tabauthflag==1){
			$fillauth=$this->app->user->account;
			$accname=$this->dao->select('realname')->from(TABLE_USER)->where('account')->eq($fillauth)->fetch();
			if($accname)$fillauth=$accname->realname;
			else $fillauth='&nbsp';
		}else $fillauth='&nbsp';
		$this->view->fillauth=$fillauth;
		
		$this->view->deptParentName	 = $this->kevincom->getParentDeptNameById($deptId);
		$this->view->todos			 = $this->kevinhours->getManHoursList($type, $account, $this->lang->kevinhours->hoursTypeOve, $deptId, 'all');
		if (!$this->view->todos) {
			die(js::error("No work record!") . js::closeModal('parent'));
		}
		$this->display();
	}

	//打印正式加班
	public function printovertable($type = 'thisMonth', $deptId = '', $isIncludeAnn = '') {
		$begin	 = "";
		$end	 = "";
		extract(kevin::getBeginEndTime($type));
		
		if (common::hasPriv('kevinhours', 'checkAll')) {
			//全部权限
			if ('' == $deptId) {
				$deptId = $this->kevincom->getDeptIdByAccount($account);
			}
			$this->view->accountArrayInDept	 = $this->kevincom->getDeptEmployeesAccount($deptId);
			$this->view->todos				 = $this->kevinhours->getOverManHoursList($type, $deptId);
		} else if (common::hasPriv('kevinhours', 'browseDeptHours')) {
			//科室查看
			$deptId = $this->kevincom->getDeptIdByAccount($this->app->user->account);
			$this->view->accountArrayInDept	 = $this->kevincom->getDeptEmployeesAccount($deptId);
			$this->view->todos				 = $this->kevinhours->getOverManHoursList($type, $deptId);
		} else {//一般用户
			$account = $this->app->user->account;
			$deptId	 = $this->kevincom->getDeptIdByAccount($account);
			$this->view->accountArrayInDept	 = array(0=>$account);
			$this->kevinhours->getGuestManHoursList($type, $account);
			$todos=$this->kevinhours->getOverManHoursList($type, $deptId);
			$acctodos=array();
			foreach($todos as $key=>$todo)
				if($todo->account==$account)$acctodos[$key]=$todo;
			$this->view->todos=$acctodos;
		}
		
		/* The title and position. */
		$this->view->title				 = $this->lang->kevinhours->common . $this->lang->colon . $this->lang->kevinhours->printover;
		$this->view->company			 = $this->loadModel('company')->getById($this->app->company->id);
		$this->view->position[]			 = $this->lang->kevinhours->common;
		$this->view->position[]			 = $this->lang->kevinhours->printover;
		$this->view->dateArray			 = kevin::getFirstYearMonth($begin);
		$this->view->deptName			 = $this->kevincom->getDeptNameById($deptId);
//		$this->view->accountArrayInDept	 = $this->kevincom->getDeptEmployeesAccount($deptId);
		//是否包含年假
		$isIncludeAnn					 = $this->kevinhours->setIsIncludeAnnSession($isIncludeAnn);
		if (isset($_SESSION['isIncludeAnn']))
			$isIncludeAnn					 = $this->session->isIncludeAnn;
		$this->view->isIncludeAnn		 = $isIncludeAnn;
		//出勤统计表
		$this->view->allTodos			 = $this->kevinhours->getDeptTodos($begin, $end, $deptId);
		//获得周期内日历
		$this->view->calendarArray		 = $this->kevincalendar->getList($begin, $end);
		$this->display();
	}

	public function printservicelist($dept = 0, $year = '', $month = '', $season = '', $classdept = 0) {
		//根据月份和季度获得月份列表
		$this->kevinhours->InitialDispachServiceClass($month, $season,$classdept);
		$this->kevinhours->getDispatchAccountsByClassDept($dept, $classdept);
		$this->kevinhours->getCashCodeArrayByDept($year, $month, $season);

		$this->view->DispachServiceClass = $this->kevinhours->DispachServiceClass;
		$this->view->year				 = $year;
		$this->view->deptID				 = $dept;

		//footer中显示当前位置
		$this->view->title		 = $this->lang->kevinhours->common . $this->lang->colon . $this->lang->kevinhours->printservicelist;
		$this->view->DeptName	 = $this->kevincom->getDeptNameById($dept);
		$this->display();
	}

	public function product($productID = 0, $type = '', $isShowDetail = '', $recTotal = 0, $recPerPage = 20, $pageID = 1) {
		$this->loadModel('product');
		$products		 = $this->product->getPairs('nocode');
		$oldProductID	 = $productID;
		/* Set product. */
		$productID		 = $this->product->saveState($productID, $products);
		if ($oldProductID != 0 && $oldProductID != $productID) {
			if(common::hasPriv('kevinhours', 'checkAll')){
				$productID = $oldProductID;
			}
			else die(js::error($this->lang->product->accessDenied) . js::locate('back'));
		}
		/* Save session. */
		$uri = $this->app->getURI(true);
		$this->session->set('productList', $uri);
		$this->session->set('productPlanList', $uri);
		$this->session->set('releaseList', $uri);
		$this->session->set('storyList', $uri);
		$this->session->set('projectList', $uri);
		$this->session->set('taskList', $uri);
		$this->session->set('buildList', $uri);
		$this->session->set('bugList', $uri);
		$this->session->set('caseList', $uri);
		$this->session->set('testtaskList', $uri);

		$pager = null;

		$this->kevinhours->isShowDetailTodo($isShowDetail);
		$isShowDetail = $this->session->isShowDetailTodo;
		//读取配置信息，是否显示详细人时
		if ('true' == $isShowDetail) {
			/* Load pager. */
			$this->app->loadClass('pager', $static	 = true);
			$pager	 = pager::init($recTotal, $recPerPage, $pageID);
		}

		$this->product->setMenu($products, $productID);
		$projectArray = $this->kevincom->getProjectsByProductID($productID);

		$this->view->title		 = $this->lang->product->common . $this->lang->colon . $this->lang->kevinhours->product;
		$this->view->position[]	 = html::a($this->createLink($this->moduleName, 'browse'), $products[$productID]);
		$this->view->position[]	 = $this->lang->kevinhours->product;

		if ('' == $type) {
			$type = date('Ym');
		}
		$stmt					 = $this->kevinhours->getProductStmtFromSql($productID, $type, $projectArray);
		$allTodos				 = $this->kevinhours->getTodosOfProduct($stmt);
		$this->view->allTodos	 = $allTodos;
		if ($pager != null) {
			$this->view->todos = $this->kevinhours->getTodosOfProduct($stmt, $pager);
		} else {
			$this->view->todos = $allTodos;
		}

		$this->view->projectArray = $projectArray;

		$this->commonWebParam($type);
		if (is_numeric($type) && strlen($type) == 8) {
			$this->view->currentDate = date('Y-m-d', strtotime($type));
			$type					 = 'byDate';
		} else {
			$this->view->currentDate = date('Y-m-d');
		}
		$this->view->isShowDetail	 = $isShowDetail;
		$this->view->type			 = $type;
		$this->view->pager			 = $pager;
		$this->view->productID		 = $productID;
		$this->view->products		 = $products;
		$this->display();
	}

	public function project($projectID = 0, $type = 'thisMonth', $isShowDetail = ''
	, $orderBy = 'date,status,begin', $recTotal = 0, $recPerPage = 20, $pageID = 1) {
		$this->loadModel('project');
		$projects		 = $this->project->getPairs('nocode');
		$oldProjectID	 = $projectID;
		$projectID		 = $this->project->saveState($projectID, array_keys($projects));

		if ($oldProjectID != 0 && $oldProjectID != $projectID) {
			if(common::hasPriv('kevinhours', 'checkAll')){
				$projectID = $oldProjectID;
			}
			else die(js::error($this->lang->project->accessDenied) . js::locate('back'));
		}
		/* Save session. */
		$project	 = $this->project->getById($projectID);
		if (!$project)	die(js::error($this->lang->project->accessDenied) . js::locate('back'));

		$projectName = ($project) ? $project->name : "";
		$pager		 = null;

		$this->kevinhours->isShowDetailTodo($isShowDetail);
		$isShowDetail = $this->session->isShowDetailTodo;

		//读取配置信息，是否显示详细人时
		if ($isShowDetail === 'true') {
			/* Load pager. */
			$this->app->loadClass('pager', $static	 = true);
			$pager	 = pager::init($recTotal, $recPerPage, $pageID);
		}

		/* Header and position. */
		$title		 = $projectName . $this->lang->colon . $this->lang->kevinhours->project;
		$position[]	 = html::a($this->createLink('kevinhours', 'project', "projectID=$projectID"), $projectName);
		$position[]	 = $this->lang->kevinhours->project;

		$stmt		 = $this->kevinhours->getStmtFromSql($projectID, $type);
		$allTodos	 = $this->kevinhours->getTodosOfProject($stmt);

		$this->view->allTodos = $allTodos;
		if ($pager != null) {
			$this->view->todos = $this->kevinhours->getTodosOfProject($stmt, $pager);
		} else {
			$this->view->todos = $allTodos;
		}
		$this->commonWebParam($type);
		if (is_numeric($type) && strlen($type) == 8) {
			$this->view->currentDate = date('Y-m-d', strtotime($type));
			$type					 = 'byDate';
		} else {
			$this->view->currentDate = date('Y-m-d');
		}

		$this->view->isShowDetail	 = $isShowDetail;
		$this->view->type			 = $type;
		$this->view->pager			 = $pager;
		$this->view->title			 = $title;
		$this->view->position		 = $position;
		$this->view->projectID		 = $projectID;
		$this->view->projectName	 = $projectName;
		$this->display();
	}

	public function ratepay() {
		if (!empty($_POST)) {
			$ratePay = $this->post->ratepay;
			$this->kevinhours->updateRatePay($ratePay);
			die(js::reload('parent.parent'));
		}
		$this->view->ratePay = $this->kevinhours->getPersonalRatePay();
		$this->display();
	}

	/**
	 * Get project.
	 * @param  string|keywords 
	 * @access public
	 * @return void
	 */
	public function searchproject($keywords = '') {
		if (!empty($_POST)) {
			$keywords = $this->post->keywords;
			die(js::locate($this->createLink('kevinhours', 'searchProject', "keywords=$keywords"), 'parent'));
		}
		$projectArray = array();
		if ('' != $keywords) {
			$projectArray = $this->kevinhours->getRelevantProject($keywords);
		}
		$this->view->projectArray	 = $projectArray;
		$this->view->keywords		 = $keywords;
		$this->display();
	}

	/**
	 * function：服务工时单
	 * param:科室 年份 月份 季度 是否打印
	 * return:无
	 */
	public function service($dept = '', $year = '', $month = '', $season = '', $classdept = 0) {
		if (!empty($_POST)) {
			$dept		 = $this->post->dept;
			$year		 = $this->post->year;
			$month		 = $this->post->month;
			$season		 = $this->post->season;
			$classdept	 = $this->post->classdept;
			die(js::locate($this->createLink('kevinhours', 'service'
						, "dept=$dept&year=$year&month=$month&season=$season&classdept=$classdept"), 'parent'));
		}
		//初次进入设置默认值
		if ('' == $dept && '' == $year) {
			$dept	 = $this->kevincom->getDeptIdByAccount($this->app->user->account);
			$year	 = date('Y', strtotime('last month'));
			$month	 = date('m', strtotime('last month'));
		}
		$this->kevinhours->InitialDispachServiceClass($month, $season,$classdept);
		$this->kevinhours->getDispatchAccountsByClassDept($dept, $classdept);
		//获得详细列表
		$this->view->detailCashCodeArray = $this->kevinhours->getDetailCashCodeHours($dept, $year, $month, $season,$classdept);

		//构造科室下拉框选项和年份选项
		$this->view->deptArray	 = $this->kevincom->getDeptOptionMenu();
		$this->view->yearList	 = $this->kevinhours->getYearList(); //默认10年,参数为显示年数
		//当前选项索引
		$this->view->deptID		 = $dept;
		$this->view->year		 = $year;
		$this->view->month		 = $month;
		$this->view->season		 = $season;
		$this->view->classdept	 = $classdept;

		//网页标题
		$this->view->title		 = $this->lang->kevinhours->common . $this->lang->colon . $this->lang->kevinhours->service;
		//footer中显示当前位置
		$this->view->position[]	 = $this->lang->kevinhours->service;
		$this->display();
	}

	/**
	 * My todos. 
	 * 
	 * @param  string $type 
	 * @param  string $account 
	 * @param  string $status 
	 * @param  int    $recTotal 
	 * @param  int    $recPerPage 
	 * @param  int    $pageID 
	 * @access public
	 * @return void
	 */
	public function todo($type = 'thisMonth', $account = '',$deptID='',$status = 'all', $orderBy = "date_desc,status,begin", $recTotal = 0, $recPerPage = 20, $pageID = 1) {
		$this->kevinhours->getShowAccount($account);
		$account = $this->kevinhours->account;
		if($deptID=='')$deptID = $this->kevinhours->accountdept;
		/* Save session. */
		$uri = $this->app->getURI(true);
		$this->session->set('todoList', $uri);
		$this->session->set('bugList', $uri);
		$this->session->set('taskList', $uri);

		/* Load pager. */
		$this->app->loadClass('pager', $static		 = true);
		if ($this->app->getViewType() == 'mhtml')
			$recPerPage	 = 10;
		$pager		 = pager::init($recTotal, $recPerPage, $pageID);

		/* The title and position. */
		$this->view->title		 = $this->lang->kevinhours->common . $this->lang->colon . $this->lang->kevinhours->todo;
		$this->view->position[]	 = $this->lang->kevinhours->todo;

		/* Append id for secend sort. */
		$sort = $this->loadModel('common')->appendOrder($orderBy);

		$this->commonWebParam($type);
		/* Assign. */
		$this->view->todos			 = $this->kevinhours->getList($type, "", $status, 0, $pager, $sort);
		$this->view->date			 = (int) $type == 0 ? date(DT_DATE1) : date(DT_DATE1, strtotime($type));
		$this->view->type			 = $type;
		$this->view->recTotal		 = $recTotal;
		$this->view->recPerPage		 = $recPerPage;
		$this->view->pageID			 = $pageID;
		$this->view->status			 = $status;
		$this->view->account		 = $this->kevinhours->account;
		$this->view->orderBy		 = $orderBy == 'date_desc,status,begin,id_desc' ? '' : $orderBy;
		$this->view->pager			 = $pager;
		$this->view->importFuture	 = ($type != 'today');
		$this->view->overtreetitle=$this->kevinhours->overtreetitle($type,$account,$deptID,'todo',array('kevinhoursModel','createTodoLink'),array($this->kevinhours->account,$this->kevinhours->realname,"$status&$orderBy&$recTotal&$recPerPage&$pageID"));

		$this->display();
	}

	public function updateCashStat($dept = '', $year = '', $month = '', $season = '') {
		$this->kevinhours->updateCashStat($dept, $year, $month, $season);
		if (dao::isError())
			die(js::error(dao::getError()));
		die(js::locate($this->createLink('kevinhours', 'service'
					, "dept=$dept&year=$year&month=$month&season=$season"), 'parent.parent'));
	}

	public function userbatchedit($deptID = 0) {
		if (isset($_POST['users'])) {
			$this->view->users = $this->dao->select('*')->from(TABLE_USER)->where('account')->in($this->post->users)->orderBy('id')->fetchAll('id');
		} elseif ($_POST) {
			if ($this->post->account)
				$this->kevinhours->userbatchedit();
			die(js::locate($this->createLink('kevinhours', 'browse', "deptID=$deptID"), 'parent'));
		}
		$this->lang->set('menugroup.user', 'company');
		$this->lang->user->menu		 = $this->lang->company->menu;
		$this->lang->user->menuOrder = $this->lang->company->menuOrder;

		$this->view->title		 = $this->lang->company->common . $this->lang->colon . $this->lang->kevinhours->userbatchedit;
		$this->view->position[]	 = $this->lang->kevinhours->browse;
		$this->view->position[]	 = $this->lang->kevinhours->userbatchedit;
		$this->view->depts		 = $this->loadModel('dept')->getOptionMenu();
		$this->display();
	}

	/**
	 * View a todo. 
	 * 
	 * @param  int    $todoID 
	 * @param  string $from     my|company
	 * @access public
	 * @return void
	 */
	public function view($todoID, $from = 'company') {
		$todo = $this->kevinhours->getTodoById($todoID, true);
		if (!$todo)
			die(js::error($this->lang->notFound) . js::locate('back'));

		/* Save the session. */
		$this->session->set('taskList', $this->app->getURI(true));
		$this->session->set('bugList', $this->app->getURI(true));

		/* Set menus. */
		$this->loadModel('user')->setMenu($this->user->getPairs(), $todo->account);
		$this->lang->company->menu->browseUser['subModule'] = 'todo';
		$this->lang->set('menugroup.todo', $from);

		$this->view->title		 = "{$this->lang->kevinhours->common} #$todo->id $todo->name";
		$this->view->position[]	 = $this->lang->kevinhours->view;
		$this->view->todo		 = $todo;
		$this->view->times		 = date::buildTimeList($this->config->kevinhours->times->begin, $this->config->kevinhours->times->end, $this->config->kevinhours->times->delta);
		$this->view->users		 = $this->user->getPairs('noletter');
		$this->view->actions	 = $this->loadModel('action')->getList('todo', $todoID);
		$this->view->from		 = $from;

		$this->display();
	}

}
