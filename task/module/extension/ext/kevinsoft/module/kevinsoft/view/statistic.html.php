<?php include '../../kevincom/view/header.html.php'; ?>
<?php include '../../common/view/datepicker.html.php'; ?>

<?php js::set('confirmDelete', $lang->kevinsoft->confirmDelete); ?>
<!-- ECharts单文件引入 -->
<?php js::import($jsRoot . 'echarts/echarts.min.js'); ?>  
<div class='side' id='treebox'>

	<div class='col-md-13 col-lg-13'>
	<a class='side-handle' data-id='companyTree'><i class='icon-caret-left'></i></a>
	<div class='side-body'>
		<div class='panel panel-sm'>
			<div class='panel-heading nobr'><?php echo "<i class='icon-bar-chart'></i>"; ?> <strong><?php echo '统计列表'; ?></strong></div>
			<div class='panel-body'>
				<ul class='tree'>
					<?php
					foreach ($this->lang->kevinsoft->statisticType as $currentStatisticType => $currentStatisticName) {
						echo '<li>' . html::a(helper::createLink('kevinsoft', 'statistic', "type=$currentStatisticType"), $currentStatisticName, '', "class='link'") ;
						if($statisticType == $currentStatisticType) echo html::icon($lang->icons['story']);
						echo '</li>';
					}
					?>
				</ul>
			</div>
		</div>
	</div></div></div>
	<div class='main'>
		<?php
		if ($statisticType) include 'statistic' . $statisticType . '.html.php';
		else echo '<h1>no such type:"' . $statisticType . ' "</h1>';
		?>
	</div>

<?php include '../../common/view/footer.html.php'; ?>
