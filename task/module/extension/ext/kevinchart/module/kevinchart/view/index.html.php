<?php
/**
 * The browse app view file of kevinchart module
 *
 * @copyright   Kevin
 * @author      Kevin<3301647@qq.com>
 * @date		 2015-10-4
 * @package     kevinchart
 */
?>
<?php
include '../../kevincom/view/header.html.php';
include '../../common/view/treeview.html.php';
?>	
<!-- ECharts单文件引入 -->
<?php js::import($jsRoot . 'echarts/echarts.min.js'); ?>   

<?php js::import($jsRoot . 'echarts/theme/shine.js'); ?>
<?php js::import($jsRoot . 'echarts/theme/dark.js'); ?>
<?php js::import($jsRoot . 'echarts/theme/roma.js'); ?>
<?php js::import($jsRoot . 'echarts/theme/infographic.js'); ?>
<div class='main'>
	<div>本页显示了echarts的原有示例，静态数据在禅道上的显示效果<br>
		Kevin 3301647@qq.com</div>
    <!--Step:1 Prepare a dom for ECharts which (must) has size (width & hight)-->
    <!--Step:1 为ECharts准备一个具备大小（宽高）的Dom-->
	<table style="width:100%;">
		<tr>
			<td width = 50%>主题：shine<div id="main1" style="height:500px;border:1px solid #ccc;padding:10px;"></div></td>
			<td width = 50%>主题：dark<div id="main3" style="height:500px;border:1px solid #ccc;padding:10px;"></div></td>
		</tr>
		<tr>
			<td width = 50%>主题：infographic<div id="main2" style="height:500px;border:1px solid #ccc;padding:10px;"></div></td>
			<td width = 50%></td>
		</tr>
	</table>

	<div id="lowie-main"><div id="theme-example"><h4>主题使用示例</h4><br>	vintage
			dark
			macarons
			infographic
			shine
			roma<pre class="html">&lt;script src="echarts.js"&gt;&lt;/script&gt;
&lt;!-- 引入 vintage 主题 --&gt;

&lt;?php js::import($jsRoot . 'echarts/echarts.min.js'); ?&gt;
&lt;?php js::import($jsRoot . 'echarts/theme/shine.js'); ?&gt;
&lt;script&gt;
// 第二个参数可以指定前面引入的主题
var chart = echarts.init(document.getElementById('main'), 'shine');
chart.setOption({
    ...
});
&lt;/script&gt;
			</pre></div></div></div>
</div>
<?php include '../../common/view/footer.html.php'; ?>

<script type="text/javascript">
	//--- 折柱 ---
	var myChart1 = echarts.init(document.getElementById('main1'), 'shine');
	var myOption1 = {
		tooltip: {
			trigger: 'axis'
		},
		legend: {
			data: ['蒸发量', '降水量']
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
				data: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月']
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
				name: '蒸发量',
				type: 'bar',
				data: [2.0, 4.9, 7.0, 23.2, 25.6, 76.7, 135.6, 162.2, 32.6, 20.0, 6.4, 3.3]
			},
			{
				name: '降水量',
				type: 'bar',
				data: [2.6, 5.9, 9.0, 26.4, 28.7, 70.7, 175.6, 182.2, 48.7, 18.8, 6.0, 2.3]
			}
		]
	}
	myChart1.setOption(myOption1);

	// 基于准备好的dom，初始化echarts实例
	var myChart2 = echarts.init(document.getElementById('main2'), 'infographic');

	// 指定图表的配置项和数据
	var myOption2 = {
		title: {
			text: 'ECharts 入门示例'
		},
		tooltip: {},
		legend: {
			data: ['销量']
		},
		xAxis: {
			data: ["衬衫", "羊毛衫", "雪纺衫", "裤子", "高跟鞋", "袜子"]
		},
		yAxis: {},
		series: [{
				name: '销量',
				type: 'bar',
				data: [5, 20, 36, 10, 10, 20]
			}]
	};

	// 使用刚指定的配置项和数据显示图表。
	myChart2.setOption(myOption2);

	// --- 地图 ---
	var myChart3 = echarts.init(document.getElementById('main3'), 'dark');
	function randomData() {
		return Math.round(Math.random() * 1000);
	}

	var myOption3 = {
		tooltip: {
			trigger: 'item',
			formatter: "{a} <br/>{b}: {c} ({d}%)"
		},
		legend: {
			orient: 'vertical',
			x: 'left',
			data: ['直达', '营销广告', '搜索引擎', '邮件营销', '联盟广告', '视频广告', '百度', '谷歌', '必应', '其他']
		},
		series: [
			{
				name: '访问来源',
				type: 'pie',
				selectedMode: 'single',
				radius: [0, '30%'],
				label: {
					normal: {
						position: 'inner'
					}
				},
				labelLine: {
					normal: {
						show: false
					}
				},
				data: [
					{value: 335, name: '直达', selected: true},
					{value: 679, name: '营销广告'},
					{value: 1548, name: '搜索引擎'}
				]
			},
			{
				name: '访问来源',
				type: 'pie',
				radius: ['40%', '55%'],
				data: [
					{value: 335, name: '直达'},
					{value: 310, name: '邮件营销'},
					{value: 234, name: '联盟广告'},
					{value: 135, name: '视频广告'},
					{value: 1048, name: '百度'},
					{value: 251, name: '谷歌'},
					{value: 147, name: '必应'},
					{value: 102, name: '其他'}
				]
			}
		]
	};
	myChart3.setOption(myOption3);
</script>
