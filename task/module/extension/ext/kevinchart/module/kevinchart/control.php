<?php

/**
 * The control file of kevinchart module
 *
 * @copyright   Kevin
 * @author      Kevin<3301647@qq.com>
 * @package     kevinchart
 */
class kevinchart extends control {

	/**
	 * Construct function.
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		$this->app->loadClass('date');
		$this->loadModel('kevincom');
	}

	public function commonWebParam(& $period) {
		if (!$period) {
			$period = date('today');
		}

		$currentMonth = date('Y-m') . '-01';
		if (is_numeric($period) && 6 == strlen($period)) {
			$currentMonth	 = $period . '01';
			$currentMonth	 = date('Y-m', strtotime($currentMonth)) . '-01';
		}
		if (is_numeric($period) && strlen($period) == 8) {
			$this->view->currentDate = date('Y-m-d', strtotime($period));
		} else
			$this->view->currentDate = date('Y-m-d');

		$this->view->lastMonth	 = date('Ym', strtotime("$currentMonth -1 month"));
		$this->view->nextMonth	 = date('Ym', strtotime("$currentMonth +1 month"));
		$this->view->thisMonth	 = date('Ym');
		$this->view->period		 = $period;

		$this->view->methodName		 = $this->app->getMethodName();
		$this->view->yearList		 = $this->kevincom->getYearList();
		$this->view->monthList		 = $this->kevincom->getMonthList();
		$this->view->currentYear	 = date('Y', strtotime("$currentMonth"));
		$this->view->currentMonth	 = date('m', strtotime("$currentMonth"));
	}

	/**
	 * view a line for app of app.
	 * 
	 * @param  int $userhost   the int userhost userhost
	 * @param  string $period   period for app
	 * @access public
	 * @return void
	 */
	public function mychart($period = 'all') {
		//period treat
		$this->commonWebParam($period);
		extract(kevin::getBeginEndTime($period));

		$this->view->charData = null;
		if ($this->kevinchart->myItemGetPoints($begin, $end, 'mychart')) {
			$this->view->chartData = & $this->kevinchart->chartData; //get ref
		}
		$this->view->baseLinkVars	 = "period=$period";

		$thisLang				 = & $this->lang->kevinchart;
		$this->view->title		 = $thisLang->common . $this->lang->colon . $thisLang->mychart;
		$this->view->position[]	 = html::a($this->createLink($this->moduleName, 'index'), $thisLang->common);
		$this->view->position[]	 = $thisLang->mychart;
		$this->display();
	}

	/**
	 * Browse itemlist and dailyItems.
	 * 
	 * @param  string $period 
	 * @param  string $orderBy 
	 * @param  int    $recTotal 
	 * @param  int    $recPerPage 
	 * @param  int    $pageID 
	 * @access public
	 * @return void
	 */
	public function itemlist( $period = 'all', $orderBy = 'start', $recTotal = 0, $recPerPage = 50, $pageID = 1) {
		//period treat
		$this->commonWebParam($period);
		extract(kevin::getBeginEndTime($period));
	
		/* Set the pager. */
		$this->app->loadClass('pager', $static	 = true);
		$pager	 = pager::init($recTotal, $recPerPage, $pageID);

		/* Append id for secend sort. */
		$sort = $this->kevincom->appendOrder($orderBy);

		$this->view->myItems		 = $this->kevinchart->myItemsGetList($begin, $end, $pager, $sort);
		$this->view->baseLinkVars	 = "period=$period";
		$this->view->pager			 = $pager;
		$this->view->orderBy		 = $orderBy;

		$thisLang				 = & $this->lang->kevinchart;
		$this->view->title		 = $thisLang->common . $this->lang->colon . $thisLang->itemlist;
		$this->view->position[]	 = html::a($this->createLink($this->moduleName, 'index'), $thisLang->common);
		$this->view->position[]	 = $thisLang->itemlist;

		$this->display();
	}

	/**
	 * Browse index and myItems.
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
	public function index( $period = 'all', $orderBy = 'start_desc', $recTotal = 0, $recPerPage = 50, $pageID = 1) {
		//$this->commonWebParam($period);
		//extract(kevin::getBeginEndTime($period));
		/* Set the pager. */
		$this->app->loadClass('pager', $static	 = true);
		$pager	 = pager::init($recTotal, $recPerPage, $pageID);

		/* Append id for secend sort. */
		$sort = $this->kevincom->appendOrder($orderBy);

		$this->view->baseLinkVars	 = "period=$period";
		$this->view->orderBy		 = $orderBy;
		$this->view->pager			 = $pager;

		$this->view->title		 = $this->lang->kevinchart->common . $this->lang->colon . $this->lang->kevinchart->index;
		$this->view->position[]	 = $this->lang->kevinchart->common;
		$this->display();
	}

	/**
	 * view a app.
	 * 
	 * @param  int $id   the int id
	 * @access public
	 * @return void
	 */
	public function view($id) {
		if (!$id) {
			die(js::error("Input app ID wrong, must a right number!"));
		}

		$this->view->myItem = $this->kevinchart->myItemGetById($id);
		if (!$id) {
			die(js::error("Can not find the app by this id = " . $id));
		}
		$thisLang				 = & $this->lang->kevinchart;
		$this->view->title		 = $thisLang->common . $this->lang->colon . $thisLang->view;
		$this->view->position[]	 = html::a($this->createLink($this->moduleName, 'index'), $thisLang->common);
		$this->view->position[]	 = $thisLang->view;

		$this->display();
	}

}
