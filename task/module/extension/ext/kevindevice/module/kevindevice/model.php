<?php

/**
 * The kevindeviceModel
 *
 * @copyright   Kevin
 * @author      kevin<3301647@qq.com>
 * @package     kevindevice
 */
?>
<?php

class kevindeviceModel extends model {

	/**
	 * Create a user.
	 * 
	 * @access public
	 * @return void
	 */
	public function devcreate() {
		$device			 = fixer::input('post')
				->setDefault('join', '0000-00-00')
				->get();
		if (!$device->group) $device->group	 = 0;

		$this->dao->insert(TABLE_KEVINDEVICE_DEVLIST)->data($device)
				->autoCheck()
				->batchCheck($this->config->kevindevice->devcreate->requiredFields, 'notempty')
				->check('nametype', 'unique')
				->exec();
		if (dao::isError()) return;
	}

	/**
	 * Delete a user.
	 * 
	 * @param  int    $deviceid 
	 * @access public
	 * @return void
	 */
	public function devdelete($deviceid) {
		$this->dao->update(TABLE_KEVINDEVICE_DEVLIST)->set('deleted = 1')
				->where('id')->eq((int) $deviceid)
				->exec();
		//$this->dao->delete()->from(TABLE_KEVINDEVICE_DEVLIST)->where('id')->eq($deviceid)->exec();
		//$this->dao->delete()->from(TABLE_KEVINDEVICE_GROUPLIST)->where('`user`')->eq($deviceid)->exec();
	}

	/**
	 * Get user info by ID.
	 * 
	 * @param  int    $deviceid 
	 * @access public
	 * @return object|bool
	 */
	public function devGetById($deviceid) {
		$device = $this->dao->select('*')->from(TABLE_KEVINDEVICE_DEVLIST)->where('id')->eq($deviceid)->fetch();
		return $device;
	}

	public function devStatusCheck(){
		$dismatches=$this->dao->select('a.id,a.status,a.group,b.type')->from(TABLE_KEVINDEVICE_DEVLIST)->alias('a')
							->leftJoin(TABLE_KEVINDEVICE_GROUP)->alias('b')->on('a.group = b.id')
							->where('a.status')->eq('discard')
							->orWhere('b.type')->eq('discard')
							->fetchAll();
		$group=$this->dao->select('id')->from(TABLE_KEVINDEVICE_GROUP)->where('type')->eq('discard')->fetch();
		foreach($dismatches as $dismatch){
			if($dismatch->status!=$dismatch->type){
				if($dismatch->status=='discard')
					$this->dao->update(TABLE_KEVINDEVICE_DEVLIST)->set('group')->eq((int)$group->id)
					->where('id')->eq($dismatch->id)->andWhere('deleted')->eq(0)
					->exec();
				elseif($dismatch->type=='discard')
					$this->dao->update(TABLE_KEVINDEVICE_DEVLIST)->set('status')->eq('normal')
						->where('id')->eq($dismatch->id)->andWhere('deleted')->eq(0)
						->exec();
			}
		}
	}
	/**
	 * Get devices by sql.
	 * 
	 * @param  int    $query 
	 * @param  int    $pager 
	 * @access public
	 * @return void
	 */
	public function devGetByQuery($groupID = 0, $pager = null, $orderBy = 'id') {
		$this->devStatusCheck();
		if ($groupID) {
			return $this->dao->select('a.*,b.name as groupName,c.name as deptName')
							->from(TABLE_KEVINDEVICE_DEVLIST)->alias('a')
							->leftJoin(TABLE_KEVINDEVICE_GROUP)->alias('b')->on('a.group = b.id')
							->leftJoin(TABLE_DEPT)->alias('c')->on('a.dept = c.id')
							->where('a.group')->eq((int) $groupID)
							->andWhere('a.deleted')->eq(0)
							->orderBy($orderBy)
							->page($pager)
							->fetchAll();
		}
		//no group select all
		return $this->dao->select('a.*,b.name as groupName,c.name as deptName')->from(TABLE_KEVINDEVICE_DEVLIST)->alias('a')
						->leftJoin(TABLE_KEVINDEVICE_GROUP)->alias('b')->on('a.group = b.id')
						->leftJoin(TABLE_DEPT)->alias('c')->on('a.dept = c.id')
						->Where('a.deleted')->eq(0)
						->orderBy($orderBy)
						->page($pager)
						->fetchAll();
	}

	/**
	 * Get devices by sql.
	 * 
	 * @param  int    $query 
	 * @param  int    $pager 
	 * @access public
	 * @return void
	 */
	public function devGetByGroup($groupID = 0, $orderBy = 'name') {
		if ($groupID) {
			return $this->dao->select('*')
							->from(TABLE_KEVINDEVICE_DEVLIST)
							->where('`group`')->eq((int) $groupID)
							->andWhere('deleted')->eq(0)
							->orderBy($orderBy)
							->fetchAll();
		}
		return null;
	}

	/**
	 * Get user pairs of a group.
	 * 
	 * @param  int    $groupID 
	 * @access public
	 * @return array
	 */
	public function devGetPairs($groupID = 0) {
		if (0 == $groupID) {
			return $this->dao->select('id,name')
							->from(TABLE_KEVINDEVICE_DEVLIST)
							->Where('deleted')->eq(0)
							->orderBy('name')
							->fetchPairs();
		}
		return $this->dao->select('id,name')
						->from(TABLE_KEVINDEVICE_DEVLIST)
						->where('`group`')->eq((int) $groupID)
						->andWhere('deleted')->eq(0)
						->orderBy('name')
						->fetchPairs();
	}

	/**
	 * Update a user.
	 * 
	 * @param  int    $deviceid 
	 * @access public
	 * @return void
	 */
	public function devupdate($deviceid) {
		$oldUser	 = $this->devGetById($deviceid);
		if (!$oldUser) return;
		$deviceid	 = (int) $deviceid;
		$device		 = fixer::input('post')
				->setDefault('join', '0000-00-00')
				->get();

		if (!$device->group) $device->group = 0;
		$this->dao->update(TABLE_KEVINDEVICE_DEVLIST)->data($device)
				->autoCheck()
				->batchCheck($this->config->kevindevice->devedit->requiredFields, 'notempty')
				->where('id')->eq((int) $deviceid)
				->exec();
		if (dao::isError()) return;
	}

	/**
	 * Create a group.
	 * 
	 * @access public
	 * @return bool
	 */
	public function groupcreate() {
		$group = fixer::input('post')->get();
		return $this->dao->insert(TABLE_KEVINDEVICE_GROUP)->data($group)->batchCheck($this->config->kevindevice->groupcreate->requiredFields, 'notempty')->exec();
	}

	/**
	 * Copy a group.
	 * 
	 * @param  int    $groupID 
	 * @access public
	 * @return void
	 */
	public function groupcopy($groupID) {
		$group = fixer::input('post')->remove('options')->get();
		$this->dao->insert(TABLE_KEVINDEVICE_GROUP)->data($group)->check('name', 'unique')->check('name', 'notempty')->exec();
		if ($this->post->options == false) return;
		if (!dao::isError()) {
			$newGroupID	 = $this->dao->lastInsertID();
			$options	 = join(',', $this->post->options);
			if (strpos($options, 'copyPriv') !== false) $this->copyPriv($groupID, $newGroupID);
			if (strpos($options, 'copyUser') !== false) $this->copyUser($groupID, $newGroupID);
		}
	}

	/**
	 * Delete a group.
	 * 
	 * @param  int    $groupID 
	 * @param  null   $null      compatible with that of model::delete()
	 * @access public
	 * @return void
	 */
	public function groupdelete($groupID, $null = null) {
		$this->dao->update(TABLE_KEVINDEVICE_GROUP)->set('deleted = 1')
				->where('id')->eq((int) $groupID)
				->exec();
		//$this->dao->delete()->from(TABLE_KEVINDEVICE_GROUP)->where('id')->eq($groupID)->exec();
	}

	/**
	 * Get group by id.
	 * 
	 * @param  int    $groupID 
	 * @access public
	 * @return object
	 */
	public function groupGetById($groupID) {
		return $this->dao->findById($groupID)->from(TABLE_KEVINDEVICE_GROUP)->fetch();
	}

	/**
	 * Get group by userID.
	 * 
	 * @param  int    $deviceid 
	 * @access public
	 * @return array
	 */
	public function groupGetByDevice($deviceid) {
		return $this->dao->select('a.*')->from(TABLE_KEVINDEVICE_GROUP)->alias('a')
						->leftJoin(TABLE_KEVINDEVICE_DEVLIST)->alias('b')
						->on('a.id = b.group')
						->where('b.id')->eq($deviceid)
						->fetchAll('id');
	}

	/**
	 * Get group lists.
	 * 
	 * @access public
	 * @return array
	 */
	public function groupGetList() {
		return $this->dao->select('*')->from(TABLE_KEVINDEVICE_GROUP)->orderBy('id')->fetchAll();
	}

	/**
	 * Update a group.
	 * 
	 * @param  int    $groupID 
	 * @access public
	 * @return void
	 */
	public function groupUpdate($groupID) {
		$group = fixer::input('post')->get();
		return $this->dao->update(TABLE_KEVINDEVICE_GROUP)->data($group)
						->batchCheck($this->config->kevindevice->nameRequire->requiredFields, 'notempty')
						->where('id')->eq($groupID)->exec();
	}

	/**
	 * Get kevindevice pairs.
	 * 
	 * @access public
	 * @return array
	 */
	public function groupGetPairs() {
		return $this->dao->select('id, name')->from(TABLE_KEVINDEVICE_GROUP)->fetchPairs();
	}

	/**
	 * Update devices.
	 * 
	 * @param  int    $groupID 
	 * @access public
	 * @return void
	 */
	public function groupUpdateUser($groupID) {
		/* Delete old. */
		$this->dao->delete()->from(TABLE_KEVINDEVICE_GROUPLIST)->where('`group`')->eq($groupID)->exec();

		/* Insert new. */
		if ($this->post->members == false) return;
		foreach ($this->post->members as $userid) {
			$data		 = new stdclass();
			$data->user	 = $userid;
			$data->group = $groupID;
			$this->dao->insert(TABLE_KEVINDEVICE_GROUPLIST)->data($data)->exec();
		}
	}

	/**
	 * statistic By Group.
	 * 
	 * @access public
	 * @return array
	 */
	public function statisticByGroup() {
		return $this->dao->select('count(*) as deviceCount, `group`,b.name as groupName')
						->from(TABLE_KEVINDEVICE_DEVLIST)->alias('a')
						->leftJoin(TABLE_KEVINDEVICE_GROUP)->alias('b')->on('a.group = b.id')
						->where('a.deleted')->eq('0')
						->groupBy('`group`')
						->orderBy('group')
						->fetchAll();
	}

	/**
	 * statistic By dept.
	 * 
	 * @access public
	 * @return array
	 */
	public function statisticByDept() {
		return $this->dao->select('count(*) as deviceCount, `group`,b.name as groupName,b.id as groupID')
						->from(TABLE_KEVINDEVICE_DEVLIST)->alias('a')
						->leftJoin(TABLE_DEPT)->alias('b')->on('a.dept = b.id')
						->where('a.deleted')->eq('0')
						->groupBy('`dept`')
						->orderBy('dept')
						->fetchAll();
	}

	/**
	 * statistic By dept.
	 * 
	 * @access public
	 * @return array
	 */
	public function statisticByType() {
		return $this->dao->select('count(*) as deviceCount, b.type as groupName')
						->from(TABLE_KEVINDEVICE_DEVLIST)->alias('a')
						->leftJoin(TABLE_KEVINDEVICE_GROUP)->alias('b')->on('a.group=b.id')
						->where('a.deleted')->eq('0')
						->groupBy('b.type')
						->orderBy('b.type')
						->fetchAll();
	}

}
