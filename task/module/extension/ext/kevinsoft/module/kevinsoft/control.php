<?php

/**
 * The control file
 *
 * @copyright   Kevin
 * @charge: free
 * @license: ZPL (http://zpl.pub/v1)
 * @author      Kevin <3301647@qq.com>
 * @package     kevinsoft
 * @link        http://www.zentao.net
 */
?>
<?php

class kevinsoft extends control {

	public function __construct() {
		parent::__construct();
		$this->loadModel('doc');
		$this->loadModel('action');
		$this->loadModel('kevincom');
	}

	/**
	 * ajax Get Soft list.
	 * 
	 * @param  string|date 
	 * @param  string      
	 * @access public
	 * @return void
	 */
	public function ajaxGetSoftlist() {
		$items = $this->dao->select("*")->from(TABLE_KEVIN_SOFT_LIST)
				->where('deleted')->eq('0')
				->orderBy('id')
				->fetchAll();
		die(json_encode($items));
	}

	/**
	 * ajax Get Soft by ID.
	 * 
	 * @param  string|date 
	 * @param  string      
	 * @access public
	 * @return void
	 */
	public function ajaxGetSoft($id) {
		$Soft = $this->kevinsoft->softGetByID($id, true);
		if(!Soft)die(json_encode($Soft));
		$Soft->sublist = array();
		$kk;
		$kk->id = "1";
		$kk->name = "kevin";
		$kk->Value = "kevin1";
		
		$Soft->sublist[0] = $kk;
		
		$kk->id = "2";
		$Soft->sublist[2] =  $kk;
		$kk->id = "2";
		$Soft->sublist[3] =  $kk;
		die(json_encode($Soft));
	}
	/**
	 * Create a schedual.
	 * 
	 * @param  string|date 
	 * @param  string      
	 * @access public
	 * @return void
	 */
	public function groupversioncreate($group) {
		if (!empty($_POST)) {
			$id = $this->kevinsoft->groupversioncreate($group);
			if (dao::isError() || !$id) die(js::error(dao::getError()));
			if (isonlybody()) die(js::reload('parent.parent'));
			die(js::locate($this->createLink('kevinsoft', 'groupversionlist'), 'parent'));
		}

		$this->view->subMenu	 = "groupversionlist";
		$this->view->groupitem	 = $this->kevinsoft->softGetByID($group);
		//网页位置
		$this->view->title		 = $this->lang->kevinsoft->common . $this->lang->colon . $this->lang->kevinsoft->groupversioncreate;
		$this->view->position[]	 = $this->lang->kevinsoft->groupversioncreate;

		$this->display();
	}

	/**
	 * Delete a kevinsoft.
	 * 
	 * @param  int    $kevinsoftID 
	 * @param  string $confirm yes|no
	 * @access public
	 * @return void
	 */
	public function groupversiondelete($group, $version, $confirm = 'no') {
		$version = (int) $version;
		if ($version <= 0) {
			die(js::error("ID is wrong! =" . $version));
		}
		if ($confirm == 'no') {
			$message = $this->lang->kevinsoft->confirmDelete . "Kevin > Soft > Group Version, Group = " . $group . ", Version = " . $version;
			die(js::confirm($message, inlink('groupversiondelete', "roup=$group&id=$version&confirm=yes")));
		}
		$item = $this->kevinsoft->groupversionGetByID($group, $version);
		if (!$item) die(js::error("Cannot find your Item!"));

		$rc = $this->dao->delete()->from(TABLE_KEVIN_SOFT_GROUPVERSION)->where('id')->eq($version)->exec();
		if (dao::isError() || !$rc || isonlybody()) die(js::reload('parent.parent'));
		die(js::locate($this->createLink('kevinsoft', 'groupversionlist'), 'parent'));
	}

	/**
	 * groupversionlist.
	 * 
	 * @param  int    $kevinsoftID 
	 * @access public
	 * @return void
	 */
	public function groupversionlist($recTotal = 0, $recPerPage = 30, $pageID = 1) {

		$this->app->loadClass('pager', $static	 = true);
		$pager	 = new pager($recTotal, $recPerPage, $pageID);

		$this->view->softfilePairs		 = $this->kevinsoft->softNameGetPairs();
		$this->view->GroupversionArray	 = $this->kevinsoft->groupversionGetList($pager);
		$this->view->pager				 = $pager;
		//网页位置
		$this->view->title				 = $this->lang->kevinsoft->common . $this->lang->colon . $this->lang->kevinsoft->groupversionlist;
		$this->view->position[]			 = html::a($this->createLink('kevinsoft', 'softlist'), $this->lang->kevinsoft->soft);
		$this->view->position[]			 = $this->lang->kevinsoft->groupversionlist;
		$this->view->subMenu			 = 'groupversionlist';
		$this->view->method				 = 'groupversionlist';
		$this->display();
	}
	
	/**
	 * Filter modules.
	 * @access public
	 * @return void
	 */
	public function moduleFilter($deleted = 0) {
		if (!empty($_POST)) {
			$module						 = $this->post->module;
			$grouptype					 = $this->post->grouptype;
			$type						 = $this->post->type;
			$deleted					 = $this->post->deleted;
			//die(js::alert(var_dump($grouptype)));
			$keywordsArray				= isset($_SESSION['kevinsoft_moduleKeywords']) ? $_SESSION['kevinsoft_moduleKeywords'] : array();
			$keywordsArray['module']	 =($module=='0')?"": $module;
			$keywordsArray['grouptype']	 =($grouptype=='0')?"": $grouptype;
			$keywordsArray['type']		 = ($type=='0')?"":$type;
			$keywordsArray['deleted']	 = $deleted;
			$this->session->set('kevinsoft_moduleKeywords', $keywordsArray); //set
			unset($_GET['onlybody']); //销毁指定的变量,否则一直是onlybody=yes
		}
		die(js::locate(helper::createLink('kevinsoft', 'modulelist'), 'top'));
	}
	
/**
	 * modulelist.
	 * 
	 * @param  int    $id 
	 * @access public
	 * @return void
	 */
	public function modulelist($orderBy = 'id_desc',$recTotal = 0, $recPerPage = 30, $pageID = 1) {
		if (isset($_SESSION['kevinsoft_moduleKeywords'])) {
			$keywordsArray = $_SESSION['kevinsoft_moduleKeywords']; //获得session中保存的筛选关键词
		} else {
			$keywordsArray = array();
		}
		if (!empty($_POST)) {

			$toDeleteKey	 = $this->post->deleteKey;
			$toDeleteValue	 = $this->post->deleteValue;
			if ("module" == $toDeleteKey) {//处理非数据
				$keywordsArray[$toDeleteKey] = "";
			}else if ("grouptype" == $toDeleteKey) {//处理非数据
				$keywordsArray[$toDeleteKey] = "";
			} else if ("type" == $toDeleteKey) {//处理非数据
				$keywordsArray[$toDeleteKey] = "";
			}else if ("deleted" == $toDeleteKey) {//处理非数据
				$keywordsArray[$toDeleteKey] = "";
			} else if (array_key_exists($toDeleteKey, $keywordsArray)) {//处理数组
				$toDeleteArray	 = & $keywordsArray[$toDeleteKey]; //获得要删除的类型数组
				$key			 = array_search($toDeleteValue, $toDeleteArray);
				array_splice($toDeleteArray, $key, 1);
				//$keywordsArray[$toDeleteKey] = $toDeleteArray;
			} else {
				$keywordsArray[$toDeleteKey] = array();
			}
			$this->session->set("kevinsoft_moduleKeywords", $keywordsArray);
			die(js::locate(helper::createLink('kevinsoft', 'modulelist', ""), 'top'));
		}
		$this->session->set("kevinsoft_moduleKeywords", $keywordsArray); 
		if(isset($keywordsArray['deleted'])){
		if ($keywordsArray['deleted'] === false||$keywordsArray['deleted'] === '') {
			$keywordsArray['deleted'] = 0;
		}}
		$module						 = array_key_exists('module', $keywordsArray) ? $keywordsArray['module'] : "";
		$this->view->selectedModule	 = $module;
		$grouptype						 = array_key_exists('grouptype', $keywordsArray) ? $keywordsArray['grouptype'] : "";
		$this->view->selectedGrouptype	 = $grouptype;
		
		$deleted					 = array_key_exists('deleted', $keywordsArray) ? $keywordsArray['deleted'] : 0;
		$this->view->selectedDeleted = $deleted;
		$type						 = array_key_exists('type', $keywordsArray) ? $keywordsArray['type'] : "";
		$this->view->selectedType	 = $type;
		
		$dataList				 = new stdClass();
		$dataList->module		 = $module;
		$dataList->grouptype	= $grouptype;
		$dataList->type			 = $type;
		$dataList->deleted		 = $deleted;
		$moduleMenu=$this->dao->select('distinct module as code,module')->from(TABLE_KEVIN_SOFT_MODULE)->fetchPairs();
		array_unshift($moduleMenu, "");
		$this->app->loadClass('pager', $static = true);
		$pager						 = new pager($recTotal, $recPerPage, $pageID);
		$this->view->module=$module;
		$this->view->grouptype=$grouptype;
		$this->view->type=$type;
		$this->view->deleted=$deleted;
		$this->view->moduleMenu=$moduleMenu;
		
		$this->view->ModuleArray	 = $this->kevinsoft->moduleGetList($dataList, $pager, $orderBy);
		$this->view->pager			 = $pager;
		//网页位置
		$this->view->title			 = $this->lang->kevinsoft->common . $this->lang->colon . $this->lang->kevinsoft->modulelist;
		//$this->view->position[] = html::a($this->createLink('kevinsoft', 'index'), $this->lang->kevincom->index);
		$this->view->position[]		 = $this->lang->kevinsoft->modulelist;
		$this->view->subMenu		 = 'modulelist';
		$this->view->method			 = 'modulelist';
		$this->display();
	}
	
	/**
	 * View a doc.
	 * 
	 * @param  int    $docID 
	 * @access public
	 * @return void
	 */
	public function moduleview($moduleID) {
		$module = $this->kevinsoft->moduleGetByID($moduleID, true);
		if (!$module) die(js::error($this->lang->notFound) . js::locate('back'));

		$deviceInfo = $this->dao->select('a.*,b.name as groupName,c.name as deptName')->from(TABLE_KEVINDEVICE_DEVLIST)->alias('a')
						->leftJoin(TABLE_KEVINDEVICE_GROUP)->alias('b')->on('a.group = b.id')
						->leftJoin(TABLE_DEPT)->alias('c')->on('a.dept = c.id')
						->Where('a.id')->eq($module->device)
						->fetch();
		$softInfo = $this->dao->findById((int) $module->software)->from(TABLE_KEVIN_SOFT_LIST)->fetch();
		$this->loadModel("user");
		$this->view->users		 = $this->user->getPairs();
		$this->view->deviceInfo=$deviceInfo;
		$this->view->softInfo=$softInfo;
		$this->view->module			 = $module;
		$this->view->subMenu		 = 'modulelist';
		$this->view->actions		 = $this->loadModel('action')->getList('kevinsoftmodule', $moduleID);
		$this->view->preAndNext		 = $this->loadModel('common')->getPreAndNextObject('kevinsoftmodule', $moduleID);
		//$this->view->keTableCSS = $this->doc->extractKETableCSS($doc->content);

		$this->view->title		 = $this->lang->kevinsoft->common . $this->lang->colon . $this->lang->kevinsoft->moduleview;
		$this->view->position[]	 = html::a($this->createLink('kevinsoft', 'modulelist'), $this->lang->kevinsoft->modulelist);
		$this->view->position[]	 = $this->lang->kevinsoft->moduleview;

		$this->display();
	}
	
		/**
	 * Create a module.
	 * 
	 * @param  string|date 
	 * @param  string      
	 * @access public
	 * @return void
	 */
	public function modulecreate() {
		if (!empty($_POST)) {
			$moduleID = $this->kevinsoft->modulecreate();
			if (dao::isError()) die(js::error(dao::getError()));
			if (isonlybody()) die(js::reload('parent.parent'));
			die(js::locate($this->createLink('kevinsoft', 'modulelist'), 'parent'));
		}
		$deviceArray=$this->dao->select('name as code,name')->from(TABLE_KEVINDEVICE_DEVLIST)->fetchPairs('code','name');
		array_unshift($deviceArray, "");
		$softArray=$this->dao->select('name as code,name')->from(TABLE_KEVIN_SOFT_LIST)->fetchPairs('code','name');
		array_unshift($softArray, "");
		$this->view->deviceArray=$deviceArray;
		$this->view->softArray=$softArray;
		//网页位置
		$this->view->title		 = $this->lang->kevinsoft->common . $this->lang->colon . $this->lang->kevinsoft->modulecreate;
		$this->view->position[]	 = $this->lang->kevinsoft->modulecreate;

		$this->display();
	}
	
		/**
	 * Edit a kevinsoft module.
	 * 
	 * @param  int    $id 
	 * @access public
	 * @return void
	 */
	public function moduledit($id) {
		if (!empty($_POST)) {
			$changes=$this->kevinsoft->moduleupdate($id);
			if (dao::isError()) die(js::error(dao::getError()));
			
			if (isonlybody()) die(js::reload('parent.parent'));
			die(js::locate($this->createLink('kevinsoft', 'softlist'), 'parent'));
			
		}
		
		$moduleInfo=$this->kevinsoft->moduleGetByID($id);
		$this->view->deviceArray=$this->dao->select('name as code,name')->from(TABLE_KEVINDEVICE_DEVLIST)->fetchPairs('code','name');
		$this->view->softArray=$this->dao->select('name as code,name')->from(TABLE_KEVIN_SOFT_LIST)->fetchPairs('code','name');
		$this->view->subMenu	 = "modulelist";
		$this->view->moduleInfo	 = $moduleInfo;
		//网页位置
		$this->view->title		 = $this->lang->kevinsoft->common . $this->lang->colon . $this->lang->kevinsoft->moduledit;
		$this->view->position[]	 = $this->lang->kevinsoft->moduledit;
		$this->display();
	}
	
	/**
	 * delete a kevinsoft module.
	 * 
	 * @param  int    $id 
	 * @access public
	 * @return void
	 */
	public function moduledelete($id, $confirm = "no") {
		if (!$id || $id <= 0) {
			die(js::error("ID is wrong! =" . $id));
		}
		if ($confirm == 'no') {
			die(js::confirm($this->lang->kevinsoft->confirmDelete . "kevinsoft module id = " . $id, inlink('moduledelete', "id=$id&confirm=yes")));
		}

		$this->dao->update(TABLE_KEVIN_SOFT_MODULE)->set('deleted')->eq('1')->where('id')->eq((int) $id)->exec();

		/* if ajax request, send result. */
		if ($this->server->ajax) {
			if (dao::isError()) {
				$response['result']	 = 'fail';
				$response['message'] = dao::getError();
			} else {
				$response['result']	 = 'success';
				$response['message'] = '';
				$this->action->create('kevinsoft', $id, 'deleted');
			}
			$this->send($response);
		}
		if (isonlybody()) die(js::reload('parent.parent'));
		die(js::locate($this->createLink('kevinsoft', 'modulelist'), 'parent'));
	}

	/**
	 * index.
	 *      
	 * @access public
	 * @return void
	 */
	public function index($orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1) {

		$this->app->loadClass('pager', $static	 = true);
		$pager	 = new pager($recTotal, $recPerPage, $pageID);

		$this->view->SoftArray	 = $this->kevinsoft->softGetList($pager);
		$this->view->pager		 = $pager;
		//网页位置
		$this->view->title		 = $this->lang->kevinsoft->common . $this->lang->colon . $this->lang->kevinsoft->index;
		$this->view->position[]	 = $this->lang->kevinsoft->index;
		$this->view->subMenu	 = 'index';
		$this->display();
	}

//</editor-fold>

	/**
	 * Create a soft.
	 * 
	 * @param  string|date 
	 * @param  string      
	 * @access public
	 * @return void
	 */
	public function softcreate() {
		if (!empty($_POST)) {
			$softID = $this->kevinsoft->softcreate();
			if (dao::isError()) die(js::error(dao::getError()));
			if (isonlybody()) die(js::reload('parent.parent'));
			die(js::locate($this->createLink('kevinsoft', 'softlist'), 'parent'));
		}

		//网页位置
		$this->view->title		 = $this->lang->kevinsoft->common . $this->lang->colon . $this->lang->kevinsoft->softcreate;
		$this->view->position[]	 = $this->lang->kevinsoft->softcreate;

		$this->display();
	}

	/**
	 * delete a kevinsoft.
	 * 
	 * @param  int    $id 
	 * @access public
	 * @return void
	 */
	public function softdelete($id, $confirm = "no") {
		if (!$id || $id <= 0) {
			die(js::error("ID is wrong! =" . $id));
		}
		if ($confirm == 'no') {
			die(js::confirm($this->lang->kevinsoft->confirmDelete . "kevinsoft soft id = " . $id, inlink('softdelete', "id=$id&confirm=yes")));
		}

		$this->dao->update(TABLE_KEVIN_SOFT_LIST)->set('deleted')->eq('1')->where('id')->eq((int) $id)->exec();

		/* if ajax request, send result. */
		if ($this->server->ajax) {
			if (dao::isError()) {
				$response['result']	 = 'fail';
				$response['message'] = dao::getError();
			} else {
				$response['result']	 = 'success';
				$response['message'] = '';
				$this->action->create('kevinsoft', $id, 'deleted');
			}
			$this->send($response);
		}
		if (isonlybody()) die(js::reload('parent.parent'));
		die(js::locate($this->createLink('kevinsoft', 'softlist'), 'parent'));
	}

	/**
	 * Edit a kevinsoft.
	 * 
	 * @param  int    $id 
	 * @access public
	 * @return void
	 */
	public function softedit($id) {
		if (!empty($_POST)) {
			$this->kevinsoft->softupdate($id);
			if (dao::isError()) die(js::error(dao::getError()));
			if (isonlybody()) die(js::reload('parent.parent'));
			die(js::locate($this->createLink('kevinsoft', 'softlist'), 'parent'));
		}
		$this->view->subMenu	 = "softlist";
		$this->view->softInfo	 = $this->kevinsoft->softGetByID($id);
		//网页位置
		$this->view->title		 = $this->lang->kevinsoft->common . $this->lang->colon . $this->lang->kevinsoft->softedit;
		$this->view->position[]	 = $this->lang->kevinsoft->softedit;
		$this->display();
	}

	/**
	 * Filter kevinsofts.
	 * @access public
	 * @return void
	 */
	public function softFilter($deleted = 0) {
		if (!empty($_POST)) {
			$name						 = $this->post->name;
			$deleted					 = $this->post->deleted;
			$keywordsArray['name']		 = $name;
			$keywordsArray['deleted']	 = $deleted;
			$this->session->set('kevinsoft_filterKeywords', $keywordsArray); //set
			unset($_GET['onlybody']); //销毁指定的变量,否则一直是onlybody=yes
		}
		die(js::locate(helper::createLink('kevinsoft', 'softlist'), 'top'));
	}

	/**
	 * softlist.
	 * 
	 * @param  int    $id 
	 * @access public
	 * @return void
	 */
	public function softlist($recTotal = 0, $recPerPage = 30, $pageID = 1) {
		$this->app->loadClass('pager', $static						 = true);
		$pager						 = new pager($recTotal, $recPerPage, $pageID);
		$this->view->keywordsArray	 = $this->session->kevinsoft_filterKeywords;
		if (!$this->view->keywordsArray) $this->view->keywordsArray	 = array();
		$this->view->SoftArray		 = $this->kevinsoft->softGetList($pager);
		$this->view->pager			 = $pager;
		//网页位置
		$this->view->title			 = $this->lang->kevinsoft->common . $this->lang->colon . $this->lang->kevinsoft->softlist;
		//$this->view->position[] = html::a($this->createLink('kevinsoft', 'index'), $this->lang->kevincom->index);
		$this->view->position[]		 = $this->lang->kevinsoft->softlist;
		$this->view->subMenu		 = 'softlist';
		$this->view->method			 = 'softlist';
		$this->display();
	}

	/**
	 * see deleted kevinsofts.
	 * @access public
	 * @return void
	 */
	public function softSeeDeleted() {
		$deleted = 1;
		$this->softFilter($deleted);
	}

	/**
	 * View a doc.
	 * 
	 * @param  int    $docID 
	 * @access public
	 * @return void
	 */
	public function softview($softID) {
		$Soft = $this->kevinsoft->softGetByID($softID, true);
		if (!$Soft) die(js::error($this->lang->notFound) . js::locate('back'));

		$versionList = $this->dao->select("*")->from(TABLE_KEVIN_SOFT_VERSION)
				->where('soft')->eq($softID)
				->fetchAll();

		$this->view->versionList	 = $versionList;
		$this->view->softfilePairs	 = $this->kevinsoft->softNameGetPairs();
		$this->view->SoftIIDList	 = $Soft;
		$this->view->Soft			 = $Soft;
		$this->view->subMenu		 = 'softlist';
		$this->view->actions		 = $this->loadModel('action')->getList('kevinsoft', $softID);
		$this->view->preAndNext		 = $this->loadModel('common')->getPreAndNextObject('kevinsoft', $softID);
		//$this->view->keTableCSS = $this->doc->extractKETableCSS($doc->content);

		$this->view->title		 = $this->lang->kevinsoft->common . $this->lang->colon . $this->lang->kevinsoft->softview;
		$this->view->position[]	 = html::a($this->createLink('kevinsoft', 'softlist'), $this->lang->kevinsoft->softlist);
		$this->view->position[]	 = $this->lang->kevinsoft->softview;

		$this->display();
	}

//<editor-fold defaultstate="collapsed" desc="Version">

	/**
	 * versionlist.
	 * 
	 * @param  int    $kevinsoftID 
	 * @access public
	 * @return void
	 */
	public function versionlist($recTotal = 0, $recPerPage = 30, $pageID = 1) {
		$this->app->loadClass('pager', $static	 = true);
		$pager	 = new pager($recTotal, $recPerPage, $pageID);

		$this->view->VersionArray	 = $this->kevinsoft->versionGetList($pager);
		$this->view->pager			 = $pager;

		//网页位置
		$this->view->title		 = $this->lang->kevinsoft->common . $this->lang->colon . $this->lang->kevinsoft->versionlist;
		$this->view->position[]	 = html::a($this->createLink('kevinsoft', 'softlist'), $this->lang->kevinsoft->soft);
		$this->view->position[]	 = $this->lang->kevinsoft->versionlist;
		$this->view->subMenu	 = 'versionlist';
		$this->view->method		 = 'versionlist';
		$this->display();
	}

	/**
	 * Create a schedual.
	 * 
	 * @param  string|date 
	 * @param  string      
	 * @access public
	 * @return void
	 */
	public function versioncreate($softID) {
		if (!empty($_POST)) {
			$itemID = $this->kevinsoft->versioncreate();
			if (dao::isError()) die(js::error(dao::getError()));
			if (isonlybody()) die(js::reload('parent.parent'));
			die(js::locate($this->createLink('kevinsoft', 'versionlist'), 'parent'));
		}
		$this->view->softfilePairs	 = $this->kevinsoft->softNameGetPairs();
		$this->view->softID			 = $softID;
		//网页位置
		$this->view->title			 = $this->lang->kevinsoft->common . $this->lang->colon . $this->lang->kevinsoft->versioncreate;
		$this->view->position[]		 = $this->lang->kevinsoft->versioncreate;

		$this->display();
	}

//</editor-fold>

	/**
	 * View a doc.
	 * 
	 * @param  int    $docID 
	 * @access public
	 * @return void
	 */
	public function versionview($versionID) {
		$Version = $this->kevinsoft->versionGetByID($versionID, true);
		if (!$Version) die(js::error($this->lang->notFound) . js::locate('back'));

		$this->view->soft		 = $this->kevinsoft->softGetByID($Version->soft);
		$this->view->title		 = $this->lang->kevinsoft->common . $this->lang->colon . $this->lang->kevinsoft->versionview;
		$this->view->position[]	 = html::a($this->createLink('kevinsoft', 'versionlist'), $this->lang->kevinsoft->versionlist);
		$this->view->position[]	 = $this->lang->kevinsoft->versionview;

		$this->view->VersionItem = $Version;
		$this->view->subMenu	 = "versionlist";
		$this->view->actions	 = $this->loadModel('action')->getList('kevinsoftversion', $versionID);
		$this->view->preAndNext	 = $this->loadModel('common')->getPreAndNextObject('kevinsoft', $versionID);
		//$this->view->keTableCSS = $this->doc->extractKETableCSS($doc->content);

		$this->display();
	}

	/**
	 * Edit a kevinsoft.
	 * 
	 * @param  int    $id 
	 * @access public
	 * @return void
	 */
	public function versiondelete($id, $confirm = "no") {
		if (!$id || $id <= 0) {
			die(js::error("ID is wrong! =" . $id));
		}
		if ($confirm == 'no') {
			die(js::confirm($this->lang->kevinsoft->confirmSoftDelete . " id = " . $id, inlink('versiondelete', "id=$id&confirm=yes")));
		}

		$this->dao->update(TABLE_KEVIN_SOFT_VERSION)->set('deleted')->eq('1')->where('id')->eq((int) $id)->exec();

		/* if ajax request, send result. */
		if ($this->server->ajax) {
			if (dao::isError()) {
				$response['result']	 = 'fail';
				$response['message'] = dao::getError();
			} else {
				$response['result']	 = 'success';
				$response['message'] = '';
				$this->action->create('kevinsoft', $id, 'deleted');
			}
			$this->send($response);
		}
		if (isonlybody()) die(js::reload('parent.parent'));
		die(js::locate($this->createLink('kevinsoft', 'versionlist'), 'parent'));
	}

	/**
	 * Edit a kevinsoft.
	 * 
	 * @param  int    $id 
	 * @access public
	 * @return void
	 */
	public function versionedit($id) {
		if (!empty($_POST)) {
			$this->loadModel('action');
			$changes = array();
			$files	 = array();
			//if($comment == false)
			{
				$changes = $this->kevinsoft->versionupdate($id);
				if (dao::isError()) die(js::error(dao::getError()));
				$files	 = $this->kevinsoft->fileSaveUpload('kevinsoftversion', $id);
			}

			if ($this->post->comment != '' or ! empty($changes) or ! empty($files)) {
				$action		 = !empty($changes) ? 'Edited' : 'Commented';
				$fileAction	 = !empty($files) ? $this->lang->addFiles . join(',', $files) . "\n" : '';
				$actionID	 = $this->action->create('kevinsoftversion', $id, $action, $fileAction . $this->post->comment);
				if (!empty($changes)) $this->action->logHistory($actionID, $changes);
				//$this->sendmail($id, $actionID);
			}

			if (isonlybody()) die(js::reload('parent.parent'));
			die(js::locate($this->createLink('kevinsoft', 'versionlist'), 'parent'));
		}
		$this->view->VersionItem = $this->kevinsoft->versionGetByID($id);
		$this->view->subMenu	 = "versionlist";
		//网页位置
		$this->view->title		 = $this->lang->kevinsoft->common . $this->lang->colon . $this->lang->kevinsoft->versionedit;
		$this->view->position[]	 = $this->lang->kevinsoft->versionedit;
		$this->display();
	}

	/**
	 * delete a kevinsoft file.
	 * 
	 * @param  int    $id 
	 * @access public
	 * @return void
	 */
	public function filedelete($id, $confirm = "no") {
		$id = (int) $id;
		if ($confirm == 'no') {
			die(js::confirm($this->lang->kevinsoft->confirmDelete . "Kevin > soft > file id = " . $id, inlink('filedelete', "id=$id&confirm=yes")));
		}
		$file = $this->kevinsoft->fileGetByID($id);
		if (!$file) {
			die(js::error("Can not find this kevinsoft file. ID! =" . $id));
		}
		$rc		 = $this->dao->delete()->from(TABLE_KEVIN_SOFT_FILE)->where('id')->eq($id)->exec();
		if (dao::isError() || !$rc) die(js::reload('parent.parent'));
		$this->loadModel('action')->create($file->objectType, $file->objectID, 'deletedFile', '', $extra	 = $file->title . '.' . $file->extension);
		//@unlink($file->realPath);
		die(js::reload('parent.parent'));
	}

	/**
	 * Down a file.
	 * 
	 * @param  int    $fileID 
	 * @param  string $mouse 
	 * @access public
	 * @return void
	 */
	public function filedownload($id, $mouse = '') {
		$file = $this->kevinsoft->fileGetByID($id);

		$this->loadModel('file');
		/* Judge the mode, down or open. */
		$mode		 = 'down';
		$fileTypes	 = 'txt|jpg|jpeg|gif|png|bmp|xml|html';
		if (stripos($fileTypes, $file->extension) !== false and $mouse == 'left') $mode		 = 'open';

		/* If the mode is open, locate directly. */
		if ($mode == 'open') {
			if (file_exists($file->realPath)) $this->locate($file->webPath);
			$this->app->triggerError("The file you visit $fileID not found.", __FILE__, __LINE__, true);
		}
		else {
			/* Down the file. */
			if (file_exists($file->realPath)) {
				$fileName	 = $file->title . '.' . $file->extension;
				$fileData	 = file_get_contents($file->realPath);
				$this->fileSendDownHeader($fileName, $file->extension, $fileData);
			} else {
				$this->app->triggerError("The file you visit $id not found.", __FILE__, __LINE__, true);
			}
		}
	}

	/**
	 * Edit a kevinsoft.
	 * 
	 * @param  int    $id 
	 * @access public
	 * @return void
	 */
	public function fileedit($id) {
		if ($_POST) {
			$this->app->loadLang('action');
			$file	 = $this->kevinsoft->fileGetByID($id);
			$title	 = $this->post->title;
			if (!$title) die(js::reload('parent.parent'));
			$this->dao->update(TABLE_KEVIN_SOFT_FILE)->set('title')->eq($title)->where('id')->eq($id)->exec();

			$extension	 = "." . $file->extension;
			$actionID	 = $this->loadModel('action')->create($file->objectType, $file->objectID, 'editfile', '', $this->post->title . $extension);
			$changes[]	 = array('field' => 'fileName', 'old' => $file->title . $extension, 'new' => $title . $extension);
			$this->action->logHistory($actionID, $changes);

			die(js::reload('parent.parent'));
		}

		$this->view->file = $this->kevinsoft->fileGetByID($id);
		$this->display();
	}

	/**
	 * Create a schedual.
	 * 
	 * @param  string|date 
	 * @param  string      
	 * @access public
	 * @return void
	 */
	public function filelist($recTotal = 0, $recPerPage = 30, $pageID = 1) {

		$this->app->loadClass('pager', $static	 = true);
		$pager	 = new pager($recTotal, $recPerPage, $pageID);

		$files = $this->dao->select("*")->from(TABLE_KEVIN_SOFT_FILE)
				->where('objectType')->eq('kevinsoftversion')
				->orderBy('deleted,id')
				->page($pager)
				->fetchAll();
		if (!$files) die(js::locate($this->createLink('kevinsoft', 'groupversionlist'), 'parent'));

		$this->view->files	 = $files;
		$this->view->subMenu = "filelist";

		//网页位置
		$this->view->title		 = $this->lang->kevinsoft->common . $this->lang->colon . $this->lang->kevinsoft->filelist;
		$this->view->position[]	 = $this->lang->kevinsoft->filelist;
		$this->view->pager		 = $pager;

		$this->display();
	}

	/**
	 * Print files. 
	 * 
	 * @param  array  $files 
	 * @param  string $fieldset 
	 * @access public
	 * @return void
	 */
	public function fileprint($files, $fieldset) {
		$this->view->files		 = $files;
		$this->view->fieldset	 = $fieldset;
		$this->display();
	}

	/**
	 * Send the download header to the client.
	 * copy from file model
	 * @param  string    $fileName 
	 * @param  string    $extension 
	 * @access public
	 * @return void
	 */
	public function fileSendDownHeader($fileName, $fileType, $content) {
		/* Clean the ob content to make sure no space or utf-8 bom output. */
		$obLevel = ob_get_level();
		for ($i = 0; $i < $obLevel; $i++)
			ob_end_clean();

		/* Set the downloading cookie, thus the export form page can use it to judge whether to close the window or not. */
		setcookie('downloading', 1);

		/* Append the extension name auto. */
		$extension = '.' . $fileType;
		if (strpos($fileName, $extension) === false) $fileName .= $extension;

		/* urlencode the filename for ie. */
		if (strpos($this->server->http_user_agent, 'Trident') !== false) $fileName = urlencode($fileName);

		/* Judge the content type. */
		$mimes		 = $this->config->file->mimes;
		$contentType = isset($mimes[$fileType]) ? $mimes[$fileType] : $mimes['default'];

		header("Content-type: $contentType");
		header("Content-Disposition: attachment; filename=\"$fileName\"");
		header("Pragma: no-cache");
		header("Expires: 0");
		die($content);
	}
	
/**
	 * Statistic kevinsofts. 
	 * @param  string    $date 
	 * @param  string    $type 
	 * @access public
	 * @return void
	 */
	public function statistic($type = 'soft') {
		//网页位置
		$this->view->title		 = $this->lang->kevinsoft->common . $this->lang->colon . $this->lang->kevinsoft->statistic;
		$this->view->position[]	 = $this->lang->kevinsoft->statistic;

		//绘制图表
		if ('soft' == $type) {
			$this->view->items = $this->kevinsoft->statisticBySoft();
		}else if('module' == $type) {
			$this->view->items = $this->kevinsoft->statisticByModule();
		}else {
			$this->view->ErrorMsg = 'input type is wrong. no suche type of "' . $type . '"';
		}
		//页面参数
		$this->view->statisticType = $type;
		$this->view->subMenu	 = "statistic";
		$this->display();
	}

}
