<?php
/**
 * The model file of kevincom module of ZenTaoPMS.
 *
 * copyright:Kevin<3301647@qq.com>
 * http://kevincom.sourceforge.net/
 * The author disclaims copyright to this source code.  In place of
 * a legal notice, here is a blessing:
 * 
 *  May you do good and not evil.
 *  May you find forgiveness for yourself and forgive others.
 *  May you share freely, never taking more than you give.
 */
?>
<?php
class kevincomModel extends model
{
	
    /**
     * Append order by.
     * 
     * @param  string $orderBy 
     * @param  string $append 
     * @access public
     * @return string
     */
    public function appendOrder($orderBy, $append = 'id')
    {
        list($firstOrder) = explode(',', $orderBy);
        $sort = strpos($firstOrder, '_') === false ? '_asc' : strstr($firstOrder, '_');
        return strpos($orderBy, $append) === false ? $orderBy . ',' . $append . $sort : $orderBy;
    }

	/**
	 * Format time 0915 to 09:15
	 * 
	 * @param  string $time 
	 * @access public
	 * @return string
	 */
	public function formatTime($time)
	{
		if(strlen($time) != 4) return '';
		if($time == '2400') return '00:00';
		return substr($time, 0, 2) . ':' . substr($time, 2, 2);
	}

	public function getCashCodeByProject($project)
	{
		$todoObj = $this->dao->select('cashCode')
			->from(TABLE_PROJECT)
			->where('id')->eq($project)
			->fetch();
		if(!$todoObj) return '';
		return $todoObj->cashCode;
	}
	public function getDeptByAccount($account)
	{
		//获得员工所在的科室id,此处科室可以为股
		$deptId = $this->getDeptIdByAccount($account);
		//获得科室名称
		$deptName = $this->getDeptNameById($deptId);
		//获得科,或者股, 或者部门
		$keywords = mb_substr($deptName,-1,1,"UTF-8");
		if('科' == $keywords ) return $deptName;
		else if('部' == $keywords) return $deptName;
		else  return $this->getParentDeptNameById($deptId);
	}
	public function getDeptEmployeesAccount($deptId, $isShowDeleted = false)
	{
		$employees = array();
		$deptArray = $this->dao->select('id')->from(TABLE_DEPT)
			->where('path')->like('%,' . $deptId . ',%')
			->fetchAll('id');
		$deptIdArray = array();
		foreach($deptArray as $dept)
		{
			$deptIdArray[] = $dept->id;	
		}
		$deptIdArray[] = $deptId;
		for($i=0;$i<count($deptIdArray);$i++)
		{
			$deptEmployees = $this->dao->select('*')->from(TABLE_USER)
			->where('dept')->eq($deptIdArray[$i])
			->beginIF(!$isShowDeleted)->andWhere('deleted')->eq(0)->fi()
			->fetchAll('account');
			if($i == 0) $employees[''] = '';
			foreach($deptEmployees as $key => $employee)
			{
				$employees[] = $employee->account;
			}
		}
		return $employees;
	}
	public function getDeptFromDB()
	{
		return $this->dao->select('*')
			->from(TABLE_DEPT)
			->fetchAll('name');
	}
	public function getDeptIdByAccount($account)
	{
		$todoObj = $this->dao->select('dept')
			->from(TABLE_USER)
			->where('account')->eq($account)
			->fetch();
		if(!$todoObj) return '';
		return $todoObj->dept;
	}
	public function getDeptNameByAccount($account)
	{
		$deptId = $this->getDeptIdByAccount($account);
		return $this->getDeptNameById($deptId);
	}
	
	public function getDeptNameById($id)
	{
		$todoObj = $this->dao->select('name')
			->from(TABLE_DEPT)
			->where('id')->eq($id)
			->fetch();
		if(!$todoObj) return '';
		return $todoObj->name;
	}
	/**
	* Get option menu of departments.
	* 
	* @param  int    $rootDeptID 
	* @access public
	* @return array
	*/
	public function getDeptOptionMenu($rootDeptID = 0)
	{
		//加载dept模块
		$this->loadModel('dept');
		$deptMenu = array();
		$stmt = $this->dbh->query($this->dept->buildMenuQuery($rootDeptID));
		$depts = array();
		while($dept = $stmt->fetch()) $depts[$dept->id] = $dept;

		foreach($depts as $dept)
		{
			$parentDepts = explode(',', $dept->path);
			$deptName = '/';
			foreach($parentDepts as $parentDeptID)
			{
				if(empty($parentDeptID)) continue;
				$deptName .= $depts[$parentDeptID]->name . '/';
			}
			$deptName = rtrim($deptName, '/');
			$deptName .= "|$dept->id\n";

			if(isset($deptMenu[$dept->id]) and !empty($deptMenu[$dept->id]))
			{
				if(isset($deptMenu[$dept->parent]))
				{
					$deptMenu[$dept->parent] .= $deptName;
				}
				else
				{
					$deptMenu[$dept->parent] = $deptName;;
				}
				$deptMenu[$dept->parent] .= $deptMenu[$dept->id];
			}
			else
			{
				if(isset($deptMenu[$dept->parent]) and !empty($deptMenu[$dept->parent]))
				{
					$deptMenu[$dept->parent] .= $deptName;
				}
				else
				{
					$deptMenu[$dept->parent] = $deptName;
				}    
			}
		}

		$topMenu = @array_pop($deptMenu);
		$topMenu = explode("\n", trim($topMenu));
		$lastMenu = array();
		foreach($topMenu as $menu)
		{
			if(!strpos($menu, '|')) continue;
			list($label, $deptID) = explode('|', $menu);
			$lastMenu[$deptID] = $label;
			foreach($lastMenu as $deptID => $deptName)
			{
				$deptNameArray = explode('/', $deptName);
				if(count($deptNameArray) > 2)
				{
					array_shift($deptNameArray);
					$parentDept = current($deptNameArray);
					$currentDept = end($deptNameArray);
					$lastMenu[$deptID] = '/' . $parentDept . '/' . $currentDept;
				}
			}
		}
		return $lastMenu;
	}
	
	public function getDispatchAccountsByDept($deptID, $isShowDeleted = false)
	{
		$accountArray	 = $this->getDeptEmployeesAccount($deptID, $isShowDeleted);
		$accountObjs = $this->dao->select('account,deptdispatch')->from(TABLE_USER)
			->where('account')->in($accountArray)
			->beginIF(!$isShowDeleted)->andWhere('deleted')->eq(0)->fi()
			->fetchGroup('deptdispatch', 'account');

		return $accountObjs;
	}
	
	public function getEmployeesByDept($dept, $isShowDeleted = false)
	{
		return $this->dao->select('account,realname,code,worktype')
			->from(TABLE_USER)
			->where('dept')->eq($dept)
			->beginIF(!$isShowDeleted)->andWhere('deleted')->eq(0)->fi()
			->fetchAll();
	}
    /**
     * get module file contents
     * 
     * @moduleName  module name
     * @refpath  file path ref to module base folder
     * @access public
     * @return string of file contents
     */
	public function getModuleFileContents($moduleName,$refpath)
	{
		$moduleName  = strtolower(trim($moduleName));
		$modulePath  = $this->app->getModulePath($moduleName);

		$contents = '';
		$mainJsFile   = $modulePath . $refpath;
		if(file_exists($mainJsFile))   $contents .= file_get_contents($mainJsFile);
		return $contents;
	}
	
	/**
	 * getMonthList .
	 * @param  bool  $withblank 
	 * @access public
	 * @return void
	 */
	public function getMonthList($withblank = 0, $fill0 = true) {
		$List = array();
		if($withblank)$List['']='';//blank
		$prefix= ($fill0)?'0':'';
		for ($i = 1; $i < 10; $i++) {
			$month = $prefix. $i;
			$List[$month] = $month;
		}
		for ($i = 10; $i <= 12; $i++) {
			$List[$i] = $i;
		}
		return $List;
	}
	
	public function getParentDeptIdById($id)
	{
		$currentDept = $this->dao->select('parent')
				->from(TABLE_DEPT)
				->where('id')->eq($id)
				->fetch();
		if(!$currentDept) return 0;
		$parentDeptId = $currentDept->parent;
		if(0 == $parentDeptId) return 0;
		$deptParent = $this->dao->select('id')
			->from(TABLE_DEPT)
			->where('id')->eq($parentDeptId)
			->fetch();
		if(!$deptParent) return 0;
		return $deptParent->id;
	}
	public function getParentDeptNameById($id)
	{
		$currentDept = $this->dao->select('*')
			->from(TABLE_DEPT)
			->where('id')->eq($id)
			->fetch();
		if(!$currentDept) return '';
		$parentDeptId = $currentDept->parent;
		if(0 == $parentDeptId) return '';
		$deptParent = $this->dao->select('name')
			->from(TABLE_DEPT)
			->where('id')->eq($parentDeptId)
			->fetch();
		if(!$deptParent) return '';
		return $deptParent->name;
	}
	public function getProjectNameByProject($project)
	{	
		if($project == '') return '';
		$todoObj = $this->dao->select('name')
			->from(TABLE_PROJECT)
			->where('id')->eq($project)
			->fetch();
		if(!$todoObj) return '';
		return $todoObj->name;
	}
	public function getProjectNameByProjectPairs($project)
	{
		$projectObj = $this->dao->select('name')
			->from(TABLE_PROJECT)
			->where('id')->eq($project)
			->fetch();
		$ret = $projectObj->name;
		if('' == $ret) $ret = '项目代号不存在';
		$projectName = array();
		$projectName[] = $ret;
		return $projectName;
	}
	public function getProjectsByProductID($productID)
	{
		$projects = array();//产品下的所有项目组成的数组
		$projectArray = $this->dao->select('*')->from(TABLE_PROJECTPRODUCT)
						->where('product')->eq($productID)
						->fetchAll();
		foreach($projectArray as $project)
		{
			$projects[] = $project->project;	
		}
		return $projects;
	}
    /**
     * get Real name By Account
     * 
     * @account  account string
     * @access public
     * @return string of real name
     */
	public function getRealnameByAccount($account)
	{	
		$rs = $this->dao->select('realname')
			->from(TABLE_USER)
			->where('account')->eq($account)
			->fetch();
		if(!$rs) return $account;
		return $rs->realname;
	}
	public function getSectionIdByDeptID($deptID)
	{
		$pathObj = $this->dao->select('path')->from(TABLE_DEPT)
			->where('id')->eq($deptID)
			->fetch();
		if (!$pathObj) {
			return 0;
		}
		$path = $pathObj->path;
		$pathArray = explode(',',$path);
		return $pathArray[2];
	}
	public function getSectionNameByAccount($account)
	{
		//获得员工所在的科室id,此处科室可以为股
		$deptId = $this->getDeptIdByAccount($account);
		//获得科室名称
		$deptName = $this->getDeptNameById($deptId);
		//获得科,或者股, 或者部门
		$keywords = mb_substr($deptName,-1,1,"UTF-8");
		if('科' == $keywords ) return $this->getParentDeptNameById($deptId);
		else if('部' == $keywords) return $deptName;
		else
		{
			while('部' != $keywords)
			{
				$deptId = $this->getParentDeptIdById($deptId);
				//获得科室名称
				$deptName = $this->getDeptNameById($deptId);
				//获得科,或者股, 或者部门
				$keywords = mb_substr($deptName,-1,1,"UTF-8");
			}
			return $deptName;
		}
	}
	public function getSectionNameByDeptID($deptID)
	{
		$pathObj = $this->dao->select('path')->from(TABLE_DEPT)
			->where('id')->eq($deptID)
			->fetch();
		if (!$pathObj) {
			return '';
		}
		$path = $pathObj->path;
		$pathArray = explode(',',$path);
		return $this->getDeptNameById($pathArray[2]);
	}
	public function getUserCodeByAccount($account)
	{	
		$todoObj = $this->dao->select('code')
			->from(TABLE_USER)
			->where('account')->eq($account)
			->fetch();
		if(!$todoObj) return '';
		return $todoObj->code;
	}
	public function getUserInfoByAccount($account)
	{	
		$userObj = $this->dao->select('*')
			->from(TABLE_USER)
			->where('account')->eq($account)
			->fetch();
		return $userObj;
	}
	public function getWorkMinutes($workhours, $begin = '', $end = '')
	{
		if($workhours == '')
		{
			$endArray = explode(':', $end);
			$beginTime = 60*((int)substr($begin,0,2)) + ((int)substr($begin,2,2));
			$endTime = 60*((int)substr($end,0,2)) + ((int)substr($end,2,2));
			if($begin > $end) return $beginTime+60*12-$endTime;
			else return $endTime - $beginTime;
		}
		$array = explode(':', $workhours);
		return 60*$array[0]+$array[1];
	}
	//通过用户名获得员工类型，默认为0
	public function getWorkTypeByAccount($account)
	{
		$worktype = $this->dao->select('worktype')
			->from(TABLE_USER)
			->where('account')->eq($account)
			->fetch();
		if(!$worktype) return 0;
		return $worktype->worktype;
	}

	/**
	 * getYearList .
	 * @param  int  $year 
	 * @access public
	 * @return void
	 */
	public function getYearList($year = 10) {
		$yearList = array();
		for ($i = -1; $i < $year; $i++) {
			$yearList[date('Y') - $i] = (date('Y') - $i);
		}
		return $yearList;
	}
	
	public function writeDataToFile($data)
	{
		//如zentao在D盘,则路径为D:\xampp\zentao\tmp\log\log.txt
		$myfile = fopen("../tmp/log/log.txt", "a") or die("Unable to open file!");
		if('' == $data) $data = 'Data is empty!';
		$data  = date('Y-m-d h:i:s') . '  =>  ' . $data  . "\r\n";
		fwrite($myfile, $data);
		fclose($myfile);
	}
}
