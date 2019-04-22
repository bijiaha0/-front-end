<?php
/**
 * The browse view file of product module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2013 青岛易软天创网络科技有限公司 (QingDao Nature Easy Soft Network Technology Co,LTD www.cnezsoft.com)
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     product
 * @version     $Id: browse.html.php 4909 2013-06-26 07:23:50Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/tablesorter.html.php'; ?>
<?php include '../../common/view/datepicker.html.php'; ?>
<?php include '../../common/view/treeview.html.php'; ?>
<div id='featurebar'>
	<ul class='nav'>
		<li class=""><a><?php echo $products[$productID] . '&nbsp;';?><span class='icon-caret-left'></span></a></li>
		<?php include './commontitlebar.html.php'; ?>
	</ul>
	<?php
	$state	 = '';
	if ('true' == $isShowDetail)
		$state	 = 'checked=checked';
	?>
	<div class='actions'><?php echo html::checkbox('isShowDetail', $lang->kevinhours->isShowDetail, 'checked', "$state onclick='changePageState(this);' 'class='form-control'"); ?></div>
</div>
<div class='side' id='treebox'>
	<a class='side-handle' data-id='productTree'><i class='icon-caret-left'></i></a>
	<div class='side-body'>
		<div class='panel panel-sm'>
			<div class='panel-heading nobr'><?php echo html::icon($lang->icons['product']); ?> <strong><?php echo '产品列表'; ?></strong></div>
			<div class='panel-body'>
				<ul class='tree'>
					<?php
					foreach ($products as $currentProductID => $currentProduct) {
						echo '<li>' . html::a(helper::createLink('kevinhours', 'product', "productID=$currentProductID&type=$type"), $currentProduct, '', "class='link'") . '</li>';
					}
					?>
				</ul>
			</div>
		</div>
	</div>
</div>
<div class='main'>
	<form method='post' id='projectform'>
		<table class='table table-condensed table-hover table-striped tablesorter table-fixed' id='kevinhoursList'>
			<thead>
				<tr class='text-center'>
					<th>项目名称</th>
					<th>项目代号</th>
					<th>正式</th>
					<th>外协</th>
					<th>总计</th>
					<th>比例图</th>
				</tr>
			</thead>
			<?php
			$productHours				 = 0;
			$productforHours			 = 0;
			$productinfHours			 = 0;
			$projectTotalHoursArray		 = array();
			$projectForHoursHoursArray	 = array();
			$projectInfHoursHoursArray	 = array();
			$projectNameArray			 = array();
//遍历项目数组
			foreach ($projectArray as $project) {
				$formalHours		 = 0;
				$informalHours		 = 0;
				$totalHours			 = 0;
				$totalProductHours	 = 0;
				$projectName		 = $this->kevincom->getProjectNameByProject($project);
				//遍历考勤
				foreach ($allTodos as $todo):
					$totalProductHours += $todo->minutes;
					if ($todo->project == $project) {
						if ($todo->code == '') {
							$informalHours+=$todo->minutes;
							$totalHours+=$todo->minutes;
						}
						else {
							$formalHours+=$todo->minutes;
							$totalHours+=$todo->minutes;
						}
					}
				endforeach;
				$productHours += $totalHours;
				$productforHours += $formalHours;
				$productinfHours += $informalHours;
				$formalHours	 = $this->kevinhours->showHours($formalHours, 2);
				$informalHours	 = $this->kevinhours->showHours($informalHours, 2);
				if ($totalHours > 0) {
					$totalPercent						 = ($totalHours / $totalProductHours) * 100 . '%';
					$totalHours							 = $this->kevinhours->showHours($totalHours, 2);
					$projectTotalHoursArray[$project]	 = $totalHours;
					$projectForHoursHoursArray[$project] = $formalHours;
					$projectInfHoursHoursArray[$project] = $informalHours;
					$projectNameArray[$project]			 = $projectName;
					$projectObj							 = $this->loadModel('project')->getById($project);
					echo "<tr class='text-center'>";
					if ($this->loadModel('project')->checkPriv($projectObj)) {
						echo "<td class='text-left'>" . html::a($this->createLink('kevinhours', 'project', "projectID=$project&type=$type", '', true)
							, '<i class="icon icon-comment-line"></i>', '', "data-toggle='modal' data-type='iframe' title='$lang->modalTip'") . ' '
						. html::a($this->createLink('kevinhours', 'project', "projectID=$project&type=$type"), $projectName) . "</td>";
					}
					else {
						echo "<td class='text-left text-muted'><i title=\"{$lang->kevinhours->accessDenied}\" class=\"icon-ban-circle\"></i> $projectName</td>";
					}
					echo "<td>$project</td>";
					echo "<td>$formalHours</td>";
					echo "<td>$informalHours</td>";
					echo "<td>$totalHours</td>";
					echo "<td class='text-left'><svg width=$totalPercent height='20' version='1.1'><rect x='0' y='0' width=100% height=100% style='fill:green'/></svg></td>";
					echo "</tr>";
				}
			}
			?>
		</table>
		<table class='table table-condensed table-hover table-striped tablesorter table-fixed' id='kevinhoursList'>
			<tr class='text-center'>
				<th><font size='3'>合计</font></th>
				<th></th>
				<th><font size='3'>
					<?php echo $this->kevinhours->showHours($productforHours, 2); ?>
					</font></th>
				<th><font size='3'>
					<?php echo $this->kevinhours->showHours($productinfHours, 2); ?>
					</font></th>
				<th><font size='3'>
					<?php
					$projectTotalHours = $this->kevinhours->showHours($productHours, 2);
					echo $projectTotalHours;
					?>
					</font></th>
				<th></th>
			</tr>
		</table>
		<?php if ($pager != null) { ?>
			<br>
			<table class='table table-condensed table-hover table-striped table-fixed' id='todoList'>
				<thead><tr><td colspan='8'><?php $pager->show(); ?></td></tr></thead>
				<tr class='text-center'>
					<th class='w-id'>    <?php echo $lang->idAB; ?></th>
					<th class='w-date'>  <?php echo $lang->kevinhours->realname; ?></th>
					<th class='w-date'>  <?php echo $lang->kevinhours->date; ?></th>
					<th>                 <?php echo $lang->kevinhours->name; ?></th>
					<th class='w-100px'> <?php echo $lang->kevinhours->hourstype; ?></th>
					<th class='w-80px'>  <?php echo $lang->kevinhours->hours; ?></th>
					<th class='w-hour'>  <?php echo $lang->kevinhours->beginAB; ?></th>
					<th class='w-hour'>  <?php echo $lang->kevinhours->endAB; ?></th>
				</tr>
				<tbody>
					<?php
					$id = 0;
					foreach ($todos as $todo):$id+=1;
						?>
						<tr class='text-center'>
							<td class='text-left'><?php echo $id; ?></td>
							<td class='text-left'><?php echo $todo->realname; ?></td>
							<td><?php echo $todo->date == '2030-01-01' ? $lang->kevinhours->periods['future'] : $todo->date; ?></td>
							<td class='text-left'><?php echo html::a($this->createLink('todo', 'view', "id=$todo->id&from=my", '', true), $todo->name, '', "data-toggle='modal' data-type='iframe' data-title='" . $lang->kevinhours->view . "' data-icon='check'"); ?></td>
							<td style="background-color:<?php echo $config->kevinhours->fontColor[$todo->hourstype]; ?>"><?php echo $lang->kevinhours->hoursTypeList[$todo->hourstype]; ?></td>
							<td><?php echo $this->kevinhours->showWorkHours($todo->minutes); ?></td>
							<td><?php echo $todo->begin; ?></td>
							<td><?php echo $todo->end; ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
				<tfoot><tr><td colspan='8'><?php $pager->show(); ?></td></tr></tfoot>
			</table>
		<?php } ?>
	</form>
</div>
<?php include '../../common/view/footer.html.php'; ?>
<script>
	var currentProductID = '<?php echo $productID; ?>';
	var nextMonth = '<?php echo $nextMonth; ?>';
	var lastMonth = '<?php echo $lastMonth; ?>';
	var thisMonth = '<?php echo $thisMonth;?>';
	var methodName = '<?php echo 'product'; ?>';
	function changeDate(date)
	{
		var productID = <?php echo $productID; ?>;
		date = date.replace(/\-/g, '');
		link = createLink('kevinhours', 'product', 'productID=' + productID + '&type=' + date);
		location.href = link;
	}
	function changePageState(switcher)
	{
		var productID = <?php echo $productID; ?>;
		var type = "<?php echo $type; ?>";
		var link;
		if (switcher.checked)
		{
			link = createLink('kevinhours', 'product', 'productID=' + productID + '&type=' + type + '&isShowDetail=true');
		}
		else
		{
			link = createLink('kevinhours', 'product', 'productID=' + productID + '&type=' + type + '&isShowDetail=false');
		}
		location.href = link;
	}
</script>
