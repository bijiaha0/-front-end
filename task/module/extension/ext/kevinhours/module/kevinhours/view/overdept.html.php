<?php
/**
 * The over dept view file of kevinhous module .
 *
 */
?>
<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/datepicker.html.php'; ?>
<?php include '../../common/view/tablesorter.html.php'; ?>
<?php
$user = & $this->kevinhours->user;
$employeesActive	 = array();
$employeesAll	 = $this->kevinhours->employeesAll;
foreach($employeesAll as $acc=>$employeeAll){
//	if($employeeAll->locked==0){
		$employeesActive[$acc]=new stdClass();
		$employeesActive[$acc]=$employeeAll;}
//	}
//$employeesInactive	 = $this->kevinhours->employeesInactive;
?>
<div id='featurebar'>
	<ul class='nav'>
		<li><b><?php echo $lang->kevinhours->overdept;?> <i class="icon-angle-right"></i> </b><i class="icon-building"><?php if(isset($deptname)) echo $deptname; ?> </i><i class="icon-angle-right"></i></li>
		<?php 
		unset($monthList['']);
		echo '<li>' . html::select('year', $yearList, $currentYear, 'onchange=getNewOverList() class=form-control') . '</li>';
		if (empty($disablePeriods)) {
			foreach ($lang->kevinhours->periods as $period => $label) {
				if($period =='all')continue;
				$vars	 = "type=$period&deptID=$deptID";
				echo "<li id='$period'>" . html::a($this->createLink('kevinhours', $methodName, $vars), $label) . '</li>';}
		}
		?>
		<script>$('#<?php echo $type; ?>').addClass('active')</script>
	</ul>  
	<div class='actions'>
        <?php echo html::a($this->createLink('kevinhours', 'over', 'type='.$type. '&account=' . $user->account. '&dept='. $deptID), "<i class='icon-user'></i> " . $lang->kevinhours->over, '', "class='btn' ");  ?>
    </div>	
</div>
<?php if(common::hasPriv('kevinhours', 'checkAll')||common::hasPriv('kevinhours', 'browseDeptHours')) include './overtreebar.html.php';?>
<?php // echo $this->fetch('kevinhours','overtreebar',array('type'=>$type,'deptID'=>$deptID));?>
<?php // echo $this->overtreebar($type,$deptID,$user->account,'overdept');?>
<?php 
$accountoverdatas=array();
foreach ($todos as $monthkey=>$todoaccount): 
	foreach($todoaccount as $accountkey=>$todo):
		$accountoverdatas[$accountkey][$monthkey]=(int)($todo->minutesSum / 60 * 100) /100;
//		$accountoverdatas[$accountkey][$monthkey]['minutesSum']=$todo->minutesSum; 
	endforeach; 
endforeach; 
$ignoreacc=array();
foreach($accountoverdatas as $acckey => $accountoverdata){
	$sum=0;
	 foreach($accountoverdata as $minutes)$sum+=$minutes;
	 if((int)$sum==0&&$employeesAll[$acckey]->locked==1)$ignoreacc[$acckey]=$acckey;
}
//die(js::alert(var_dump($accountoverdatas)));
$xData		 = '';
$yData		 = array();
$legendData	 = "'" . $this->lang->kevinhours->over . "',";
$yTitle		 = $this->lang->kevinhours->count;

$items		 = & $accountoverdatas;
$p=0;
foreach ($items as $month=>$accountitem):
	if(!in_array($month, $ignoreacc)){
	$yname="yData".$p;
	$titlename="yTitle".$p;
	$$yname='';
	$i	= 1;
	$$titlename=$employeesActive[$month]->realname;
	foreach($accountitem as $item):
	if (1 != $i) {
			$$yname.= ",";
	}
	$$yname.= $item;
	$i++;
	endforeach;
	//js::alert(var_dump($$yname));
	$p++;}
endforeach;
$serieNames		 = "";

?>
<?php js::import($jsRoot . 'echarts/theme/shine.js'); $examplerow=(count($accountoverdatas)+1)/9+2;?>
<div class="main" id='mainfootauto' style="margin-left:<?php echo $config->kevinhours->sideWidth+20;?>px;">
	<!--Step:1 Prepare a dom for ECharts which (must) has size (width & hight)-->
	<!--Step:1 为ECharts准备一个具备大小（宽高）的Dom-->
	<div  id="mainChart" style="height:<?php $staticn=$examplerow/25+1;echo ($staticn*500).'px;';?>border:0px solid #ccc;padding:10px;"></div>
	<!-- ECharts单文件引入 -->
	<?php js::import($jsRoot . 'echarts/echarts.min.js'); ?>  
	<?php if($accountoverdatas):?>
	<table class='table table-condensed table-datatable table-striped tablesorter table-fixed w-400px' id='todoList'>
		<?php $vars = "type=$type&dept=$deptID"; ?>
		<thead>
			<tr class='text-center'>
				<th class='w-35px'>    <?php echo 'No.';?></th>
				<th class='w-60px'>    <?php echo "Account" ?></th>
				<?php $monthoversum=array();$i=1;foreach ($todos as $monthkey=>$todomonth): ?>
				<th style='width:55px;'> <?php echo $monthkey;
				if (1 != $i) {$xData .= ",";}$xData .= "'".substr($monthkey, -5). "'"; $i++; ?></th>
					<?php foreach($todomonth as $accovertime){
						if(!isset($monthoversum[$monthkey])){$monthoversum[$monthkey]='';}
						$monthoversum[$monthkey]+=$accovertime->minutesSum/60;
					}?>
				<?php endforeach; ?>
				<th class='w-50px'><?php echo "Sum"; ?></th>
			</tr>
		</thead>
		
		<tbody >
			<?php $i=1;foreach($accountoverdatas as $keyaccount=>$accountoverdata):
				if(isset($employeesActive[$keyaccount]->isexternal)&&$employeesActive[$keyaccount]->isexternal==0&&!in_array($keyaccount,$ignoreacc)){?>
			<tr class='text-center' style='background:white;'>
				<td><?php echo $i; ?></td>
				<td class='text-center'><?php echo $employeesActive[$keyaccount]->realname; $sum=0;$serieNames.="'".$employeesActive[$keyaccount]->realname."'".',';?>  </td>
				<?php foreach($accountoverdata as $monthoverdata):?>
					<td class='text-right'><?php echo sprintf('%.1f',$monthoverdata); $sum=$sum+$monthoverdata;?></td>
				<?php	endforeach;?>
					<td class='text-right'><?php echo sprintf('%.1f',$sum);?></td>
			</tr>
			<?php $i++;} endforeach;?>
			<?php foreach($accountoverdatas as $keyaccount=>$accountoverdata):
			if(isset($employeesActive[$keyaccount]->isexternal)&&$employeesActive[$keyaccount]->isexternal==1&&!in_array($keyaccount,$ignoreacc)){?>
			<tr class='text-center' style='background:white;'>
				<td><?php echo $i; ?></td>
				<td class='text-center'><?php echo $employeesActive[$keyaccount]->realname; $sum=0;$serieNames.="'".$employeesActive[$keyaccount]->realname."'".',';?>  </td>
				<?php foreach($accountoverdata as $monthoverdata):?>
					<td class='text-right'><?php echo sprintf('%.1f',$monthoverdata); $sum=$sum+$monthoverdata;?></td>
				<?php	endforeach;?>
					<td class='text-right'><?php echo sprintf('%.1f',$sum);?></td>
			</tr>
			<?php $i++;} endforeach;$averageoverstr='';?>
			<tr class='text-center' style='background:white;'>
				<td class='text-center'></td>
				<td class='text-center'>Average</td>
				<?php foreach ($todos as $monthkey=>$todomonth): ?>
				<td class='text-right'><?php $averageover=(int)(($monthoversum[$monthkey]/(count($accountoverdatas)-count($ignoreacc)))*10)/10; echo sprintf('%.1f',$averageover);$averageoverstr.=$averageover.',';?></td>
				<?php endforeach;$averageoverstr=rtrim($averageoverstr, ',');?>
				<td class='text-right'></td>
			</tr>
		</tbody>
	</table>
	<?php endif;?>
</div>

<?php 
js::set('listName', 'todoList');
include '../../common/view/footer.html.php';
?>
<script language='Javascript'>
	//var currentAccount = '<?php //echo $account; ?>';
	var nextMonth = '<?php echo $nextMonth; ?>';
	var lastMonth = '<?php echo $lastMonth; ?>';
	var thisMonth = '<?php echo $thisMonth; ?>';
	var currentdeptID='<?php echo $this->session->currentdeptID;?>';
	var type='<?php echo $type;?>';
	var methodName = 'overdept';
</script>
<script type="text/javascript">
	//--- 折柱 ---
	var myChart = echarts.init(document.getElementById('mainChart'), 'shine');
	var markLineOpt = {
    animation: false,
    lineStyle: {
        normal: {
            type: 'dashed',
        }
    },
    tooltip: {
        formatter: 'average over'
    },
    data: [
		<?php
		if(isset($averageoverstr)){
		$averagex=explode(',', $xData);
		$averagey=explode(',', $averageoverstr);
		$str='';
		for($h=0;$h<=count($averagex)-2;$h++)
			$str.='[{ coord:['.$averagex[$h].','.$averagey[$h]."],symbol:'none'},{coord:[".$averagex[$h+1].','.$averagey[$h+1]."],symbol:'none'}],";
		echo $str;}
		 ?>]
};
	myChart.setOption({
		title: {
				text: '<?php if(isset($deptname)){$lastdept=explode('/',$deptname);echo $lastdept[count($lastdept)-1];} ?>',
				subtext: '加班曲线',
				x: 'center'
		},
		grid:{
		
			y2:'<?php echo (5/$staticn*$examplerow+3*5/$staticn).'%';?>'
		},
		tooltip: {
			trigger: 'axis'
		},
		legend: {
			data: [<?php echo $serieNames."'平均'"; ?>],
			orient : 'horizontal',
			y:'<?php echo (100-5/$staticn*$examplerow).'%';?>'
		},
		toolbox: {
			show: true,
			x:'right',
			feature: {
				mark: {show: true},
				dataView: {show: true, readOnly: false},
				magicType: {show: true, type: ['line', 'bar', 'stack', 'tiled']},
				restore: {show: true},
				saveAsImage: {show: true}
			}
		},
		dataZoom: {
					show: true,
							realtime: true,
							start: 0,
							end: 100,
							y:'<?php echo (100-5/$staticn*$examplerow-2*5/$staticn).'%';?>'
					},		
		calculable: true,
		xAxis: [
			{
				type: 'category',
				'axisLabel': {'interval': 0<?php if (count($todos) > 16 && count($todos) < 40) echo ",'rotate':30";else if (count($todos) > 40) echo ",'rotate':60"; ?>},
				data: [<?php echo $xData; ?>]
			}
		],
		yAxis: [
			{
				type: 'value',
				splitArea: {show: true}
			}
		],
		series: [
			<?php for($r=0;$r<$p;$r++):
				$yname="yData".$r;
				$titlename="yTitle".$r;
				echo "{
					name: '".$$titlename."',
					type: 'bar',
					
					data: [".$$yname."]";
				if($r == round(count($accountoverdatas)/2)){
				echo ",markLine: markLineOpt";
				}
				echo "},";
			 endfor; ?>]
	});
	$("#mainChart").resize(function(){
		$(myChart).resize();
	});
</script>