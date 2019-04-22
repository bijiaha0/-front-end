<?php
/**
 * The feature app line view file of kevinchart module
 *
 * @copyright   Kevin
 * @author      Kevin<3301647@qq.com>
 * @date		 2015-10-5
 * @package     kevinchart
 */
?>
<?php include '../../kevincom/view/header.html.php'; ?>
<?php include '../../common/view/datepicker.html.php'; ?>
<?php include './indexheader.html.php'; ?>
<?php
$strAppend	 = "";
$xAxisData	 = $strAppend;
$seriesData1 = $strAppend;
$seriesData2 = $strAppend;
$timeArray	 = &$chartData->timeArray;
$countPoints = count($timeArray);
$xAxisData	 = "";
$TimeRange	 = 86400;
$TimeBegin	 = "";
$TimeEnd	 = "";
if ($countPoints > 2) {
	$TimeRange	 = end($timeArray) - $timeArray[0];
	$TimeEnd	 = Date("Y-m-d H:i", end($timeArray));
	$TimeBegin	 = Date("Y-m-d H:i", $timeArray[0]);
}
$isShowDay	 = ($TimeRange > 129600); //1.5 day
$i			 = 0;
//get xAxisData
foreach ($timeArray as $timeItem):
	$Time = $isShowDay ? Date("Y-m-d H:i", $timeItem) : Date("H:i", $timeItem);
	$xAxisData .= "'" . $Time . "'";

	if ($countPoints == $i) {
		$strAppend = "";
	} else if ($i % 20 == 19) {
		$strAppend = "\n,";
	} else {
		$strAppend = ",";
	}
	$xAxisData .= $strAppend;
	$i++;
endforeach;
$i				 = 0;
$serieNames		 = "";
$countSerials	 = count($chartData->serials);
foreach ($chartData->serials as $key => $serial):
	if ($i > 0) $serieNames .= ',';
	$serieNames .= "'monitor'";
	if ($countSerials == 1) $serieNames .=",'total'";
	$i++;
endforeach;
?>

<div class='main'>
	<div>百度Echarts 示例：本页显示了从数据库读取数据，并动态显示的效果。可以拖动时间轴。<br>
		Kevin 3301647@qq.com</div>
	<?php
	/* 	
	  echo $seriesData1 . "\n<br>";
	  echo $seriesData2 . "\n<br>";
	 * 
	 */
	if (0 == $countSerials) echo "<h1>没有数据可以显示，请选择正确的日期</h1>";
	?>
	<!--Step:1 Prepare a dom for ECharts which (must) has size (width & hight)-->
	<!--Step:1 为ECharts准备一个具备大小（宽高）的Dom-->
	<div id="lineChart" style="height:500px;width:80%;border:0px solid #ccc;padding:10px;"></div>
	<!-- ECharts单文件引入 -->

	<?php
	if ($TimeBegin) echo "<center>Chart data from " . $TimeBegin . " To " . $TimeEnd;
	?>
</div>
<?php include '../../common/view/footer.html.php'; ?>

<?php js::import($jsRoot . 'echarts/echarts.min.js'); ?>
<?php js::import($jsRoot . 'echarts/theme/shine.js'); ?>
<script type="text/javascript">
	// 基于准备好的dom，初始化echarts图表
	var myChart = echarts.init(document.getElementById('lineChart'), 'shine');
			option = {
			title: {
			text: 'Kevin表格曲线',
					subtext: '',
					x: 'center'
			},
					tooltip : {
					trigger: 'axis',
							formatter: function(params) {
							return params[0].name + '<br/>'
									+ params[0].seriesName + ' : ' + params[0].value + '<br/>'
									+ params[1].seriesName + ' : ' + params[1].value;
							}
					},
					legend: {
					data: [<?php echo $serieNames; ?>],
							orient : 'vertical',
							x: 'right'
					},
					toolbox: {
					show: true,
							x: 'left',
							feature: {
							mark: {show: true},
									dataView: {show: true, readOnly: true},
									magicType: {show: true, type: ['line', 'bar', 'stack', 'tiled']},
									restore: {show: true},
									saveAsImage: {show: true}
							}
					},
					dataZoom: {
					show: true,
							realtime: true,
							start: 0,
							end: 100
					},
					xAxis: [
					{
					type: 'category',
							boundaryGap: true,
							axisLine: {onZero: false},
							data: [<?php echo $xAxisData; ?>]
					}
					],
					yAxis: [
					{
					name: '数量',
							type: 'value',
					},
					],
					series: [
<?php
foreach ($chartData->serials as $key => $serial):
	$strAppend	 = "";
	$seriesData1 = $strAppend;
	$seriesData2 = $strAppend;
	$totalArray	 = &$serial->totalArray;
	$usedArray	 = &$serial->usedArray;

	$i = 0;
	//get xAxisData
	foreach ($timeArray as $timeItem):
		$Time = Date("Y-m-d H:i", $timeItem);
		$seriesData1 .= $usedArray[$i];
		$seriesData2 .= $totalArray[$i];

		if ($countPoints == $i) {
			$strAppend = "";
		} else if ($i % 20 == 19) {
			$strAppend = "\n,";
		} else {
			$strAppend = ",";
		}
		$seriesData1 .= $strAppend;
		$seriesData2 .= $strAppend;
		$i++;
	endforeach;
	?>
						{
						name: 'monitor',
								type: 'line',
								itemStyle: {normal: {areaStyle: {type: 'default'}}},
								data: [<?php echo $seriesData1; ?>]
						},
	<?php if ($countSerials == 1): ?>
							{
							name: 'total',
									type: 'line',
									yAxisIndex: 0,
									itemStyle: {normal: {areaStyle: {type: 'default'}}},
									data: [<?php echo $seriesData2; ?>]
							},
	<?php endif; ?>
<?php endforeach; ?>
					]
			};
			// 为echarts对象加载数据 
			myChart.setOption(option);
</script>