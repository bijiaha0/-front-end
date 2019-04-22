<div><?php echo "&nbsp&nbsp&nbsp&nbsp&nbsp".$this->lang->kevinsoft->statisticType[$statisticType]; ?></div>
<!--Step:1 Prepare a dom for ECharts which (must) has size (width & hight)-->
<!--Step:1 为ECharts准备一个具备大小（宽高）的Dom-->
<div id="mainChart" style="height:500px;border:0px solid #ccc;padding:0px;"></div>
<div>
	<table class='table table-condensed table-hover table-striped tablesorter w-400px' id='kevinsoftCountList'>
		<thead>
			<tr class='colhead text-center'>
				<th class='w-80px'>    <?php echo $lang->kevinsoft->serial; ?></th>
				<th class='w-200px text-left'>    <?php echo $lang->kevinsoft->grouptype; ?></th>
				<th class='w-120px'>    <?php echo $lang->kevinsoft->floatCount; ?></th>
				<th class='w-120px'>    <?php echo $lang->kevinsoft->fixCount; ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			$totalarray=array();
			foreach($items as $item){
//				$totalarray[$item->groupName]=$item->groupName;
				if($item->type=='float'){
					$totalarray[$item->groupName]['float']=$item->totalCount;
					if(!isset($totalarray[$item->groupName]['fix']))
						$totalarray[$item->groupName]['fix']='0';
				}elseif($item->type=='fix'){
					$totalarray[$item->groupName]['fix']=$item->totalCount;
					if(!isset($totalarray[$item->groupName]['float']))
						$totalarray[$item->groupName]['float']='0';
				}
			}
//			die(js::alert(var_dump($totalarray)));
			$xData		 = '';
			$yDatafloat	 = '';
			$yDatafix	 = '';
			$legendData	 = "'" . $lang->kevinsoft->floatCount . "','".$lang->kevinsoft->fixCount."',";
			$floatTitle		 = $lang->kevinsoft->floatCount;
			$fixTitle		 = $lang->kevinsoft->fixCount;
			$i			 = 1;
			foreach($totalarray as $xkey=>$xtotaldata):
				if (1 != $i) {
					$xData .= ",";
					$yDatafloat .= ",";
					$yDatafix .= ",";
				}
				$xData .= "'".$lang->kevinsoft->grouptypeMenu[$xkey] . "'";
				$yDatafloat .= $totalarray[$xkey]['float'];
				$yDatafix .= $totalarray[$xkey]['fix'];
				?>
			<tr class='text-center'>
					<td><?php echo $i; ?></td>
					<td class='text-left'><?php echo $lang->kevinsoft->grouptypeMenu[$xkey]; ?></td>
					<td><?php echo $totalarray[$xkey]['float']; ?></td>
					<td><?php echo $totalarray[$xkey]['fix']; ?></td>
				</tr>
				<?php
				$i++;
			endforeach;
			?>
		</tbody>
	</table>
</div>
<script type="text/javascript">
	//--- 折柱 ---
	var myChart = echarts.init(document.getElementById('mainChart'));
	myChart.setOption({
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
				'axisLabel': {'interval': 0<?php if (count($items) > 10) echo ",'rotate':20"; ?>},
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
				name:'<?php echo $floatTitle;?>',
				type: 'bar',
				itemStyle: {
					normal: {
						color: 'rgba(110,139,61,1)',
						label: {
							show: true,
							position:'top'
						}
					}
				},
				data: [<?php echo $yDatafloat;?>]
			},
			{
				name: '<?php echo $fixTitle;?>',
				type: 'bar',
				itemStyle: {
					normal: {
						//color: 'rgba(110,139,61,1)',
						label: {
							show: true,
							position:'top'
						}
					}
				},
				data: [<?php echo $yDatafix;?>]
			}
		]
	});
	$("#mainChart").resize(function(){
		$(myChart).resize();
	});
</script>