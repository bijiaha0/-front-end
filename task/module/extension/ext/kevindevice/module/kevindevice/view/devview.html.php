<?php
/**
 * The view file of user file of kevindevice module
 *
 * @copyright   Kevin
 * @author      kevin<3301647@qq.com>
 * @package     kevindevice
 */
?>
<?php include '../../kevincom/view/header.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<?php include 'kevindevicebar.html.php'; ?>
<div id='headdev'>
	<div class='heading'>
		<span class='prefix'><strong><?php echo $title . " > "; ?></strong></span>
		<strong><?php echo $device->name; ?></strong>
		<?php if ($device->deleted): ?>
			<span class='label label-danger'><?php echo $lang->kevindevice->deleted; ?></span>
		<?php endif; ?>
	</div>
	<?php
	// $browseLink  = $app->session->taskList != false ? $app->session->taskList : $this->createLink('project', 'browse', "projectID=$device->project");
	$actionLinks = '';
	if (!$device->deleted) {
		ob_start();
		echo "<div class='btn-group'>";
		common::printIcon('kevindevice', 'devedit', "userID=$device->id", 'gfdfgdf', '', 'pencil');
		common::printIcon('kevindevice', 'devdelete', "userID=$device->id", '', '', 'remove', '', 'iframe', true, "data-width='550'");
		echo '</div>';

		echo "<div class='btn-group'>";
		//  common::printRPN($browseLink, $preAndNext);
		echo '</div>';


		$actionLinks = ob_get_contents();
		ob_clean();
		//echo $actionLinks;
	} else {
		echo "this user is deleted. please first undeleted.";
	}
	?>
</div>
<div class='row-table'>
	<div class='col-main main-side' >
		<fieldset>
			<legend><?php echo $lang->kevindevice->legendBasic; ?></legend>
			<table class='table table-data table-condensed table-borderless'> 
				<tr>
					<th class='w-80px'><?php echo $lang->kevindevice->id; ?></th>
					<td class='w-auto'><?php echo $device->id; ?></td>
					<th class='w-80px'><?php echo $lang->kevindevice->name; ?></th>
					<td class='w-auto'><?php echo $device->name; ?></td>
				</tr>  
				<tr>
					<th><?php echo $lang->kevindevice->label; ?></th>
					<td><?php echo $device->label; ?></td>
					<th><?php echo $lang->kevindevice->type; ?></th>
					<td><?php echo $lang->kevindevice->DevTypeList[$device->type]; ?></td>
				</tr>
				<tr>
					<th><?php echo $lang->kevindevice->status; ?></th>
					<td><?php echo $lang->kevindevice->StatusList[$device->status]; ?></td>	
					<th><?php echo $lang->kevindevice->version; ?></th>
					<td><?php echo $device->version; ?></td>	
				</tr>  
				<tr>
					<th><?php echo $lang->kevindevice->assetNumber; ?></th>
					<td><?php echo $device->assetNumber; ?></td>
				</tr>  	
			</table>
		</fieldset>
		<fieldset>
			<legend><?php echo $lang->kevindevice->computerInfor; ?></legend>
			<table class='table table-data table-condensed table-borderless'> 
				<tr>
					<th class='w-80px'><?php echo $lang->kevindevice->tcpip; ?></th>
					<td><?php echo $device->tcpip; ?></td>
					<th><?php echo $lang->kevindevice->cpuID; ?></th>
					<td><?php echo $device->cpuID; ?></td>	
				</tr>  
				<tr>
					<th><?php echo $lang->kevindevice->deviceSN; ?></th>
					<td><?php echo $device->deviceSN; ?></td>
					<th><?php echo $lang->kevindevice->monitorSN; ?></th>
					<td><?php echo $device->monitorSN; ?></td>

				</tr>
				<tr>
					<th><?php echo $lang->kevindevice->mac; ?></th>
					<td><?php echo $device->mac; ?></td>
					<th><?php echo $lang->kevindevice->purpose; ?></th>
					<td><?php echo $device->purpose; ?></td>
				</tr>
				<tr>
					<th><?php echo $lang->kevindevice->vidioCard; ?></th>
					<td><?php echo $device->vidioCard; ?></td>	
					<th><?php echo $lang->kevindevice->discCapacity; ?></th>
					<td><?php echo $device->discCapacity; ?></td>
				</tr>
				<tr>
				    <th><?php echo $lang->kevindevice->memoryCapacity; ?></th>
					<td><?php echo $device->memoryCapacity; ?></td>	
				</tr>
			</table>
		</fieldset>
		<fieldset>
			<legend><?php echo $lang->kevindevice->desc; ?></legend>
			<div class='article-content'><?php echo nl2br($device->description); ?></div>
		</fieldset>
		<fieldset>
			<legend><?php echo $lang->kevindevice->group; ?></legend>
			<div class='article-content'><?php
				$groulist = "";
				foreach ($deviceGroups as $group):
					if ($group->id) {
						echo " <span class='prefix'><strong>";
						common::printLink('kevindevice', 'groupview', "groupid=$group->id", $group->name);
						echo "</strong></span> ";
					}
				endforeach;
				?></div>
		</fieldset>
		<div class='actions'> <?php if (!$device->deleted) echo $actionLinks; ?></div>
	</div>
	<div class='col-side main-side'>
		<fieldset>
			<legend><?php echo $lang->kevindevice->UserVisitInfor; ?></legend>
			<table class='table table-data table-condensed table-borderless'> 
				<tr>
					<th><?php echo $lang->kevindevice->join; ?></th>
					<td><?php echo $device->join; ?> </td>
				</tr>  
				<tr>
					<th><?php echo $lang->kevindevice->dieDate; ?></th>
					<td><?php echo $device->dieDate; ?> </td>
				</tr> 
				<tr>
					<th><?php echo $lang->kevindevice->dept; ?></th>
					<td><?php echo $deptName; ?></td>
				</tr>
				<tr>
					<th><?php echo $lang->kevindevice->user; ?></th>
					<td><?php echo $device->userName; ?></td>	
				</tr>  
				<tr>
					<th><?php echo $lang->kevindevice->charge; ?></th>
					<td><?php echo $device->chargeName; ?></td>	
				</tr>
			</table>
		</fieldset>
	</div>
</div>
<?php include '../../common/view/syntaxhighlighter.html.php'; ?>
<?php include '../../common/view/footer.html.php'; ?>
