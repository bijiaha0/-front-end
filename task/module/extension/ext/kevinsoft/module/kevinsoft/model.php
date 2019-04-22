<?php

/**
 * The model file
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

class kevinsoftModel extends model {

	/**
	 * Construct function, load model of kevinsoft.
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct() {
		parent::__construct();

		$this->app->loadClass('kevin'); //加载kevin类	
	}

	/**
	 * Create a device.
	 * 
	 * @param  date   $date 
	 * @param  string $account 
	 * @access public
	 * @return void
	 */
	public function filecreate() {
		$file = fixer::input('post')->get();
		$this->dao->insert(TABLE_KEVIN_SOFT_FILE)->data($file)
				->autoCheck()
				->batchCheck($this->config->kevinsoft->filecreate->requiredFields, 'notempty')
				->exec();
		return $this->dao->lastInsertID();
	}

	/**
	 * Get doc info by id.
	 * 
	 * @param  int    $docID  
	 * @access public
	 * @return void
	 */
	public function fileGetByID($fileID) {
		$file			 = $this->dao->select('*')
				->from(TABLE_KEVIN_SOFT_FILE)
				->where('id')->eq((int) $fileID)
				->fetch();
		if (!$file) return null;
		$this->loadModel('file');
		$file->webPath	 = $this->file->webPath . $file->pathname;
		$file->realPath	 = $this->app->getAppRoot() . "www/data/upload/{$this->app->company->id}/" . $file->pathname;
		return $file;
	}

	/**
	 * Get file GetList
	 * 
	 * 
	 * @access public
	 * @return array
	 */
	public function fileGetList($softID, $versionID) {
		$filelist = $this->dao->select("f.id,f.soft,f.version,f.pathname,f.title,f.size,f.downloads,f.valid,f.md5,f.type,f.comment,f.addedBy,f.addedDate,f.lastEditedBy,f.lastEditedDate,f.deleted")->from(TABLE_KEVIN_SOFT_FILE)->alias('f')
				->leftJoin(TABLE_KEVIN_SOFT_LIST)->alias('s')->on('f.soft=s.id')
				->leftJoin(TABLE_KEVIN_SOFT_VERSION)->alias('v')->on('f.soft=v.soft and f.version=v.version')
				->where('f.deleted')->eq('0')
				->orderBy('f.deleted,f.id')
				->fetchAll();

		foreach ($filelist as
				$key =>
				$value) {
			if ($value->soft != $softID or $value->version != $versionID) {
				unset($filelist[$key]);
			}
		}
		return $filelist;
	}

	/**
	 * Save upload.
	 * 
	 * @param  string $objectType 
	 * @param  string $objectID 
	 * @param  string $extra 
	 * @access public
	 * @return array
	 */
	public function fileSaveUpload($objectType = 'kevinsoftversion', $objectID = '', $extra = '') {
		$this->loadModel('file'); //file
		$fileTitles	 = array();
		$now		 = helper::now();
		$files		 = $this->file->getUpload();

		foreach ($files as
				$id =>
				$file) {
			if ($file['size'] == 0) continue;
			move_uploaded_file($file['tmpname'], $this->file->savePath . $file['pathname']);
			$file['objectType']						 = $objectType;
			$file['objectID']						 = $objectID;
			$file['addedBy']						 = $this->app->user->account;
			$file['addedDate']						 = $now;
			$file['extra']							 = $extra;
			unset($file['tmpname']);
			$this->dao->insert(TABLE_KEVIN_SOFT_FILE)->data($file)->exec();
			$fileTitles[$this->dao->lastInsertId()]	 = $file['title'];
		}
		return $fileTitles;
	}

	/**
	 * Create a kevinsoft.
	 * 
	 * @param  date   $date 
	 * @param  string $account 
	 * @access public
	 * @return void
	 */
	public function groupversioncreate($group) {
		$group = (int)$group;
		if($group<0) return 0;//error
		$kevinsoft = fixer::input('post')
				->get();
		
		$kevinsoft->groupversion = $group;
		$version = (int)$kevinsoft->version;
		if($version<0) return 0;//error
		$item = $this->groupversionGetByID($group, $version);
		if ($item) die(js::error("Already exist the version in group!"));
		$item = $this->versionGetByID( $version);
		if (!$item) die(js::error("Do not exit input version!"));
		$item = $this->versionGetByID( $group);
		if (!$item) die(js::error("Do not exit input Group!"));
			
		$this->dao->insert(TABLE_KEVIN_SOFT_GROUPVERSION)->data($kevinsoft)
				->batchCheck($this->config->kevinsoft->groupversioncreate->requiredFields, 'notempty')
				->exec();
		return $this->dao->lastInsertID();
	}

	/**
	 * Get info of a device.
	 * @param  int    $id 
	 * @access public
	 * @return object|bool
	 */
	public function groupversionGetByID($group, $version) {
		$groupversionInfo = $this->dao->select("*")->from(TABLE_KEVIN_SOFT_GROUPVERSION)
				->where('groupversion')->eq($group)
				->andwhere('version')->eq($version)
				->fetch();
		return $groupversionInfo;
	}

	/**
	 * Get info of a item.
	 * @param  int    $id 
	 * @access public
	 * @return object|bool
	 */
	public function groupversionGetList($pager) {
		return $this->dao->select("*")->from(TABLE_KEVIN_SOFT_GROUPVERSION)
						->orderBy('groupversion')
						->page($pager)
						->fetchAll();
	}

	/**
	 * Update a kevinsoft.
	 * 
	 * @param  int    $id 
	 * @access public
	 * @return void
	 */
	public function groupversionupdate($versionID, $fileID) {
		$groupversion = fixer::input('post')
				->get();
		$this->dao->update(TABLE_KEVIN_SOFT_GROUPVERSION)->data($groupversion)
				->autoCheck()
				->batchCheck($this->config->kevinsoft->groupversionupdate->requiredFields, 'notempty')
				->where('groupversion')->eq($versionID)
				->andwhere('subversion')->eq($fileID)
				->exec();
	}

	/**
	 * Create a kevinsoft.
	 * 
	 * @param  date   $date 
	 * @param  string $account 
	 * @access public
	 * @return void
	 */
	public function softcreate() {
		$now		 = helper::now();
		$kevinsoft	 = fixer::input('post')
				->add('addedBy', $this->app->user->account)
				->add('addedDate', $now)
				->add('lastEditedBy', $this->app->user->account)
				->add('lastEditedDate', $now)
				->setDefault('deleted', '0')
				->get();
		$this->dao->insert(TABLE_KEVIN_SOFT_LIST)->data($kevinsoft)
				->autoCheck()
				->batchCheck($this->config->kevinsoft->softcreate->requiredFields, 'notempty')
				->exec();
		return $this->dao->lastInsertID();
	}

	/**
	 * Get info of a kevinsoft.
	 * @param  int    $id 
	 * @access public
	 * @return object|bool
	 */
	public function softGetByID($id) {
		$softInfo = $this->dao->findById((int) $id)->from(TABLE_KEVIN_SOFT_LIST)->fetch();
		if (!$softInfo) return null;
		return $softInfo;
	}

	/**
	 * Get info of a item.
	 * @param  int    $id 
	 * @access public
	 * @return object|bool
	 */
	public function softGetList($pager) {
		return $this->dao->select("*")->from(TABLE_KEVIN_SOFT_LIST)
						->orderBy('deleted,id')
						->page($pager)
						->fetchAll();
	}
	
		/**
	 * Get module list.
	 * 
	 * @param  date   $date 
	 * @param  string $account 
	 * @param  string $status   all|today|thisweek|lastweek|before, or a date.
	 * @param  object    $pager    
	 * @param  string    $orderBy    
	 * @access public
	 * @return void
	 */
	public function moduleGetList(&$dataList, $pager = null, $orderBy = "id_desc") {
//		if($dataList->module==0) $module='';
	 $module = $dataList->module;
	 $grouptype = $dataList->grouptype;
//		if($dataList->type==0) $type='';
	 $type= $dataList->type;
		$deleted = $dataList->deleted;
		$stmt =$this->dao->select("t1.*,t2.name as devicename,t3.name as softname,t4.type as grouptype")->from(TABLE_KEVIN_SOFT_MODULE)->alias('t1')
				->leftJoin(TABLE_KEVINDEVICE_DEVLIST)->alias('t2')->on('t1.device=t2.id')
				->leftJoin(TABLE_KEVIN_SOFT_LIST)->alias('t3')->on('t1.software=t3.id')
				->leftJoin(TABLE_KEVINDEVICE_GROUP)->alias('t4')->on('t2.group=t4.id')
				->where('t1.deleted')->eq($deleted)
				->beginIF(!empty($type))->andWhere('t1.type')->eq($type)->fi()
				->beginIF(!empty($module))->andWhere('t1.module')->eq($module)->fi()
				->beginIF(!empty($grouptype))->andWhere('t4.type')->eq($grouptype)->fi()
				->orderBy($orderBy)
				->page($pager);
		return $stmt->fetchAll();
	}
	
	/**
	 * Get info of a kevinsoft.
	 * @param  int    $id 
	 * @access public
	 * @return object|bool
	 */
	public function moduleGetByID($id) {
		$moduleInfo = $this->dao->select("t1.*,t2.name as devicename,t3.name as softname")->from(TABLE_KEVIN_SOFT_MODULE)->alias('t1')
				->leftJoin(TABLE_KEVINDEVICE_DEVLIST)->alias('t2')->on('t1.device=t2.id')
				->leftJoin(TABLE_KEVIN_SOFT_LIST)->alias('t3')->on('t1.software=t3.id')
				->where('t1.id')->eq($id)
				->fetch();
		if (!$moduleInfo) return null;
		return $moduleInfo;
	}
	
	/**
	 * Create a kevinsoft module.
	 * 
	 * @param  int    $id 
	 * @access public
	 * @return void
	 */
	public function modulecreate() {
		$item	 = fixer::input('post')->get();
		if(empty($item->module)||$item->module==''){die(js::alert("请输入模块名！"));}
		if($item->endDate<$item->startDate){die(js::alert("结束日期必须大于开始日期！"));}
		$device=$this->dao->select('id,name')->from(TABLE_KEVINDEVICE_DEVLIST)->where('name')->eq($item->device)->fetch();
		if($device) $item->device=$device->id;
		$soft=$this->dao->select('id,name')->from(TABLE_KEVIN_SOFT_LIST)->where('name')->eq($item->software)->fetch();
		if($soft) $item->software=$soft->id;
		$this->dao->insert(TABLE_KEVIN_SOFT_MODULE)->data($item)
				->autoCheck()
				->batchCheck($this->config->kevinsoft->modulecreate->requiredFields, 'notempty')
				->exec();
		return $this->dao->lastInsertID();
	}
	
	/**
	 * Update a kevinsoft module.
	 * 
	 * @param  int    $id 
	 * @access public
	 * @return void
	 */
	public function moduleupdate($id) {
		$item	 = fixer::input('post')->get();
		if(empty($item->module)||$item->module==''){die(js::alert("请输入模块名！"));}
		if($item->endDate<$item->startDate){die(js::alert("结束日期必须大于开始日期！"));}
		$device=$this->dao->select('id,name')->from(TABLE_KEVINDEVICE_DEVLIST)->where('name')->eq($item->device)->fetch();
		if($device) $item->device=$device->id;
		$soft=$this->dao->select('id,name')->from(TABLE_KEVIN_SOFT_LIST)->where('name')->eq($item->software)->fetch();
		if($soft) $item->software=$soft->id;
//		$endymd=explode('-',$item->endDate);
//		$item->endDate=mktime(0, 0, 0, $endymd[1], $endymd[2], $endymd[0]);
		$this->dao->update(TABLE_KEVIN_SOFT_MODULE)->data($item)
				->autoCheck()
				->batchCheck($this->config->kevinsoft->moduledit->requiredFields, 'notempty')
				->where('id')->eq($id)
				->exec();
	}

	/**
	 * Get info of a item.
	 * @param  int    $id 
	 * @access public
	 * @return object|bool
	 */
	public function softNameGetPairs() {
		return $this->dao->select("id,name")->from(TABLE_KEVIN_SOFT_LIST)
						->orderBy('id')
						->fetchPairs('id', 'name');
	}

	/**
	 * Update a kevinsoft.
	 * 
	 * @param  int    $id 
	 * @access public
	 * @return void
	 */
	public function softupdate($id) {
		$now	 = helper::now();
		$item	 = fixer::input('post')
				->add('lastEditedBy', $this->app->user->account)
				->add('lastEditedDate', $now)
				->get();

		$this->dao->update(TABLE_KEVIN_SOFT_LIST)->data($item)
				->autoCheck()
				->batchCheck($this->config->kevinsoft->softupdate->requiredFields, 'notempty')
				->where('id')->eq($id)
				->exec();
	}

	/**
	 * Create a kevinsoft.
	 * 
	 * @param  date   $date 
	 * @param  string $account 
	 * @access public
	 * @return void
	 */
	public function versioncreate($SoftID) {
		$SoftID	 = (int) $SoftID; //id
		if ($SoftID < 1) return 0;
		$soft	 = $this->softGetByID($SoftID);
		if (!$soft) return 0;
		$now	 = helper::now();
		$item	 = fixer::input('post')
				->add('soft', $soft->id)
				->add('addedBy', $this->app->user->account)
				->add('addedDate', $now)
				->add('lastEditedBy', $this->app->user->account)
				->add('lastEditedDate', $now)
				->setDefault('deleted', '0')
				->get();
		if(!$item->version)return 0;//error
		$item->name = $soft->name ." ". $item->version;
		$this->dao->insert(TABLE_KEVIN_SOFT_VERSION)->data($item)
				->autoCheck()
				->batchCheck($this->config->kevinsoft->versioncreate->requiredFields, 'notempty')
				->exec();
		if (dao::isError()) return 0;
		$id		 = $this->dao->lastInsertID();
		return $id;
	}

	/**
	 * Get info of a version.
	 * @param  int    $id 
	 * @access public
	 * @return object|bool
	 */
	public function versionGetByID($id) {
		$item		 = $this->dao->findById((int) $id)->from(TABLE_KEVIN_SOFT_VERSION)->fetch();
		if (!$item) return null;
		$item->files = $this->dao->select('*')->from(TABLE_KEVIN_SOFT_FILE)->where('objectType')->eq('kevinsoftversion')->andWhere('objectID')->eq((int) $id)->orderBy('id')->fetchAll();
		//$item = $this->loadModel('file')->processEditor($item, $this->config->kevinsoft->editor->versionedit['id']);

		return $item;
	}

	/**
	 * Get item array
	 * 
	 * 
	 * @access public
	 * @return array
	 */
	public function versionGetList($pager) {
		return $this->dao->select("v.*,s.name as softname")->from(TABLE_KEVIN_SOFT_VERSION)->alias('v')
						->leftJoin(TABLE_KEVIN_SOFT_LIST)->alias('s')
						->on('v.soft=s.id')
						->orderBy('v.deleted,v.id')
						->page($pager)
						->fetchAll();
	}

	/**
	 * Update a kevinsoft.
	 * 
	 * @param  int    $id 
	 * @access public
	 * @return void
	 */
	public function versionupdate($id) {
		$oldItem = $this->versionGetByID($id);

		$now	 = helper::now();
		$item	 = fixer::input('post')
				->stripTags($this->config->kevinsoft->editor->versionedit['id'], $this->config->allowedTags)
				->add('lastEditedBy', $this->app->user->account)
				->add('lastEditedDate', $now)
				->remove('comment,files, labels')
				->get();

		$item = $this->loadModel('file')->processEditor($item, $this->config->kevinsoft->editor->versionedit['id']);
		$this->dao->update(TABLE_KEVIN_SOFT_VERSION)->data($item)
				->autoCheck()
				->batchCheck($this->config->kevinsoft->versionupdate->requiredFields, 'notempty')
				->where('id')->eq($id)
				->exec();
		if (!dao::isError()) return common::createChanges($oldItem, $item);
		return false;
	}

/**
	 * statistic By soft.
	 * 
	 * @access public
	 * @return array
	 */
	public function statisticBySoft() {
		return $this->dao->select('sum(a.count) as softCount,b.name as softName')
						->from(TABLE_KEVIN_SOFT_MODULE)->alias('a')
						->leftJoin(TABLE_KEVIN_SOFT_LIST)->alias('b')->on('a.software = b.id')
//						->leftJoin(TABLE_KEVINDEVICE_GROUP)->alias('c')->on('b.group = c.id')
						->where('a.deleted')->eq('0')
						->groupBy('b.name')
						->orderBy('b.name')
						->fetchAll();
	}
	
	/**
	 * statistic By module.
	 * 
	 * @access public
	 * @return array
	 */
	public function statisticByModule() {
		return $this->dao->select('sum(a.count) as totalCount,c.type as groupName,a.type')
						->from(TABLE_KEVIN_SOFT_MODULE)->alias('a')
						->leftJoin(TABLE_KEVINDEVICE_DEVLIST)->alias('b')->on('a.device = b.id')
						->leftJoin(TABLE_KEVINDEVICE_GROUP)->alias('c')->on('b.group = c.id')
						->where('a.deleted')->eq('0')
						->groupBy('c.type,a.type')
						->orderBy('c.type')
						->fetchAll();
	}
	
}
