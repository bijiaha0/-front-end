<?php
/**
 * The over view file of kevinhours
 *
 */
?>
<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/datepicker.html.php'; ?>
<?php
$user = & $this->kevinhours->user;
$deptLink =  html::a($this->createLink('kevinhours', 'overdept', 'type='.$type. '&dept='. $deptID), "<i class='icon-building'></i> " . $lang->kevinhours->overdept, '', "class='btn' ");
?>
<div id='featurebar'>
	<ul class='nav'>
		<li><b><?php 
		echo $lang->kevinhours->over;?> </b> <i class="icon-angle-right"></i> <i class='icon-user'><?php echo $this->kevinhours->employeesAll[$this->kevinhours->account]->realname; ?> </i><i class="icon-angle-right"></i></li>
		<?php
		unset($monthList['']);
		echo '<li>' . html::select('year', $yearList, $currentYear, 'onchange=getNewOverList() class=form-control') . '</li>';
		if (empty($disablePeriods)) {
			foreach ($lang->kevinhours->periods as $period => $label) {
				if($period =='all')continue;
				$vars	 = "type=$period&account=$user->account&deptID=$deptID";
				echo "<li id='$period'>" . html::a($this->createLink('kevinhours', $methodName, $vars), $label) . '</li>';
			}
		}	
		?>
		<script>$('#<?php echo $type; ?>').addClass('active')</script>
	</ul>  
	<div class='actions'>
		<?php if (common::hasPriv('kevinhours', 'browseDeptHours')) { echo $deptLink; } ?>
    </div>
</div>
<?php if(common::hasPriv('kevinhours', 'checkAll')||common::hasPriv('kevinhours', 'browseDeptHours')) include './overtreebar.html.php';?>
<?php // echo $this->overtreebar($type,$deptID,$user->account,'over');?>
	<?php
$xData		 = '';
$yData		 = '';
$legendData	 = "'" . $this->lang->kevinhours->over . "',";
$yTitle		 = $this->lang->kevinhours->count;
$i			 = 1;
$items		 = & $todos;
foreach ($items as $item):
	if (1 != $i) {
		$xData .= ",";
		$yData .= ",";
	}
	$item->hours = (int)($item->minutesSum / 60 * 100) /100;
	$xData .= "'" . substr($item->month, -5) . "'"; // "'" . substr($item->month ,-2). "'";
	$yData .= $item->hours;
	$i++;
endforeach;
?>
<div class="main" style="margin-left:<?php if(common::hasPriv('kevinhours', 'browseDeptHours'))echo $config->kevinhours->sideWidth+20;?>px;">
	<!--Step:1 Prepare a dom for ECharts which (must) has size (width & hight)-->
	<!--Step:1 为ECharts准备一个具备大小（宽高）的Dom-->
	<div id="mainChart" style="height:500px;border:0px solid #ccc;padding:10px;"></div>

	<!-- ECharts单文件引入 -->
	<?php js::import($jsRoot . 'echarts/echarts.min.js');?>

	<table class='table table-condensed table-datatable table-striped  tablesorter  w-400px' id='todoList'>
		<?php $vars = "type=$type&account=$account&dept=$deptID"; ?>
		<thead>
			<tr class='text-center'>
				<th class='w-id'>    <?php echo "Month"; ?></th>
				<th class='w-date'> <?php echo "Account"; ?></th>
				<th>              <?php echo "Sum" ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($todos as $todo): ?>
				<tr class='text-center'>
					<td class='text-left'><?php echo $todo->month; ?></td>
					<td class='text-center'><?php echo $todo->account; ?></td>
					<td><?php echo sprintf("%.1f",$todo->hours); ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
<?php
js::set('listName', 'todoList');
include '../../common/view/footer.html.php';
?>
<script language='Javascript'>
	var currentAccount = '<?php echo $account; ?>';
	var nextMonth = '<?php echo $nextMonth; ?>';
	var lastMonth = '<?php echo $lastMonth; ?>';
	var thisMonth = '<?php echo $thisMonth; ?>';
	var currentdeptID='<?php echo $this->session->currentdeptID;?>';
	var type='<?php echo $type;?>';
	var methodName = 'over';
</script>
<script type="text/javascript">
	//--- 折柱 ---
	var myChart = echarts.init(document.getElementById('mainChart'));
	myChart.setOption({
		title: {
		text: '<?php echo $this->kevinhours->employeesAll[$this->kevinhours->account]->realname; ?>',
				subtext: '加班曲线',
				x: 'center'
		},
		tooltip: {
			trigger: 'axis'
		},
		legend: {
			data: [<?php echo $legendData; ?>]
		},
		toolbox: {
			show: true,
			feature: {
				mark: {show: true},
				dataView: {show: true, readOnly: false},
				magicType: {show: true, type: ['line', 'bar']},
				restore: {show: true},
				saveAsImage: {show: true}
			}
		},
		calculable: true,
		xAxis: [
			{
				type: 'category',
				'axisLabel': {'interval': 0<?php if (count($items) > 16 && count($items) < 40) echo ",'rotate':30";else if (count($items) > 40) echo ",'rotate':60"; ?>},
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
			{
				name: '<?php echo $yTitle; ?>',
				type: 'bar',
				itemStyle: {
					normal: {
						color:['#3398DB'],
						label: {
							show: true,
							position: 'top'
						}
					}
				},
				data: [<?php echo $yData; ?>]
			}
		]
	});
	$("#mainChart").resize(function(){
		$(myChart).resize();
	});
</script>