<?php

class kevincalendar extends control {

	public function __construct() {
		parent::__construct();
		$this->loadModel('my')->setMenu();
		$this->app->loadClass('kevin'); //加载kevin类
		$this->app->loadClass('date');
	}

	public function batchcreate() {
		if (!empty($_POST)) {
			$this->kevincalendar->batchcreate();
			if (dao::isError()) {
				die(js::error(dao::getError()));
			}
			if (isonlybody()) {
				die(js::reload('parent.parent'));
			}
			die(js::locate($this->createLink('kevincalendar', 'index', ""), 'parent'));
		}
		$this->view->title		 = $this->lang->kevincalendar->common . $this->lang->colon . $this->lang->kevincalendar->batchcreate;
		$this->view->position[]	 = $this->lang->kevincalendar->batchcreate;
		$this->view->controlType	 = 'batchcreate';
		$this->display();
	}

	public function commonWebParam($type) {
		if ('' == $type && !is_numeric($type)) {
			$type = date('Ym');
		}

		$currentMonth = date('Y-m') . '-01';
		if (is_numeric($type) && 6 == strlen($type)) {
			$currentMonth	 = $type . '01';
			$currentMonth	 = date('Y-m', strtotime($currentMonth)) . '-01';
		}

		$this->view->lastMonth	 = date('Ym', strtotime("$currentMonth -1 month"));
		$this->view->nextMonth	 = date('Ym', strtotime("$currentMonth +1 month"));
		$this->view->thisMonth	 = date('Ym');

		$this->view->methodName		 = $this->app->getMethodName();
		$this->view->yearList		 = $this->kevincalendar->getYearList();
		$this->view->monthList		 = $this->lang->kevincalendar->month;
		$this->view->currentYear	 = date('Y', strtotime("$currentMonth"));
		$this->view->currentMonth	 = date('m', strtotime("$currentMonth"));
	}

	public function create($date = '') {
		if (!empty($_POST)) {
			$this->kevincalendar->create();
			if (dao::isError())
				die(js::error(dao::getError()));
			if (isonlybody())
				die(js::reload('parent.parent'));
			die(js::locate($this->createLink('kevincalendar', 'index', ""), 'parent.parent'));
		}
		$this->view->title		 = $this->lang->kevincalendar->common . $this->lang->colon . $this->lang->kevincalendar->create;
		$this->view->position[]	 = $this->lang->kevincalendar->create;
		$this->view->date		 = ('' == $date) ? date('Y-m-d') : (date('Y-m-d', strtotime($date)));
		$this->view->controlType	 = 'create';
		$this->display();
	}

	public function edit($kevincalendarID) {
		if (!empty($_POST)) {
			$this->kevincalendar->update($kevincalendarID);
			if (dao::isError())
				die(js::error(dao::getError()));
			die(js::closeModal('parent.parent'));
		}
		$this->view->title		 = $this->lang->kevincalendar->common . $this->lang->colon . $this->lang->kevincalendar->edit;
		$this->view->position[]	 = $this->lang->kevincalendar->edit;

		$this->view->kevincalendar = $this->kevincalendar->getById($kevincalendarID);
		$this->display();
	}

	/**
	 * calendar.
	 *      
	 * @access public
	 * @return void
	 */
	public function index($type = '') {
		if ('' == $type) {
			$type = date('Ym');
		}
		//$this->loadModel('my')->setMenu();
		/* The title and position. */
		$this->view->title		 = $this->lang->kevincalendar->common . $this->lang->colon . $this->lang->kevincalendar->index;
		$this->view->position[]	 = $this->lang->kevincalendar->index;

		$this->commonWebParam($type);

		extract(kevin::getBeginEndTime($type));
		$this->view->kevincalendars	 = $this->kevincalendar->getList($begin, $end);
		$this->view->date			 = (int) $type == 0 ? date(DT_DATE1) : date(DT_DATE1, strtotime($type));
		$this->view->type			 = $type;
		$this->view->account		 = $this->app->user->account;
		$this->view->controlType	 = 'index';

		$this->display();
	}

	/**
	 * index.
	 *      
	 * @access public
	 * @return void
	 */
	public function lists($type = 'thisMonth') {
		$this->loadModel('my')->setMenu();
		/* The title and position. */
		$this->view->title		 = $this->lang->kevincalendar->common . $this->lang->colon . $this->lang->kevincalendar->index;
		$this->view->position[]	 = $this->lang->kevincalendar->index;
		$this->commonWebParam($type);
		extract(kevin::getBeginEndTime($type));
		$this->view->kevincalendars	 = $this->kevincalendar->getList($begin, $end);
		$this->view->date			 = (int) $type == 0 ? date(DT_DATE1) : date(DT_DATE1, strtotime($type));
		$this->view->type			 = $type;
		$this->view->account		 = $this->app->user->account;
		$this->view->controlType	 = 'lists';
		$this->display();
	}

	public function log($type = '', $logType = 'php') {
		$date =(6 == strlen($type))?$type.'01':$type;

		if ('' == $date || !is_numeric($date) || 8 != strlen($date)) {
			$date = date('Ymd');
		}
		$type = substr($date, 0, 6);
		$this->commonWebParam($type);

		$dateOfOpenFile	 = date('Ymd', strtotime($date));
		$path			 = dirname(dirname(dirname(__FILE__))) . '/tmp/log/';

		if('sql' === $logType){
			$filePath = $path . 'sql.' . $dateOfOpenFile . '.log.php';
		}else{
			$filePath = $path . 'php.' . $dateOfOpenFile . '.log.php';
		}
		
		if (file_exists($filePath)) {
			$contents = file_get_contents($filePath);
		} else {
			$contents = '该天无错误日志文件!';
		}
		$this->view->currentDate = date('Y-m-d', strtotime($date));
		$this->view->date		 = $date;
		$this->view->contents	 = $contents;
		$this->view->type		 = $type;
		$this->view->account	 = $this->app->user->account;
		$this->view->controlType = 'log';

		$this->view->title		 = $this->lang->kevincalendar->common . $this->lang->colon . $this->lang->kevincalendar->log;
		$this->view->position[]	 = $this->lang->kevincalendar->log;
		$this->display();
	}

	public function logdelete($type) {
		$path = dirname(dirname(dirname(__FILE__))) . '/tmp/log/';
		if ('' != $type && is_numeric($type) && 8 === strlen($type)) {
			$filePath = $path . 'php.' . $type . '.log.php';
			if (file_exists($filePath)) {
				$result = @unlink($filePath); //删除日志
				if (!$result) {
					$this->dao->logError('ErrorDeleteLog', '', '');
					echo "<script>alert('" . dao::getError(true) . "');</script>";
				}
			}
		} else if ('all' === $type && common::hasPriv('kevincalendar', 'logdeleteall')) {
			$filesnames = scandir($path); //获取也就是扫描文件夹内的文件及文件夹名存入数组 $filesnames
			foreach ($filesnames as $currentFileName) {
				if ('php.' === substr($currentFileName, 0, 4) && '.log.php' === substr($currentFileName, -8)) {
					$result = @unlink($path . $currentFileName); //删除日志
					if (!$result) {
						$this->dao->logError('ErrorDeleteLog', '', '');
						echo "<script>alert('" . dao::getError(true) . "');</script>";
					}
				}
			}
		} else if ('allsql' === $type && common::hasPriv('kevincalendar', 'logdeletesql')) {
			$filesnames = scandir($path); //获取也就是扫描文件夹内的文件及文件夹名存入数组 $filesnames
			foreach ($filesnames as $currentFileName) {
				if ('sql.' === substr($currentFileName, 0, 4) && '.log.php' === substr($currentFileName, -8)) {
					$result = @unlink($path . $currentFileName); //删除日志
					if (!$result) {
						$this->dao->logError('ErrorDeleteLog', '', '');
						echo "<script>alert('" . dao::getError(true) . "');</script>";
					}
				}
			}
		}
		if (isonlybody()) {
			die(js::reload('parent.parent'));
		}
		die(js::locate($this->createLink('kevincalendar', 'log', ""), 'parent'));
	}

	public function todo($type = '') {
		if ('' == $type) {
			$type = date('Ym');
		}

		/* The title and position. */
		$this->view->title		 = $this->lang->kevincalendar->common . $this->lang->colon . $this->lang->kevincalendar->todo;
		$this->view->position[]	 = $this->lang->kevincalendar->todo;

		$this->commonWebParam($type);

		extract(kevin::getBeginEndTime($type));
		$this->view->kevincalendars	 = $this->kevincalendar->getList($begin, $end);
		$this->view->date			 = (int) $type == 0 ? date(DT_DATE1) : date(DT_DATE1, strtotime($type));
		$this->view->type			 = $type;
		$this->view->account		 = $this->app->user->account;
		$this->view->controlType	 = 'todo';
		
		$this->display();
	}
}
