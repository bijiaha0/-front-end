<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/tablesorter.html.php'; ?>
<div id='featurebar'>
    <ul class='nav'>
		<li><i class="icon-user"><?php echo $this->kevinhours->employeesAll[$this->kevinhours->account]->realname; ?> </i><i class="icon-angle-right"></i></li>
			<?php include './commontitlebar.html.php'; ?>
    </ul>  
</div>
<style>
.DIV-KEVIN-Left{
	border:1px solid #ccc;
	padding-right:10px;
}
</style>
<!--<div id="searchResult">-->
  <!--<div id="defaultMenu" class="search-list panel-heading nobr DIV-KEVIN-Left">-->
	  <?php echo $overtreetitle;?>
<!-- <div>激活用户</div>
    <div>  <ul>
    <li>用户1</li> 
    <li>用户2</li>
	</ul>
    <div class="pull-left actions"><a id="more" href="javascript:switchMore()">未激活用户 <i class="icon-angle-right"></i></a></div>
   </div>
  <div id="moreMenu">
    <ul>
    <li>用户1</li> 
    <li>用户2</li> 
	</ul>
  </div>-->
  <!--</div>-->
  <div class="main" style="margin-left:<?php if(common::hasPriv('kevinhours', 'browseDeptHours'))echo $config->kevinhours->sideWidth+20;?>px;">
  <div style="padding-left:10px;">
		<table class='table table-condensed table-hover table-striped tablesorter table-fixed' id='kevinhoursList' width="100%">
			<thead>
				<tr class='text-left'>
					<th class='text-left w-50px'>序号</th>
					<th class='text-left w-120px'>项目代号</th>
					<th class='text-left w-auto'>项目名称</th>
					<th class='text-center w-80px'>正常</th>
					<th class='text-center w-80px'>加班</th>
					<th class='text-center w-80px'>总计</th>
					<th class='text-left  w-80px'>%</th>
					<th class='text-left w-150px'>比例图</th>
				</tr>
			</thead>
			<?php
			$id				 = 0;
			$totalHours		 = round($countHours[0]->total / 60, 1);
			$totalNorHours	 = round($countHours[0]->nor / 60, 1);
			$totalOveHours	 = round($countHours[0]->ove / 60, 1);
	//遍历项目数组
			foreach ($todos as $todo) {
				if ($todo->total == 0)
					continue;
				$id+=1;
				$tempNor	 = round($todo->nor / 60, 1);
				$tempOve	 = round($todo->ove / 60, 1);
				$tempTotal	 = round($todo->total / 60, 1);
				$tempWidth	 = sprintf('%.1f', $tempTotal / $totalHours * 100, 0, -2);
				$tempStyle	 = $tempWidth . 'px';
				$tempPercent = $tempWidth . '%';
				$projectObj	 = $this->loadModel('project')->getById($todo->project);
				echo "<tr class='text-center'>";
				echo "<td class='text-left'>$id</td>";
				echo "<td class='text-left'>$todo->project</td>";
				if ($this->loadModel('project')->checkPriv($projectObj)) {
					echo "<td class='text-left'>" . html::a($this->createLink('kevinhours', 'project', "projectID=$todo->project&type=$type", '', true)
						, '<i class="icon icon-comment-line"></i>', '', "data-toggle='modal' data-type='iframe' title='$lang->modalTip'") . ' '
					. html::a($this->createLink('kevinhours', 'project', "projectID=$todo->project&type=$type"), $todo->name) . "</td>";
				}
				else {
					echo "<td class='text-left text-muted'><i title=\"{$lang->kevinhours->accessDenied}\" class=\"icon-ban-circle\"> $todo->name</i></td>";
				}
				echo "<td>$tempNor</td>";
				echo "<td>$tempOve</td>";
				echo "<td>$tempTotal</td>";
				echo "<td class='text-left w-150px'>$tempPercent</td>";
				echo "<td class='text-left w-150px'><div class='progressbar' style='width:$tempStyle'>&nbsp;</div></td>";
				echo "</tr>";
			}
			echo "<tr class='text-center'>";
			echo "<td class='text-left'>总计</td><td></td><td></td>";
			echo "<td class='text-center'>$totalNorHours</td>";
			echo "<td class='text-center'>$totalOveHours</td>";
			echo "<td class='text-center'>$totalHours</td><td></td><td></td>";
			echo "</tr>";
			?>
		</table>
	</div>
</div>
  <!--</div>-->
<?php include '../../common/view/footer.html.php'; ?>
<script>
	var myname = $('myname');
	var realname = "<?php echo $this->kevinhours->realname; ?>";
	myname.after(realname);
	var currentAccount = '<?php echo $this->kevinhours->account; ?>';
	var currentdept = '<?php echo $this->kevinhours->accountdept; ?>';
	var nextMonth = '<?php echo $nextMonth; ?>';
	var lastMonth = '<?php echo $lastMonth; ?>';
	var thisMonth = '<?php echo $thisMonth;?>';
	var methodName = '<?php echo 'my'; ?>';
</script>
