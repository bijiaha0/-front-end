<?php
/**
 * kevin calendar
 * @copyright   kevin
 */
?>
<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/datepicker.html.php'; ?>
<?php
include '../../common/view/treeview.html.php';
?>
<script language='Javascript'>var account = '<?php echo $this->kevinhours->account; ?>'</script>
<div id='featurebar'>
  <ul class='nav'>
	<li><i class="icon-user"><?php echo $this->kevinhours->realname ?> </i><i class="icon-angle-right"></i></li>
	<?php include './commontitlebar.html.php'; ?>
  </ul>  
  <div class='actions'>
	  <?php
	  if ($this->kevinhours->isSelfUser && common::hasPriv('kevinhours', 'clockact')) {
		  if ($this->kevinhours->clockInor->isExistIn) {
			  echo html::a(helper::createLink('kevinhours', 'clockact', "action=out", '', true)
				  , "<i class='icon-signout'></i> " . $lang->kevinhours->clockout, '', " data-toggle='modal' data-type='iframe' class='btn'");
		  } else {
			  echo html::a(helper::createLink('kevinhours', 'clockact', "action=in", '', true)
				  , "<i class='icon-signin'></i> " . $lang->kevinhours->clockin, '', " data-toggle='modal' data-type='iframe' class='btn'");
		  }
	  }
	  ?>
  </div>
</div>
<?php
if ($employeesActive = $this->kevinhours->employeesActive) {
	$this->loadModel('dept');
	?>
	<div class='side'>
	  <div class='side-body'>
		<div class='panel panel-sm'>
		  <div class='panel-heading nobr'>
			<strong><?php
				if (!empty($deptArray)) {
					echo html::select('dept', $deptArray, $deptID, 'onchange=gotoOtherDept(this.value) class=form-control');
				} else
					echo $lang->kevinhours->deptAccount;
				?>
			  <a class='side-handle' data-id='companyTree'><i class='icon-caret-left'></i></a></strong>

		  </div>
		  <div class='panel-body'>
			  <?php
			  echo "<ul class='nav'>";
			  foreach ($employeesActive as $key => $name) {
				  if ($key) {
					  $tempKey = str_replace(".", "-", $key);
					  echo "<li id=$tempKey>" . html::a(helper::createLink('kevinhours', 'clock', "type=" . $type . "&account=" . $key . "&deptID=$deptID")
						  , "<i class='icon-user'>&nbsp;</i>" . $name, '', "") . '</li>';
				  }
			  }

			  $employeesInactive = $this->kevinhours->employeesInactive;
			  if ($employeesInactive) {
				  echo "<li id = 'InactiveUsers' style = 'background:yellow;'> 未激活</li>";
				  foreach ($employeesInactive as $key => $name) {
					  if ($key) {
						  $tempKey = str_replace(".", "-", $key);
						  echo "<li id=$tempKey>" . html::a(helper::createLink('kevinhours', 'clock', "type=" . $type . "&account=" . $key . "&deptID=$deptID")
							  , "<i class='icon-user'>&nbsp;</i>" . $name, '', "") . '</li>';
					  }
				  }
			  }
			  echo "</ul>";
			  ?>
			<script>$('#<?php echo str_replace(".", "-", $account); ?>').addClass('k-Color-W')</script>
		  </div>
		</div>
	  </div>
	</div>
	<?php
}

if ($CalendarTableString == "") {
	$warningString = '<h1>Get calendar array error!</h1>';
	echo $warningString;
} else {
	?>
	<div class="main">
	  <table width = 100%>
		<tr style="vertical-align: top;">			
		  <td> <?php echo $oveHoursTableString . $CalendarTableString; ?></td></tr></table>	 
	</div>
	<?php
}
include '../../common/view/footer.html.php';
?>
<script>
	function gotoOtherDept(deptID)
	{
		link = createLink('kevinhours', 'clock', 'type=&account=&deptID=' + deptID);
		location.href = link;
	}
</script>