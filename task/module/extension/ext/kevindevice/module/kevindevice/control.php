<?php

/**
 * The control file of kevindevice module
 *
 * @copyright   Kevin
 * @author      kevin<3301647@qq.com>
 * @package     kevindevice
 */
class kevindevice extends control {

	/**
	 * Construct function.
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		$this->app->loadClass('date');
		$this->loadModel('dept');
		$this->loadModel('kevincom');
	}

	/**
	 * Copy a kevindevice.
	 * 
	 * @param  int    $groupID 
	 * @access public
	 * @return void
	 */
	public function groupcopy($groupID) {
		if (!empty($_POST)) {
			$this->kevindevice->groupcopy($groupID);
			if (dao::isError()) die(js::error(dao::getError()));
			if (isonlybody()) die(js::closeModal('parent.parent', 'this'));
			die(js::locate($this->createLink('kevindevice', 'browse'), 'parent'));
		}

		$this->view->title		 = $this->lang->kevindevice->common . $this->lang->colon . $this->lang->kevindevice->groupcopy;
		$this->view->position[]	 = $this->lang->kevindevice->groupcopy;
		$this->view->group		 = $this->kevindevice->groupGetById($groupID);
		$this->view->subMenu	 = "grouplist";
		$this->display();
	}

	/**
	 * Create a kevindevice.
	 * 
	 * @access public
	 * @return void
	 */
	public function groupcreate() {
		if (!empty($_POST)) {
			$this->kevindevice->groupcreate();
			if (dao::isError()) die(js::error(dao::getError()));
			if (isonlybody()) die(js::closeModal('parent.parent', 'this'));
			die(js::locate($this->createLink('kevindevice', 'grouplist'), 'parent'));
		}

		$this->view->title		 = $this->lang->kevindevice->title . $this->lang->colon . $this->lang->kevindevice->groupcreate;
		$this->view->position[]	 = $this->lang->kevindevice->groupcreate;
		$this->view->subMenu	 = "grouplist";
		$this->display();
	}

	/**
	 * Delete a kevindevice.
	 * 
	 * @param  int    $groupID 
	 * @param  string $confirm  yes|no
	 * @access public
	 * @return void
	 */
	public function groupdelete($groupID, $confirm = 'no') {
		if ($confirm == 'no') {
			die(js::confirm($this->lang->kevindevice->confirmDelete . $this->lang->kevindevice->group . "?", $this->createLink('kevindevice', 'groupdelete', "groupID=$groupID&confirm=yes"))
					. js::closeModal('parent.parent', 'this'));
		} else {
			$this->kevindevice->groupdelete($groupID);
			if (dao::isError()) die(js::error(dao::getError()));
			if (isonlybody()) die(js::closeModal('parent.parent', 'this'));
			die(js::locate($this->createLink('kevindevice', 'grouplist'), 'parent'));
		}
	}

	/**
	 * Edit a kevindevice.
	 * 
	 * @param  int    $groupID 
	 * @access public
	 * @return void
	 */
	public function groupedit($groupID) {
		if (!empty($_POST)) {
			$this->kevindevice->groupUpdate($groupID);
			if (isonlybody()) die(js::closeModal('parent.parent', 'this'));
			die(js::locate($this->createLink('kevindevice', 'browse'), 'parent'));
		}

		$title					 = $this->lang->kevindevice->common . $this->lang->colon . $this->lang->kevindevice->groupedit;
		$position[]				 = $this->lang->kevindevice->groupedit;
		$this->view->title		 = $title;
		$this->view->position	 = $position;
		$this->view->group		 = $this->kevindevice->groupGetById($groupID);
		$this->view->subMenu	 = "grouplist";

		$this->display();
	}

	/**
	 * Browse groups.
	 * 
	 * @param  int    $companyID 
	 * @access public
	 * @return void
	 */
	public function grouplist() {

		$title		 = $this->lang->kevindevice->common . $this->lang->colon . $this->lang->kevindevice->grouplist;
		$position[]	 = $this->lang->kevindevice->grouplist;

		$groups					 = $this->kevindevice->groupGetList();
		$groupUsers				 = array();
		foreach ($groups as $group)
			$groupUsers[$group->id]	 = $this->kevindevice->devGetPairs($group->id);

		$this->view->title		 = $title;
		$this->view->position	 = $position;
		$this->view->groups		 = $groups;
		$this->view->groupUsers	 = $groupUsers;
		$this->view->subMenu	 = "grouplist";

		$this->display();
	}

	/**
	 * view a group.
	 * 
	 * @param  int $groupID   the int group id
	 * @access public
	 * @return void
	 */
	public function groupview($groupID) {
		if (!$groupID) {
			die(js::error("Input group ID wrong, must a right number!"));
		}
		$this->view->group = $this->kevindevice->groupGetById($groupID);

		if (!$this->view->group) {
			die(js::error("Can not find the group by this id = " . $groupID));
		}

		$title					 = $this->lang->kevindevice->common . $this->lang->colon . $this->lang->kevindevice->groupedit;
		$position[]				 = $this->lang->kevindevice->grouplist;
		$this->view->title		 = $title;
		$this->view->devices	 = $this->kevindevice->devGetByGroup($groupID);
		$this->view->position	 = $position;

		$this->view->subMenu = "grouplist";
		$this->display();
	}

	/**
	 * Create a suer.
	 * 
	 * @param  int    $deptID 
	 * @access public
	 * @return void
	 */
	public function devcreate($deptID = 0) {
		if (!empty($_POST)) {
			$this->kevindevice->devcreate();

			if (dao::isError()) die(js::error(dao::getError()));
			if (isonlybody()) die(js::closeModal('parent.parent', 'this'));
			die(js::locate($this->createLink('kevindevice', 'devlist'), 'parent'));
		}
		$groups		 = $this->dao->select('id, name, type')->from(TABLE_KEVINDEVICE_GROUP)->fetchAll();
		$groupList	 = array('' => '');
		$typeGroup	 = array();
		foreach ($groups as $group) {
			$groupList[$group->id]	 = $group->name;
			if ($group->type) $typeGroup[$group->type] = $group->id;
		}

		$title					 = $this->lang->kevindevice->common . $this->lang->colon . $this->lang->kevindevice->devlist;
		$position[]				 = $this->lang->kevindevice->common;
		$position[]				 = $this->lang->kevindevice->devlist;
		$this->view->title		 = $title;
		$this->view->position	 = $position;

		$this->view->depts		 = $this->dept->getOptionMenu();
		$this->view->groupList	 = $groupList;
		$this->view->typeGroup	 = $typeGroup;
		$this->view->deptID		 = $deptID;
		$this->view->subMenu	 = "devlist";

		$this->display();
	}

	/**
	 * Delete a device.
	 * 
	 * @param  int    $deviceID 
	 * @param  string $confirm  yes|no
	 * @access public
	 * @return void
	 */
	public function devdelete($deviceID, $confirm = 'no') {
		if (!$deviceID) return;
		if ($confirm == 'no') {
			die(js::confirm($this->lang->kevindevice->confirmDelete . $this->lang->kevindevice->dev
							, $this->createLink('kevindevice', 'devdelete', "userID=$deviceID&confirm=yes"))
					. js::closeModal('parent.parent', 'this'));
		} else {
			$this->kevindevice->devdelete($deviceID);
			if (dao::isError()) die(js::error(dao::getError()));
			if (isonlybody()) die(js::closeModal('parent.parent', 'this'));
			die(js::locate($this->createLink('kevindevice', 'devlist'), 'parent'));
		}
	}

	/**
	 * Edit a device.
	 * 
	 * @param  int $deviceID   the int device id
	 * @access public
	 * @return void
	 */
	public function devedit($deviceID) {
		if (!empty($_POST)) {
			$this->kevindevice->devupdate($deviceID);
			if (dao::isError()) die(js::error(dao::getError()));
			if (isonlybody()) die(js::closeModal('parent.parent', 'this'));
			die(js::locate($this->createLink('kevindevice', 'devlist'), 'parent'));
		}
		$this->loadModel("user");
		$device				 = $this->kevindevice->devGetById($deviceID);
		if(!$device) die(js::error("Can not Get Device with id = " . $deviceID));
		
		$this->view->users	 = $this->user->getPairs('nodeleted');
		$users = &$this->view->users;
		if(!array_key_exists($device->user,$users)) $users[$device->user]=$device->user;
		if(!array_key_exists($device->charge,$users)) $users[$device->charge]=$device->user;

		$deviceGroups		 = $this->kevindevice->groupGetByDevice($device->id);

		$title						 = $this->lang->kevindevice->common . $this->lang->colon . $this->lang->kevindevice->devedit;
		$position[]					 = $this->lang->kevindevice->devlist;
		$this->view->title			 = $title;
		$this->view->position		 = $position;
		$this->view->device			 = $device;
		$this->view->depts			 = $this->dept->getOptionMenu();
		$this->view->deviceGroups	 = implode(',', array_keys($deviceGroups));
		$this->view->groups			 = $this->kevindevice->groupGetPairs();
		$this->view->subMenu		 = "devlist";

		$this->display();
	}

	/**
	 * Browse group and devices.
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
	public function devlist($param = 0, $type = 'bygroup', $orderBy = 'id', $recTotal = 0, $recPerPage = 20, $pageID = 1) {
		$groupID = $type == 'bygroup' ? (int) $param : 0;

		/* Set the pager. */
		$this->app->loadClass('pager', $static	 = true);
		$pager	 = pager::init($recTotal, $recPerPage, $pageID);

		$sort = $this->kevincom->appendOrder($orderBy);

		$kevin_showStyle = 1; //默认简单
		//if post ,delete filter key word
		if (!empty($_POST)) {
			$kevin_showStyle = $this->post->showStyle;
			if ($kevin_showStyle) {
				$this->session->set("kevin_showStyle", $kevin_showStyle); //save sesison
			}
		} else {
			if (isset($_SESSION['kevin_showStyle'])) {
				$kevin_showStyle = $_SESSION['kevin_showStyle']; //获得session中保存的筛选关键词
			}
		}

		$this->loadModel("user");
		$this->view->users		 = $this->user->getPairs();
		$this->view->showStyle	 = $kevin_showStyle;

		//$userQuery = ;
		$devices				 = $this->kevindevice->devGetByQuery($groupID, $pager, $sort);
		$this->view->targetgroup = ($groupID) ? $this->kevindevice->groupGetById($groupID) : 0;

		$groups					 = $this->kevindevice->groupGetList();
		$this->view->title		 = $this->lang->kevindevice->common . $this->lang->colon . $this->lang->kevindevice->devlist;
		$this->view->position[]	 = $this->lang->kevindevice->common;
		$this->view->devices	 = $devices;
		$this->view->orderBy	 = $orderBy;
		$this->view->groupID	 = $groupID;
		$this->view->pager		 = $pager;
		$this->view->param		 = $param;
		$this->view->groups		 = $groups;
		$this->view->type		 = $type;
		$this->view->subMenu	 = "devlist";

		$this->display();
	}

	/**
	 * view a device.
	 * 
	 * @param  int $deviceID   the int device id
	 * @access public
	 * @return void
	 */
	public function devview($deviceID) {
		if (!$deviceID)die(js::error("Input device ID wrong, must a right number!"));

		$device = $this->kevindevice->devGetById($deviceID);
		if (!$device)die(js::error("Can not find the device with id = " . $deviceID));

		$deviceGroups = $this->kevindevice->groupGetByDevice($device->id);

		$dept = $this->dept->getByID($device->dept);

		$this->loadModel("user");
		$users = $this->user->getPairs('nodeleted');
		if(!array_key_exists($device->user,$users)) $users[$device->user]=$device->user;
		if(!array_key_exists($device->charge,$users)) $users[$device->charge]=$device->user;
		
		if ($device) {
			$device->userName	 = $users[$device->user];
			$device->chargeName	 = $users[$device->charge];
		} else {
			$device->chargeName	 = "";
			$device->userName	 = "";
		}

		$title						 = $this->lang->kevindevice->common . $this->lang->colon . $this->lang->kevindevice->devview;
		$position[]					 = $this->lang->kevindevice->devlist;
		$this->view->title			 = $title;
		$this->view->position		 = $position;
		$this->view->device			 = $device;
		$this->view->deptName		 = (!$dept) ? "" : $dept->name;
		$this->view->deviceGroups	 = $deviceGroups;
		$this->view->subMenu		 = "devlist";


		$this->display();
	}

	/**
	 * Statistic kevindevices. 
	 * @param  string    $date 
	 * @param  string    $type 
	 * @access public
	 * @return void
	 */
	public function statistic($type = 'group') {
		//网页位置
		$this->view->title		 = $this->lang->kevindevice->common . $this->lang->colon . $this->lang->kevindevice->statistic;
		$this->view->position[]	 = $this->lang->kevindevice->statistic;

		//绘制图表
		if ('group' == $type) {
			$this->view->items = $this->kevindevice->statisticByGroup();
		}else if('dept' == $type) {
			$this->view->items = $this->kevindevice->statisticByDept();
		}else if('type' == $type) {
			$this->view->items = $this->kevindevice->statisticByType();
		}
		else {
			$this->view->ErrorMsg = 'input type is wrong. no suche type of "' . $type . '"';
		}
		//页面参数
		$this->view->statisticType = $type;
		$this->view->subMenu	 = "statistic";
		$this->display();
	}

}
