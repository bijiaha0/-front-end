<?php

/**
 * The kevinchartModel
 *
 * @copyright   Kevin
 * @author      Kevin<3301647@qq.com>
 * @package     kevinchart
 */
?>
<?php

class kevinchartModel extends model {
	
	/**
	 * kevin class.
	 * 
	 * @access public
	 */
	public $kevin = null;
	
	/**
	 * chart data class.
	 * 
	 * @access public
	 */	
	public $chartData = null;
	
	/**
	 * kevin class.
	 * 
	 * @access public
	 */

	/**
	 * Construct function, load model of todo.
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		$this->loadModel('kevincom');		
		$kevin = $this->app->loadClass('kevin'); //加载kevin类
	}

	 /**
     * Get usages by sql.
     * 
     * @param  time    $begin 
     * @param  time    $end 
     * @param  int    $orderBy 
     * @access public
     * @return void
     */
    public function myItemGetPoints( $begin, $end) {
		$orderBy = 'start';
		$chartData = &$this->chartData;
        if(!$chartData) $chartData = new stdClass ();
		$chartData->serials = array();
		$query    = $this->dao->select('a.start, a.monitor, a.total')
                ->from(TABLE_KEVIN_CHARTEXAMPLE)->alias('a');
        $query    =  $query->where("a.start >= ".strtotime($begin));
        $end = strtotime($end);
        if($end) $query = $query->andWhere("a.start <= ". $end );
        $query = $query ->orderBy($orderBy);
		$array = $query->fetchAll();	
		
        $serial = new stdClass();
        $timeArray  = array();
        $usedArray  = array();
        $totalArray = array();
        $countPoints = count($array);
		$minTime = $maxTime= 0;
        if($countPoints){
            $minTime = $array[0]->start;
            $maxTime = $array[0]->start;

        }
        foreach ($array as $item) {
            $itemTime = (int)$item->start;
            if($minTime>$itemTime)$minTime = $itemTime;
            if($maxTime<$itemTime)$maxTime = $itemTime;
            $timeArray[] = $itemTime;
            $usedArray[] = (int)$item->monitor;
            $totalArray[] = (int)$item->total;
        }
        $begin = Date("Y-m-d H:i",$minTime);
        $end = Date("Y-m-d H:i",$maxTime);
        $this->timeCreateArray($begin, $end);

        $this->myItemGetArray($serial,$timeArray,$usedArray,$totalArray);
        $chartData->serials[] = $serial;
        return	true;
	}
	
	 /**
     * Get usages by sql.
     * 
     * @param  int    $id 
     * @param  time    $begin 
     * @param  time    $end 
     * @param  string    $orderBy 
     * @access public
     * @return void
     */
    public function myItemsGetList($begin, $end, $pager = null, $orderBy = 'start_desc') {
		if (!$orderBy) $orderBy = 'start_desc';
		$rec = $this->dao->select('a.*')
						->from(TABLE_KEVIN_CHARTEXAMPLE)->alias('a')
						->where("a.start")->ge(strtotime($begin));
        $end = strtotime($end);
        if($end) $rec = $rec->andWhere("a.start <= ". $end );
		return	$rec->orderBy($orderBy)
						->page($pager)
						->fetchAll();	
	}
    
    /**
     * crate activeSec Array.
     * 
     * @param  array    $serial 
     * @access public
     * @return void
     */
    public function myItemGetArray(&$ioSerial,$iTimeArray,$iUsedArray,$iTotalArray) {
		if(!$ioSerial)return false;
		//get ref
		$sag = &$this->chartData->sag;
		$ioSerial->usedArray = array();
		$ioSerial->totalArray = array();
		$usedArray = & $ioSerial->usedArray ;
		$totalArray = & $ioSerial->totalArray ;
		$timeArray = & $this->chartData->timeArray;

		if(!$timeArray) return false;
		
		$i = 0;
		$j = 0;
		$coutJ = count($iTimeArray);
		$coutI = count($timeArray);
		if(!$coutI) return false;

		$timeCur = 0;
		$timeIn = $iTimeArray[0];
		//find first one
		while($i<$coutI){
			if($timeArray[$i]<$timeIn){
				$usedArray[] = '';
				$totalArray[] = '';
				$i++;
				continue;//find next one
			}
			//find one $timeIn >= $timeArray[$i] 
			$usedArray[] = $iUsedArray[0];
			$totalArray[] = $iTotalArray[0];				
			$i++;
			break;//find first
		}
		$j = 1;//from next 1

		for(;$i<$coutI;$i++){
			//default 0
			if($j >=$coutJ ){ //没有监控数据了
				$usedArray[$i] = '';//$usedArray[$i-1];
				$totalArray[$i] ='';//$totalArray[$i-1];	
				continue;
			}
			$total = $totalArray[$i-1];
			$activeSec = 0;
			$countInSag = 0;//发现的数量
			$checkTime = $timeArray[$i];
			for(;$j<$coutJ;$j++){
				//找到下一个大于CheckTime的值
				$delta = $iTimeArray[$j] - $checkTime;
				if($delta <=-10){
					$countInSag++;//find one
					continue;
				}
				if(-10 < $delta && $delta <10 ){//10秒误差
					$activeSec = $iUsedArray[$j];
					$total = $iTotalArray[$j];	
					$j++;
					$countInSag++;
					break;
				} 

				//find one $checkTime>=$iTimeArray[$j],get this new value
				if( $countInSag>0){
					$activeSec = $iUsedArray[$j-1];
					$total = $iTotalArray[$j-1];
					//$j++;
				}
				break;
			}
			$usedArray[$i] = $activeSec;//$usedArray[$i-1];
			$totalArray[$i] =$total;//$totalArray[$i-1];
		}
		return true;
    }
	
	/**
     * Get app Usage info by ID.
     * 
     * @param  int    $id 
     * @access public
     * @return object|bool
     */
    public function myItemGetById($id) {
        $myItem       = $this->dao->select('*')->from(TABLE_KEVIN_CHARTEXAMPLE)->where('id')->eq($id)->fetch();

        return $myItem;
    }
  
    /**
     * Update a server.
     * 
     * @param  timeString    $begin 
     * @param  timeString   $end 
     * @param  int   $sagMinute ,default 10 minutes
     * @param  array   $timeArray output
     * @access public
     * @return void
     */
    public function timeCreateArray($begin,$end,$sagMinute = 10) {
		if($sagMinute <=0) $sagMinute = 10;
		$chartData = &$this->chartData;
		if(!$chartData) $chartData = new stdClass ();
		$chartData->timeArray = array();
		$timeArray = & $chartData->timeArray;
		$chartData->sagMinute = $sagMinute;
		
		$sag = $sagMinute *60;
		$chartData->sag = $sag;
		$halfSag = $sag/2;
		
        $beginTime = ((int)((strtotime($begin) + $halfSag)/$sag))*$sag;

        $endTime =  ((int)((strtotime($end) + $halfSag)/$sag))*$sag;

		// date("Y-m-d H:i",$i);
		for($i = $beginTime;$i<$endTime;$i+=$sag){
			$timeArray[] = $i;
		}

		return true;
    }
}
